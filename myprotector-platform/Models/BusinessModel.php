<?php
/**
 * MyProtector Platform - Business Model
 * 
 * Handles all business/company database operations
 * 
 * @package MyProtector\Models
 * @version 1.0.0
 */

namespace MyProtector\Models;

class BusinessModel extends BaseModel {
    /**
     * Table name
     * 
     * @var string
     */
    protected $table = 'mp_businesses';

    /**
     * Primary key
     * 
     * @var string
     */
    protected $primary_key = 'business_id';

    /**
     * Cache group
     * 
     * @var string
     */
    protected $cache_group = 'mp_businesses';

    /**
     * Get business by slug
     * 
     * @param string $slug
     * @return object|null
     */
    public function getBySlug(string $slug) {
        $cache_key = 'slug_' . md5($slug);
        $cached = $this->getCache($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }

        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->getTableName()} WHERE business_slug = %s",
                sanitize_title($slug)
            )
        );
        
        if ($result) {
            $this->setCache($cache_key, $result);
        }
        
        return $result;
    }

    /**
     * Get business by user ID
     * 
     * @param int $user_id
     * @return object|null
     */
    public function getByUser(int $user_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->getTableName()} WHERE user_id = %d LIMIT 1",
                $user_id
            )
        );
    }

    /**
     * Get all active businesses
     * 
     * @param array $args
     * @return array
     */
    public function getAllActive(array $args = []): array {
        $defaults = [
            'category_id' => null,
            'status' => 'active',
            'orderby' => 'avg_rating',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0,
            'search' => '',
            'min_rating' => 0,
            'trust_status' => '',
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT b.*, ts.trust_status, ts.trust_score, ts.traffic_light_color
                FROM {$this->getTableName()} b
                LEFT JOIN {$this->wpdb->prefix}mp_traffic_signals ts ON b.business_id = ts.business_id
                WHERE b.business_status = %s AND b.deleted_at IS NULL";
        
        $values = [$status];
        
        if (!empty($category_id)) {
            $sql .= " AND b.category_id = %d";
            $values[] = $category_id;
        }
        
        if (!empty($search)) {
            $sql .= " AND (b.business_name LIKE %s OR b.business_description LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($search) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
        }
        
        if ($min_rating > 0) {
            $sql .= " AND b.avg_rating >= %f";
            $values[] = $min_rating;
        }
        
        if (!empty($trust_status)) {
            $sql .= " AND ts.trust_status = %s";
            $values[] = $trust_status;
        }
        
        $order = sanitize_sql_orderby("{$orderby} {$order}");
        $sql .= " ORDER BY b.{$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Create a new business
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        // Generate slug if not provided
        if (empty($data['business_slug'])) {
            $data['business_slug'] = $this->generateSlug($data['business_name'] ?? 'business');
        }
        
        // Set defaults
        $defaults = [
            'business_status' => 'pending',
            'claim_status' => 'unclaimed',
            'total_reviews' => 0,
            'approved_reviews' => 0,
            'avg_rating' => 0.00,
            'total_rating_sum' => 0,
            'response_rate' => 0.00,
            'is_verified' => 0,
            'is_featured' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        // Sanitize
        $data['business_name'] = sanitize_text_field($data['business_name'] ?? '');
        
        $result = parent::insert($data);
        
        if ($result) {
            // Create traffic signal entry
            $this->createTrafficSignal($result);
        }
        
        return $result;
    }

    /**
     * Update a business
     * 
     * @param int $business_id
     * @param array $data
     * @return bool
     */
    public function update($business_id, array $data): bool {
        // Unset protected fields
        unset($data['business_id']);
        unset($data['created_at']);
        unset($data['total_reviews']);
        unset($data['approved_reviews']);
        
        // Sanitize
        if (isset($data['business_name'])) {
            $data['business_name'] = sanitize_text_field($data['business_name']);
        }
        if (isset($data['business_slug'])) {
            $data['business_slug'] = sanitize_title($data['business_slug']);
        }
        
        $result = parent::update($business_id, $data);
        
        if ($result) {
            // Clear slug cache
            $business = $this->get($business_id);
            if ($business) {
                wp_cache_delete('slug_' . md5($business->business_slug), $this->cache_group);
            }
        }
        
        return $result;
    }

    /**
     * Verify a business
     * 
     * @param int $business_id
     * @param int $verified_by
     * @return bool
     */
    public function verify(int $business_id, int $verified_by = 0): bool {
        return $this->update($business_id, [
            'is_verified' => 1,
            'verified_at' => current_time('mysql'),
            'verified_by' => $verified_by,
            'claim_status' => 'verified',
            'business_status' => 'active',
        ]);
    }

    /**
     * Suspend a business
     * 
     * @param int $business_id
     * @param string $reason
     * @return bool
     */
    public function suspend(int $business_id, string $reason = ''): bool {
        return $this->update($business_id, [
            'business_status' => 'suspended',
            'suspension_reason' => sanitize_text_field($reason),
        ]);
    }

    /**
     * Create initial traffic signal for business
     * 
     * @param int $business_id
     * @return bool
     */
    protected function createTrafficSignal(int $business_id): bool {
        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_traffic_signals',
            [
                'business_id' => $business_id,
                'trust_status' => 'bad',
                'traffic_light_color' => 'red',
                'trust_score' => 0.00,
                'requirements_met' => json_encode([]),
                'requirements_total' => 5,
                'requirements_fulfilled' => 0,
                'has_min_reviews' => 0,
                'has_min_rating' => 0,
                'has_verified_domain' => 0,
                'has_insurance' => 0,
                'has_terms' => 0,
                'has_promise_page' => 0,
                'has_active_subscription' => 0,
                'is_auto_calculated' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]
        );
        
        return $result !== false;
    }

    /**
     * Get categories for a business
     * 
     * @param int $business_id
     * @return array
     */
    public function getCategories(int $business_id): array {
        $business = $this->get($business_id);
        
        if (!$business || !$business->category_id) {
            return [];
        }
        
        $term = get_term($business->category_id, 'mp_company_category');
        
        return $term ? [$term] : [];
    }

    /**
     * Get business social links
     * 
     * @param int $business_id
     * @return array
     */
    public function getSocialLinks(int $business_id): array {
        $business = $this->get($business_id);
        
        if (!$business) {
            return [];
        }
        
        return [
            'facebook' => $business->facebook_url ?? '',
            'twitter' => $business->twitter_url ?? '',
            'instagram' => $business->instagram_url ?? '',
            'linkedin' => $business->linkedin_url ?? '',
        ];
    }

    /**
     * Update business rating stats
     * 
     * @param int $business_id
     * @return void
     */
    public function updateRatingStats(int $business_id): void {
        $stats = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(review_rating) as avg_rating,
                    SUM(review_rating) as total_sum
                 FROM {$this->wpdb->prefix}mp_reviews 
                 WHERE business_id = %d AND review_status = 'approved'",
                $business_id
            )
        );
        
        $this->update($business_id, [
            'total_reviews' => $stats->total_reviews ?? 0,
            'avg_rating' => round($stats->avg_rating ?? 0, 2),
            'total_rating_sum' => $stats->total_sum ?? 0,
            'approved_reviews' => $stats->total_reviews ?? 0,
        ]);
    }

    /**
     * Search businesses
     * 
     * @param string $query
     * @param array $args
     * @return array
     */
    public function search(string $query, array $args = []): array {
        $defaults = [
            'status' => 'active',
            'limit' => 20,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT b.*, ts.trust_status, ts.trust_score
                FROM {$this->getTableName()} b
                LEFT JOIN {$this->wpdb->prefix}mp_traffic_signals ts ON b.business_id = ts.business_id
                WHERE b.business_status = %s 
                AND (b.business_name LIKE %s OR b.business_description LIKE %s)
                AND b.deleted_at IS NULL
                ORDER BY b.avg_rating DESC
                LIMIT %d OFFSET %d";
        
        $search_term = '%' . $this->wpdb->esc_like($query) . '%';
        
        $sql = $this->wpdb->prepare($sql, $status, $search_term, $search_term, $limit, $offset);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Generate unique slug
     * 
     * @param string $name
     * @return string
     */
    protected function generateSlug(string $name): string {
        $slug = sanitize_title($name);
        $original_slug = $slug;
        $count = 1;
        
        while ($this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE business_slug = %s",
                $slug
            )
        ) > 0) {
            $slug = $original_slug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    /**
     * Get businesses by reseller
     * 
     * @param int $reseller_id
     * @param array $args
     * @return array
     */
    public function getByReseller(int $reseller_id, array $args = []): array {
        $defaults = [
            'status' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT * FROM {$this->getTableName()} WHERE reseller_id = %d";
        $values = [$reseller_id];
        
        if (!empty($status)) {
            $sql .= " AND business_status = %s";
            $values[] = $status;
        }
        
        $order = sanitize_sql_orderby("{$orderby} {$order}");
        $sql .= " ORDER BY {$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Count businesses by status
     * 
     * @param string|null $status
     * @param int|null $reseller_id
     * @return int
     */
    public function countByStatus(string $status = null, int $reseller_id = null): int {
        $where = ['deleted_at IS NULL'];
        $values = [];
        
        if (!empty($status)) {
            $where[] = 'business_status = %s';
            $values[] = $status;
        }
        
        if (!empty($reseller_id)) {
            $where[] = 'reseller_id = %d';
            $values[] = $reseller_id;
        }
        
        $sql = "SELECT COUNT(*) FROM {$this->getTableName()} WHERE " . implode(' AND ', $where);
        
        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }
        
        return (int) $this->wpdb->get_var($sql);
    }

    /**
     * Check if business has WooCommerce subscription
     * 
     * @param int $business_id
     * @return bool
     */
    public function hasActiveSubscription(int $business_id): bool {
        $business = $this->get($business_id);
        
        if (!$business || !$business->woocommerce_id) {
            return false;
        }
        
        // Check WooCommerce subscription status
        if (class_exists('WC_Subscriptions')) {
            $subscriptions = wcs_get_subscriptions([
                'customer_id' => $business->user_id,
                'status' => ['active', 'pending'],
            ]);
            
            return !empty($subscriptions);
        }
        
        return false;
    }

    /**
     * Link WooCommerce customer to business
     * 
     * @param int $business_id
     * @param int $woocommerce_order_id
     * @return bool
     */
    public function linkWooCommerce(int $business_id, int $woocommerce_order_id): bool {
        $order = wc_get_order($woocommerce_order_id);
        
        if (!$order) {
            return false;
        }
        
        return $this->update($business_id, [
            'woocommerce_id' => $order->get_customer_id(),
            'woocommerce_shop_name' => $order->get_billing_company() ?: '',
        ]);
    }
}