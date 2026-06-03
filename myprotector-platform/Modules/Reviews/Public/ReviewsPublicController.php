<?php
/**
 * MyProtector Platform - Reviews Public Controller
 * 
 * Public-facing functionality for reviews
 * 
 * @package MyProtector\Modules\Reviews\Public
 * @version 1.0.0
 */

namespace MyProtector\Modules\Reviews\Public_;

use MyProtector\Modules\Reviews\Reviews;
use MyProtector\Modules\Reviews\Services\ReviewService;

class ReviewsPublicController {
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
    }

    /**
     * Enqueue public assets
     * 
     * @return void
     */
    public function enqueueAssets(): void {
        $this->module->enqueueStyle('reviews-public', 'css/reviews-public.css');
        $this->module->enqueueScript('reviews-public', 'js/reviews-public.js', ['jquery']);
    }

    /**
     * Render review form
     * 
     * @param int $company_id
     * @return string
     */
    public function renderReviewForm(int $company_id): string {
        if (!is_user_logged_in()) {
            return '<p class="mp-login-required">' . __('Please log in to leave a review.', 'myprotector-platform') . '</p>';
        }
        
        ob_start();
        include $this->module->getPath('templates/public/review-form.php');
        return ob_get_clean();
    }

    /**
     * Render single review
     * 
     * @param int $review_id
     * @return string
     */
    public function renderSingleReview(int $review_id): string {
        $review = $this->services['review']->getReview($review_id);
        
        if (!$review) {
            return '';
        }
        
        ob_start();
        include $this->module->getPath('templates/public/single-review.php');
        return ob_get_clean();
    }

    /**
     * Render reviews list for a company
     * 
     * @param int $company_id
     * @param int $limit
     * @return string
     */
    public function renderReviewsList(int $company_id, int $limit = 10): string {
        $reviews = $this->services['review']->getApprovedReviews($company_id, $limit);
        
        if (empty($reviews)) {
            return '<p class="mp-no-reviews">' . __('No reviews yet. Be the first to review!', 'myprotector-platform') . '</p>';
        }
        
        ob_start();
        include $this->module->getPath('templates/public/reviews-list.php');
        return ob_get_clean();
    }
}
