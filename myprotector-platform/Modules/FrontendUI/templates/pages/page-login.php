<?php
/**
 * MyProtector Platform - Login Page Template
 * 
 * Redirects logged-in users to dashboard
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Redirect logged-in users to dashboard
if (is_user_logged_in()) {
    $redirect_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL . '/dashboard' : home_url('/dashboard');
    wp_redirect($redirect_url);
    exit;
}

get_header();

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$register_url = $company_url . '/register';
$logo_url = $company_url;
$redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : $company_url . '/dashboard';
?>

<div class="mp-frontend-ui">
    <div class="mp-auth-page">
        <div class="mp-auth-container">
            <!-- Logo -->
            <div class="mp-auth-header">
                <a href="<?php echo esc_url($logo_url); ?>" class="mp-logo">
                    <div class="mp-logo-icon">MP</div>
                    <div class="mp-logo-text">My<span>Protector</span></div>
                </a>
                <h1 class="mp-auth-title">Welcome Back</h1>
                <p class="mp-auth-subtitle">Sign in to your account to continue</p>
            </div>

            <!-- Login Form -->
            <form id="mp-login-form" class="mp-auth-form">
                <input type="hidden" name="action" value="mp_ajax_login">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">

                <div class="mp-form-group">
                    <label for="username" class="mp-form-label">Email or Username</label>
                    <input type="text" id="username" name="username" class="mp-form-input" required 
                           placeholder="Enter your email or username" autocomplete="username">
                </div>

                <div class="mp-form-group">
                    <label for="password" class="mp-form-label">Password</label>
                    <input type="password" id="password" name="password" class="mp-form-input" required 
                           placeholder="Enter your password" autocomplete="current-password">
                </div>

                <div class="mp-form-row mp-flex mp-items-center mp-justify-between" style="margin-bottom: var(--mp-spacing-lg);">
                    <label class="mp-checkbox">
                        <input type="checkbox" name="remember" value="true">
                        <span style="font-size: var(--mp-font-size-sm); color: var(--mp-gray-600);">Remember me</span>
                    </label>
                    <a href="#lost-password" class="mp-forgot-link" id="mp-show-lost-password" style="font-size: var(--mp-font-size-sm);">
                        Forgot password?
                    </a>
                </div>

                <div class="mp-message" id="mp-login-message" style="display: none;"></div>

                <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-login-btn">
                    Sign In
                </button>
            </form>

            <!-- Lost Password Form (Hidden by default) -->
            <form id="mp-lost-password-form" class="mp-auth-form" style="display: none;">
                <input type="hidden" name="action" value="mp_ajax_lost_password">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">

                <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-lg);">
                    Enter your email address and we'll send you a link to reset your password.
                </p>

                <div class="mp-form-group">
                    <label for="lost-email" class="mp-form-label">Email Address</label>
                    <input type="email" id="lost-email" name="email" class="mp-form-input" required 
                           placeholder="Enter your email address">
                </div>

                <div class="mp-message" id="mp-lost-password-message" style="display: none;"></div>

                <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-lost-password-btn">
                    Send Reset Link
                </button>

                <div class="mp-auth-links" style="margin-top: var(--mp-spacing-md);">
                    <a href="#login" id="mp-show-login">← Back to login</a>
                </div>
            </form>

            <!-- Divider -->
            <div class="mp-auth-divider">
                <span>Don't have an account?</span>
            </div>

            <!-- Register Link -->
            <a href="<?php echo esc_url($register_url); ?>" class="mp-btn mp-btn-outline mp-btn-full">
                Create an Account
            </a>

            <!-- Back to Home -->
            <div class="mp-auth-footer">
                <a href="<?php echo esc_url($company_url); ?>">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();