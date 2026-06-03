<?php
/**
 * MyProtector Platform - Header Component
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$support_url = defined('MYPROTECTOR_SUPPORT_URL') ? MYPROTECTOR_SUPPORT_URL : home_url('/support');
$dashboard_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/dashboard' : home_url('/dashboard');
$about_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/about' : home_url('/about');
?>

<header class="mp-header">
    <div class="mp-container">
        <div class="mp-header-inner">
            <a href="<?php echo esc_url($company_url); ?>" class="mp-logo">
                <div class="mp-logo-icon">MP</div>
                <div class="mp-logo-text">My<span>Protector</span></div>
            </a>
            
            <nav class="mp-nav">
                <a href="<?php echo esc_url($company_url); ?>" class="mp-nav-link">Home</a>
                <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-nav-link">Businesses</a>
                <a href="<?php echo esc_url($company_url); ?>/how-it-works" class="mp-nav-link">How It Works</a>
                <a href="<?php echo esc_url($dashboard_url); ?>" class="mp-nav-link">Dashboard</a>
            </nav>
            
            <div class="mp-header-actions">
                <a href="<?php echo esc_url($company_url); ?>/login" class="mp-btn mp-btn-ghost">Log In</a>
                <a href="<?php echo esc_url($company_url); ?>/register" class="mp-btn mp-btn-primary">Sign Up</a>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mp-btn mp-btn-icon mp-btn-ghost mp-mobile-menu-toggle" style="display: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>
