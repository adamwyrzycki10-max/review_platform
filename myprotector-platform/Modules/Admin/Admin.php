<?php
/**
 * MyProtector Platform - Admin Module
 * 
 * Handles all admin functionality including reviews moderation,
 * business management, traffic signal override, reseller commissions,
 * and system settings.
 * 
 * @package MyProtector\Modules\Admin
 * @version 1.0.0
 */

namespace MyProtector\Modules\Admin;

use MyProtector\Core\Module;

class Admin extends Module {
    /**
     * Module name
     * 
     * @var string
     */
    protected $name = 'admin';

    /**
     * Admin menu pages
     * 
     * @var array
     */
    protected $pages = [];

    /**
     * Get module directory
     * 
     * @return string
     */
    protected function getModuleDirectory(): string {
        return 'Admin';
    }

    /**
     * Boot the module
     * 
     * @return void
     */
    public function boot(): void {
        // Initialize models
        $this->initModels();
        
        // Register admin menu pages
        $this->registerAdminMenu();
        
        // Register AJAX handlers
        $this->registerAjaxHandlers();
    }

    /**
     * Initialize models
     * 
     * @return void
     */
    protected function initModels(): void {
        // Models will be used by admin pages
    }

    /**
     * Register admin hooks
     * 
     * @return void
     */
    public function registerHooks(): void {
        $this->addAction('admin_menu', [$this, 'addAdminMenu']);
        $this->addAction('admin_init', [$this, 'handleAdminActions']);
        $this->addAction('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * Register admin menu
     * 
     * @return void
     */
    protected function registerAdminMenu(): void {
        // Main menu
        add_menu_page(
            __('MyProtector', 'myprotector-platform'),
            __('MyProtector', 'myprotector-platform'),
            'manage_myprotector',
            'myprotector',
            [$this, 'renderDashboardPage'],
            'dashicons-shield',
            30
        );

        // Dashboard
        add_submenu_page(
            'myprotector',
            __('Dashboard', 'myprotector-platform'),
            __('Dashboard', 'myprotector-platform'),
            'manage_myprotector',
            'myprotector',
            [$this, 'renderDashboardPage']
        );

        // Reviews moderation
        add_submenu_page(
            'myprotector',
            __('Reviews', 'myprotector-platform'),
            __('Reviews', 'myprotector-platform'),
            'manage_myprotector',
            'mp-reviews',
            [$this, 'renderReviewsPage']
        );

        // Businesses
        add_submenu_page(
            'myprotector',
            __('Businesses', 'myprotector-platform'),
            __('Businesses', 'myprotector-platform'),
            'manage_myprotector',
            'mp-businesses',
            [$this, 'renderBusinessesPage']
        );

        // Traffic Signals
        add_submenu_page(
            'myprotector',
            __('Trust Signals', 'myprotector-platform'),
            __('Trust Signals', 'myprotector-platform'),
            'manage_myprotector',
            'mp-traffic-signals',
            [$this, 'renderTrafficSignalsPage']
        );

        // Resellers
        add_submenu_page(
            'myprotector',
            __('Resellers', 'myprotector-platform'),
            __('Resellers', 'myprotector-platform'),
            'mp_view_resellers',
            'mp-resellers',
            [$this, 'renderResellersPage']
        );

        // Commissions
        add_submenu_page(
            'myprotector',
            __('Commissions', 'myprotector-platform'),
            __('Commissions', 'myprotector-platform'),
            'mp_release_commissions',
            'mp-commissions',
            [$this, 'renderCommissionsPage']
        );

        // Settings
        add_submenu_page(
            'myprotector',
            __('Settings', 'myprotector-platform'),
            __('Settings', 'myprotector-platform'),
            'mp_manage_settings',
            'mp-settings',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Add admin menu items
     * 
     * @return void
     */
    public function addAdminMenu(): void {
        // Additional admin menu registration
    }

    /**
     * Handle admin actions
     * 
     * @return void
     */
    public function handleAdminActions(): void {
        // Check if this is our admin page
        if (!isset($_GET['page']) || strpos($_GET['page'], 'mp-') !== 0) {
            return;
        }

        // Handle review actions
        if (isset($_POST['mp_action'])) {
            $this->handleReviewAction();
        }

        // Handle business actions
        if (isset($_POST['mp_business_action'])) {
            $this->handleBusinessAction();
        }

        // Handle traffic signal override
        if (isset($_POST['mp_signal_override'])) {
            $this->handleSignalOverride();
        }
    }

    /**
     * Handle review actions
     * 
     * @return void
     */
    protected function handleReviewAction(): void {
        if (!current_user_can('manage_myprotector')) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'mp_admin_nonce')) {
            return;
        }

        $action = sanitize_text_field($_POST['mp_action']);
        $review_id = (int) ($_POST['review_id'] ?? 0);

        if (!$review_id) {
            return;
        }

        $reviewModel = new \MyProtector\Models\ReviewModel();

        switch ($action) {
            case 'approve':
                $reviewModel->approve($review_id, get_current_user_id());
                do_action('mp_review_approved', $review_id);
                break;
            case 'reject':
                $reason = sanitize_text_field($_POST['rejection_reason'] ?? '');
                $reviewModel->reject($review_id, $reason);
                do_action('mp_review_rejected', $review_id);
                break;
            case 'delete':
                $reviewModel->delete($review_id);
                break;
        }

        // Redirect back
        wp_redirect(add_query_arg('message', 'review_' . $action, wp_get_referer()));
        exit;
    }

    /**
     * Handle business actions
     * 
     * @return void
     */
    protected function handleBusinessAction(): void {
        if (!current_user_can('manage_myprotector')) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'mp_admin_nonce')) {
            return;
        }

        $action = sanitize_text_field($_POST['mp_business_action']);
        $business_id = (int) ($_POST['business_id'] ?? 0);

        if (!$business_id) {
            return;
        }

        $businessModel = new \MyProtector\Models\BusinessModel();

        switch ($action) {
            case 'verify':
                $businessModel->verify($business_id, get_current_user_id());
                break;
            case 'suspend':
                $reason = sanitize_text_field($_POST['suspension_reason'] ?? '');
                $businessModel->suspend($business_id, $reason);
                break;
            case 'delete':
                $businessModel->update($business_id, ['deleted_at' => current_time('mysql')]);
                break;
        }

        wp_redirect(add_query_arg('message', 'business_' . $action, wp_get_referer()));
        exit;
    }

    /**
     * Handle traffic signal override
     * 
     * @return void
     */
    protected function handleSignalOverride(): void {
        if (!current_user_can('manage_myprotector')) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'mp_admin_nonce')) {
            return;
        }

        $business_id = (int) ($_POST['business_id'] ?? 0);
        $status = sanitize_text_field($_POST['trust_status'] ?? '');
        $reason = sanitize_text_field($_POST['override_reason'] ?? '');

        if (!$business_id || !in_array($status, ['green', 'amber', 'red'])) {
            return;
        }

        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        $trafficService->override($business_id, $status, $reason, get_current_user_id());

        wp_redirect(add_query_arg('message', 'signal_overridden', wp_get_referer()));
        exit;
    }

    /**
     * Enqueue admin assets
     * 
     * @param string $hook
     * @return void
     */
    public function enqueueAdminAssets(string $hook): void {
        // Only load on our pages
        if (strpos($hook, 'myprotector') === false && strpos($hook, 'mp-') === false) {
            return;
        }

        wp_enqueue_style(
            'mp-admin',
            $this->getUrl('assets/css/admin.css'),
            [],
            $this->version
        );

        wp_enqueue_script(
            'mp-admin',
            $this->getUrl('assets/js/admin.js'),
            ['jquery'],
            $this->version,
            true
        );

        wp_localize_script('mp-admin', 'mpAdmin', [
            'nonce' => wp_create_nonce('mp_admin_nonce'),
        ]);
    }

    /**
     * Register AJAX handlers
     * 
     * @return void
     */
    protected function registerAjaxHandlers(): void {
        add_action('wp_ajax_mp_admin_get_reviews', [$this, 'ajaxGetReviews']);
        add_action('wp_ajax_mp_admin_bulk_action', [$this, 'ajaxBulkAction']);
        add_action('wp_ajax_mp_admin_get_businesses', [$this, 'ajaxGetBusinesses']);
        add_action('wp_ajax_mp_admin_get_signal', [$this, 'ajaxGetSignal']);
    }

    /**
     * Render dashboard page
     * 
     * @return void
     */
    public function renderDashboardPage(): void {
        // Get stats
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $businessModel = new \MyProtector\Models\BusinessModel();

        $stats = [
            'total_reviews' => $reviewModel->count(),
            'pending_reviews' => $reviewModel->count(['review_status' => 'pending']),
            'total_businesses' => $businessModel->countByStatus(),
            'active_businesses' => $businessModel->countByStatus('active'),
        ];

        // Get recent pending reviews
        $recent_reviews = $reviewModel->getPending(5);

        include $this->getPath('Pages/dashboard.php');
    }

    /**
     * Render reviews page
     * 
     * @return void
     */
    public function renderReviewsPage(): void {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $businessModel = new \MyProtector\Models\BusinessModel();

        // Get filter parameters
        $status = sanitize_text_field($_GET['status'] ?? '');
        $search = sanitize_text_field($_GET['s'] ?? '');
        $page = max(1, (int) ($_GET['paged'] ?? 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        // Build args
        $args = [
            'limit' => $per_page,
            'offset' => $offset,
        ];

        if (!empty($status)) {
            $args['status'] = $status;
        }

        // Get reviews
        if (!empty($search)) {
            $reviews = $reviewModel->search($search, $args);
        } else {
            $reviews = $reviewModel->getAll($args);
        }

        // Count total
        $total = $reviewModel->count($status ? ['review_status' => $status] : []);

        include $this->getPath('Pages/reviews.php');
    }

    /**
     * Render businesses page
     * 
     * @return void
     */
    public function renderBusinessesPage(): void {
        $businessModel = new \MyProtector\Models\BusinessModel();

        // Get filter parameters
        $status = sanitize_text_field($_GET['status'] ?? '');
        $search = sanitize_text_field($_GET['s'] ?? '');
        $page = max(1, (int) ($_GET['paged'] ?? 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        // Build args
        $args = [
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => $per_page,
            'offset' => $offset,
        ];

        if (!empty($status)) {
            $args['status'] = $status;
        }

        // Get businesses
        if (!empty($search)) {
            $businesses = $businessModel->search($search, $args);
        } else {
            $businesses = $businessModel->getAllActive($args);
        }

        $total = $businessModel->countByStatus($status);

        include $this->getPath('Pages/businesses.php');
    }

    /**
     * Render traffic signals page
     * 
     * @return void
     */
    public function renderTrafficSignalsPage(): void {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();

        // Get businesses with signals
        $businesses = $businessModel->getAllActive([
            'limit' => 50,
        ]);

        // Build signals array
        $signals = [];
        foreach ($businesses as $business) {
            $signal = $trafficService->getSignal($business->business_id);
            if ($signal) {
                $signals[$business->business_id] = $trafficService->getSignalData($signal);
            }
        }

        include $this->getPath('Pages/traffic-signals.php');
    }

    /**
     * Render resellers page
     * 
     * @return void
     */
    public function renderResellersPage(): void {
        $resellerModel = new \MyProtector\Models\ResellerModel();

        $resellers = $resellerModel->getAllActive([
            'limit' => 50,
        ]);

        include $this->getPath('Pages/resellers.php');
    }

    /**
     * Render commissions page
     * 
     * @return void
     */
    public function renderCommissionsPage(): void {
        $resellerModel = new \MyProtector\Models\ResellerModel();

        global $wpdb;
        
        // Get recent commissions
        $commissions = $wpdb->get_results(
            "SELECT c.*, r.company_name as reseller_name, b.business_name
             FROM {$wpdb->prefix}mp_commissions c
             LEFT JOIN {$wpdb->prefix}mp_resellers r ON c.reseller_id = r.reseller_id
             LEFT JOIN {$wpdb->prefix}mp_businesses b ON c.business_id = b.business_id
             ORDER BY c.created_at DESC
             LIMIT 100"
        );

        // Get pending total
        $pending_total = $wpdb->get_var(
            "SELECT SUM(commission_amount) FROM {$wpdb->prefix}mp_commissions WHERE commission_status = 'pending'"
        );

        include $this->getPath('Pages/commissions.php');
    }

    /**
     * Render settings page
     * 
     * @return void
     */
    public function renderSettingsPage(): void {
        // Save settings if posted
        if (isset($_POST['mp_save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'mp_settings')) {
            $this->saveSettings();
            wp_redirect(add_query_arg('message', 'settings_saved', wp_get_referer()));
            exit;
        }

        // Get current settings
        $settings = [
            'mp_subscription_price' => get_option('mp_subscription_price', 50),
            'mp_min_reviews' => get_option('mp_min_reviews', 5),
            'mp_min_rating' => get_option('mp_min_rating', 3.5),
            'mp_review_approval_required' => get_option('mp_review_approval_required', 1),
            'mp_email_notifications' => get_option('mp_email_notifications', 1),
        ];

        include $this->getPath('Pages/settings.php');
    }

    /**
     * Save settings
     * 
     * @return void
     */
    protected function saveSettings(): void {
        if (!current_user_can('mp_manage_settings')) {
            return;
        }

        $settings = [
            'mp_subscription_price' => floatval($_POST['mp_subscription_price'] ?? 50),
            'mp_min_reviews' => absint($_POST['mp_min_reviews'] ?? 5),
            'mp_min_rating' => floatval($_POST['mp_min_rating'] ?? 3.5),
            'mp_review_approval_required' => isset($_POST['mp_review_approval_required']) ? 1 : 0,
            'mp_email_notifications' => isset($_POST['mp_email_notifications']) ? 1 : 0,
        ];

        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
    }

    /**
     * AJAX: Get reviews
     * 
     * @return void
     */
    public function ajaxGetReviews(): void {
        check_ajax_referer('mp_admin_nonce', 'nonce');

        if (!current_user_can('manage_myprotector')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $reviewModel = new \MyProtector\Models\ReviewModel();

        $status = sanitize_text_field($_POST['status'] ?? '');
        $search = sanitize_text_field($_POST['search'] ?? '');
        $page = (int) ($_POST['page'] ?? 1);
        $per_page = 20;

        $args = [
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ];

        if (!empty($status)) {
            $args['status'] = $status;
        }

        if (!empty($search)) {
            $reviews = $reviewModel->search($search, $args);
        } else {
            $reviews = $reviewModel->getAll($args);
        }

        wp_send_json_success(['reviews' => $reviews]);
    }

    /**
     * AJAX: Bulk action
     * 
     * @return void
     */
    public function ajaxBulkAction(): void {
        check_ajax_referer('mp_admin_nonce', 'nonce');

        if (!current_user_can('manage_myprotector')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $action = sanitize_text_field($_POST['bulk_action'] ?? '');
        $ids = array_map('intval', $_POST['review_ids'] ?? []);

        if (empty($ids)) {
            wp_send_json_error(['message' => 'No items selected']);
        }

        $reviewModel = new \MyProtector\Models\ReviewModel();

        switch ($action) {
            case 'approve':
                foreach ($ids as $id) {
                    $reviewModel->approve($id, get_current_user_id());
                }
                break;
            case 'delete':
                foreach ($ids as $id) {
                    $reviewModel->delete($id);
                }
                break;
        }

        wp_send_json_success(['message' => 'Bulk action completed']);
    }

    /**
     * AJAX: Get businesses
     * 
     * @return void
     */
    public function ajaxGetBusinesses(): void {
        check_ajax_referer('mp_admin_nonce', 'nonce');

        if (!current_user_can('manage_myprotector')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $businessModel = new \MyProtector\Models\BusinessModel();

        $search = sanitize_text_field($_POST['search'] ?? '');
        $page = (int) ($_POST['page'] ?? 1);
        $per_page = 20;

        $args = [
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ];

        if (!empty($search)) {
            $businesses = $businessModel->search($search, $args);
        } else {
            $businesses = $businessModel->getAllActive($args);
        }

        wp_send_json_success(['businesses' => $businesses]);
    }

    /**
     * AJAX: Get traffic signal
     * 
     * @return void
     */
    public function ajaxGetSignal(): void {
        check_ajax_referer('mp_admin_nonce', 'nonce');

        if (!current_user_can('manage_myprotector')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $business_id = (int) ($_POST['business_id'] ?? 0);

        if (!$business_id) {
            wp_send_json_error(['message' => 'Invalid business ID']);
        }

        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        $signal = $trafficService->getSignal($business_id);

        if (!$signal) {
            $result = $trafficService->calculate($business_id);
            $signal = $trafficService->getSignal($business_id);
        }

        $data = $signal ? $trafficService->getSignalData($signal, true) : [];

        wp_send_json_success(['signal' => $data]);
    }

    /**
     * Display admin notice
     * 
     * @param string $message
     * @param string $type
     * @return void
     */
    protected function adminNotice(string $message, string $type = 'success'): void {
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }
}