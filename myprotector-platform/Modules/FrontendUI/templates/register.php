<?php
/**
 * MyProtector Platform - Register Page Template
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$login_url = $company_url . '/login';
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
            <h1 class="mp-auth-title">Create Account</h1>
            <p class="mp-auth-subtitle">Join MyProtector to write reviews and build trust</p>
        </div>

        <!-- Register Form -->
        <form id="mp-register-form" class="mp-auth-form">
            <input type="hidden" name="action" value="mp_ajax_register">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">
            <input type="hidden" name="redirect" value="<?php echo esc_attr($_GET['redirect'] ?? ''); ?>">

            <!-- Account Type Selection -->
            <div class="mp-account-type-selector">
                <label class="mp-account-type <?php echo (isset($_GET['type']) && $_GET['type'] === 'business') ? '' : 'active'; ?>">
                    <input type="radio" name="user_type" value="individual" <?php echo (isset($_GET['type']) && $_GET['type'] === 'business') ? '' : 'checked'; ?>>
                    <div class="mp-account-type-card">
                        <span class="mp-account-icon">👤</span>
                        <span class="mp-account-label">Individual</span>
                        <span class="mp-account-desc">Write reviews as a consumer</span>
                    </div>
                </label>
                <label class="mp-account-type <?php echo (isset($_GET['type']) && $_GET['type'] === 'business') ? 'active' : ''; ?>">
                    <input type="radio" name="user_type" value="business" <?php echo (isset($_GET['type']) && $_GET['type'] === 'business') ? 'checked' : ''; ?>>
                    <div class="mp-account-type-card">
                        <span class="mp-account-icon">🏢</span>
                        <span class="mp-account-label">Business</span>
                        <span class="mp-account-desc">Manage your business profile</span>
                    </div>
                </label>
            </div>

            <div class="mp-form-row-2">
                <div class="mp-form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                        placeholder="First name" autocomplete="given-name">
                </div>
                <div class="mp-form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                        placeholder="Last name" autocomplete="family-name">
                </div>
            </div>

            <div class="mp-form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                    placeholder="Enter your email" autocomplete="email">
            </div>

            <div class="mp-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                    placeholder="Create a password (min 8 characters)" autocomplete="new-password">
                <div class="mp-password-strength" id="mp-password-strength">
                    <div class="mp-strength-bar"></div>
                    <span class="mp-strength-text"></span>
                </div>
            </div>

            <div class="mp-form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                    placeholder="Confirm your password" autocomplete="new-password">
            </div>

            <div class="mp-terms">
                <label class="mp-checkbox">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the <a href="<?php echo esc_url($company_url); ?>/terms" target="_blank">Terms of Service</a> and <a href="<?php echo esc_url($company_url); ?>/privacy" target="_blank">Privacy Policy</a></span>
                </label>
            </div>

            <div class="mp-form-message" id="mp-register-message" style="display: none;"></div>

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
            Sign In to Existing Account
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
    max-width: 480px;
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

.mp-account-type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--mp-spacing-md);
    margin-bottom: var(--mp-spacing-lg);
}

.mp-account-type {
    cursor: pointer;
}

.mp-account-type input {
    display: none;
}

.mp-account-type-card {
    border: 2px solid var(--mp-gray-200);
    border-radius: var(--mp-radius-lg);
    padding: var(--mp-spacing-md);
    text-align: center;
    transition: all 0.2s;
}

.mp-account-type input:checked + .mp-account-type-card {
    border-color: var(--mp-primary);
    background: var(--mp-primary-light);
}

.mp-account-type:hover .mp-account-type-card {
    border-color: var(--mp-primary);
}

.mp-account-icon {
    display: block;
    font-size: 32px;
    margin-bottom: var(--mp-spacing-xs);
}

.mp-account-label {
    display: block;
    font-weight: 600;
    color: var(--mp-dark-navy);
    margin-bottom: 4px;
}

.mp-account-desc {
    display: block;
    font-size: 12px;
    color: var(--mp-gray-500);
}

.mp-auth-form {
    margin-bottom: var(--mp-spacing-lg);
}

.mp-form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--mp-spacing-md);
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

.mp-password-strength {
    margin-top: var(--mp-spacing-xs);
}

.mp-strength-bar {
    height: 4px;
    background: var(--mp-gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.mp-strength-bar::after {
    content: '';
    display: block;
    height: 100%;
    width: var(--strength, 0%);
    background: var(--mp-gray-300);
    transition: width 0.3s, background 0.3s;
}

.mp-strength-text {
    font-size: 12px;
    color: var(--mp-gray-500);
}

.mp-terms {
    margin-bottom: var(--mp-spacing-md);
}

.mp-terms .mp-checkbox {
    display: flex;
    align-items: flex-start;
    gap: var(--mp-spacing-sm);
    cursor: pointer;
    font-size: 13px;
    color: var(--mp-gray-600);
    line-height: 1.4;
}

.mp-terms .mp-checkbox input {
    margin-top: 3px;
    width: 16px;
    height: 16px;
}

.mp-terms .mp-checkbox a {
    color: var(--mp-primary);
    text-decoration: none;
}

.mp-terms .mp-checkbox a:hover {
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

@media (max-width: 480px) {
    .mp-form-row-2 {
        grid-template-columns: 1fr;
    }
    
    .mp-account-type-selector {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Password strength checker
    $('#password').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        var text = '';
        var color = 'var(--mp-gray-300)';
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/)) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/) || password.match(/[^a-zA-Z0-9]/)) strength += 25;
        
        if (strength <= 25) {
            text = 'Weak';
            color = 'var(--mp-red)';
        } else if (strength <= 50) {
            text = 'Fair';
            color = 'var(--mp-amber)';
        } else if (strength <= 75) {
            text = 'Good';
            color = 'var(--mp-green)';
        } else {
            text = 'Strong';
            color = 'var(--mp-green)';
        }
        
        $('#mp-password-strength .mp-strength-bar').css({
            '--strength': strength + '%',
            'background': color
        });
        $('#mp-password-strength .mp-strength-text').text(text).css('color', color);
    });

    // Register form submission
    $('#mp-register-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#mp-register-btn');
        var $message = $('#mp-register-message');
        
        // Validate passwords match
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        
        if (password !== confirm_password) {
            $message.addClass('error').removeClass('success').html('Passwords do not match.').show();
            return;
        }
        
        // Validate password length
        if (password.length < 8) {
            $message.addClass('error').removeClass('success').html('Password must be at least 8 characters.').show();
            return;
        }
        
        $btn.prop('disabled', true).text('Creating account...');
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
                    $btn.prop('disabled', false).text('Create Account');
                }
            },
            error: function() {
                $message.addClass('error').removeClass('success').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Create Account');
            }
        });
    });

    // Account type toggle styling
    $('input[name="user_type"]').on('change', function() {
        $('.mp-account-type').removeClass('active');
        $(this).closest('.mp-account-type').addClass('active');
    });
});
</script>