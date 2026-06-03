<?php
/**
 * MyProtector Platform - Login Page Template
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$register_url = $company_url . '/register';
$logo_url = $company_url;
?>

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
            <input type="hidden" name="redirect" value="<?php echo esc_attr($_GET['redirect'] ?? ''); ?>">

            <div class="mp-form-group">
                <label for="username">Email or Username</label>
                <input type="text" id="username" name="username" required 
                    placeholder="Enter your email or username" autocomplete="username">
            </div>

            <div class="mp-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                    placeholder="Enter your password" autocomplete="current-password">
            </div>

            <div class="mp-form-row mp-flex mp-items-center mp-justify-between">
                <label class="mp-checkbox">
                    <input type="checkbox" name="remember" value="true">
                    <span>Remember me</span>
                </label>
                <a href="#" class="mp-forgot-link" id="mp-show-lost-password">Forgot password?</a>
            </div>

            <div class="mp-form-message" id="mp-login-message" style="display: none;"></div>

            <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-login-btn">
                Sign In
            </button>
        </form>

        <!-- Lost Password Form (Hidden by default) -->
        <form id="mp-lost-password-form" class="mp-auth-form" style="display: none;">
            <input type="hidden" name="action" value="mp_ajax_lost_password">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">

            <p class="mp-lost-password-info">Enter your email address and we'll send you a link to reset your password.</p>

            <div class="mp-form-group">
                <label for="lost-email">Email Address</label>
                <input type="email" id="lost-email" name="email" required 
                    placeholder="Enter your email address">
            </div>

            <div class="mp-form-message" id="mp-lost-password-message" style="display: none;"></div>

            <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-lost-password-btn">
                Send Reset Link
            </button>

            <div class="mp-auth-links">
                <a href="#" id="mp-show-login">← Back to login</a>
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

<style>
.mp-auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--mp-primary) 0%, #1a365d 100%);
    padding: var(--mp-spacing-xl);
}

.mp-auth-container {
    background: #fff;
    border-radius: var(--mp-radius-xl);
    padding: var(--mp-spacing-2xl);
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.mp-auth-header {
    text-align: center;
    margin-bottom: var(--mp-spacing-xl);
}

.mp-auth-header .mp-logo {
    display: inline-flex;
    align-items: center;
    gap: var(--mp-spacing-sm);
    margin-bottom: var(--mp-spacing-lg);
    text-decoration: none;
}

.mp-auth-header .mp-logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--mp-primary) 0%, var(--mp-primary-dark) 100%);
    border-radius: var(--mp-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}

.mp-auth-header .mp-logo-text {
    font-size: 24px;
    font-weight: 700;
    color: var(--mp-dark-navy);
}

.mp-auth-header .mp-logo-text span {
    color: var(--mp-primary);
}

.mp-auth-title {
    margin: 0 0 var(--mp-spacing-xs);
    color: var(--mp-dark-navy);
}

.mp-auth-subtitle {
    color: var(--mp-gray-500);
    margin: 0;
}

.mp-auth-form {
    margin-bottom: var(--mp-spacing-lg);
}

.mp-form-group {
    margin-bottom: var(--mp-spacing-md);
}

.mp-form-group label {
    display: block;
    margin-bottom: var(--mp-spacing-xs);
    font-weight: 500;
    color: var(--mp-gray-700);
}

.mp-form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--mp-gray-200);
    border-radius: var(--mp-radius-md);
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.mp-form-group input:focus {
    outline: none;
    border-color: var(--mp-primary);
    box-shadow: 0 0 0 3px rgba(10, 31, 68, 0.1);
}

.mp-form-row {
    margin-bottom: var(--mp-spacing-md);
}

.mp-checkbox {
    display: flex;
    align-items: center;
    gap: var(--mp-spacing-xs);
    cursor: pointer;
    font-size: 14px;
    color: var(--mp-gray-600);
}

.mp-checkbox input {
    width: 16px;
    height: 16px;
}

.mp-forgot-link {
    color: var(--mp-primary);
    font-size: 14px;
    text-decoration: none;
}

.mp-forgot-link:hover {
    text-decoration: underline;
}

.mp-form-message {
    padding: var(--mp-spacing-md);
    border-radius: var(--mp-radius-md);
    margin-bottom: var(--mp-spacing-md);
    font-size: 14px;
}

.mp-form-message.success {
    background: var(--mp-green-bg);
    color: var(--mp-green);
    border: 1px solid var(--mp-green);
}

.mp-form-message.error {
    background: var(--mp-red-bg);
    color: var(--mp-red);
    border: 1px solid var(--mp-red);
}

.mp-auth-divider {
    text-align: center;
    margin: var(--mp-spacing-lg) 0;
    position: relative;
}

.mp-auth-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--mp-gray-200);
}

.mp-auth-divider span {
    background: #fff;
    padding: 0 var(--mp-spacing-md);
    color: var(--mp-gray-500);
    font-size: 14px;
    position: relative;
}

.mp-auth-links {
    text-align: center;
    margin-top: var(--mp-spacing-md);
}

.mp-auth-links a {
    color: var(--mp-primary);
    text-decoration: none;
    font-size: 14px;
}

.mp-auth-links a:hover {
    text-decoration: underline;
}

.mp-auth-footer {
    text-align: center;
    margin-top: var(--mp-spacing-lg);
    padding-top: var(--mp-spacing-lg);
    border-top: 1px solid var(--mp-gray-100);
}

.mp-auth-footer a {
    color: var(--mp-gray-500);
    text-decoration: none;
    font-size: 14px;
}

.mp-auth-footer a:hover {
    color: var(--mp-gray-700);
}

.mp-lost-password-info {
    color: var(--mp-gray-600);
    font-size: 14px;
    margin-bottom: var(--mp-spacing-lg);
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle between login and lost password forms
    $('#mp-show-lost-password').on('click', function(e) {
        e.preventDefault();
        $('#mp-login-form').hide();
        $('#mp-lost-password-form').show();
    });

    $('#mp-show-login').on('click', function(e) {
        e.preventDefault();
        $('#mp-lost-password-form').hide();
        $('#mp-login-form').show();
    });

    // Login form submission
    $('#mp-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#mp-login-btn');
        var $message = $('#mp-login-message');
        
        $btn.prop('disabled', true).text('Signing in...');
        $message.hide();
        
        $.ajax({
            url: mpFrontend.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    $message.addClass('success').removeClass('error').html(response.data.message).show();
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 500);
                } else {
                    $message.addClass('error').removeClass('success').html(response.data.message).show();
                    $btn.prop('disabled', false).text('Sign In');
                }
            },
            error: function() {
                $message.addClass('error').removeClass('success').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Sign In');
            }
        });
    });

    // Lost password form submission
    $('#mp-lost-password-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#mp-lost-password-btn');
        var $message = $('#mp-lost-password-message');
        
        $btn.prop('disabled', true).text('Sending...');
        $message.hide();
        
        $.ajax({
            url: mpFrontend.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                $message.addClass('success').removeClass('error').html(response.data.message).show();
                $btn.prop('disabled', false).text('Send Reset Link');
            },
            error: function() {
                $message.addClass('error').removeClass('success').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Send Reset Link');
            }
        });
    });
});
</script>