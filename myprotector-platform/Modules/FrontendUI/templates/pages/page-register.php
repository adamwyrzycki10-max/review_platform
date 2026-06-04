<?php
/**
 * MyProtector Platform - Register Page Template
 * 
 * User registration with individual/business options
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
$login_url = $company_url . '/login';
$logo_url = $company_url;
$user_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'individual';
?>

<div class="mp-frontend-ui">
    <div class="mp-auth-page">
        <div class="mp-auth-container" style="max-width: 480px;">
            <!-- Logo -->
            <div class="mp-auth-header">
                <a href="<?php echo esc_url($logo_url); ?>" class="mp-logo">
                    <div class="mp-logo-icon">MP</div>
                    <div class="mp-logo-text">My<span>Protector</span></div>
                </a>
                <h1 class="mp-auth-title">Create Your Account</h1>
                <p class="mp-auth-subtitle">Join thousands who trust MyProtector</p>
            </div>

            <!-- User Type Selection -->
            <div class="mp-flex mp-gap-sm" style="margin-bottom: var(--mp-spacing-xl);">
                <button type="button" class="mp-btn mp-btn-secondary mp-user-type-btn <?php echo $user_type === 'individual' ? 'active' : ''; ?>" 
                        data-type="individual" style="flex: 1;">
                    <span style="display: block; font-size: 20px; margin-bottom: 4px;">👤</span>
                    <span style="font-size: var(--mp-font-size-sm);">Individual</span>
                </button>
                <button type="button" class="mp-btn mp-btn-secondary mp-user-type-btn <?php echo $user_type === 'business' ? 'active' : ''; ?>" 
                        data-type="business" style="flex: 1;">
                    <span style="display: block; font-size: 20px; margin-bottom: 4px;">🏢</span>
                    <span style="font-size: var(--mp-font-size-sm);">Business</span>
                </button>
            </div>

            <!-- Register Form -->
            <form id="mp-register-form" class="mp-auth-form">
                <input type="hidden" name="action" value="mp_ajax_register">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">
                <input type="hidden" id="user_type" name="user_type" value="<?php echo esc_attr($user_type); ?>">

                <div class="mp-form-row" style="margin-bottom: var(--mp-spacing-md);">
                    <div class="mp-form-group" style="margin-bottom: 0;">
                        <label for="first_name" class="mp-form-label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="mp-form-input" 
                               placeholder="First name" autocomplete="given-name">
                    </div>
                    <div class="mp-form-group" style="margin-bottom: 0;">
                        <label for="last_name" class="mp-form-label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="mp-form-input" 
                               placeholder="Last name" autocomplete="family-name">
                    </div>
                </div>

                <div class="mp-form-group">
                    <label for="email" class="mp-form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="mp-form-input" required 
                           placeholder="Enter your email address" autocomplete="email">
                </div>

                <div class="mp-form-group">
                    <label for="username" class="mp-form-label">Username</label>
                    <input type="text" id="username" name="username" class="mp-form-input" 
                           placeholder="Choose a username" autocomplete="username">
                    <small style="color: var(--mp-gray-500); font-size: var(--mp-font-size-xs); margin-top: 4px; display: block;">
                        Leave blank to use your email prefix
                    </small>
                </div>

                <div class="mp-form-group">
                    <label for="password" class="mp-form-label">Password</label>
                    <input type="password" id="password" name="password" class="mp-form-input" required 
                           placeholder="Create a strong password" autocomplete="new-password">
                    <small style="color: var(--mp-gray-500); font-size: var(--mp-font-size-xs); margin-top: 4px; display: block;">
                        Must be at least 8 characters
                    </small>
                </div>

                <div class="mp-form-group">
                    <label for="confirm_password" class="mp-form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="mp-form-input" required 
                           placeholder="Confirm your password" autocomplete="new-password">
                </div>

                <!-- Business Fields (shown conditionally) -->
                <div class="mp-business-fields" style="<?php echo $user_type !== 'business' ? 'display: none;' : ''; ?>">
                    <div class="mp-form-group">
                        <label for="business_name" class="mp-form-label">Business Name</label>
                        <input type="text" id="business_name" name="business_name" class="mp-form-input" 
                               placeholder="Enter your business name">
                    </div>
                    <div class="mp-form-group">
                        <label for="business_website" class="mp-form-label">Business Website</label>
                        <input type="url" id="business_website" name="business_website" class="mp-form-input" 
                               placeholder="https://www.yourbusiness.com">
                    </div>
                </div>

                <!-- Terms -->
                <div style="margin-bottom: var(--mp-spacing-lg);">
                    <label class="mp-checkbox" style="font-size: var(--mp-font-size-sm); color: var(--mp-gray-600);">
                        <input type="checkbox" name="terms" required>
                        <span>I agree to the <a href="<?php echo esc_url($company_url); ?>/terms" target="_blank">Terms of Service</a> and <a href="<?php echo esc_url($company_url); ?>/privacy" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>

                <div class="mp-message" id="mp-register-message" style="display: none;"></div>

                <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-register-btn">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="mp-auth-divider">
                <span>Already have an account?</span>
            </div>

            <!-- Login Link -->
            <a href="<?php echo esc_url($login_url); ?>" class="mp-btn mp-btn-outline mp-btn-full">
                Sign In
            </a>

            <!-- Back to Home -->
            <div class="mp-auth-footer">
                <a href="<?php echo esc_url($company_url); ?>">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

<style>
.mp-user-type-btn.active {
    background: var(--mp-primary);
    color: #ffffff;
    border-color: var(--mp-primary);
}
</style>

<script>
(function($) {
    'use strict';
    
    // User type toggle
    $('.mp-user-type-btn').on('click', function() {
        const type = $(this).data('type');
        
        $('.mp-user-type-btn').removeClass('active');
        $(this).addClass('active');
        
        $('#user_type').val(type);
        
        // Show/hide business fields
        if (type === 'business') {
            $('.mp-business-fields').slideDown();
        } else {
            $('.mp-business-fields').slideUp();
        }
    });
    
})(jQuery);
</script>

<?php
get_footer();