<?php
/**
 * MyProtector Platform - Individual Dashboard Template
 * 
 * User dashboard with reviews, activity, and settings
 * Requires WordPress authentication
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Require authentication
if (!is_user_logged_in()) {
    $login_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/login' : home_url('/login');
    $redirect_to = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/dashboard' : home_url('/dashboard');
    wp_redirect(add_query_arg('redirect_to', urlencode($redirect_to), $login_url));
    exit;
}

get_header();

// Get FrontendUI module instance
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$current_user = wp_get_current_user();
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$logout_url = wp_logout_url($company_url);

// Get user's data
$user_id = get_current_user_id();
$user_reviews = $frontend_ui->getMockData('reviews');
$stats = [
    'total_reviews' => count($user_reviews),
    'helpful_votes' => 45,
    'trust_score' => 92,
    'member_since' => 'January 2025'
];
?>

<div class="mp-frontend-ui">
    <div class="mp-dashboard">
        <div class="mp-container">
            <div class="mp-dashboard-grid">
                <!-- Sidebar -->
                <aside class="mp-dashboard-sidebar">
                    <div class="mp-flex mp-flex-col mp-items-center mp-mb-lg" style="text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--mp-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; margin-bottom: var(--mp-spacing-md);">
                            <?php echo esc_html(substr($current_user->display_name ?: $current_user->user_email, 0, 1)); ?>
                        </div>
                        <h3 style="margin: 0 0 var(--mp-spacing-xs);"><?php echo esc_html($current_user->display_name ?: $current_user->user_email); ?></h3>
                        <p style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); margin: 0;">Individual Member</p>
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
                                Overview
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#my-reviews" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                My Reviews
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#bookmarks" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                Bookmarks
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
                        <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-btn mp-btn-outline mp-btn-full mp-mb-sm">
                            Write a Review
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
                            <h2 class="mp-dashboard-title">Welcome back, <?php echo esc_html($current_user->first_name ?: $current_user->display_name); ?>!</h2>
                            <span style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm);">Member since <?php echo esc_html($stats['member_since']); ?></span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mp-grid mp-grid-4" style="margin-bottom: var(--mp-spacing-2xl);">
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['total_reviews']; ?></div>
                                <div class="mp-stat-label">Reviews Written</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['helpful_votes']; ?></div>
                                <div class="mp-stat-label">Helpful Votes</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['trust_score']; ?>%</div>
                                <div class="mp-stat-label">Trust Score</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo count(array_filter($user_reviews, fn($r) => $r['rating'] >= 4)); ?></div>
                                <div class="mp-stat-label">5-Star Reviews</div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <h3 style="margin-bottom: var(--mp-spacing-lg);">Recent Activity</h3>
                        <div class="mp-grid mp-grid-2">
                            <?php foreach (array_slice($user_reviews, 0, 4) as $review): ?>
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
                                <p class="mp-review-content"><?php echo esc_html($review['content']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- My Reviews Section -->
                    <section id="my-reviews" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">My Reviews</h2>
                            <a href="<?php echo esc_url($company_url); ?>/write-review" class="mp-btn mp-btn-primary">
                                Write New Review
                            </a>
                        </div>

                        <div class="mp-grid">
                            <?php foreach ($user_reviews as $review): ?>
                            <div class="mp-card">
                                <div class="mp-card-header">
                                    <div>
                                        <h4 class="mp-card-title"><?php echo esc_html($review['title']); ?></h4>
                                        <p style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); margin: 0;">
                                            <?php echo esc_html($review['business']); ?> • <?php echo esc_html($review['date']); ?>
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
                                    <button class="mp-btn mp-btn-sm mp-btn-ghost">Edit</button>
                                    <button class="mp-btn mp-btn-sm mp-btn-ghost">Delete</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Bookmarks Section -->
                    <section id="bookmarks" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Saved Businesses</h2>
                        </div>

                        <p style="color: var(--mp-gray-500);">You haven't saved any businesses yet.</p>
                        <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-btn mp-btn-primary">
                            Browse Businesses
                        </a>
                    </section>

                    <!-- Settings Section -->
                    <section id="settings" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Account Settings</h2>
                        </div>

                        <form>
                            <div class="mp-grid mp-grid-2" style="margin-bottom: var(--mp-spacing-xl);">
                                <div class="mp-form-group">
                                    <label class="mp-form-label">First Name</label>
                                    <input type="text" class="mp-form-input" value="<?php echo esc_attr($current_user->first_name); ?>">
                                </div>
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Last Name</label>
                                    <input type="text" class="mp-form-input" value="<?php echo esc_attr($current_user->last_name); ?>">
                                </div>
                            </div>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Email Address</label>
                                <input type="email" class="mp-form-input" value="<?php echo esc_attr($current_user->user_email); ?>" readonly>
                                <small style="color: var(--mp-gray-500); font-size: var(--mp-font-size-xs); margin-top: 4px; display: block;">
                                    Contact support to change your email address
                                </small>
                            </div>

                            <h3 style="margin: var(--mp-spacing-xl) 0 var(--mp-spacing-lg);">Change Password</h3>

                            <div class="mp-form-group">
                                <label class="mp-form-label">Current Password</label>
                                <input type="password" class="mp-form-input" placeholder="Enter current password">
                            </div>

                            <div class="mp-grid mp-grid-2">
                                <div class="mp-form-group">
                                    <label class="mp-form-label">New Password</label>
                                    <input type="password" class="mp-form-input" placeholder="Enter new password">
                                </div>
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Confirm New Password</label>
                                    <input type="password" class="mp-form-input" placeholder="Confirm new password">
                                </div>
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