<?php
/**
 * MyProtector Platform - Reseller Dashboard Template
 * 
 * Reseller dashboard with commission tracking, referral links, and performance
 * Requires WordPress authentication and reseller role
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Require authentication
if (!is_user_logged_in()) {
    $login_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/login' : home_url('/login');
    $redirect_to = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/reseller-dashboard' : home_url('/reseller-dashboard');
    wp_redirect(add_query_arg('redirect_to', urlencode($redirect_to), $login_url));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$logout_url = wp_logout_url($company_url);

// Mock reseller data
$reseller = [
    'name' => $current_user->display_name ?: 'Reseller',
    'tier' => 'Gold',
    'referral_code' => 'ADAM2024',
    'referral_url' => 'https://myprotector.com/ref/ADAM2024'
];

$stats = [
    'total_commission' => 2450.00,
    'pending_commission' => 350.00,
    'total_referrals' => 47,
    'active_subscriptions' => 23,
    'conversion_rate' => 8.5
];

$commissions = [
    ['date' => 'June 1, 2026', 'amount' => 150.00, 'type' => 'subscription', 'business' => 'TechVentures'],
    ['date' => 'May 28, 2026', 'amount' => 50.00, 'type' => 'subscription', 'business' => 'GreenLeaf'],
    ['date' => 'May 25, 2026', 'amount' => 150.00, 'type' => 'subscription', 'business' => 'HealthFirst'],
    ['date' => 'May 20, 2026', 'amount' => 100.00, 'type' => 'one-time', 'business' => 'Metro Auto'],
];

$tier_info = [
    'Bronze' => ['min_referrals' => 0, 'commission_rate' => 15, 'color' => '#cd7f32'],
    'Silver' => ['min_referrals' => 10, 'commission_rate' => 20, 'color' => '#c0c0c0'],
    'Gold' => ['min_referrals' => 25, 'commission_rate' => 25, 'color' => '#ffd700'],
    'Platinum' => ['min_referrals' => 50, 'commission_rate' => 30, 'color' => '#e5e4e2']
];
?>

<div class="mp-frontend-ui">
    <div class="mp-dashboard">
        <div class="mp-container">
            <div class="mp-dashboard-grid">
                <!-- Sidebar -->
                <aside class="mp-dashboard-sidebar">
                    <!-- Reseller Badge -->
                    <div class="mp-flex mp-flex-col mp-items-center mp-mb-lg" style="text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #ffd700, #ffed4a); color: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: var(--mp-spacing-md);">
                            <?php echo esc_html(substr($reseller['name'], 0, 2)); ?>
                        </div>
                        <h3 style="margin: 0 0 var(--mp-spacing-xs);"><?php echo esc_html($reseller['name']); ?></h3>
                        <span class="mp-trust-badge" style="background: linear-gradient(135deg, #ffd700, #ffed4a); color: #1a1a1a;">
                            ⭐ <?php echo esc_html($reseller['tier']); ?> Partner
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
                            <a href="#commissions" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Commissions
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#referrals" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                                Referrals
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#marketing" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                                Marketing
                            </a>
                        </div>
                        <div class="mp-dashboard-nav-item">
                            <a href="#training" class="mp-dashboard-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                Training
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
                            <h2 class="mp-dashboard-title">Reseller Dashboard</h2>
                            <span class="mp-trust-badge" style="background: linear-gradient(135deg, #ffd700, #ffed4a); color: #1a1a1a; padding: 6px 12px;">
                                ⭐ <?php echo esc_html($reseller['tier']); ?> Partner
                            </span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mp-grid mp-grid-4" style="margin-bottom: var(--mp-spacing-2xl);">
                            <div class="mp-stat-card">
                                <div class="mp-stat-value">$<?php echo number_format($stats['total_commission'], 2); ?></div>
                                <div class="mp-stat-label">Total Earnings</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value">$<?php echo number_format($stats['pending_commission'], 2); ?></div>
                                <div class="mp-stat-label">Pending</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['total_referrals']; ?></div>
                                <div class="mp-stat-label">Total Referrals</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['active_subscriptions']; ?></div>
                                <div class="mp-stat-label">Active Subs</div>
                            </div>
                        </div>

                        <!-- Tier Progress -->
                        <div class="mp-card" style="margin-bottom: var(--mp-spacing-xl);">
                            <h3 style="margin: 0 0 var(--mp-spacing-lg);">Partner Tier Progress</h3>
                            <div class="mp-flex mp-items-center mp-gap-xl">
                                <?php foreach ($tier_info as $tier => $info): ?>
                                <div class="mp-text-center" style="flex: 1;">
                                    <div style="width: 50px; height: 50px; border-radius: 50%; background: <?php echo $info['color']; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--mp-spacing-sm);">
                                        <?php if ($tier === $reseller['tier']): ?>
                                        <span style="font-size: 20px;">⭐</span>
                                        <?php else: ?>
                                        <span style="font-size: 20px; opacity: 0.5;">○</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="font-size: var(--mp-font-size-sm); font-weight: 600;"><?php echo esc_html($tier); ?></span>
                                    <p style="font-size: var(--mp-font-size-xs); color: var(--mp-gray-500); margin: var(--mp-spacing-xs) 0 0;">
                                        <?php echo $info['commission_rate']; ?>% commission
                                    </p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top: var(--mp-spacing-lg);">
                                <div class="mp-flex mp-items-center mp-justify-between" style="margin-bottom: var(--mp-spacing-sm);">
                                    <span style="font-size: var(--mp-font-size-sm); color: var(--mp-gray-500);">Next tier: Platinum</span>
                                    <span style="font-size: var(--mp-font-size-sm); color: var(--mp-gray-500);"><?php echo max(0, $tier_info['Platinum']['min_referrals'] - $stats['total_referrals']); ?> more referrals needed</span>
                                </div>
                                <div style="height: 8px; background: var(--mp-gray-200); border-radius: 4px; overflow: hidden;">
                                    <div style="height: 100%; width: <?php echo min(100, ($stats['total_referrals'] / $tier_info['Platinum']['min_referrals']) * 100); ?>%; background: linear-gradient(90deg, #ffd700, #ffed4a);"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Commissions -->
                        <h3 style="margin-bottom: var(--mp-spacing-lg);">Recent Commissions</h3>
                        <div class="mp-card">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--mp-gray-100);">
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Date</th>
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Business</th>
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Type</th>
                                        <th style="text-align: right; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commissions as $commission): ?>
                                    <tr style="border-bottom: 1px solid var(--mp-gray-100);">
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm);"><?php echo esc_html($commission['date']); ?></td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm); font-weight: 500;"><?php echo esc_html($commission['business']); ?></td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm);">
                                            <span class="mp-trust-badge mp-trust-badge-<?php echo $commission['type'] === 'subscription' ? 'green' : 'amber'; ?>" style="font-size: 10px;">
                                                <?php echo ucfirst($commission['type']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm); text-align: right; color: var(--mp-green); font-weight: 600;">
                                            +$<?php echo number_format($commission['amount'], 2); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Commissions Section -->
                    <section id="commissions" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Commission History</h2>
                            <div class="mp-flex mp-gap-sm">
                                <select class="mp-form-select" style="width: auto; padding: 0.5rem 1rem;">
                                    <option value="all">All Types</option>
                                    <option value="subscription">Subscriptions</option>
                                    <option value="one-time">One-time</option>
                                </select>
                                <select class="mp-form-select" style="width: auto; padding: 0.5rem 1rem;">
                                    <option value="30">Last 30 days</option>
                                    <option value="90">Last 90 days</option>
                                    <option value="365">Last year</option>
                                </select>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="mp-grid mp-grid-3" style="margin-bottom: var(--mp-spacing-xl);">
                            <div class="mp-stat-card">
                                <div class="mp-stat-value">$<?php echo number_format($stats['total_commission'], 2); ?></div>
                                <div class="mp-stat-label">Total Earned</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value">$<?php echo number_format($stats['pending_commission'], 2); ?></div>
                                <div class="mp-stat-label">Pending</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value">$<?php echo number_format($stats['total_commission'] - $stats['pending_commission'], 2); ?></div>
                                <div class="mp-stat-label">Paid Out</div>
                            </div>
                        </div>

                        <div class="mp-card">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--mp-gray-100);">
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Date</th>
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Business</th>
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Type</th>
                                        <th style="text-align: left; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Status</th>
                                        <th style="text-align: right; padding: var(--mp-spacing-sm); color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); font-weight: 500;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commissions as $commission): ?>
                                    <tr style="border-bottom: 1px solid var(--mp-gray-100);">
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm);"><?php echo esc_html($commission['date']); ?></td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm); font-weight: 500;"><?php echo esc_html($commission['business']); ?></td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm);"><?php echo ucfirst($commission['type']); ?></td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm);">
                                            <span class="mp-trust-badge mp-trust-badge-amber" style="font-size: 10px;">Pending</span>
                                        </td>
                                        <td style="padding: var(--mp-spacing-sm); font-size: var(--mp-font-size-sm); text-align: right; color: var(--mp-green); font-weight: 600;">
                                            +$<?php echo number_format($commission['amount'], 2); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Referrals Section -->
                    <section id="referrals" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Your Referrals</h2>
                            <a href="#" class="mp-btn mp-btn-primary">
                                Generate New Link
                            </a>
                        </div>

                        <!-- Referral Link -->
                        <div class="mp-card" style="margin-bottom: var(--mp-spacing-xl);">
                            <h3 style="margin: 0 0 var(--mp-spacing-md);">Your Referral Link</h3>
                            <div class="mp-flex mp-gap-sm">
                                <input type="text" class="mp-form-input" value="<?php echo esc_url($reseller['referral_url']); ?>" readonly style="flex: 1;">
                                <button class="mp-btn mp-btn-secondary" onclick="copyReferralLink()">Copy</button>
                            </div>
                            <p style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm); margin: var(--mp-spacing-sm) 0 0;">
                                Share this link to earn commission on every subscription!
                            </p>
                        </div>

                        <!-- Referral Stats -->
                        <div class="mp-grid mp-grid-2">
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['total_referrals']; ?></div>
                                <div class="mp-stat-label">Total Referrals</div>
                            </div>
                            <div class="mp-stat-card">
                                <div class="mp-stat-value"><?php echo $stats['conversion_rate']; ?>%</div>
                                <div class="mp-stat-label">Conversion Rate</div>
                            </div>
                        </div>
                    </section>

                    <!-- Marketing Section -->
                    <section id="marketing" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Marketing Resources</h2>
                        </div>

                        <div class="mp-grid mp-grid-2">
                            <div class="mp-card">
                                <h3 style="margin: 0 0 var(--mp-spacing-md);">Banners</h3>
                                <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                                    Download ready-to-use banners for your website or social media.
                                </p>
                                <div class="mp-flex mp-gap-sm">
                                    <button class="mp-btn mp-btn-outline">Download PNG</button>
                                    <button class="mp-btn mp-btn-outline">Download SVG</button>
                                </div>
                            </div>

                            <div class="mp-card">
                                <h3 style="margin: 0 0 var(--mp-spacing-md);">Email Templates</h3>
                                <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                                    Pre-written email templates you can use to promote MyProtector.
                                </p>
                                <button class="mp-btn mp-btn-outline">Download Templates</button>
                            </div>

                            <div class="mp-card">
                                <h3 style="margin: 0 0 var(--mp-spacing-md);">Social Media Kit</h3>
                                <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                                    Ready-to-share posts for Facebook, Twitter, and LinkedIn.
                                </p>
                                <button class="mp-btn mp-btn-outline">Download Kit</button>
                            </div>

                            <div class="mp-card">
                                <h3 style="margin: 0 0 var(--mp-spacing-md);">Video Tutorials</h3>
                                <p style="color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                                    Learn how to effectively promote MyProtector.
                                </p>
                                <button class="mp-btn mp-btn-outline">Watch Videos</button>
                            </div>
                        </div>
                    </section>

                    <!-- Training Section -->
                    <section id="training" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Training & Resources</h2>
                        </div>

                        <div class="mp-grid">
                            <div class="mp-card" style="border-left: 4px solid var(--mp-primary);">
                                <h4 style="margin: 0 0 var(--mp-spacing-sm);">Getting Started as a Reseller</h4>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Learn the basics of becoming a successful MyProtector reseller.
                                </p>
                                <span class="mp-trust-badge mp-trust-badge-green" style="font-size: 10px;">Beginner</span>
                            </div>

                            <div class="mp-card" style="border-left: 4px solid var(--mp-amber);">
                                <h4 style="margin: 0 0 var(--mp-spacing-sm);">Advanced Marketing Techniques</h4>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Master advanced strategies to maximize your referrals and earnings.
                                </p>
                                <span class="mp-trust-badge mp-trust-badge-amber" style="font-size: 10px;">Intermediate</span>
                            </div>

                            <div class="mp-card" style="border-left: 4px solid var(--mp-green);">
                                <h4 style="margin: 0 0 var(--mp-spacing-sm);">Partner Tier System Explained</h4>
                                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-md);">
                                    Understand how to climb the tiers and increase your commission rate.
                                </p>
                                <span class="mp-trust-badge mp-trust-badge-green" style="font-size: 10px;">All Levels</span>
                            </div>
                        </div>
                    </section>

                    <!-- Settings Section -->
                    <section id="settings" class="mp-dashboard-section" style="display: none;">
                        <div class="mp-dashboard-header">
                            <h2 class="mp-dashboard-title">Account Settings</h2>
                        </div>

                        <form>
                            <h3 style="margin-bottom: var(--mp-spacing-lg);">Payment Information</h3>

                            <div class="mp-grid mp-grid-2">
                                <div class="mp-form-group">
                                    <label class="mp-form-label">PayPal Email</label>
                                    <input type="email" class="mp-form-input" placeholder="your@email.com">
                                </div>
                                <div class="mp-form-group">
                                    <label class="mp-form-label">Bank Account (Optional)</label>
                                    <input type="text" class="mp-form-input" placeholder="Account details">
                                </div>
                            </div>

                            <h3 style="margin: var(--mp-spacing-xl) 0 var(--mp-spacing-lg);">Notification Preferences</h3>

                            <div style="margin-bottom: var(--mp-spacing-lg);">
                                <label class="mp-checkbox" style="margin-bottom: var(--mp-spacing-sm);">
                                    <input type="checkbox" checked>
                                    <span>Email me when I earn a commission</span>
                                </label>
                                <label class="mp-checkbox" style="margin-bottom: var(--mp-spacing-sm);">
                                    <input type="checkbox" checked>
                                    <span>Email me when a referral subscribes</span>
                                </label>
                                <label class="mp-checkbox">
                                    <input type="checkbox">
                                    <span>Email me with monthly performance reports</span>
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
    
    // Copy referral link function
    window.copyReferralLink = function() {
        const input = document.querySelector('input[readonly]');
        input.select();
        document.execCommand('copy');
        alert('Referral link copied to clipboard!');
    };
    
})(jQuery);
</script>

<?php
get_footer();