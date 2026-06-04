<?php
/**
 * MyProtector Platform - Business Dashboard Template
 * 
 * Business owner dashboard with reviews, stats, and settings
 * Requires WordPress authentication and business role
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Require authentication
if (!is_user_logged_in()) {
    $login_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/login' : home_url('/login');
    $redirect_to = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/business-dashboard' : home_url('/business-dashboard');
    wp_redirect(add_query_arg('redirect_to', urlencode($redirect_to), $login_url));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$logout_url = wp_logout_url($company_url);

// Mock business data
$business = [
    'name' => 'Your Business',
    'slug' => 'your-business',
    'rating' => 4.6,
    'total_reviews' => 89,
    'trust_status' => 'green',
    'trust_score' => 100,
    'category' => 'General',
    'location' => 'United States',
    'claimed' => true
];

$stats = [
    'total_reviews' => 89,
    'pending_reviews' => 3,
    'avg_rating' => 4.6,
    'profile_views' => 1247,
    'response_rate' => 94
];

$recent_reviews = $this->getMockData('reviews');
?>

<div class="mp-frontend-ui">
    <div class="mp-dashboard">
        <div class="mp-container">
            <div class="mp-dashboard-grid">
                <!-- Sidebar -->
                <aside class="mp-dashboard-sidebar">
                    <!-- Business Logo -->
                    <div class="mp-flex mp-flex-col mp-items-center mp-mb-lg" style="text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: var(--mp-radius-xl); background: var(--mp-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; margin-bottom: var(--mp-spacing-md);">
                            <?php echo esc_html(substr($business['name'], 0, 2)); ?>
                        </div>
                        <h3 style="margin: 0 0 var(--mp-spacing-xs);"><?php echo esc_html($business['name']); ?></h3>
                        <span class="mp-trust-badge mp-trust-badge-<?php echo esc_attr($business['trust_status']); ?>">
                            <?php echo strtoupper($business['trust_status']); ?>
                        </span>
                    </div>

                    <nav class="mp-dashboard-nav">
                        <div class="mp-dashboard-nav-item">
                            <a href="#overview" class="mp-dashboard-nav-link active">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                Dashboard
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#reviews" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                Reviews
                                <?php if ($stats['pending_reviews'] > 0): ?>
                                <span style="background: var(--mp-amber); color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 10px; margin-left: auto;"><?php echo $stats['pending_reviews']; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#profile" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Business Profile
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#trust" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                Trust Settings
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#analytics" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                                Analytics
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#settings" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                Settings
                            </a>
                        </div>
                    </nav>

                    <div style="border-top: 1px solid var(--mp-gray-100); padding-top: var(--mp-spacing-lg); margin-top: var(--mp-spacing-lg);">
                        <a href="<?php echo esc_url($company_url); ?>/business/<?php echo esc_attr($business['slug']); ?>" class="mp-btn mp-btn-outline mp-btn-full mp-mb-sm">
                            View Public Profile
                        </a>
                        <a href="<?php echo esc_url($logout_url); ?>" class="mp-btn mp-btn-ghost mp-btn-full">
                            Sign Out
                        </a>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="mp-dashboard-content">
                    <!-- Overview Section -->
                    <section id="overview" class="mp-dashboard-section">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Business Dashboard</h2>
                            <span style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm);">
                                Last updated: <?php echo date('F j, Y'); ?>
                            </span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mp-grid mp-grid-4" style="margin-bottom: var(--mp-spacing-2xl);">
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['total_reviews']; ?></div>
                                <div class="mp-stat-label">Total Reviews</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo number_format($stats['avg_rating'], 1); ?></div>
                                <div class="mp-stat-label">Average Rating</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo number_format($stats['profile_views']); ?></div>
                                <div class="mp-stat-label">Profile Views</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['response_rate']; ?>%</div>
                                <div class="mp-stat-label">Response Rate</div>
                            </div>
                        </div>

                        <!-- Trust Score Card -->
                        <div class="mp-card" style="margin-bottom: var(--mp-spacing-xl); background: var(--mp-green-bg); border-color: var(--mp-green);">
                            <div class="mp-flex mp-items-center mp-justify-between">
                                <div class="mp-flex mp-items-center mp-gap-lg">
                                    <div class="mp-trust-light mp-trust-light-green" style="width: 60px; height: 60px; font-size: 24px;">
                                        ✓
                                    </div>
                                    <div>
                                        <h3 style="margin: 0 0 var(--mp-spacing-xs); color: var(--mp-green-dark);">Trust Score: <?php echo $business['trust_score']; ?>%</h3>
                                        <p style="margin: 0; color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                                            Your business meets all trust criteria! Keep up the good work.
                                        </p>
                                    </div>
                                </div>
                                <a href="#trust" class="mp-btn mp-btn-sm mp-btn-outline" style="border-color: var(--mp-green); color: var(--mp-green);">
                                    Manage Trust Settings
                                </a>
                            </div>
                        </div>

                        <!-- Recent Reviews -->
                        <h3 style="margin-bottom: var(--mp-spacing-lg);">Recent Reviews</h3>
                        <div class="mp-grid">
                            <?php foreach (array_slice($recent_reviews, 0, 3) as $review): ?>
                            <div class="mp-review-card">
                                <div class="mp-review-header">
                                    <div class="mp-review-avatar"><?php echo esc_html(substr($review['author'], 0, 1)); ?></div>
                                    <div class="mp-review-meta">
                                        <h4 class="mp-review-author"><?php echo esc_html($review['author']); ?></h4>
                                        <span class="mp-review-date"><?php echo esc_html($review['date']); ?></span>
                                    </div>
                                    <div class="mp-review-rating">
                                        <div class="mp-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="mp-star <?php echo $i <= $review['rating'] ? 'mp-star-filled' : 'mp-star-empty'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="mp-review-title"><?php echo esc_html($review['title']); ?></h4>
                                <p class="mp-review-content"><?php echo esc_html($review['content']); ?></p>
                                <div class="mp-flex mp-gap-sm">
                                    <button class="mp-btn mp-btn-sm mp-btn-primary">Reply</button>
                                    <button class="mp-btn mp-btn-sm mp-btn-ghost">Flag</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Reviews Section -->
                    <section id="reviews" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Reviews Management</h2>
                            <div class="mp-flex mp-gap-sm">
                                <select class="mp-form-select" style="width: auto; padding: 0.5rem 1rem;">
                                    <option value="all">All Reviews</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="flagged">Flagged</option>
                                </select>
                            </div>
                        </div>

                        <div class="mp-grid">
                            <?php foreach ($recent_reviews as $review): ?>
                            <div class="mp-card">
                                <div class="mp-card-header">
                                    <div>
                                        <h4 class="mp-card-title"><?php echo esc_html($review['title']); ?></h4>
                                        <p style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); margin: 0;">
                                            by <?php echo esc_html($review['author']); ?> • <?php echo esc_html($review['date']); ?>
                                        </p>
                                    </div>
                                    <div class="mp-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="mp-star <?php echo $i <= $review['rating'] ? 'mp-star-filled' : 'mp-star-empty'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-md);">
                                    <?php echo esc_html($review['content']); ?>
                                </p>
                                <div class="mp-flex mp-gap-sm">
                                    <button class="mp-btn mp-btn-sm mp-btn-primary">Reply</button>
                                    <button class="mp-btn mp-btn-sm mp-btn-ghost">Edit</button>
                                    <button class="mp-btn mp-btn-sm mp-btn-ghost">Flag</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Profile Section -->
                    <section id="profile" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Business Profile</h2>
                        </div>

                        <form>
                            <div class="mp-grid mp-grid-2" style="margin-bottom: var(--mp-spacing-lg);">
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Business Name</label>
                                    <input type="text" class="mp-form-input" value="<?php echo esc_attr($business['name']); ?>">
                                </div>
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Category</label>
                                    <select class="mp-form-select">
                                        <option value="">Select category</option>
                                        <option value="technology" <?php selected($business['category'], 'Technology'); ?>>Technology</option>
                                        <option value="home-services" <?php selected($business['category'], 'Home Services'); ?>>Home Services</option>
                                        <option value="restaurants" <?php selected($business['category'], 'Restaurants'); ?>>Restaurants</option>
                                        <option value="healthcare" <?php selected($business['category'], 'Healthcare'); ?>>Healthcare</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Description</label>
                                <textarea class="mp-form-textarea" rows="4" placeholder="Describe your business..."></textarea>
                            </div>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Website</label>
                                <input type="url" class="mp-form-input" placeholder="https://www.yourbusiness.com">
                            </div>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Location</label>
                                <input type="text" class="mp-form-input" value="<?php echo esc_attr($business['location']); ?>">
                            </div>

                            <button type="submit" class="mp-btn mp-btn-primary">Save Changes</button>
                        </form>
                    </section>

                    <!-- Trust Section -->
                    <section id="trust" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Trust Settings</h2>
                        </div>

                        <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-xl);">
                            Complete all requirements to achieve a Green Trust Score and stand out to potential customers.
                        </p>

                        <div class="mp-grid mp-grid-2">
                            <div class="mp-card" style="border-left: 4px solid var(--mp-green);">
                                <div class="mp-flex mp-items-center mp-gap-md" style="margin-bottom: var(--mp-spacing-md);">
                                    <span style="font-size: 24px;">✓</span>
                                    <h4 style="margin: 0;">Insurance Verified</h4>
                                </div>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Your business insurance has been verified and is current.
                                </p>
                                <a href="#" class="mp-btn mp-btn-sm mp-btn-outline" style="border-color: var(--mp-green); color: var(--mp-green);">Update Insurance</a>
                            </div>

                            <div class="mp-card" style="border-left: 4px solid var(--mp-green);">
                                <div class="mp-flex mp-items-center mp-gap-md" style="margin-bottom: var(--mp-spacing-md);">
                                    <span style="font-size: 24px;">✓</span>
                                    <h4 style="margin: 0;">Terms & Conditions</h4>
                                </div>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Your terms and conditions page is linked and accessible.
                                </p>
                                <a href="#" class="mp-btn mp-btn-sm mp-btn-outline" style="border-color: var(--mp-green); color: var(--mp-green);">Update Terms</a>
                            </div>

                            <div class="mp-card" style="border-left: 4px solid var(--mp-green);">
                                <div class="mp-flex mp-items-center mp-gap-md" style="margin-bottom: var(--mp-spacing-md);">
                                    <span style="font-size: 24px;">✓</span>
                                    <h4 style="margin: 0;">Promise Page</h4>
                                </div>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    You've created a promise page outlining your commitments to customers.
                                </p>
                                <a href="#" class="mp-btn mp-btn-sm mp-btn-outline" style="border-color: var(--mp-green); color: var(--mp-green);">Update Promise</a>
                            </div>

                            <div class="mp-card" style="border-left: 4px solid var(--mp-green);">
                                <div class="mp-flex mp-items-center mp-gap-md" style="margin-bottom: var(--mp-spacing-md);">
                                    <span style="font-size: 24px;">✓</span>
                                    <h4 style="margin: 0;">Identity Verified</h4>
                                </div>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Your business identity has been verified through our verification process.
                                </p>
                                <span class="mp-trust-badge mp-trust-badge-green">Verified</span>
                            </div>
                        </div>
                    </section>

                    <!-- Analytics Section -->
                    <section id="analytics" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Analytics</h2>
                            <select class="mp-form-select" style="width: auto; padding: 0.5rem 1rem;">
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="365">Last year</option>
                            </select>
                        </div>

                        <div class="mp-grid mp-grid-2">
                            <div class="mp-card">
                                <h4 style="margin: 0 0 var(--mp-spacing-md);">Profile Views</h4>
                                <div class="mp-stat-value"><?php echo number_format($stats['profile_views']); ?></div>
                                <p style="color: var(--mp-green); font-size: var(--mp-font-size-sm); margin: var(--mp-spacing-xs) 0 0;">
                                    ↑ 12% from last period
                                </p>
                            </div>

                            <div class="mp-card">
                                <h4 style="margin: 0 0 var(--mp-spacing-md);">Review Requests</h4>
                                <div class="mp-stat-value">47</div>
                                <p style="color: var(--mp-green); font-size: var(--mp-font-size-sm); margin: var(--mp-spacing-xs) 0 0;">
                                    ↑ 8% from last period
                                </p>
                            </div>

                            <div class="mp-card">
                                <h4 style="margin: 0 0 var(--mp-spacing-md);">Click-through Rate</h4>
                                <div class="mp-stat-value">3.2%</div>
                                <p style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); margin: var(--mp-spacing-xs) 0 0;">
                                    Stable from last period
                                </p>
                            </div>

                            <div class="mp-card">
                                <h4 style="margin: 0 0 var(--mp-spacing-md);">Response Time</h4>
                                <div class="mp-stat-value">4h</div>
                                <p style="color: var(--mp-green); font-size: var(--mp-font-size-sm); margin: var(--mp-spacing-xs) 0 0;">
                                    ↓ 2h faster than average
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Settings Section -->
                    <section id="settings" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Account Settings</h2>
                        </div>

                        <form>
                            <h3 style="margin-bottom: var(--mp-spacing-lg);">Contact Information</h3>

                            <div class="mp-grid mp-grid-2">
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Contact Name</label>
                                    <input type="text" class="mp-form-input" value="<?php echo esc_attr($current_user->display_name); ?>">
                                </div>
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Email</label>
                                    <input type="email" class="mp-form-input" value="<?php echo esc_attr($current_user->user_email); ?>">
                                </div>
                            </div>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Phone</label>
                                <input type="tel" class="mp-form-input" placeholder="+1 (555) 123-4567">
                            </div>

                            <h3 style="margin: var(--mp-spacing-xl) 0 var(--mp-spacing-lg);">Notifications</h3>

                            <div style="margin-bottom: var(--mp-spacing-lg);">
                                <label class="mp-checkbox" style="margin-bottom: var(--mp-spacing-sm);">
                                    <input type="checkbox" checked>
                                    <span>Email me when I receive a new review</span>
                                </label>
                                <label class="mp-checkbox" style="margin-bottom: var(--mp-spacing-sm);">
                                    <input type="checkbox" checked>
                                    <span>Email me with weekly analytics summary</span>
                                </label>
                                <label class="mp-checkbox">
                                    <input type="checkbox">
                                    <span>Email me when someone flags a review</span>
                                </label>
                            </div>

                            <button type="submit" class="mp-btn mp-btn-primary">Save Changes</button>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    'use strict';
    
    // Dashboard navigation
    $('.mp-dashboard-nav-link').on('click', function(e) {
        const href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = $(href);
            
            if (target.length) {
                $('.mp-dashboard-section').hide();
                target.show();
                
                $('.mp-dashboard-nav-link').removeClass('active');
                $(this).addClass('active');
            }
        }
    });
    
})(jQuery);
</script>

<?php
get_footer();