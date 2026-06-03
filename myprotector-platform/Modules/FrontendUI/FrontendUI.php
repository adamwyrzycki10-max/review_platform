<?php
/**
 * MyProtector Platform - Frontend UI Module
 * 
 * Frontend UI components with real database integration
 * 
 * @package MyProtector\Modules\FrontendUI
 * @version 1.0.0
 */

namespace MyProtector\Modules\FrontendUI;

use MyProtector\Core\Module;
use MyProtector\Models\ReviewModel;
use MyProtector\Models\BusinessModel;
use MyProtector\Services\TrafficSignal\TrafficSignalService;

class FrontendUI extends Module {
    protected $name = 'frontend-ui';
    protected $dependencies = ['reviews', 'business-profiles', 'traffic-signals'];
    protected $reviewModel;
    protected $businessModel;
    protected $trafficService;

    protected function getModuleDirectory(): string {
        return 'FrontendUI';
    }

    public function boot(): void {
        $this->reviewModel = new ReviewModel();
        $this->businessModel = new BusinessModel();
        $this->trafficService = new TrafficSignalService();
        $this->registerShortcodes();
    }

    public function registerHooks(): void {
        $this->addAction('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        $this->addAction('wp_ajax_mp_open_review_modal', [$this, 'handleReviewModal']);
        $this->addAction('wp_ajax_nopriv_mp_open_review_modal', [$this, 'handleReviewModal']);
        $this->addAction('wp_ajax_mp_search_businesses', [$this, 'handleSearch']);
        $this->addAction('wp_ajax_nopriv_mp_search_businesses', [$this, 'handleSearch']);
        $this->addAction('wp_ajax_mp_submit_review', [$this, 'handleSubmitReview']);
        $this->addAction('wp_ajax_nopriv_mp_submit_review', [$this, 'handleSubmitReview']);
        $this->addAction('wp_ajax_mp_get_business_reviews', [$this, 'handleGetReviews']);
        $this->addAction('wp_ajax_nopriv_mp_get_business_reviews', [$this, 'handleGetReviews']);
        $this->addAction('wp_ajax_mp_mark_helpful', [$this, 'handleMarkHelpful']);
        $this->addAction('wp_ajax_nopriv_mp_mark_helpful', [$this, 'handleMarkHelpful']);
        $this->addAction('wp_ajax_mp_respond_to_review', [$this, 'handleRespondToReview']);
    }

    protected function registerShortcodes(): void {
        add_shortcode('mp_business_profile', [$this, 'renderBusinessProfile']);
        add_shortcode('mp_business_list', [$this, 'renderBusinessList']);
        add_shortcode('mp_reviews', [$this, 'renderReviewsList']);
        add_shortcode('mp_trust_signal', [$this, 'renderTrustSignal']);
        add_shortcode('mp_rating_badge', [$this, 'renderRatingBadge']);
        add_shortcode('mp_search', [$this, 'renderSearch']);
    }

    public function enqueueAssets(): void {
        wp_register_style('mp-frontend-ui', $this->getUrl('assets/css/frontend.css'), [], $this->version);
        wp_register_script('mp-frontend-ui', $this->getUrl('assets/js/frontend.js'), ['jquery'], $this->version, true);
        wp_localize_script('mp-frontend-ui', 'mpFrontend', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mp_frontend_nonce'),
            'strings' => [
                'submitting' => __('Submitting...', 'myprotector-platform'),
                'submitted' => __('Review submitted!', 'myprotector-platform'),
                'error' => __('An error occurred. Please try again.', 'myprotector-platform'),
            ],
        ]);
    }

    public function renderBusinessProfile(array $atts = []): string {
        $atts = shortcode_atts(['id' => 0, 'slug' => ''], $atts);
        $business = null;
        if (!empty($atts['id'])) {
            $business = $this->businessModel->get((int) $atts['id']);
        } elseif (!empty($atts['slug'])) {
            $business = $this->businessModel->getBySlug($atts['slug']);
        }
        if (!$business) {
            return '<div class="mp-error">' . __('Business not found.', 'myprotector-platform') . '</div>';
        }
        $signal = $this->trafficService->getSignal($business->business_id);
        $reviews = $this->reviewModel->getByBusiness($business->business_id, [
            'status' => 'approved', 'orderby' => 'published_at', 'order' => 'DESC', 'limit' => 20,
        ]);
        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');
        ob_start();
        include $this->getPath('templates/business.php');
        return ob_get_clean();
    }

    public function renderBusinessList(array $atts = []): string {
        $atts = shortcode_atts(['category' => '', 'limit' => 12, 'orderby' => 'avg_rating', 'order' => 'DESC', 'show_filters' => 'true'], $atts);
        $businesses = $this->businessModel->getAllActive([
            'category_id' => !empty($atts['category']) ? (int) $atts['category'] : null,
            'orderby' => $atts['orderby'], 'order' => $atts['order'], 'limit' => (int) $atts['limit'],
        ]);
        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');
        ob_start();
        include $this->getPath('templates/directory.php');
        return ob_get_clean();
    }

    public function renderReviewsList(array $atts = []): string {
        $atts = shortcode_atts(['business_id' => 0, 'limit' => 10, 'sort' => 'recent'], $atts);
        if (empty($atts['business_id'])) return '';
        $orderby = 'published_at'; $order = 'DESC';
        if ($atts['sort'] === 'highest') { $orderby = 'review_rating'; $order = 'DESC'; }
        elseif ($atts['sort'] === 'lowest') { $orderby = 'review_rating'; $order = 'ASC'; }
        elseif ($atts['sort'] === 'helpful') { $orderby = 'helpful_count'; $order = 'DESC'; }
        $reviews = $this->reviewModel->getByBusiness((int) $atts['business_id'], [
            'status' => 'approved', 'orderby' => $orderby, 'order' => $order, 'limit' => (int) $atts['limit'],
        ]);
        ob_start();
        echo '<div class="mp-reviews-list">';
        foreach ($reviews as $review) {
            $reviewer = get_userdata($review->user_id);
            $avatar = get_avatar_url($review->user_id, ['size' => 48]);
            $reviewer_name = $reviewer ? $reviewer->display_name : __('Anonymous', 'myprotector-platform');
            $date = $review->published_at ? date_i18n('F j, Y', strtotime($review->published_at)) : '';
            echo '<div class="mp-review-card">';
            echo '<div class="mp-review-header">';
            echo '<img src="' . esc_url($avatar) . '" alt="" class="mp-review-avatar">';
            echo '<div class="mp-review-meta"><div class="mp-review-reviewer">' . esc_html($reviewer_name) . '</div>';
            echo '<div class="mp-review-date">' . esc_html($date) . '</div></div>';
            echo '<div class="mp-rating">' . $this->renderStars($review->review_rating) . '</div></div>';
            echo '<h4 class="mp-review-title">' . esc_html($review->review_title) . '</h4>';
            echo '<p class="mp-review-content">' . esc_html($review->review_content) . '</p>';
            echo '<div class="mp-review-footer"><button class="mp-review-helpful-btn" data-review-id="' . esc_attr($review->review_id) . '">';
            echo '<span>👍</span> Helpful <span class="mp-helpful-count">(' . esc_html($review->helpful_count) . ')</span></button></div>';
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function renderTrustSignal(array $atts = []): string {
        $atts = shortcode_atts(['business_id' => 0, 'style' => 'standard', 'show_checklist' => 'true'], $atts);
        if (empty($atts['business_id'])) return '';
        return $this->trafficService->render((int) $atts['business_id'], [
            'style' => $atts['style'], 'show_checklist' => $atts['show_checklist'] === 'true',
        ]);
    }

    public function renderRatingBadge(array $atts = []): string {
        $atts = shortcode_atts(['business_id' => 0, 'style' => 'compact', 'size' => 'medium'], $atts);
        if (empty($atts['business_id'])) return '';
        $business = $this->businessModel->get((int) $atts['business_id']);
        if (!$business) return '';
        ob_start();
        include $this->getPath('templates/components/rating-badge.php');
        return ob_get_clean();
    }

    public function renderSearch(array $atts = []): string {
        $atts = shortcode_atts(['placeholder' => 'Search businesses...', 'show_category_filter' => 'true'], $atts);
        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');
        ob_start();
        echo '<div class="mp-search-widget"><form class="mp-search-form"><input type="text" name="mp_search" placeholder="' . esc_attr($atts['placeholder']) . '"><button type="submit">Search</button></form></div>';
        return ob_get_clean();
    }

    public function handleReviewModal(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        if (!$business_id) wp_send_json_error(['message' => __('Invalid business.', 'myprotector-platform')]);
        $business = $this->businessModel->get($business_id);
        if (!$business) wp_send_json_error(['message' => __('Business not found.', 'myprotector-platform')]);
        ob_start();
        include $this->getPath('templates/components/review-modal.php');
        wp_send_json_success(['html' => ob_get_clean()]);
    }

    public function handleSearch(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        $args = ['search' => $query, 'limit' => 20];
        if (!empty($_POST['category'])) $args['category_id'] = (int) $_POST['category'];
        if (!empty($_POST['rating'])) $args['min_rating'] = (float) $_POST['rating'];
        $businesses = $this->businessModel->getAllActive($args);
        ob_start();
        foreach ($businesses as $business) {
            include $this->getPath('templates/components/business-card.php');
        }
        $cards_html = ob_get_clean();
        if (empty($cards_html)) $cards_html = '<p class="mp-no-results">No businesses found.</p>';
        wp_send_json_success(['html' => $cards_html, 'count' => count($businesses)]);
    }

    public function handleSubmitReview(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error(['message' => __('Please log in.', 'myprotector-platform')]);
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $title = isset($_POST['review_title']) ? sanitize_text_field($_POST['review_title']) : '';
        $content = isset($_POST['review_content']) ? sanitize_textarea_field($_POST['review_content']) : '';
        if (!$business_id || $rating < 1 || $rating > 5 || empty($title) || strlen($content) < 10) {
            wp_send_json_error(['message' => __('Invalid input.', 'myprotector-platform')]);
        }
        if ($this->reviewModel->hasUserReviewed(get_current_user_id(), $business_id)) {
            wp_send_json_error(['message' => __('Already reviewed.', 'myprotector-platform')]);
        }
        $review_id = $this->reviewModel->create([
            'business_id' => $business_id, 'user_id' => get_current_user_id(),
            'review_title' => $title, 'review_content' => $content, 'review_rating' => $rating,
            'review_status' => 'pending', 'ip_address' => $this->getClientIp(),
        ]);
        if (!$review_id) wp_send_json_error(['message' => __('Failed.', 'myprotector-platform')]);
        do_action('mp_review_submitted', $review_id);
        wp_send_json_success(['message' => __('Thank you!', 'myprotector-platform'), 'review_id' => $review_id]);
    }

    public function handleGetReviews(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
        $per_page = isset($_POST['per_page']) ? (int) $_POST['per_page'] : 10;
        if (!$business_id) wp_send_json_error(['message' => __('Invalid business.', 'myprotector-platform')]);
        $offset = ($page - 1) * $per_page;
        $reviews = $this->reviewModel->getByBusiness($business_id, [
            'status' => 'approved', 'orderby' => 'published_at', 'order' => 'DESC', 'limit' => $per_page, 'offset' => $offset,
        ]);
        ob_start();
        foreach ($reviews as $review) {
            $reviewer = get_userdata($review->user_id);
            $reviewer_name = $reviewer ? $reviewer->display_name : __('Anonymous', 'myprotector-platform');
            echo '<div class="mp-review-card"><strong>' . esc_html($reviewer_name) . '</strong>';
            echo $this->renderStars($review->review_rating);
            echo '<p>' . esc_html($review->review_content) . '</p></div>';
        }
        wp_send_json_success(['html' => ob_get_clean(), 'count' => count($reviews)]);
    }

    public function handleMarkHelpful(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error(['message' => __('Please log in.', 'myprotector-platform')]);
        $review_id = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        if (!$review_id) wp_send_json_error(['message' => __('Invalid review.', 'myprotector-platform')]);
        $result = $this->reviewModel->markHelpful($review_id, get_current_user_id());
        if ($result) {
            $review = $this->reviewModel->get($review_id);
            wp_send_json_success(['message' => __('Marked!', 'myprotector-platform'), 'count' => $review ? $review->helpful_count : 0]);
        } else {
            wp_send_json_error(['message' => __('Already marked.', 'myprotector-platform')]);
        }
    }

    public function handleRespondToReview(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error(['message' => __('Please log in.', 'myprotector-platform')]);
        $review_id = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        $content = isset($_POST['response_content']) ? sanitize_textarea_field($_POST['response_content']) : '';
        if (!$review_id || empty($content)) wp_send_json_error(['message' => __('Invalid input.', 'myprotector-platform')]);
        $result = $this->reviewModel->addResponse($review_id, $content, get_current_user_id());
        if ($result) wp_send_json_success(['message' => __('Response submitted!', 'myprotector-platform')]);
        else wp_send_json_error(['message' => __('Failed.', 'myprotector-platform')]);
    }

    public function renderStars(float $rating): string {
        $html = '<div class="mp-stars">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) $html .= '<span class="mp-star-filled">★</span>';
            elseif ($i - 0.5 <= $rating) $html .= '<span class="mp-star-half">★</span>';
            else $html .= '<span class="mp-star-empty">☆</span>';
        }
        return $html . '</div>';
    }

    protected function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return sanitize_text_field(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        if (!empty($_SERVER['REMOTE_ADDR'])) return sanitize_text_field($_SERVER['REMOTE_ADDR']);
        return '';
    }

    public function getTemplatePart(string $template, array $data = []): string {
        extract($data);
        ob_start();
        include $this->getPath('templates/' . $template . '.php');
        return ob_get_clean();
    }
}
