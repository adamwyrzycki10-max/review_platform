<?php
/**
 * MyProtector Platform - Subscription Service
 * 
 * Handles WooCommerce subscription integration
 * 
 * @package MyProtector\Modules\WooCommerce\Services
 * @version 1.0.0
 */

namespace MyProtector\Modules\WooCommerce\Services;

class SubscriptionService {
    /**
     * WooCommerce product ID for subscription
     * 
     * @var int
     */
    protected $product_id;

    /**
     * Business model
     * 
     * @var \MyProtector\Models\BusinessModel
     */
    protected $businessModel;

    /**
     * Traffic signal service
     * 
     * @var \MyProtector\Services\TrafficSignal\TrafficSignalService
     */
    protected $trafficService;

    /**
     * Constructor
     */
    public function __construct() {
        $this->product_id = get_option('mp_woocommerce_subscription_product_id', 0);
        $this->businessModel = new \MyProtector\Models\BusinessModel();
        $this->trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
    }

    /**
     * Get subscription status for a user
     * 
     * @param int $user_id
     * @return array
     */
    public function getSubscriptionStatus(int $user_id): array {
        if (!class_exists('WC_Subscriptions')) {
            return [
                'is_active' => false,
                'message' => 'WooCommerce Subscriptions not installed',
                'has_subscription' => false,
            ];
        }

        $subscriptions = wcs_get_subscriptions([
            'customer_id' => $user_id,
            'status' => ['active', 'pending', 'on-hold'],
        ]);

        if (empty($subscriptions)) {
            return [
                'is_active' => false,
                'message' => 'No active subscription',
                'has_subscription' => false,
            ];
        }

        $subscription = array_shift($subscriptions);
        
        return [
            'is_active' => in_array($subscription->get_status(), ['active']),
            'status' => $subscription->get_status(),
            'message' => $this->getStatusMessage($subscription->get_status()),
            'has_subscription' => true,
            'subscription_id' => $subscription->get_id(),
            'next_payment' => $subscription->get_date('next_payment_date'),
            'start_date' => $subscription->get_date('start_date'),
            'end_date' => $subscription->get_date('end_date'),
        ];
    }

    /**
     * Check if user has active subscription
     * 
     * @param int $user_id
     * @return bool
     */
    public function hasActiveSubscription(int $user_id): bool {
        $status = $this->getSubscriptionStatus($user_id);
        return $status['is_active'];
    }

    /**
     * Handle subscription activated
     * 
     * @param int $subscription_id
     * @return void
     */
    public function onSubscriptionActivated(int $subscription_id): void {
        if (!function_exists('wcs_get_subscription')) {
            return;
        }
        
        $subscription = wcs_get_subscription($subscription_id);
        
        if (!$subscription) {
            return;
        }

        $user_id = $subscription->get_customer_id();
        $business = $this->businessModel->getByUser($user_id);

        if ($business) {
            // Link WooCommerce to business
            $this->businessModel->update($business->business_id, [
                'woocommerce_id' => $user_id,
            ]);

            // Recalculate traffic signal
            $this->trafficService->calculate($business->business_id);

            // Send notification
            $this->notifySubscriptionActivated($business, $subscription);
        }

        // Log the event
        $this->logSubscriptionEvent($subscription_id, 'activated', $user_id);
    }

    /**
     * Handle subscription cancelled
     * 
     * @param int $subscription_id
     * @return void
     */
    public function onSubscriptionCancelled(int $subscription_id): void {
        if (!function_exists('wcs_get_subscription')) {
            return;
        }
        
        $subscription = wcs_get_subscription($subscription_id);
        
        if (!$subscription) {
            return;
        }

        $user_id = $subscription->get_customer_id();
        $business = $this->businessModel->getByUser($user_id);

        if ($business) {
            // Recalculate traffic signal (may downgrade to amber/red)
            $this->trafficService->calculate($business->business_id);

            // Send notification
            $this->notifySubscriptionCancelled($business);
        }

        // Log the event
        $this->logSubscriptionEvent($subscription_id, 'cancelled', $user_id);
    }

    /**
     * Handle subscription expired
     * 
     * @param int $subscription_id
     * @return void
     */
    public function onSubscriptionExpired(int $subscription_id): void {
        if (!function_exists('wcs_get_subscription')) {
            return;
        }
        
        $subscription = wcs_get_subscription($subscription_id);
        
        if (!$subscription) {
            return;
        }

        $user_id = $subscription->get_customer_id();
        $business = $this->businessModel->getByUser($user_id);

        if ($business) {
            // Recalculate traffic signal
            $this->trafficService->calculate($business->business_id);

            // Send notification
            $this->notifySubscriptionExpired($business);
        }

        // Log the event
        $this->logSubscriptionEvent($subscription_id, 'expired', $user_id);
    }

    /**
     * Handle subscription renewed
     * 
     * @param int $subscription_id
     * @return void
     */
    public function onSubscriptionRenewed(int $subscription_id): void {
        if (!function_exists('wcs_get_subscription')) {
            return;
        }
        
        $subscription = wcs_get_subscription($subscription_id);
        
        if (!$subscription) {
            return;
        }

        $user_id = $subscription->get_customer_id();
        $business = $this->businessModel->getByUser($user_id);

        if ($business) {
            // Recalculate traffic signal
            $this->trafficService->calculate($business->business_id);
        }

        // Log the event
        $this->logSubscriptionEvent($subscription_id, 'renewed', $user_id);
    }

    /**
     * Handle order completed (for review trigger)
     * 
     * @param int $order_id
     * @return void
     */
    public function onOrderCompleted(int $order_id): void {
        // This can trigger review invitation emails
        do_action('mp_woocommerce_order_completed', $order_id);
    }

    /**
     * Get subscription URL
     * 
     * @return string
     */
    public function getSubscriptionUrl(): string {
        if (!$this->product_id) {
            return wc_get_checkout_url();
        }

        return add_query_arg([
            'add-to-cart' => $this->product_id,
        ], wc_get_checkout_url());
    }

    /**
     * Get manage subscription URL
     * 
     * @param int $user_id
     * @return string
     */
    public function getManageUrl(int $user_id): string {
        if (!function_exists('wc_get_account_endpoint_url')) {
            return admin_url('profile.php');
        }

        return wc_get_account_endpoint_url('subscriptions');
    }

    /**
     * Get status message
     * 
     * @param string $status
     * @return string
     */
    protected function getStatusMessage(string $status): string {
        $messages = [
            'active' => 'Your subscription is active',
            'pending' => 'Your subscription is pending',
            'on-hold' => 'Your subscription is on hold',
            'cancelled' => 'Your subscription is cancelled',
            'expired' => 'Your subscription has expired',
            'trash' => 'Your subscription is deleted',
        ];

        return $messages[$status] ?? 'Unknown status';
    }

    /**
     * Notify subscription activated
     * 
     * @param object $business
     * @param object $subscription
     * @return void
     */
    protected function notifySubscriptionActivated($business, $subscription): void {
        $user = get_user_by('id', $business->user_id);
        
        if (!$user) {
            return;
        }

        wp_mail(
            $user->user_email,
            sprintf(__('[MyProtector] Your subscription is now active for %s', 'myprotector-platform'), $business->business_name),
            sprintf(
                __("Hello %s,\n\nYour MyProtector Business Subscription is now active!\n\nBusiness: %s\nSubscription ID: %d\n\nYour trust signal has been updated to reflect your active subscription status.\n\nThank you for choosing MyProtector!\n\nBest regards,\nThe MyProtector Team", 'myprotector-platform'),
                $user->display_name,
                $business->business_name,
                $subscription->get_id()
            )
        );
    }

    /**
     * Notify subscription cancelled
     * 
     * @param object $business
     * @return void
     */
    protected function notifySubscriptionCancelled($business): void {
        $user = get_user_by('id', $business->user_id);
        
        if (!$user) {
            return;
        }

        wp_mail(
            $user->user_email,
            sprintf(__('[MyProtector] Your subscription has been cancelled', 'myprotector-platform')),
            sprintf(
                __("Hello %s,\n\nYour MyProtector Business Subscription has been cancelled.\n\nBusiness: %s\n\nYour trust signal may be affected. Please renew your subscription to maintain your verification status.\n\nBest regards,\nThe MyProtector Team", 'myprotector-platform'),
                $user->display_name,
                $business->business_name
            )
        );
    }

    /**
     * Notify subscription expired
     * 
     * @param object $business
     * @return void
     */
    protected function notifySubscriptionExpired($business): void {
        $user = get_user_by('id', $business->user_id);
        
        if (!$user) {
            return;
        }

        wp_mail(
            $user->user_email,
            sprintf(__('[MyProtector] Your subscription has expired', 'myprotector-platform')),
            sprintf(
                __("Hello %s,\n\nYour MyProtector Business Subscription has expired.\n\nBusiness: %s\n\nPlease renew your subscription to continue enjoying the benefits of MyProtector verification.\n\nBest regards,\nThe MyProtector Team", 'myprotector-platform'),
                $user->display_name,
                $business->business_name
            )
        );
    }

    /**
     * Log subscription event
     * 
     * @param int $subscription_id
     * @param string $event
     * @param int $user_id
     * @return void
     */
    protected function logSubscriptionEvent(int $subscription_id, string $event, int $user_id): void {
        global $wpdb;
        
        $table = $wpdb->prefix . 'mp_subscription_events';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
            return;
        }
        
        $wpdb->insert(
            $table,
            [
                'subscription_id' => $subscription_id,
                'user_id' => $user_id,
                'event_type' => $event,
                'event_data' => json_encode([
                    'timestamp' => current_time('mysql'),
                    'subscription_id' => $subscription_id,
                ]),
                'created_at' => current_time('mysql'),
            ]
        );
    }
}