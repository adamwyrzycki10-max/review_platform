<?php
/**
 * MyProtector Platform - Reviews Admin Controller
 * 
 * Admin functionality for managing reviews
 * 
 * @package MyProtector\Modules\Reviews\Admin
 * @version 1.0.0
 */

namespace MyProtector\Modules\Reviews\Admin;

use MyProtector\Modules\Reviews\Reviews;
use MyProtector\Modules\Reviews\Services\ReviewService;
use MyProtector\Modules\Reviews\Services\ReviewModerationService;

class ReviewsAdminController {
    /**
     * Module instance
     * 
     * @var Reviews
     */
    protected $module;

    /**
     * Service instances
     * 
     * @var array
     */
    protected $services = [];

    /**
     * Constructor
     * 
     * @param Reviews $module
     */
    public function __construct(Reviews $module) {
        $this->module = $module;
        
        $container = $module->plugin()->getContainer();
        $this->services['review'] = new ReviewService($container);
        $this->services['moderation'] = new ReviewModerationService($container);
    }

    /**
     * Render the reviews list page
     * 
     * @return void
     */
    public function renderListPage(): void {
        $args = [
            'status' => isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null,
            'search' => isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '',
            'limit' => 20,
            'page' => isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1,
        ];
        
        $reviews = $this->services['review']->getReviews($args);
        $pending_count = $this->services['moderation']->getPendingCount();
        
        $this->render('admin/reviews-list', [
            'reviews' => $reviews,
            'pending_count' => $pending_count,
            'current_status' => $args['status'],
        ]);
    }

    /**
     * Render the pending approval page
     * 
     * @return void
     */
    public function renderPendingPage(): void {
        $args = [
            'status' => 'pending',
            'limit' => 20,
            'page' => isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1,
        ];
        
        $reviews = $this->services['review']->getReviews($args);
        
        $this->render('admin/reviews-pending', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Render details meta box
     * 
     * @param \WP_Post $post
     * @return void
     */
    public function renderDetailsMetaBox(\WP_Post $post): void {
        $review_id = $post->ID;
        $rating = get_post_meta($review_id, '_mp_rating', true);
        $company_id = get_post_meta($review_id, '_company_id', true);
        $verified = get_post_meta($review_id, '_mp_verified_purchase', true);
        
        $company = $company_id ? $this->services['review']->getCompanyById($company_id) : null;
        ?>
        <table class="widefat">
            <tr>
                <th><?php _e('Rating', 'myprotector-platform'); ?></th>
                <td><?php echo esc_html($rating ? str_repeat('⭐', (int) $rating) : '-'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Company', 'myprotector-platform'); ?></th>
                <td><?php echo $company ? esc_html($company['company_name']) : '-'; ?></td>
            </tr>
            <tr>
                <th><?php _e('Verified Purchase', 'myprotector-platform'); ?></th>
                <td><?php echo $verified ? __('Yes', 'myprotector-platform') : __('No', 'myprotector-platform'); ?></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render a template
     * 
     * @param string $template
     * @param array $data
     * @return void
     */
    protected function render(string $template, array $data = []): void {
        extract($data);
        
        $template_path = $this->module->getPath('templates/' . $template . '.php');
        
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo '<div class="notice notice-error"><p>' . sprintf(__('Template not found: %s', 'myprotector-platform'), esc_html($template)) . '</p></div>';
        }
    }
}
