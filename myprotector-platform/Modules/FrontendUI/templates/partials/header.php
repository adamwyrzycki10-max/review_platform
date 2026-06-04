<?php
/**
 * MyProtector Platform - Header Partial Component
 * 
 * Reusable navigation header component
 * Uses WordPress URLs for all navigation links
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Get configuration
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();

// WordPress URLs
$login_url = wp_login_url($company_url . '/dashboard');
$register_url = site_url('/register');
$dashboard_url = site_url('/dashboard');
$business_dashboard_url = site_url('/business-dashboard');
$reseller_dashboard_url = site_url('/reseller-dashboard');
$logout_url = wp_logout_url($company_url);

// Get current user
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();

// Get user type (for dashboard redirect)
$user_type = 'individual';
if ($is_logged_in) {
    $user_roles = $current_user->roles ?? [];
    if (in_array('mp_business', $user_roles)) {
        $user_type = 'business';
    } elseif (in_array('mp_reseller', $user_roles)) {
        $user_type = 'reseller';
    }
}

// Determine dashboard URL based on user type
$user_dashboard_url = $company_url;
switch ($user_type) {
    case 'business':
        $user_dashboard_url = $business_dashboard_url;
        break;
    case 'reseller':
        $user_dashboard_url = $reseller_dashboard_url;
        break;
    default:
        $user_dashboard_url = $dashboard_url;
}

// Current page for nav highlighting
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>

<header class="mp-header">
    <div class="mp-container">
        <div class="mp-header-inner">
            <!-- Logo -->
            <a href="<?php echo esc_url($company_url); ?>" class="mp-logo">
                <div class="mp-logo-icon">MP</div>
                <div class="mp-logo-text">My<span>Protector</span></div>
            </a>
            
            <!-- Navigation -->
            <nav class="mp-nav">
                <a href="<?php echo esc_url($company_url); ?>" 
                   class="mp-nav-link <?php echo $current_path === '/' ? 'active' : ''; ?>">
                    Home
                </a>
                <a href="<?php echo esc_url($company_url); ?>/businesses" 
                   class="mp-nav-link <?php echo strpos($current_path, '/businesses') !== false ? 'active' : ''; ?>">
                    Businesses
                </a>
                <a href="<?php echo esc_url($company_url); ?>/about" 
                   class="mp-nav-link <?php echo strpos($current_path, '/about') !== false ? 'active' : ''; ?>">
                    About
                </a>
                <a href="<?php echo esc_url($company_url); ?>/contact" 
                   class="mp-nav-link <?php echo strpos($current_path, '/contact') !== false ? 'active' : ''; ?>">
                    Contact
                </a>
            </nav>
            
            <!-- Header Actions -->
            <div class="mp-header-actions">
                <?php if ($is_logged_in): ?>
                    <!-- Logged In User Menu -->
                    <div class="mp-user-menu">
                        <span class="mp-user-name">
                            <?php echo esc_html($current_user->display_name ?: $current_user->user_email); ?>
                        </span>
                        <a href="<?php echo esc_url($user_dashboard_url); ?>" class="mp-btn mp-btn-ghost">
                            Dashboard
                        </a>
                        <a href="<?php echo esc_url($logout_url); ?>" class="mp-btn mp-btn-ghost">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Guest User Actions -->
                    <a href="<?php echo esc_url($login_url); ?>" class="mp-btn mp-btn-ghost">
                        Log In
                    </a>
                    <a href="<?php echo esc_url($register_url); ?>" class="mp-btn mp-btn-primary">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mp-btn mp-btn-icon mp-btn-ghost mp-mobile-menu-toggle" 
                    aria-label="Toggle navigation menu" 
                    aria-expanded="false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Mobile Navigation -->
    <div class="mp-mobile-nav" style="display: none;">
        <nav class="mp-container">
            <a href="<?php echo esc_url($company_url); ?>" class="mp-mobile-nav-link">Home</a>
            <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-mobile-nav-link">Businesses</a>
            <a href="<?php echo esc_url($company_url); ?>/about" class="mp-mobile-nav-link">About</a>
            <a href="<?php echo esc_url($company_url); ?>/contact" class="mp-mobile-nav-link">Contact</a>
            <div class="mp-mobile-nav-divider"></div>
            <?php if ($is_logged_in): ?>
                <a href="<?php echo esc_url($user_dashboard_url); ?>" class="mp-mobile-nav-link">Dashboard</a>
                <a href="<?php echo esc_url($logout_url); ?>" class="mp-mobile-nav-link">Logout</a>
            <?php else: ?>
                <a href="<?php echo esc_url($login_url); ?>" class="mp-mobile-nav-link">Log In</a>
                <a href="<?php echo esc_url($register_url); ?>" class="mp-mobile-nav-link mp-mobile-nav-cta">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<style>
/* Mobile Navigation Styles */
.mp-mobile-nav {
    background: #ffffff;
    border-top: 1px solid var(--mp-gray-100);
    padding: var(--mp-spacing-md) 0;
}

.mp-mobile-nav nav {
    display: flex;
    flex-direction: column;
}

.mp-mobile-nav-link {
    display: block;
    padding: var(--mp-spacing-sm) 0;
    color: var(--mp-gray-700);
    font-size: var(--mp-font-size-base);
    font-weight: 500;
    text-decoration: none;
    border-bottom: 1px solid var(--mp-gray-100);
}

.mp-mobile-nav-link:hover {
    color: var(--mp-primary);
}

.mp-mobile-nav-divider {
    height: 1px;
    background: var(--mp-gray-100);
    margin: var(--mp-spacing-sm) 0;
}

.mp-mobile-nav-cta {
    color: var(--mp-primary);
    font-weight: 600;
}

@media (min-width: 769px) {
    .mp-mobile-nav {
        display: none !important;
    }
}
</style>

<script>
(function($) {
    'use strict';
    
    // Mobile menu toggle
    $('.mp-mobile-menu-toggle').on('click', function() {
        const $nav = $('.mp-mobile-nav');
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        
        $nav.slideToggle();
        $(this).attr('aria-expanded', !isExpanded);
    });
    
})(jQuery);
</script>