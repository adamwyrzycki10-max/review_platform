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
    /**
     * Module name
     * 
     * @var string
     */
    protected $name = 'frontend-ui';

    /**
     * Module dependencies
     * 
     * @var array
     */
    protected $dependencies = ['reviews', 'business-profiles', 'traffic-signals'];

    /**
     * Review model
     * 
     * @var ReviewModel
     */
    protected $reviewModel;

    /**
     * Business model
     * 
     * @var BusinessModel
     */
    protected $businessModel;

    /**
     * Traffic signal service
     * 
     * @var TrafficSignalService
     */
    protected $trafficService;

    /**
     * Mock data for development/fallback
     * 
     * @var array
     */
    protected $mock_data = [
        'businesses' => [
            [
                'id' => 1,
                'name' => 'TechVentures Solutions',
                'slug' => 'techventures-solutions',
                'logo' => 'https://ui-avatars.com/api/?name=TV&background=0A1F44&color=fff&size=128',
                'description' => 'Enterprise software development and cloud infrastructure solutions for modern businesses.',
                'website' => 'https://techventures.example.com',
                'rating' => 4.8,
                'total_reviews' => 247,
                'trust_status' => 'green',
                'trust_score' => 100,
                'category' => 'Technology',
                'location' => 'San Francisco, CA',
                'claimed' => true,
                'insurance_url' => 'https://example.com/insurance',
                'terms_url' => 'https://example.com/terms',
                'promise_url' => 'https://example.com/promise',
                'established' => 2015,
            ],
            [
                'id' => 2,
                'name' => 'GreenLeaf Landscaping',
                'slug' => 'greenleaf-landscaping',
                'logo' => 'https://ui-avatars.com/api/?name=GL&background=2E7D32&color=fff&size=128',
                'description' => 'Professional landscaping and garden design services for residential and commercial properties.',
                'website' => 'https://greenleaf.example.com',
                'rating' => 4.2,
                'total_reviews' => 89,
                'trust_status' => 'amber',
                'trust_score' => 66.67,
                'category' => 'Home Services',
                'location' => 'Portland, OR',
                'claimed' => true,
                'insurance_url' => 'https://example.com/insurance',
                'terms_url' => '',
                'promise_url' => '',
                'established' => 2018,
            ],
            [
                'id' => 3,
                'name' => 'Metro Auto Repair',
                'slug' => 'metro-auto-repair',
                'logo' => 'https://ui-avatars.com/api/?name=MA&background=D50000&color=fff&size=128',
                'description' => 'Full-service auto repair shop providing quality mechanical services with certified technicians.',
                'website' => 'https://metroauto.example.com',
                'rating' => 3.5,
                'total_reviews' => 156,
                'trust_status' => 'red',
                'trust_score' => 33.33,
                'category' => 'Automotive',
                'location' => 'Chicago, IL',
                'claimed' => false,
                'insurance_url' => '',
                'terms_url' => '',
                'promise_url' => '',
                'established' => 2020,
            ],
            [
                'id' => 4,
                'name' => 'Crave Kitchen & Bar',
                'slug' => 'crave-kitchen-bar',
                'logo' => 'https://ui-avatars.com/api/?name=CK&background=FF6D00&color=fff&size=128',
                'description' => 'Farm-to-table restaurant featuring locally sourced ingredients and craft cocktails.',
                'website' => 'https://cravekitchen.example.com',
                'rating' => 4.6,
                'total_reviews' => 312,
                'trust_status' => 'green',
                'trust_score' => 100,
                'category' => 'Restaurants',
                'location' => 'Austin, TX',
                'claimed' => true,
                'insurance_url' => 'https://example.com/insurance',
                'terms_url' => 'https://example.com/terms',
                'promise_url' => 'https://example.com/promise',
                'established' => 2016,
            ],
            [
                'id' => 5,
                'name' => 'HealthFirst Medical Group',
                'slug' => 'healthfirst-medical-group',
                'logo' => 'https://ui-avatars.com/api/?name=HM&background=0288D1&color=fff&size=128',
                'description' => 'Comprehensive healthcare provider offering primary care, specialists, and wellness programs.',
                'website' => 'https://healthfirst.example.com',
                'rating' => 4.9,
                'total_reviews' => 523,
                'trust_status' => 'green',
                'trust_score' => 100,
                'category' => 'Healthcare',
                'location' => 'Boston, MA',
                'claimed' => true,
                'insurance_url' => 'https://example.com/insurance',
                'terms_url' => 'https://example.com/terms',
                'promise_url' => 'https://example.com/promise',
                'established' => 2010,
            ],
            [
                'id' => 6,
                'name' => 'Swift Logistics Co',
                'slug' => 'swift-logistics-co',
                'logo' => 'https://ui-avatars.com/api/?name=SL&background=5E35B1&color=fff&size=128',
                'description' => 'Global shipping and logistics solutions for businesses of all sizes.',
                'website' => 'https://swiftlogistics.example.com',
                'rating' => 3.8,
                'total_reviews' => 78,
                'trust_status' => 'amber',
                'trust_score' => 66.67,
                'category' => 'Logistics',
                'location' => 'Atlanta, GA',
                'claimed' => true,
                'insurance_url' => '',
                'terms_url' => 'https://example.com/terms',
                'promise_url' => '',
                'established' => 2019,
            ],
        ],
        'reviews' => [
            [
                'id' => 1,
                'business_id' => 1,
                'title' => 'Exceptional service and technical expertise',
                'content' => 'TechVentures helped us migrate our entire infrastructure to the cloud. Their team was professional, knowledgeable, and delivered ahead of schedule. Highly recommended for any enterprise looking to modernize their systems.',
                'rating' => 5,
                'reviewer' => 'Michael Chen',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=MC&background=0A1F44&color=fff&size=64',
                'date' => '2026-05-28',
                'verified' => true,
                'helpful' => 24,
                'images' => [],
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'title' => 'Great results but communication could improve',
                'content' => 'The final product was excellent, but there were times when it was difficult to get status updates. Once we flagged this, they assigned a dedicated project manager who improved the experience significantly.',
                'rating' => 4,
                'reviewer' => 'Sarah Johnson',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=SJ&background=2E7D32&color=fff&size=64',
                'date' => '2026-05-15',
                'verified' => true,
                'helpful' => 12,
                'images' => [],
            ],
            [
                'id' => 3,
                'business_id' => 1,
                'title' => 'Exceeded expectations on all fronts',
                'content' => 'From the initial consultation to final delivery, every step was handled with precision. The ROI we\'ve seen in just 6 months has been remarkable. Our team productivity increased by 40% after implementing their solutions.',
                'rating' => 5,
                'reviewer' => 'David Park',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=DP&background=D50000&color=fff&size=64',
                'date' => '2026-05-02',
                'verified' => true,
                'helpful' => 31,
                'images' => [],
            ],
            [
                'id' => 4,
                'business_id' => 2,
                'title' => 'Beautiful garden transformation',
                'content' => 'GreenLeaf completely transformed our backyard into a stunning oasis. The design team understood our vision perfectly and the installation crew was respectful and efficient.',
                'rating' => 5,
                'reviewer' => 'Emily Watson',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=EW&background=FF6D00&color=fff&size=64',
                'date' => '2026-05-20',
                'verified' => true,
                'helpful' => 8,
                'images' => [],
            ],
            [
                'id' => 5,
                'business_id' => 2,
                'title' => 'Good work but pricey',
                'content' => 'The quality of work was excellent, but I felt the pricing was on the higher side compared to other landscapers I got quotes from. That said, you get what you pay for.',
                'rating' => 4,
                'reviewer' => 'Robert Miller',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=RM&background=0288D1&color=fff&size=64',
                'date' => '2026-04-28',
                'verified' => true,
                'helpful' => 5,
                'images' => [],
            ],
            [
                'id' => 6,
                'business_id' => 3,
                'title' => 'Decent service, slow turnaround',
                'content' => 'The repairs they did were solid, but it took twice as long as quoted. Had to rent a car for an extra week which was inconvenient. Quality was good though.',
                'rating' => 3,
                'reviewer' => 'James Wilson',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=JW&background=5E35B1&color=fff&size=64',
                'date' => '2026-05-10',
                'verified' => true,
                'helpful' => 3,
                'images' => [],
            ],
            [
                'id' => 7,
                'business_id' => 4,
                'title' => 'Best farm-to-table experience in Austin',
                'content' => 'Crave Kitchen has become our go-to spot for special occasions. The seasonal menu never disappoints, and the cocktail program is creative without being pretentious. Service is consistently excellent.',
                'rating' => 5,
                'reviewer' => 'Amanda Foster',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=AF&background=0A1F44&color=fff&size=64',
                'date' => '2026-05-25',
                'verified' => true,
                'helpful' => 45,
                'images' => [],
            ],
            [
                'id' => 8,
                'business_id' => 4,
                'title' => 'Great food, noisy atmosphere',
                'content' => 'The food is absolutely amazing - every dish was perfectly executed. However, if you\'re looking for a quiet dinner conversation, this isn\'t the place. It\'s very lively and loud.',
                'rating' => 4,
                'reviewer' => 'Chris Thompson',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=CT&background=2E7D32&color=fff&size=64',
                'date' => '2026-05-12',
                'verified' => true,
                'helpful' => 18,
                'images' => [],
            ],
            [
                'id' => 9,
                'business_id' => 5,
                'title' => 'Healthcare done right',
                'content' => 'Finally found a medical practice that truly puts patients first. The staff is incredibly caring, appointments run on time, and the doctors take time to explain everything thoroughly. The new patient portal is also excellent.',
                'rating' => 5,
                'reviewer' => 'Lisa Anderson',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=LA&background=D50000&color=fff&size=64',
                'date' => '2026-05-30',
                'verified' => true,
                'helpful' => 52,
                'images' => [],
            ],
            [
                'id' => 10,
                'business_id' => 5,
                'title' => 'Wonderful experience with the wellness program',
                'content' => 'Enrolled in their holistic wellness program and have seen remarkable improvements in my overall health. The nutritionist and fitness coaches work together seamlessly. Highly recommend their preventive care services.',
                'rating' => 5,
                'reviewer' => 'Patricia Moore',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=PM&background=FF6D00&color=fff&size=64',
                'date' => '2026-05-18',
                'verified' => true,
                'helpful' => 29,
                'images' => [],
            ],
        ],
        'categories' => [
            'Technology',
            'Home Services',
            'Automotive',
            'Restaurants',
            'Healthcare',
            'Logistics',
            'Retail',
            'Finance',
            'Education',
            'Entertainment',
        ],
        'stats' => [
            'total_businesses' => 1247,
            'total_reviews' => 8945,
            'avg_rating' => 4.2,
            'trust_score' => 78,
        ],
    ];

    /**
     * Get module directory
     * 
     * @return string
     */
    protected function getModuleDirectory(): string {
        return 'FrontendUI';
    }

    /**
     * Boot the module
     * 
     * @return void
     */
    public function boot(): void {
        // Initialize models
        $this->reviewModel = new ReviewModel();
        $this->businessModel = new BusinessModel();
        $this->trafficService = new TrafficSignalService();

        // Register shortcodes
        $this->registerShortcodes();
    }

    /**
     * Register module hooks
     * 
     * @return void
     */
    public function registerHooks(): void {
        // Enqueue frontend assets
        $this->addAction('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        
        // AJAX handlers
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
    /**
     * Render business profile page
     * 
     * @param array $atts
     * @return string
     */
    public function renderBusinessProfile(array $atts = []): string {
        $atts = shortcode_atts([
            'id' => 0,
            'slug' => '',
        ], $atts);

        // Get business from database
        $business = null;
        if (!empty($atts['id'])) {
            $business = $this->businessModel->get((int) $atts['id']);
        } elseif (!empty($atts['slug'])) {
            $business = $this->businessModel->getBySlug($atts['slug']);
        }

        if (!$business) {
            return '<div class="mp-error">' . __('Business not found.', 'myprotector-platform') . '</div>';
        }

        // Get traffic signal
        $signal = $this->trafficService->getSignal($business->business_id);
        $signal_data = $signal ? $this->trafficService->getSignalData($signal, true) : [];

        // Get approved reviews
        $reviews = $this->reviewModel->getByBusiness($business->business_id, [
            'status' => 'approved',
            'orderby' => 'published_at',
            'order' => 'DESC',
            'limit' => 20,
        ]);

        // Enqueue assets
        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');

        // Get categories
        $categories = $this->businessModel->getCategories($business->business_id);
        $category_name = !empty($categories) ? $categories[0]->name : '';

        // Build location string
        $location_parts = array_filter([
            $business->city,
            $business->state,
        ]);
        $location = implode(', ', $location_parts);

        ob_start();
        include $this->getPath('templates/business.php');
        return ob_get_clean();
    }

    /**
     * Render business list/directory
     * 
     * @param array $atts
     * @return string
     */
    public function renderBusinessList(array $atts = []): string {
        $atts = shortcode_atts([
            'category' => '',
            'limit' => 12,
            'orderby' => 'avg_rating',
            'order' => 'DESC',
            'show_filters' => 'true',
        ], $atts);

        // Get businesses from database
        $businesses = $this->businessModel->getAllActive([
            'category_id' => !empty($atts['category']) ? (int) $atts['category'] : null,
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'limit' => (int) $atts['limit'],
        ]);

        // Enqueue assets
        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');

        ob_start();
        include $this->getPath('templates/directory.php');
        return ob_get_clean();
    }

    /**
     * Render reviews list for a business
     * 
     * @param array $atts
     * @return string
     */
    public function renderReviewsList(array $atts = []): string {
        $atts = shortcode_atts([
            'business_id' => 0,
            'limit' => 10,
            'sort' => 'recent',
        ], $atts);

        if (empty($atts['business_id'])) {
            return '';
        }

        // Determine sort order
        $orderby = 'published_at';
        $order = 'DESC';
        switch ($atts['sort']) {
            case 'highest':
                $orderby = 'review_rating';
                $order = 'DESC';
                break;
            case 'lowest':
                $orderby = 'review_rating';
                $order = 'ASC';
                break;
            case 'helpful':
                $orderby = 'helpful_count';
                $order = 'DESC';
                break;
        }

        $reviews = $this->reviewModel->getByBusiness((int) $atts['business_id'], [
            'status' => 'approved',
            'orderby' => $orderby,
            'order' => $order,
            'limit' => (int) $atts['limit'],
        ]);

        ob_start();
        echo '<div class="mp-reviews-list">';
        foreach ($reviews as $review) {
            $reviewer = get_userdata($review->user_id);
            $avatar = get_avatar_url($review->user_id, ['size' => 48]);
            $reviewer_name = $reviewer ? $reviewer->display_name : __('Anonymous', 'myprotector-platform');
            $date = $review->published_at ? date_i18n('F j, Y', strtotime($review->published_at)) : '';
            
            // Get response if exists
            $responses = $this->reviewModel->getResponses($review->review_id);
            ?>
            <div class="mp-review-card" data-review-id="<?php echo esc_attr($review->review_id); ?>">
                <div class="mp-review-header">
                    <img src="<?php echo esc_url($avatar); ?>" alt="" class="mp-review-avatar">
                    <div class="mp-review-meta">
                        <div class="mp-review-reviewer">
                            <?php echo esc_html($reviewer_name); ?>
                            <?php if ($review->trust_level === 'verified'): ?>
                            <span class="mp-review-verified">✓ Verified</span>
                            <?php endif; ?>
                        </div>
                        <div class="mp-review-date"><?php echo esc_html($date); ?></div>
                    </div>
                    <div class="mp-rating">
                        <?php echo $this->renderStars($review->review_rating); ?>
                    </div>
                </div>
                
                <h4 class="mp-review-title"><?php echo esc_html($review->review_title); ?></h4>
                <p class="mp-review-content"><?php echo esc_html($review->review_content); ?></p>
                
                <?php if (!empty($responses)): ?>
                <div class="mp-review-responses">
                    <?php foreach ($responses as $response): ?>
                    <div class="mp-review-response">
                        <strong><?php echo esc_html($response->responder_name ?? 'Business'); ?></strong>
                        <p><?php echo esc_html($response->response_content); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="mp-review-footer">
                    <button class="mp-review-helpful-btn" data-review-id="<?php echo esc_attr($review->review_id); ?>">
                        <span>👍</span>
                        <span>Helpful</span>
                        <span class="mp-helpful-count">(<?php echo esc_html($review->helpful_count); ?>)</span>
                    </button>
                </div>
            </div>
            <?php
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Render trust signal widget
     * 
     * @param array $atts
     * @return string
     */
    public function renderTrustSignal(array $atts = []): string {
        $atts = shortcode_atts([
            'business_id' => 0,
            'style' => 'standard',
            'show_checklist' => 'true',
        ], $atts);

        if (empty($atts['business_id'])) {
            return '';
        }

        return $this->trafficService->render((int) $atts['business_id'], [
            'style' => $atts['style'],
            'show_checklist' => $atts['show_checklist'] === 'true',
        ]);
    }

    

    /**
     * Render search widget
     * 
     * @param array $atts
     * @return string
     */
    public function renderSearch(array $atts = []): string {
        $atts = shortcode_atts([
            'placeholder' => 'Search businesses...',
            'show_category_filter' => 'true',
        ], $atts);

        wp_enqueue_style('mp-frontend-ui');
        wp_enqueue_script('mp-frontend-ui');

        ob_start();
        ?>
        <div class="mp-search-widget" data-show-category="<?php echo esc_attr($atts['show_category_filter']); ?>">
            <form class="mp-search-form" action="" method="GET">
                <div class="mp-search-input-wrapper">
                    <input type="text" name="mp_search" class="mp-search-input" placeholder="<?php echo esc_attr($atts['placeholder']); ?>">
                    <button type="submit" class="mp-search-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </div>
                <?php if ($atts['show_category_filter'] === 'true'): ?>
                <select name="mp_category" class="mp-category-filter">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_terms([
                        'taxonomy' => 'mp_company_category',
                        'hide_empty' => false,
                    ]);
                    foreach ($categories as $cat): ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </form>
            <div class="mp-search-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    /**
     * Handle review submission AJAX
     * 
     * @return void
     */
    public function handleSubmitReview(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please log in to submit a review.', 'myprotector-platform')]);
        }

        // Validate required fields
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $title = isset($_POST['review_title']) ? sanitize_text_field($_POST['review_title']) : '';
        $content = isset($_POST['review_content']) ? sanitize_textarea_field($_POST['review_content']) : '';

        if (!$business_id) {
            wp_send_json_error(['message' => __('Invalid business.', 'myprotector-platform')]);
        }

        if ($rating < 1 || $rating > 5) {
            wp_send_json_error(['message' => __('Please select a rating.', 'myprotector-platform')]);
        }

        if (empty($title)) {
            wp_send_json_error(['message' => __('Please enter a review title.', 'myprotector-platform')]);
        }

        if (empty($content) || strlen($content) < 10) {
            wp_send_json_error(['message' => __('Please enter a review (minimum 10 characters).', 'myprotector-platform')]);
        }

        // Check if user already reviewed this business
        if ($this->reviewModel->hasUserReviewed(get_current_user_id(), $business_id)) {
            wp_send_json_error(['message' => __('You have already submitted a review for this business.', 'myprotector-platform')]);
        }

        // Create the review
        $review_id = $this->reviewModel->create([
            'business_id' => $business_id,
            'user_id' => get_current_user_id(),
            'review_title' => $title,
            'review_content' => $content,
            'review_rating' => $rating,
            'review_status' => 'pending',
            'ip_address' => $this->getClientIp(),
        ]);

        if (!$review_id) {
            wp_send_json_error(['message' => __('Failed to submit review. Please try again.', 'myprotector-platform')]);
        }

        // Trigger email notification
        do_action('mp_review_submitted', $review_id);

        wp_send_json_success([
            'message' => __('Thank you for your review! It will be published after moderation.', 'myprotector-platform'),
            'review_id' => $review_id,
        ]);
    }

    /**
     * Handle get reviews AJAX
     * 
     * @return void
     */
    public function handleGetReviews(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
        $per_page = isset($_POST['per_page']) ? (int) $_POST['per_page'] : 10;
        $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'recent';

        if (!$business_id) {
            wp_send_json_error(['message' => __('Invalid business.', 'myprotector-platform')]);
        }

        // Determine sort
        $orderby = 'published_at';
        $order = 'DESC';
        switch ($sort) {
            case 'highest':
                $orderby = 'review_rating';
                $order = 'DESC';
                break;
            case 'lowest':
                $orderby = 'review_rating';
                $order = 'ASC';
                break;
            case 'helpful':
                $orderby = 'helpful_count';
                $order = 'DESC';
                break;
        }

        $offset = ($page - 1) * $per_page;
        
        $reviews = $this->reviewModel->getByBusiness($business_id, [
            'status' => 'approved',
            'orderby' => $orderby,
            'order' => $order,
            'limit' => $per_page,
            'offset' => $offset,
        ]);

        // Build HTML
        ob_start();
        foreach ($reviews as $review) {
            $reviewer = get_userdata($review->user_id);
            $avatar = get_avatar_url($review->user_id, ['size' => 48]);
            $reviewer_name = $reviewer ? $reviewer->display_name : __('Anonymous', 'myprotector-platform');
            $date = $review->published_at ? date_i18n('F j, Y', strtotime($review->published_at)) : '';
            ?>
            <div class="mp-review-card" data-review-id="<?php echo esc_attr($review->review_id); ?>">
                <div class="mp-review-header">
                    <img src="<?php echo esc_url($avatar); ?>" alt="" class="mp-review-avatar">
                    <div class="mp-review-meta">
                        <div class="mp-review-reviewer">
                            <?php echo esc_html($reviewer_name); ?>
                            <?php if ($review->trust_level === 'verified'): ?>
                            <span class="mp-review-verified">✓</span>
                            <?php endif; ?>
                        </div>
                        <div class="mp-review-date"><?php echo esc_html($date); ?></div>
                    </div>
                    <div class="mp-rating"><?php echo $this->renderStars($review->review_rating); ?></div>
                </div>
                <h4 class="mp-review-title"><?php echo esc_html($review->review_title); ?></h4>
                <p class="mp-review-content"><?php echo esc_html($review->review_content); ?></p>
                <div class="mp-review-footer">
                    <button class="mp-review-helpful-btn" data-review-id="<?php echo esc_attr($review->review_id); ?>">
                        <span>👍</span>
                        <span>Helpful</span>
                        <span class="mp-helpful-count">(<?php echo esc_html($review->helpful_count); ?>)</span>
                    </button>
                </div>
            </div>
            <?php
        }
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'count' => count($reviews),
            'has_more' => count($reviews) === $per_page,
        ]);
    }

    /**
     * Handle mark helpful AJAX
     * 
     * @return void
     */
    public function handleMarkHelpful(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please log in.', 'myprotector-platform')]);
        }

        $review_id = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        
        if (!$review_id) {
            wp_send_json_error(['message' => __('Invalid review.', 'myprotector-platform')]);
        }

        $result = $this->reviewModel->markHelpful($review_id, get_current_user_id());
        
        if ($result) {
            $review = $this->reviewModel->get($review_id);
            wp_send_json_success([
                'message' => __('Marked as helpful!', 'myprotector-platform'),
                'count' => $review ? $review->helpful_count : 0,
            ]);
        } else {
            wp_send_json_error(['message' => __('You have already marked this as helpful.', 'myprotector-platform')]);
        }
    }

    /**
     * Handle respond to review AJAX
     * 
     * @return void
     */
    public function handleRespondToReview(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please log in.', 'myprotector-platform')]);
        }

        $review_id = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        $content = isset($_POST['response_content']) ? sanitize_textarea_field($_POST['response_content']) : '';

        if (!$review_id) {
            wp_send_json_error(['message' => __('Invalid review.', 'myprotector-platform')]);
        }

        if (empty($content)) {
            wp_send_json_error(['message' => __('Please enter a response.', 'myprotector-platform')]);
        }

        $result = $this->reviewModel->addResponse($review_id, $content, get_current_user_id());
        
        if ($result) {
            wp_send_json_success([
                'message' => __('Response submitted!', 'myprotector-platform'),
            ]);
        } else {
            wp_send_json_error(['message' => __('Failed to submit response.', 'myprotector-platform')]);
        }
    }

    /**
     * Render star rating HTML
     * 
     * @param float $rating
     * @return string
     */
    public function renderStars(float $rating): string {
        $html = '<div class="mp-stars">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $html .= '<span class="mp-star mp-star-filled">★</span>';
            } elseif ($i - 0.5 <= $rating) {
                $html .= '<span class="mp-star mp-star-half">★</span>';
            } else {
                $html .= '<span class="mp-star mp-star-empty">☆</span>';
            }
        }
        $html .= '</div>';
        return $html;
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
     * Register shortcodes
     * 
     * @return void
     */
    protected function registerShortcodes(): void {
        add_shortcode('mp_home', [$this, 'renderHomepage']);
        add_shortcode('mp_directory', [$this, 'renderDirectory']);
        add_shortcode('mp_business_profile', [$this, 'renderBusinessProfile']);
        add_shortcode('mp_dashboard', [$this, 'renderDashboard']);
        add_shortcode('mp_rating', [$this, 'renderRatingBadge']);
        add_shortcode('mp_reviews', [$this, 'renderReviewSummary']);
        add_shortcode('mp_trust', [$this, 'renderTrustWidget']);
    }

    /**
     * Enqueue frontend assets
     * 
     * @return void
     */
    public function enqueueAssets(): void {
        // Google Fonts
        wp_enqueue_style(
            'mp-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
            [],
            null
        );

        // Main stylesheet
        wp_enqueue_style(
            'mp-frontend-ui',
            $this->getUrl('assets/css/style.css'),
            [],
            MYPROTECTOR_VERSION
        );

        // Main JavaScript
        wp_enqueue_script(
            'mp-frontend-ui',
            $this->getUrl('assets/js/app.js'),
            ['jquery'],
            MYPROTECTOR_VERSION,
            true
        );

        // Localize script
        wp_localize_script('mp-frontend-ui', 'mpFrontend', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mp_frontend_nonce'),
            'strings' => [
                'loading' => __('Loading...', 'myprotector-platform'),
                'error' => __('Something went wrong. Please try again.', 'myprotector-platform'),
                'submitReview' => __('Submit Review', 'myprotector-platform'),
                'searchPlaceholder' => __('Search businesses...', 'myprotector-platform'),
            ],
            'mockData' => $this->mock_data,
        ]);
    }

    /**
     * Render homepage
     * 
     * @param array $atts
     * @return string
     */
    public function renderHomepage(array $atts = []): string {
        $atts = shortcode_atts([
            'featured_count' => 6,
        ], $atts);

        ob_start();
        include $this->getPath('templates/home.php');
        return ob_get_clean();
    }

    /**
     * Render directory page
     * 
     * @param array $atts
     * @return string
     */
    public function renderDirectory(array $atts = []): string {
        $atts = shortcode_atts([
            'per_page' => 12,
            'show_filters' => 'true',
        ], $atts);

        ob_start();
        include $this->getPath('templates/directory.php');
        return ob_get_clean();
    }

  
    /**
     * Render dashboard
     * 
     * @param array $atts
     * @return string
     */
    public function renderDashboard(array $atts = []): string {
        $atts = shortcode_atts([
            'type' => 'individual', // individual, business, reseller
        ], $atts);

        ob_start();
        include $this->getPath('templates/dashboard.php');
        return ob_get_clean();
    }

    /**
     * Render rating badge widget
     * 
     * @param array $atts
     * @return string
     */
    public function renderRatingBadge(array $atts = []): string {
        $atts = shortcode_atts([
            'business_id' => 0,
            'style' => 'compact', // compact, full, badge
            'size' => 'medium', // small, medium, large
        ], $atts);

        $business = null;
        if (!empty($atts['business_id'])) {
            foreach ($this->mock_data['businesses'] as $b) {
                if ($b['id'] == $atts['business_id']) {
                    $business = $b;
                    break;
                }
            }
        }

        if (!$business) {
            $business = $this->mock_data['businesses'][0];
        }

        ob_start();
        include $this->getPath('templates/components/rating-badge.php');
        return ob_get_clean();
    }

    /**
     * Render review summary widget
     * 
     * @param array $atts
     * @return string
     */
    public function renderReviewSummary(array $atts = []): string {
        $atts = shortcode_atts([
            'business_id' => 0,
            'limit' => 3,
        ], $atts);

        $business = null;
        if (!empty($atts['business_id'])) {
            foreach ($this->mock_data['businesses'] as $b) {
                if ($b['id'] == $atts['business_id']) {
                    $business = $b;
                    break;
                }
            }
        }

        if (!$business) {
            $business = $this->mock_data['businesses'][0];
        }

        $reviews = array_filter($this->mock_data['reviews'], function($r) use ($business) {
            return $r['business_id'] == $business['id'];
        });

        $reviews = array_slice(array_values($reviews), 0, (int) $atts['limit']);

        ob_start();
        include $this->getPath('templates/components/review-summary.php');
        return ob_get_clean();
    }

    /**
     * Render trust widget
     * 
     * @param array $atts
     * @return string
     */
    public function renderTrustWidget(array $atts = []): string {
        $atts = shortcode_atts([
            'business_id' => 0,
            'style' => 'badge', // badge, bar, full
            'size' => 'medium',
        ], $atts);

        $business = null;
        if (!empty($atts['business_id'])) {
            foreach ($this->mock_data['businesses'] as $b) {
                if ($b['id'] == $atts['business_id']) {
                    $business = $b;
                    break;
                }
            }
        }

        if (!$business) {
            $business = $this->mock_data['businesses'][0];
        }

        ob_start();
        include $this->getPath('templates/components/trust-signal.php');
        return ob_get_clean();
    }

    /**
     * Get mock data
     * 
     * @param string $key
     * @return mixed
     */
    public function getMockData(string $key = null) {
        if ($key === null) {
            return $this->mock_data;
        }
        return $this->mock_data[$key] ?? null;
    }

    /**
     * Handle review modal AJAX
     * 
     * @return void
     */
    public function handleReviewModal(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        $business_id = isset($_POST['business_id']) ? (int) $_POST['business_id'] : 0;
        
        // Get business
        $business = null;
        foreach ($this->mock_data['businesses'] as $b) {
            if ($b['id'] == $business_id) {
                $business = $b;
                break;
            }
        }
        
        if (!$business) {
            wp_send_json_error(['message' => __('Business not found.', 'myprotector-platform')]);
        }
        
        // Return modal HTML
        ob_start();
        include $this->getPath('templates/components/review-modal.php');
        $html = ob_get_clean();
        
        wp_send_json_success(['html' => $html]);
    }

    /**
     * Handle search AJAX
     * 
     * @return void
     */
    public function handleSearch(): void {
        check_ajax_referer('mp_frontend_nonce', 'nonce');
        
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $rating = isset($_POST['rating']) ? (float) $_POST['rating'] : 0;
        $trust = isset($_POST['trust']) ? sanitize_text_field($_POST['trust']) : '';
        
        $results = $this->mock_data['businesses'];
        
        // Filter by search query
        if (!empty($query)) {
            $results = array_filter($results, function($b) use ($query) {
                return stripos($b['name'], $query) !== false || 
                       stripos($b['description'], $query) !== false;
            });
        }
        
        // Filter by category
        if (!empty($category)) {
            $results = array_filter($results, function($b) use ($category) {
                return $b['category'] === $category;
            });
        }
        
        // Filter by rating
        if ($rating > 0) {
            $results = array_filter($results, function($b) use ($rating) {
                return $b['rating'] >= $rating;
            });
        }
        
        // Filter by trust status
        if (!empty($trust)) {
            $results = array_filter($results, function($b) use ($trust) {
                return $b['trust_status'] === $trust;
            });
        }
        
        // Get review cards HTML
        ob_start();
        foreach ($results as $business) {
            include $this->getPath('templates/components/business-card.php');
        }
        $cards_html = ob_get_clean();
        
        wp_send_json_success([
            'html' => $cards_html,
            'count' => count($results),
        ]);
    }

    /**
     * Get template part
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    public function getTemplatePart(string $template, array $data = []): string {
        extract($data);
        ob_start();
        include $this->getPath('templates/' . $template . '.php');
        return ob_get_clean();
    }
}
