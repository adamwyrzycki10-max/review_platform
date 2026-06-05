<?php
/**
 * MyProtector Platform - Header Component
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$support_url = defined('MYPROTECTOR_SUPPORT_URL') ? MYPROTECTOR_SUPPORT_URL : home_url('/support');
$dashboard_url = $company_url . '/dashboard';
$login_url = $company_url . '/login';
$register_url = $company_url . '/register';
$logout_url = wp_logout_url($company_url);

// Get current user info
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
?>

<header class="mp-header">
    <div class="mp-container">
        <div class="mp-header-inner">
            <a href="<?php echo esc_url($company_url); ?>" class="mp-logo">
                <div class="mp-logo-icon">MP</div>
                <div class="mp-logo-text">My<span>Protector</span></div>
            </a>
            
            <nav class="mp-nav">
                <a href="<?php echo esc_url($company_url); ?>" class="mp-nav-link <?php echo is_front_page() ? 'active' : ''; ?>">Home</a>
                <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-nav-link">Businesses</a>
                <a href="<?php echo esc_url($company_url); ?>/about" class="mp-nav-link">About</a>
                <?php if ($is_logged_in): ?>
                <a href="<?php echo esc_url($dashboard_url); ?>" class="mp-nav-link">Dashboard</a>
                <?php endif; ?>
            </nav>
            
            <div class="mp-header-actions">
                <?php if ($is_logged_in): ?>
                <div class="mp-user-menu">
                    <span class="mp-user-name"><?php echo esc_html($current_user->display_name ?: $current_user->user_email); ?></span>
                    <a href="<?php echo esc_url($logout_url); ?>" class="mp-btn mp-btn-ghost">Log Out</a>
                </div>
                <?php else: ?>
                <a href="<?php echo esc_url($login_url); ?>" class="mp-btn mp-btn-ghost">Log In</a>
                <a href="<?php echo esc_url($register_url); ?>" class="mp-btn mp-btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mp-btn mp-btn-icon mp-btn-ghost mp-mobile-menu-toggle" aria-label="Toggle menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>
