<?php
/**
 * MyProtector Platform - Review Model
 * 
 * Handles all review database operations
 * 
 * @package MyProtector\Models
 * @version 1.0.0
 */

namespace MyProtector\Models;

class ReviewModel extends BaseModel {
    /**
     * Table name
     * 
     * @var string
     */
    protected $table = 'mp_reviews';

    /**
     * Primary key
     * 
     * @var string
     */
    protected $primary_key = 'review_id';

    /**
     * Get reviews for a business
     * 
     * @param int $business_id
     * @param array $args Optional arguments
     * @return array
     */
    public function getByBusiness(int $business_id, array $args = []): array {
        $defaults = [
            'status' => 'approved',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT r.*, u.display_name as reviewer_name, u.user_email as reviewer_email
                FROM {$this->getTableName()} r
                LEFT JOIN {$this->wpdb->users} u ON r.user_id = u.ID
                WHERE r.business_id = %d";
        
        $values = [$business_id];
        
        if (!empty($status)) {
            $sql .= " AND r.review_status = %s";
            $values[] = $status;
        }
        
        $sql .= " ORDER BY r.{$orderby} {$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Get reviews by user
     * 
     * @param int $user_id
     * @param array $args
     * @return array
     */
    public function getByUser(int $user_id, array $args = []): array {
        $defaults = [
            'status' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT r.*, b.business_name
                FROM {$this->getTableName()} r
                LEFT JOIN {$this->wpdb->prefix}mp_businesses b ON r.business_id = b.business_id
                WHERE r.user_id = %d";
        
        $values = [$user_id];
        
        if (!empty($status)) {
            $sql .= " AND r.review_status = %s";
            $values[] = $status;
        }
        
        $sql .= " ORDER BY r.{$orderby} {$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Create a new review
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        $defaults = [
            'review_status' => 'pending',
            'helpful_count' => 0,
            'report_count' => 0,
            'is_published' => 0,
            'ai_analyzed' => 0,
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        // Sanitize
        $data['review_title'] = sanitize_text_field($data['review_title'] ?? '');
        $data['review_content'] = sanitize_textarea_field($data['review_content'] ?? '');
        $data['review_rating'] = absint($data['review_rating']);
        
        // Validate rating
        if ($data['review_rating'] < 1 || $data['review_rating'] > 5) {
            return false;
        }
        
        // Get IP address
        if (!isset($data['ip_address'])) {
            $data['ip_address'] = $this->getClientIp();
        }
        
        return parent::insert($data);
    }

    /**
     * Approve a review
     * 
     * @param int $review_id
     * @param int $approved_by
     * @return bool
     */
    public function approve(int $review_id, int $approved_by = 0): bool {
        $result = $this->update($review_id, [
            'review_status' => 'approved',
            'is_published' => 1,
            'published_at' => current_time('mysql'),
        ]);
        
        if ($result) {
            // Update business stats
            $this->updateBusinessStats($review_id);
            
            // Clear cache
            $this->clearCache($review_id);
        }
        
        return $result;
    }

    /**
     * Reject a review
     * 
     * @param int $review_id
     * @param string $reason
     * @return bool
     */
    public function reject(int $review_id, string $reason = ''): bool {
        $result = $this->update($review_id, [
            'review_status' => 'rejected',
            'rejection_reason' => sanitize_text_field($reason),
        ]);
        
        if ($result) {
            $this->clearCache($review_id);
        }
        
        return $result;
    }

    /**
     * Flag a review
     * 
     * @param int $review_id
     * @return bool
     */
    public function flag(int $review_id): bool {
        $sql = $this->wpdb->prepare(
            "UPDATE {$this->getTableName()} SET report_count = report_count + 1, review_status = 'flagged' WHERE review_id = %d",
            $review_id
        );
        
        return $this->wpdb->query($sql) !== false;
    }

    /**
     * Mark review as helpful
     * 
     * @param int $review_id
     * @param int $user_id
     * @return bool
     */
    public function markHelpful(int $review_id, int $user_id): bool {
        // Check if already marked
        $exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->wpdb->prefix}mp_review_helpful WHERE review_id = %d AND user_id = %d",
                $review_id,
                $user_id
            )
        );
        
        if ($exists) {
            return false;
        }
        
        // Add helpful mark
        $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_review_helpful',
            [
                'review_id' => $review_id,
                'user_id' => $user_id,
                'created_at' => current_time('mysql'),
            ]
        );
        
        // Update count
        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->getTableName()} SET helpful_count = helpful_count + 1 WHERE review_id = %d",
                $review_id
            )
        );
        
        return true;
    }

    /**
     * Add response to review
     * 
     * @param int $review_id
     * @param int $user_id
     * @param string $content
     * @return int|false
     */
    public function addResponse(int $review_id, string $content, int $user_id) {
        $review = $this->get($review_id);
        
        if (!$review) {
            return false;
        }
        
        return $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_review_responses',
            [
                'review_id' => $review_id,
                'business_id' => $review->business_id,
                'user_id' => $user_id,
                'response_content' => sanitize_textarea_field($content),
                'is_official' => 1,
                'status' => 'published',
                'created_at' => current_time('mysql'),
            ]
        );
    }

    /**
     * Get responses for a review
     * 
     * @param int $review_id
     * @return array
     */
    public function getResponses(int $review_id): array {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT r.*, u.display_name as responder_name
                 FROM {$this->wpdb->prefix}mp_review_responses r
                 LEFT JOIN {$this->wpdb->users} u ON r.user_id = u.ID
                 WHERE r.review_id = %d AND r.status = 'published'
                 ORDER BY r.created_at ASC",
                $review_id
            )
        );
    }

    /**
     * Add image to review
     * 
     * @param int $review_id
     * @param array $image_data
     * @return int|false
     */
    public function addImage(int $review_id, array $image_data) {
        $defaults = [
            'image_type' => 'review',
            'is_approved' => 0,
            'uploaded_by' => 0,
            'created_at' => current_time('mysql'),
        ];
        
        $image_data = wp_parse_args($image_data, $defaults);
        $image_data['review_id'] = $review_id;
        
        return $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_review_images',
            $image_data
        ) ? $this->wpdb->insert_id : false;
    }

    /**
     * Get images for a review
     * 
     * @param int $review_id
     * @param bool $approved_only
     * @return array
     */
    public function getImages(int $review_id, bool $approved_only = true): array {
        $where = "review_id = %d";
        $values = [$review_id];
        
        if ($approved_only) {
            $where .= " AND is_approved = 1";
        }
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}mp_review_images WHERE {$where}",
                $values
            )
        );
    }

    /**
     * Get average rating for a business
     * 
     * @param int $business_id
     * @return float
     */
    public function getAverageRating(int $business_id): float {
        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT AVG(review_rating) FROM {$this->getTableName()} 
                 WHERE business_id = %d AND review_status = 'approved'",
                $business_id
            )
        );
    }

    /**
     * Get total review count for a business
     * 
     * @param int $business_id
     * @param string $status
     * @return int
     */
    public function getCount(int $business_id, string $status = 'approved'): int {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->getTableName()} 
                 WHERE business_id = %d AND review_status = %s",
                $business_id,
                $status
            )
        );
    }

    /**
     * Update business statistics after review change
     * 
     * @param int $review_id
     * @return void
     */
    protected function updateBusinessStats(int $review_id): void {
        $review = $this->get($review_id);
        
        if (!$review) {
            return;
        }
        
        $business_id = $review->business_id;
        
        // Get stats
        $avg_rating = $this->getAverageRating($business_id);
        $total_reviews = $this->getCount($business_id);
        
        // Update business
        $this->wpdb->update(
            $this->wpdb->prefix . 'mp_businesses',
            [
                'avg_rating' => $avg_rating,
                'total_reviews' => $total_reviews,
                'approved_reviews' => $total_reviews,
            ],
            ['business_id' => $business_id],
            ['%f', '%d', '%d'],
            ['%d']
        );
        
        // Clear business cache
        wp_cache_delete('mp_business_' . $business_id, 'mp_businesses');
    }

    /**
     * Get rating distribution for a business
     * 
     * @param int $business_id
     * @return array
     */
    public function getRatingDistribution(int $business_id): array {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT review_rating, COUNT(*) as count 
                 FROM {$this->getTableName()} 
                 WHERE business_id = %d AND review_status = 'approved'
                 GROUP BY review_rating
                 ORDER BY review_rating DESC",
                $business_id
            ),
            ARRAY_A
        );
        
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        foreach ($results as $row) {
            $distribution[$row['review_rating']] = (int) $row['count'];
        }
        
        return $distribution;
    }

    /**
     * Search reviews
     * 
     * @param string $query
     * @param array $args
     * @return array
     */
    public function search(string $query, array $args = []): array {
        $defaults = [
            'status' => null,
            'limit' => 20,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT r.*, b.business_name, u.display_name as reviewer_name
                FROM {$this->getTableName()} r
                LEFT JOIN {$this->wpdb->prefix}mp_businesses b ON r.business_id = b.business_id
                LEFT JOIN {$this->wpdb->users} u ON r.user_id = u.ID
                WHERE (r.review_title LIKE %s OR r.review_content LIKE %s)";
        
        $search_term = '%' . $this->wpdb->esc_like($query) . '%';
        $values = [$search_term, $search_term];
        
        if (!empty($status)) {
            $sql .= " AND r.review_status = %s";
            $values[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Get pending reviews for moderation
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPending(int $limit = 20, int $offset = 0): array {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT r.*, b.business_name, u.display_name as reviewer_name
                 FROM {$this->getTableName()} r
                 LEFT JOIN {$this->wpdb->prefix}mp_businesses b ON r.business_id = b.business_id
                 LEFT JOIN {$this->wpdb->users} u ON r.user_id = u.ID
                 WHERE r.review_status = 'pending'
                 ORDER BY r.created_at ASC
                 LIMIT %d OFFSET %d",
                $limit,
                $offset
            )
        );
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    protected function getClientIp(): string {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
        
        return $ip;
    }

    /**
     * Check if user has reviewed a business
     * 
     * @param int $user_id
     * @param int $business_id
     * @return bool
     */
    public function hasUserReviewed(int $user_id, int $business_id): bool {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->getTableName()} 
                 WHERE user_id = %d AND business_id = %d AND review_status IN ('pending', 'approved')",
                $user_id,
                $business_id
            )
        ) > 0;
    }
}