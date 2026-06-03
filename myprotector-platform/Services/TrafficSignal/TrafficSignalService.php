<?php
/**
 * MyProtector Platform - Traffic Signal Service
 * 
 * Core service for calculating and managing trust signals
 * 
 * @package MyProtector\Services\TrafficSignal
 * @version 1.0.0
 */

namespace MyProtector\Services\TrafficSignal;

class TrafficSignalService {
    /**
     * Trust status constants
     */
    const STATUS_GREEN = 'green';      // Shopping Safe
    const STATUS_AMBER = 'amber';      // Walking Safe
    const STATUS_RED = 'red';          // Caution
    const STATUS_UNVERIFIED = 'unverified';

    /**
     * Trust status display names
     */
    const STATUS_LABELS = [
        'green' => 'Shopping Safe',
        'amber' => 'Walking Safe',
        'red' => 'Caution',
        'unverified' => 'Unverified',
    ];

    /**
     * Trust status icons
     */
    const STATUS_ICONS = [
        'green' => '🛒',
        'amber' => '🚶',
        'red' => '⚠️',
        'unverified' => '❓',
    ];

    /**
     * WordPress database object
     * 
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Business model
     * 
     * @var \MyProtector\Models\BusinessModel
     */
    protected $businessModel;

    /**
     * Requirements weights (percentages)
     */
    protected $weights = [
        'has_min_reviews' => 20,
        'has_min_rating' => 20,
        'has_verified_domain' => 15,
        'has_insurance' => 20,
        'has_terms' => 15,
        'has_promise_page' => 10,
    ];

    /**
     * Minimum reviews threshold
     */
    const MIN_REVIEWS = 5;

    /**
     * Minimum rating threshold
     */
    const MIN_RATING = 3.5;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->businessModel = new \MyProtector\Models\BusinessModel();
    }

    /**
     * Get traffic signal for a business
     * 
     * @param int $business_id
     * @return object|null
     */
    public function getSignal(int $business_id) {
        $cache_key = 'traffic_signal_' . $business_id;
        $cached = wp_cache_get($cache_key, 'mp_traffic_signals');
        
        if ($cached !== false) {
            return $cached;
        }

        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}mp_traffic_signals WHERE business_id = %d",
                $business_id
            )
        );

        if ($result) {
            wp_cache_set($cache_key, $result, 'mp_traffic_signals', 3600);
        }

        return $result;
    }

    /**
     * Calculate trust signal for a business
     * 
     * @param int $business_id
     * @param bool $save Whether to save the result
     * @return array
     */
    public function calculate(int $business_id, bool $save = true): array {
        $business = $this->businessModel->get($business_id);
        
        if (!$business) {
            return [
                'success' => false,
                'error' => 'Business not found',
            ];
        }

        // Check for manual override
        $signal = $this->getSignal($business_id);
        if ($signal && $signal->manual_override) {
            return $this->getSignalData($signal, true);
        }

        // Get requirements data
        $requirements = $this->evaluateRequirements($business_id);
        
        // Calculate trust score
        $score = $this->calculateScore($requirements);
        
        // Determine status
        $status = $this->determineStatus($score, $requirements);
        
        // Build breakdown
        $breakdown = $this->buildScoreBreakdown($requirements);
        
        // Get improvement tips
        $tips = $this->getImprovementTips($requirements, $status);

        $signal_data = [
            'business_id' => $business_id,
            'trust_status' => $status,
            'traffic_light_color' => $status,
            'trust_score' => $score,
            'trust_score_breakdown' => json_encode($breakdown),
            'requirements_met' => json_encode($this->getMetRequirements($requirements)),
            'requirements_total' => count($this->weights),
            'requirements_fulfilled' => $this->countFulfilled($requirements),
            'has_min_reviews' => $requirements['has_min_reviews'] ? 1 : 0,
            'has_min_rating' => $requirements['has_min_rating'] ? 1 : 0,
            'has_verified_domain' => $requirements['has_verified_domain'] ? 1 : 0,
            'has_insurance' => $requirements['has_insurance'] ? 1 : 0,
            'has_terms' => $requirements['has_terms'] ? 1 : 0,
            'has_promise_page' => $requirements['has_promise_page'] ? 1 : 0,
            'has_active_subscription' => $requirements['has_active_subscription'] ? 1 : 0,
            'is_auto_calculated' => 1,
            'last_calculated_at' => current_time('mysql'),
            'calculation_data' => json_encode($requirements),
            'status_reasons' => json_encode($this->getStatusReasons($status)),
            'improvement_tips' => json_encode($tips),
            'updated_at' => current_time('mysql'),
        ];

        if ($save) {
            $this->saveSignal($business_id, $signal_data);
        }

        return [
            'success' => true,
            'data' => $signal_data,
            'score' => $score,
            'status' => $status,
            'requirements' => $requirements,
            'breakdown' => $breakdown,
            'tips' => $tips,
        ];
    }

    /**
     * Evaluate all requirements for a business
     * 
     * @param int $business_id
     * @return array
     */
    protected function evaluateRequirements(int $business_id): array {
        $business = $this->businessModel->get($business_id);
        
        if (!$business) {
            return [];
        }

        // Check minimum reviews
        $has_min_reviews = $business->total_reviews >= self::MIN_REVIEWS;
        
        // Check minimum rating
        $has_min_rating = $business->avg_rating >= self::MIN_RATING;
        
        // Check verified domain
        $has_verified_domain = !empty($business->business_website) && 
                                $this->isValidDomain($business->business_website);
        
        // Check insurance URL
        $has_insurance = !empty($business->insurance_url) && 
                         filter_var($business->insurance_url, FILTER_VALIDATE_URL);
        
        // Check terms URL
        $has_terms = !empty($business->terms_url) && 
                     filter_var($business->terms_url, FILTER_VALIDATE_URL);
        
        // Check promise page URL
        $has_promise_page = !empty($business->promise_page_url) && 
                            filter_var($business->promise_page_url, FILTER_VALIDATE_URL);
        
        // Check active subscription
        $has_active_subscription = $this->businessModel->hasActiveSubscription($business_id);

        return [
            'has_min_reviews' => $has_min_reviews,
            'has_min_rating' => $has_min_rating,
            'has_verified_domain' => $has_verified_domain,
            'has_insurance' => $has_insurance,
            'has_terms' => $has_terms,
            'has_promise_page' => $has_promise_page,
            'has_active_subscription' => $has_active_subscription,
            'total_reviews' => $business->total_reviews,
            'avg_rating' => $business->avg_rating,
        ];
    }

    /**
     * Calculate trust score based on requirements
     * 
     * @param array $requirements
     * @return float
     */
    protected function calculateScore(array $requirements): float {
        $score = 0;
        
        foreach ($this->weights as $requirement => $weight) {
            if (!empty($requirements[$requirement])) {
                $score += $weight;
            }
        }
        
        return round($score, 2);
    }

    /**
     * Determine trust status based on score and requirements
     * 
     * @param float $score
     * @param array $requirements
     * @return string
     */
    protected function determineStatus(float $score, array $requirements): string {
        // GREEN: 80-100% with subscription and compliance
        if ($score >= 80 && 
            $requirements['has_insurance'] && 
            $requirements['has_terms'] && 
            $requirements['has_active_subscription']) {
            return self::STATUS_GREEN;
        }
        
        // AMBER: 40-79% or partial compliance
        if ($score >= 40) {
            return self::STATUS_AMBER;
        }
        
        // RED: Below 40% or missing critical requirements
        return self::STATUS_RED;
    }

    /**
     * Build score breakdown
     * 
     * @param array $requirements
     * @return array
     */
    protected function buildScoreBreakdown(array $requirements): array {
        $breakdown = [];
        
        foreach ($this->weights as $requirement => $weight) {
            $met = !empty($requirements[$requirement]);
            $breakdown[$requirement] = [
                'weight' => $weight,
                'met' => $met,
                'points' => $met ? $weight : 0,
            ];
        }
        
        return $breakdown;
    }

    /**
     * Get requirements that are met
     * 
     * @param array $requirements
     * @return array
     */
    protected function getMetRequirements(array $requirements): array {
        $met = [];
        
        foreach ($requirements as $key => $value) {
            if (in_array($key, array_keys($this->weights)) && $value) {
                $met[] = $key;
            }
        }
        
        return $met;
    }

    /**
     * Count fulfilled requirements
     * 
     * @param array $requirements
     * @return int
     */
    protected function countFulfilled(array $requirements): int {
        $count = 0;
        
        foreach (array_keys($this->weights) as $key) {
            if (!empty($requirements[$key])) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get status reasons
     * 
     * @param string $status
     * @return array
     */
    protected function getStatusReasons(string $status): array {
        switch ($status) {
            case self::STATUS_GREEN:
                return [
                    'All critical trust requirements verified',
                    'Active subscription confirmed',
                    'Compliance documentation complete',
                ];
            case self::STATUS_AMBER:
                return [
                    'Partial trust requirements met',
                    'Some verification pending',
                    'Review compliance status',
                ];
            case self::STATUS_RED:
                return [
                    'Missing critical requirements',
                    'Trust status requires attention',
                    'Complete verification process',
                ];
            default:
                return [];
        }
    }

    /**
     * Get improvement tips
     * 
     * @param array $requirements
     * @param string $status
     * @return array
     */
    protected function getImprovementTips(array $requirements, string $status): array {
        $tips = [];
        
        if (empty($requirements['has_min_reviews'])) {
            $tips[] = 'Collect more reviews (minimum ' . self::MIN_REVIEWS . ' required)';
        }
        
        if (empty($requirements['has_min_rating'])) {
            $tips[] = 'Improve your average rating (minimum ' . self::MIN_RATING . ' required)';
        }
        
        if (empty($requirements['has_insurance'])) {
            $tips[] = 'Add an insurance verification URL';
        }
        
        if (empty($requirements['has_terms'])) {
            $tips[] = 'Add Terms & Conditions page URL';
        }
        
        if (empty($requirements['has_promise_page'])) {
            $tips[] = 'Create and link a Promise Page';
        }
        
        if (empty($requirements['has_active_subscription'])) {
            $tips[] = 'Subscribe to MyProtector Business Plan for full verification';
        }
        
        if (empty($tips) && $status !== self::STATUS_GREEN) {
            $tips[] = 'All requirements met! Check for subscription status.';
        }
        
        return $tips;
    }

    /**
     * Save traffic signal to database
     * 
     * @param int $business_id
     * @param array $data
     * @return bool
     */
    protected function saveSignal(int $business_id, array $data): bool {
        $existing = $this->getSignal($business_id);
        
        if ($existing) {
            $result = $this->wpdb->update(
                $this->wpdb->prefix . 'mp_traffic_signals',
                $data,
                ['business_id' => $business_id]
            );
        } else {
            $data['created_at'] = current_time('mysql');
            $result = $this->wpdb->insert(
                $this->wpdb->prefix . 'mp_traffic_signals',
                $data
            );
        }
        
        // Clear cache
        wp_cache_delete('traffic_signal_' . $business_id, 'mp_traffic_signals');
        
        return $result !== false;
    }

    /**
     * Manual override for traffic signal
     * 
     * @param int $business_id
     * @param string $status
     * @param string $reason
     * @param int $override_by
     * @return bool
     */
    public function override(int $business_id, string $status, string $reason, int $override_by = 0): bool {
        if (!in_array($status, [self::STATUS_GREEN, self::STATUS_AMBER, self::STATUS_RED])) {
            return false;
        }

        $data = [
            'trust_status' => $status,
            'traffic_light_color' => $status,
            'manual_override' => 1,
            'override_reason' => sanitize_text_field($reason),
            'override_by' => $override_by,
            'override_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];

        // Get current score or calculate new one
        $signal = $this->getSignal($business_id);
        if ($signal) {
            $data['trust_score'] = $signal->trust_score;
        }

        $result = $this->saveSignal($business_id, $data);

        if ($result) {
            $this->logHistory($business_id, 'override', [
                'previous_status' => $signal->trust_status ?? null,
                'new_status' => $status,
                'reason' => $reason,
                'override_by' => $override_by,
            ]);
        }

        return $result;
    }

    /**
     * Clear manual override
     * 
     * @param int $business_id
     * @return bool
     */
    public function clearOverride(int $business_id): bool {
        $data = [
            'manual_override' => 0,
            'override_reason' => null,
            'override_by' => null,
            'override_at' => null,
            'updated_at' => current_time('mysql'),
        ];

        // Recalculate
        $this->calculate($business_id);

        return true;
    }

    /**
     * Log traffic signal history
     * 
     * @param int $business_id
     * @param string $action
     * @param array $details
     * @return void
     */
    protected function logHistory(int $business_id, string $action, array $details = []): void {
        $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_traffic_signal_history',
            [
                'business_id' => $business_id,
                'action' => $action,
                'details' => json_encode($details),
                'created_at' => current_time('mysql'),
            ]
        );
    }

    /**
     * Get signal data for display
     * 
     * @param object $signal
     * @param bool $include_override_reason
     * @return array
     */
    public function getSignalData($signal, bool $include_override_reason = false): array {
        $status = $signal->trust_status ?? self::STATUS_RED;
        
        $data = [
            'business_id' => $signal->business_id,
            'status' => $status,
            'label' => self::STATUS_LABELS[$status] ?? 'Unknown',
            'icon' => self::STATUS_ICONS[$status] ?? '❓',
            'score' => (float) ($signal->trust_score ?? 0),
            'color' => $status,
            'fulfilled' => (int) ($signal->requirements_fulfilled ?? 0),
            'total' => (int) ($signal->requirements_total ?? 5),
            'percentage' => $signal->requirements_total > 0 
                ? round(($signal->requirements_fulfilled / $signal->requirements_total) * 100, 0) 
                : 0,
            'is_auto_calculated' => (bool) ($signal->is_auto_calculated ?? true),
            'manual_override' => (bool) ($signal->manual_override ?? false),
            'last_calculated' => $signal->last_calculated_at ?? null,
            'requirements' => [
                'insurance' => (bool) ($signal->has_insurance ?? false),
                'terms' => (bool) ($signal->has_terms ?? false),
                'promise_page' => (bool) ($signal->has_promise_page ?? false),
                'subscription' => (bool) ($signal->has_active_subscription ?? false),
                'min_reviews' => (bool) ($signal->has_min_reviews ?? false),
                'min_rating' => (bool) ($signal->has_min_rating ?? false),
            ],
        ];

        if ($include_override_reason && $signal->manual_override) {
            $data['override_reason'] = $signal->override_reason ?? '';
            $data['override_at'] = $signal->override_at ?? '';
        }

        // Decode JSON fields
        if (!empty($signal->trust_score_breakdown)) {
            $data['score_breakdown'] = json_decode($signal->trust_score_breakdown, true);
        }
        
        if (!empty($signal->improvement_tips)) {
            $data['improvement_tips'] = json_decode($signal->improvement_tips, true);
        }
        
        if (!empty($signal->status_reasons)) {
            $data['status_reasons'] = json_decode($signal->status_reasons, true);
        }

        return $data;
    }

    /**
     * Render traffic signal HTML
     * 
     * @param int $business_id
     * @param array $args
     * @return string
     */
    public function render(int $business_id, array $args = []): string {
        $defaults = [
            'style' => 'standard', // standard, compact, badge_only
            'size' => 'medium',    // small, medium, large
            'show_checklist' => false,
            'show_score' => true,
        ];
        
        $args = wp_parse_args($args, $defaults);
        $signal = $this->getSignal($business_id);
        
        if (!$signal) {
            // Calculate if not exists
            $result = $this->calculate($business_id);
            if ($result['success']) {
                $signal = $this->getSignal($business_id);
            }
        }
        
        if (!$signal) {
            return '<div class="mp-traffic-signal mp-traffic-unknown">Status unavailable</div>';
        }
        
        $data = $this->getSignalData($signal, true);
        $data['args'] = $args;
        
        ob_start();
        
        switch ($args['style']) {
            case 'badge_only':
                include __DIR__ . '/Templates/badge.php';
                break;
            case 'compact':
                include __DIR__ . '/Templates/compact.php';
                break;
            default:
                include __DIR__ . '/Templates/standard.php';
        }
        
        return ob_get_clean();
    }

    /**
     * Validate domain
     * 
     * @param string $url
     * @return bool
     */
    protected function isValidDomain(string $url): bool {
        $parsed = parse_url($url);
        
        if (!isset($parsed['host'])) {
            return false;
        }
        
        // Basic domain validation
        return filter_var($parsed['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    /**
     * Recalculate all signals (batch operation)
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function recalculateAll(int $limit = 100, int $offset = 0): array {
        $businesses = $this->businessModel->getAll([
            'status' => 'active',
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $results = [
            'processed' => 0,
            'updated' => 0,
            'errors' => [],
        ];

        foreach ($businesses as $business) {
            try {
                $result = $this->calculate($business->business_id);
                
                if ($result['success']) {
                    $results['processed']++;
                    
                    // Check if status changed
                    $signal = $this->getSignal($business->business_id);
                    if ($signal && $signal->trust_status !== $result['status']) {
                        $results['updated']++;
                        $this->logHistory($business->business_id, 'recalculated', [
                            'previous_status' => $signal->trust_status,
                            'new_status' => $result['status'],
                            'score_change' => $result['score'] - ($signal->trust_score ?? 0),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'business_id' => $business->business_id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get signal summary for a business
     * 
     * @param int $business_id
     * @return array
     */
    public function getSummary(int $business_id): array {
        $signal = $this->getSignal($business_id);
        
        if (!$signal) {
            return [
                'status' => 'unknown',
                'score' => 0,
                'can_be_green' => false,
                'missing_requirements' => [],
            ];
        }

        $requirements = json_decode($signal->calculation_data ?? '{}', true);
        $missing = [];

        foreach ($this->weights as $req => $weight) {
            if (empty($requirements[$req])) {
                $missing[] = str_replace('_', ' ', $req);
            }
        }

        return [
            'status' => $signal->trust_status,
            'score' => (float) $signal->trust_score,
            'can_be_green' => $signal->trust_status === self::STATUS_GREEN,
            'missing_requirements' => $missing,
            'met_requirements' => count($this->weights) - count($missing),
            'total_requirements' => count($this->weights),
        ];
    }
}