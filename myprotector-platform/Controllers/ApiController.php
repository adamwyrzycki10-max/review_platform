<?php
/**
 * MyProtector Platform - REST API Controller
 * 
 * Handles all REST API endpoints for the plugin
 * 
 * @package MyProtector\Controllers
 * @version 1.0.0
 */

namespace MyProtector\Controllers;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class ApiController extends WP_REST_Controller {
    /**
     * Namespace for API
     * 
     * @var string
     */
    protected $namespace = 'mp/v1';

    /**
     * Register routes
     * 
     * @return void
     */
    public function registerRoutes(): void {
        // Reviews
        register_rest_route($this->namespace, '/reviews', [
            'methods' => 'GET',
            'callback' => [$this, 'getReviews'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/reviews', [
            'methods' => 'POST',
            'callback' => [$this, 'createReview'],
            'permission_callback' => function() {
                return is_user_logged_in();
            },
        ]);
        
        register_rest_route($this->namespace, '/reviews/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getReview'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/reviews/business/(?P<business_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getBusinessReviews'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/reviews/approve', [
            'methods' => 'POST',
            'callback' => [$this, 'approveReview'],
            'permission_callback' => function() {
                return current_user_can('manage_myprotector');
            },
        ]);
        
        register_rest_route($this->namespace, '/reviews/reject', [
            'methods' => 'POST',
            'callback' => [$this, 'rejectReview'],
            'permission_callback' => function() {
                return current_user_can('manage_myprotector');
            },
        ]);
        
        // Businesses
        register_rest_route($this->namespace, '/businesses', [
            'methods' => 'GET',
            'callback' => [$this, 'getBusinesses'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/businesses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getBusiness'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/businesses/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'updateBusiness'],
            'permission_callback' => function() {
                return is_user_logged_in();
            },
        ]);
        
        register_rest_route($this->namespace, '/businesses/slug/(?P<slug>[a-z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getBusinessBySlug'],
            'permission_callback' => '__return_true',
        ]);
        
        // Traffic Signals
        register_rest_route($this->namespace, '/traffic-signals/(?P<business_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getTrafficSignal'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/traffic-signals/(?P<business_id>\d+)/override', [
            'methods' => 'POST',
            'callback' => [$this, 'overrideTrafficSignal'],
            'permission_callback' => function() {
                return current_user_can('manage_myprotector');
            },
        ]);
        
        // Dashboard
        register_rest_route($this->namespace, '/dashboard/reviews', [
            'methods' => 'GET',
            'callback' => [$this, 'getUserReviews'],
            'permission_callback' => function() {
                return is_user_logged_in();
            },
        ]);
        
        register_rest_route($this->namespace, '/dashboard/business-stats/(?P<business_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getBusinessStats'],
            'permission_callback' => function() {
                return is_user_logged_in();
            },
        ]);
    }

    /**
     * Get all reviews (admin)
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getReviews(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        $args = [
            'status' => $request->get_param('status'),
            'orderby' => $request->get_param('orderby') ?: 'created_at',
            'order' => $request->get_param('order') ?: 'DESC',
            'limit' => (int) $request->get_param('per_page') ?: 20,
            'offset' => ((int) $request->get_param('page') - 1) * 20,
        ];
        
        $reviews = $reviewModel->getAll($args);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $reviews,
            'total' => count($reviews),
        ], 200);
    }

    /**
     * Get single review
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getReview(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $review = $reviewModel->get((int) $request->get_param('id'));
        
        if (!$review) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }
        
        // Get responses
        $responses = $reviewModel->getResponses($review->review_id);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $review,
            'responses' => $responses,
        ], 200);
    }

    /**
     * Create a new review
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function createReview(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        // Validate nonce
        if (!wp_verify_nonce($request->get_param('nonce'), 'mp_frontend_nonce')) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid nonce',
            ], 403);
        }
        
        // Validate input
        $business_id = (int) $request->get_param('business_id');
        $rating = (int) $request->get_param('rating');
        $title = sanitize_text_field($request->get_param('review_title'));
        $content = sanitize_textarea_field($request->get_param('review_content'));
        
        if (!$business_id || !$rating || empty($title) || empty($content)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Missing required fields',
            ], 400);
        }
        
        if ($rating < 1 || $rating > 5) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Rating must be between 1 and 5',
            ], 400);
        }
        
        // Check duplicate
        if ($reviewModel->hasUserReviewed(get_current_user_id(), $business_id)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'You have already reviewed this business',
            ], 400);
        }
        
        // Create review
        $review_id = $reviewModel->create([
            'business_id' => $business_id,
            'user_id' => get_current_user_id(),
            'review_title' => $title,
            'review_content' => $content,
            'review_rating' => $rating,
            'review_status' => 'pending',
            'ip_address' => $this->getClientIp(),
        ]);
        
        if (!$review_id) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to create review',
            ], 500);
        }
        
        // Trigger hook
        do_action('mp_review_submitted', $review_id);
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review_id' => $review_id,
        ], 201);
    }

    /**
     * Get reviews for a business
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getBusinessReviews(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        $args = [
            'status' => 'approved',
            'orderby' => $request->get_param('orderby') ?: 'published_at',
            'order' => $request->get_param('order') ?: 'DESC',
            'limit' => (int) $request->get_param('limit') ?: 20,
            'offset' => (int) $request->get_param('offset') ?: 0,
        ];
        
        $reviews = $reviewModel->getByBusiness((int) $request->get_param('business_id'), $args);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $reviews,
            'total' => count($reviews),
        ], 200);
    }

    /**
     * Approve a review
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function approveReview(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        $review_id = (int) $request->get_param('review_id');
        
        if (!$reviewModel->exists($review_id)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }
        
        $result = $reviewModel->approve($review_id, get_current_user_id());
        
        if (!$result) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to approve review',
            ], 500);
        }
        
        // Trigger hook
        do_action('mp_review_approved', $review_id);
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Review approved',
        ], 200);
    }

    /**
     * Reject a review
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function rejectReview(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        $review_id = (int) $request->get_param('review_id');
        $reason = sanitize_text_field($request->get_param('reason') ?: '');
        
        if (!$reviewModel->exists($review_id)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }
        
        $result = $reviewModel->reject($review_id, $reason);
        
        if (!$result) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to reject review',
            ], 500);
        }
        
        // Trigger hook
        do_action('mp_review_rejected', $review_id);
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Review rejected',
        ], 200);
    }

    /**
     * Get all businesses
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getBusinesses(WP_REST_Request $request): WP_REST_Response {
        $businessModel = new \MyProtector\Models\BusinessModel();
        
        $args = [
            'category_id' => $request->get_param('category') ? (int) $request->get_param('category') : null,
            'orderby' => $request->get_param('orderby') ?: 'avg_rating',
            'order' => $request->get_param('order') ?: 'DESC',
            'limit' => (int) $request->get_param('per_page') ?: 20,
            'offset' => ((int) $request->get_param('page') - 1) * 20,
            'search' => sanitize_text_field($request->get_param('search') ?: ''),
            'min_rating' => (float) $request->get_param('min_rating') ?: 0,
            'trust_status' => sanitize_text_field($request->get_param('trust') ?: ''),
        ];
        
        $businesses = $businessModel->getAllActive($args);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $businesses,
            'total' => count($businesses),
        ], 200);
    }

    /**
     * Get single business
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getBusiness(WP_REST_Request $request): WP_REST_Response {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        
        $business = $businessModel->get((int) $request->get_param('id'));
        
        if (!$business) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Business not found',
            ], 404);
        }
        
        // Get traffic signal
        $signal = $trafficService->getSignal($business->business_id);
        $signal_data = $signal ? $trafficService->getSignalData($signal, true) : [];
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $business,
            'traffic_signal' => $signal_data,
        ], 200);
    }

    /**
     * Get business by slug
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getBusinessBySlug(WP_REST_Request $request): WP_REST_Response {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        
        $business = $businessModel->getBySlug($request->get_param('slug'));
        
        if (!$business) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Business not found',
            ], 404);
        }
        
        // Get traffic signal
        $signal = $trafficService->getSignal($business->business_id);
        $signal_data = $signal ? $trafficService->getSignalData($signal, true) : [];
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $business,
            'traffic_signal' => $signal_data,
        ], 200);
    }

    /**
     * Update a business
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function updateBusiness(WP_REST_Request $request): WP_REST_Response {
        $businessModel = new \MyProtector\Models\BusinessModel();
        
        $business_id = (int) $request->get_param('id');
        $business = $businessModel->get($business_id);
        
        if (!$business) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Business not found',
            ], 404);
        }
        
        // Check ownership
        if ($business->user_id != get_current_user_id() && !current_user_can('manage_myprotector')) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        // Build update data
        $data = [];
        
        $fields = ['business_name', 'business_description', 'business_tagline', 'business_email', 
                   'business_phone', 'business_website', 'insurance_url', 'terms_url', 'promise_page_url'];
        
        foreach ($fields as $field) {
            if ($request->has_param($field)) {
                $data[$field] = sanitize_text_field($request->get_param($field));
            }
        }
        
        if (empty($data)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No data to update',
            ], 400);
        }
        
        $result = $businessModel->update($business_id, $data);
        
        if (!$result) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to update business',
            ], 500);
        }
        
        // Recalculate traffic signal
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        $trafficService->calculate($business_id);
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Business updated successfully',
        ], 200);
    }

    /**
     * Get traffic signal for a business
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getTrafficSignal(WP_REST_Request $request): WP_REST_Response {
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        
        $signal = $trafficService->getSignal((int) $request->get_param('business_id'));
        
        if (!$signal) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Traffic signal not found',
            ], 404);
        }
        
        $signal_data = $trafficService->getSignalData($signal, true);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $signal_data,
        ], 200);
    }

    /**
     * Override traffic signal (admin)
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function overrideTrafficSignal(WP_REST_Request $request): WP_REST_Response {
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        
        $business_id = (int) $request->get_param('business_id');
        $status = sanitize_text_field($request->get_param('status'));
        $reason = sanitize_text_field($request->get_param('reason') ?: 'Admin override');
        
        if (!in_array($status, ['green', 'amber', 'red'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid status',
            ], 400);
        }
        
        $result = $trafficService->override($business_id, $status, $reason, get_current_user_id());
        
        if (!$result) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to override traffic signal',
            ], 500);
        }
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Traffic signal overridden successfully',
        ], 200);
    }

    /**
     * Get user's reviews (dashboard)
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getUserReviews(WP_REST_Request $request): WP_REST_Response {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        
        $reviews = $reviewModel->getByUser(get_current_user_id(), [
            'status' => null,
            'limit' => 20,
        ]);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $reviews,
            'total' => count($reviews),
        ], 200);
    }

    /**
     * Get business stats for dashboard
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getBusinessStats(WP_REST_Request $request): WP_REST_Response {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
        
        $business_id = (int) $request->get_param('business_id');
        $business = $businessModel->get($business_id);
        
        if (!$business) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Business not found',
            ], 404);
        }
        
        // Check ownership
        if ($business->user_id != get_current_user_id() && !current_user_can('manage_myprotector')) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        // Get stats
        $rating_distribution = $reviewModel->getRatingDistribution($business_id);
        $pending_reviews = $reviewModel->count($business_id, 'pending');
        $signal = $trafficService->getSignal($business_id);
        $signal_data = $signal ? $trafficService->getSignalData($signal) : [];
        
        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'total_reviews' => $business->total_reviews,
                'avg_rating' => $business->avg_rating,
                'pending_reviews' => $pending_reviews,
                'rating_distribution' => $rating_distribution,
                'traffic_signal' => $signal_data,
            ],
        ], 200);
    }

    /**
     * Get client IP
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
}