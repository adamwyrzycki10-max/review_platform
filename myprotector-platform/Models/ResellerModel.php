<?php
/**
 * MyProtector Platform - Reseller Model
 * 
 * Handles all reseller database operations
 * 
 * @package MyProtector\Models
 * @version 1.0.0
 */

namespace MyProtector\Models;

class ResellerModel extends BaseModel {
    /**
     * Table name
     * 
     * @var string
     */
    protected $table = 'mp_resellers';

    /**
     * Primary key
     * 
     * @var string
     */
    protected $primary_key = 'reseller_id';

    /**
     * Cache group
     * 
     * @var string
     */
    protected $cache_group = 'mp_resellers';

    /**
     * Get reseller by user ID
     * 
     * @param int $user_id
     * @return object|null
     */
    public function getByUser(int $user_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->getTableName()} WHERE user_id = %d",
                $user_id
            )
        );
    }

    /**
     * Get reseller by referral code
     * 
     * @param string $code
     * @return object|null
     */
    public function getByReferralCode(string $code) {
        $cache_key = 'refcode_' . md5($code);
        $cached = $this->getCache($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }

        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->getTableName()} WHERE referral_code = %s",
                sanitize_text_field($code)
            )
        );
        
        if ($result) {
            $this->setCache($cache_key, $result);
        }
        
        return $result;
    }

    /**
     * Get all active resellers
     * 
     * @param array $args
     * @return array
     */
    public function getAllActive(array $args = []): array {
        $defaults = [
            'tier' => null,
            'orderby' => 'total_earnings',
            'order' => 'DESC',
            'limit' => 50,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT r.*, u.display_name as user_name, u.user_email as user_email
                FROM {$this->getTableName()} r
                LEFT JOIN {$this->wpdb->users} u ON r.user_id = u.ID
                WHERE r.reseller_status = 'active'";
        
        $values = [];
        
        if (!empty($tier)) {
            $sql .= " AND r.commission_tier = %s";
            $values[] = $tier;
        }
        
        $order = sanitize_sql_orderby("{$orderby} {$order}");
        $sql .= " ORDER BY r.{$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Create a new reseller
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        // Generate unique referral code
        if (empty($data['referral_code'])) {
            $data['referral_code'] = $this->generateReferralCode();
        }
        
        // Set defaults
        $defaults = [
            'commission_rate' => 10.00,
            'commission_tier' => 'standard',
            'total_referrals' => 0,
            'total_earnings' => 0.00,
            'pending_earnings' => 0.00,
            'paid_earnings' => 0.00,
            'reseller_status' => 'pending',
            'marketing_materials_access' => 1,
            'api_access' => 0,
            'total_clicks' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        return parent::insert($data);
    }

    /**
     * Approve a reseller
     * 
     * @param int $reseller_id
     * @param int $approved_by
     * @return bool
     */
    public function approve(int $reseller_id, int $approved_by): bool {
        return $this->update($reseller_id, [
            'reseller_status' => 'active',
            'approved_at' => current_time('mysql'),
            'approved_by' => $approved_by,
        ]);
    }

    /**
     * Suspend a reseller
     * 
     * @param int $reseller_id
     * @param string $reason
     * @return bool
     */
    public function suspend(int $reseller_id, string $reason = ''): bool {
        return $this->update($reseller_id, [
            'reseller_status' => 'suspended',
            'suspension_reason' => sanitize_text_field($reason),
        ]);
    }

    /**
     * Update earnings
     * 
     * @param int $reseller_id
     * @param float $amount
     * @param string $type pending|paid
     * @return bool
     */
    public function updateEarnings(int $reseller_id, float $amount, string $type = 'pending'): bool {
        $column = $type === 'paid' ? 'paid_earnings' : 'pending_earnings';
        
        return $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->getTableName()} 
                 SET total_earnings = total_earnings + %f,
                     {$column} = {$column} + %f,
                     last_activity_at = %s
                 WHERE reseller_id = %d",
                $amount,
                $amount,
                current_time('mysql'),
                $reseller_id
            )
        ) !== false;
    }

    /**
     * Increment referral count
     * 
     * @param int $reseller_id
     * @return bool
     */
    public function incrementReferrals(int $reseller_id): bool {
        return $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->getTableName()} 
                 SET total_referrals = total_referrals + 1,
                     last_activity_at = %s
                 WHERE reseller_id = %d",
                current_time('mysql'),
                $reseller_id
            )
        ) !== false;
    }

    /**
     * Track click
     * 
     * @param int $reseller_id
     * @return bool
     */
    public function trackClick(int $reseller_id): bool {
        return $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->getTableName()} 
                 SET total_clicks = total_clicks + 1,
                     last_activity_at = %s
                 WHERE reseller_id = %d",
                current_time('mysql'),
                $reseller_id
            )
        ) !== false;
    }

    /**
     * Get commissions for a reseller
     * 
     * @param int $reseller_id
     * @param array $args
     * @return array
     */
    public function getCommissions(int $reseller_id, array $args = []): array {
        $defaults = [
            'status' => null,
            'type' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 50,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $sql = "SELECT c.*, b.business_name as referred_business_name
                FROM {$this->wpdb->prefix}mp_commissions c
                LEFT JOIN {$this->wpdb->prefix}mp_businesses b ON c.business_id = b.business_id
                WHERE c.reseller_id = %d";
        
        $values = [$reseller_id];
        
        if (!empty($status)) {
            $sql .= " AND c.commission_status = %s";
            $values[] = $status;
        }
        
        if (!empty($type)) {
            $sql .= " AND c.commission_type = %s";
            $values[] = $type;
        }
        
        $order = sanitize_sql_orderby("{$orderby} {$order}");
        $sql .= " ORDER BY c.{$order} LIMIT %d OFFSET %d";
        $values[] = $limit;
        $values[] = $offset;
        
        $sql = $this->wpdb->prepare($sql, $values);
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Get pending commissions total
     * 
     * @param int $reseller_id
     * @return float
     */
    public function getPendingCommissions(int $reseller_id): float {
        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(commission_amount) 
                 FROM {$this->wpdb->prefix}mp_commissions 
                 WHERE reseller_id = %d AND commission_status = 'pending'",
                $reseller_id
            )
        );
    }

    /**
     * Get paid commissions total
     * 
     * @param int $reseller_id
     * @return float
     */
    public function getPaidCommissions(int $reseller_id): float {
        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(commission_amount) 
                 FROM {$this->wpdb->prefix}mp_commissions 
                 WHERE reseller_id = %d AND commission_status = 'paid'",
                $reseller_id
            )
        );
    }

    /**
     * Create commission record
     * 
     * @param array $data
     * @return int|false
     */
    public function createCommission(array $data): int|false {
        $defaults = [
            'commission_status' => 'pending',
            'commission_rate' => 10.00,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        $result = $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_commissions',
            $data
        );
        
        if ($result) {
            // Update reseller earnings
            if ($data['commission_status'] === 'approved') {
                $this->updateEarnings($data['reseller_id'], $data['commission_amount'], 'pending');
            }
        }
        
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Generate unique referral code
     * 
     * @return string
     */
    protected function generateReferralCode(): string {
        $length = 8;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        // Check uniqueness
        $exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE referral_code = %s",
                $code
            )
        );
        
        if ($exists) {
            return $this->generateReferralCode();
        }
        
        return $code;
    }

    /**
     * Get tier based on performance
     * 
     * @param int $reseller_id
     * @return string
     */
    public function calculateTier(int $reseller_id): string {
        $reseller = $this->get($reseller_id);
        
        if (!$reseller) {
            return 'standard';
        }
        
        // Calculate based on referrals and earnings
        if ($reseller->total_earnings >= 10000 && $reseller->total_referrals >= 100) {
            return 'platinum';
        } elseif ($reseller->total_earnings >= 5000 && $reseller->total_referrals >= 50) {
            return 'gold';
        } elseif ($reseller->total_earnings >= 1000 && $reseller->total_referrals >= 20) {
            return 'silver';
        }
        
        return 'standard';
    }

    /**
     * Get reseller payout details
     * 
     * @param int $reseller_id
     * @return array
     */
    public function getPayoutDetails(int $reseller_id): array {
        $reseller = $this->get($reseller_id);
        
        if (!$reseller) {
            return [];
        }
        
        return [
            'pending_earnings' => $reseller->pending_earnings,
            'paid_earnings' => $reseller->paid_earnings,
            'total_earnings' => $reseller->total_earnings,
            'payout_method' => $reseller->payout_method ?? 'bank_transfer',
            'payout_details' => json_decode($reseller->payout_details ?? '{}', true),
            'payout_threshold' => $reseller->payout_threshold,
            'payout_schedule' => $reseller->payout_schedule,
            'minimum_payout' => $reseller->minimum_payout,
        ];
    }

    /**
     * Update payout settings
     * 
     * @param int $reseller_id
     * @param array $settings
     * @return bool
     */
    public function updatePayoutSettings(int $reseller_id, array $settings): bool {
        $allowed_methods = ['bank_transfer', 'paypal', 'stripe', 'wire'];
        $allowed_schedules = ['weekly', 'biweekly', 'monthly'];
        
        $data = [];
        
        if (isset($settings['payout_method']) && in_array($settings['payout_method'], $allowed_methods)) {
            $data['payout_method'] = sanitize_text_field($settings['payout_method']);
        }
        
        if (isset($settings['payout_details'])) {
            $data['payout_details'] = json_encode($settings['payout_details']);
        }
        
        if (isset($settings['payout_threshold']) && is_numeric($settings['payout_threshold'])) {
            $data['payout_threshold'] = floatval($settings['payout_threshold']);
        }
        
        if (isset($settings['payout_schedule']) && in_array($settings['payout_schedule'], $allowed_schedules)) {
            $data['payout_schedule'] = sanitize_text_field($settings['payout_schedule']);
        }
        
        if (isset($settings['minimum_payout']) && is_numeric($settings['minimum_payout'])) {
            $data['minimum_payout'] = floatval($settings['minimum_payout']);
        }
        
        if (empty($data)) {
            return false;
        }
        
        return $this->update($reseller_id, $data);
    }

    /**
     * Generate API key for reseller
     * 
     * @param int $reseller_id
     * @return string
     */
    public function generateApiKey(int $reseller_id): string {
        $key = wp_generate_uuid4();
        
        $this->update($reseller_id, [
            'api_key' => hash('sha256', $key),
            'api_access' => 1,
        ]);
        
        return $key;
    }

    /**
     * Verify API key
     * 
     * @param string $key
     * @return object|null
     */
    public function verifyApiKey(string $key): ?object {
        $hash = hash('sha256', $key);
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->getTableName()} 
                 WHERE api_key = %s AND reseller_status = 'active' AND api_access = 1",
                $hash
            )
        );
    }

    /**
     * Get reseller statistics
     * 
     * @param int $reseller_id
     * @return array
     */
    public function getStats(int $reseller_id): array {
        $reseller = $this->get($reseller_id);
        
        if (!$reseller) {
            return [];
        }
        
        $commissions = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN commission_status = 'pending' THEN commission_amount ELSE 0 END) as pending_total,
                    SUM(CASE WHEN commission_status = 'paid' THEN commission_amount ELSE 0 END) as paid_total
                 FROM {$this->wpdb->prefix}mp_commissions
                 WHERE reseller_id = %d",
                $reseller_id
            )
        );
        
        return [
            'total_referrals' => $reseller->total_referrals,
            'total_clicks' => $reseller->total_clicks,
            'total_earnings' => $reseller->total_earnings,
            'pending_earnings' => $reseller->pending_earnings,
            'paid_earnings' => $reseller->paid_earnings,
            'commission_tier' => $reseller->commission_tier,
            'conversion_rate' => $reseller->total_clicks > 0 
                ? round(($reseller->total_referrals / $reseller->total_clicks) * 100, 2) 
                : 0,
            'status' => $reseller->reseller_status,
            'last_activity' => $reseller->last_activity_at,
        ];
    }
}