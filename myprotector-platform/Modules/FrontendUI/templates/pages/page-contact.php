<?php
/**
 * MyProtector Platform - Contact Page Template
 * 
 * Contact form with business/support information
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

get_header();

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$company_email = defined('MYPROTECTOR_COMPANY_EMAIL') ? MYPROTECTOR_COMPANY_EMAIL : 'contact@myprotector.com';
$support_email = defined('MYPROTECTOR_SUPPORT_EMAIL') ? MYPROTECTOR_SUPPORT_EMAIL : 'support@myprotector.com';
?>

<div class="mp-frontend-ui">
    <!-- Page Header -->
    <section class="mp-hero" style="padding: var(--mp-spacing-3xl) 0;">
        <div class="mp-container mp-hero-content">
            <h1>Contact Us</h1>
            <p class="mp-hero-subtitle">
                Have questions or need help? We're here for you. 
                Reach out and we'll get back to you as soon as possible.
            </p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-grid" style="max-width: 1000px; margin: 0 auto; gap: var(--mp-spacing-3xl);">
                <!-- Contact Form -->
                <div style="flex: 1.5;">
                    <div class="mp-card">
                        <h2 style="margin: 0 0 var(--mp-spacing-xl);">Send us a message</h2>
                        
                        <form id="mp-contact-form">
                            <div class="mp-form-group">
                                <label for="contact_name" class="mp-form-label">Your Name</label>
                                <input type="text" id="contact_name" name="name" class="mp-form-input" required 
                                       placeholder="Enter your full name">
                            </div>

                            <div class="mp-form-group">
                                <label for="contact_email" class="mp-form-label">Email Address</label>
                                <input type="email" id="contact_email" name="email" class="mp-form-input" required 
                                       placeholder="Enter your email address">
                            </div>

                            <div class="mp-form-group">
                                <label for="contact_subject" class="mp-form-label">Subject</label>
                                <select id="contact_subject" name="subject" class="mp-form-select" required>
                                    <option value="">Select a topic</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="business">Business Partnership</option>
                                    <option value="reseller">Reseller Program</option>
                                    <option value="feedback">Feedback & Suggestions</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mp-form-group">
                                <label for="contact_message" class="mp-form-label">Message</label>
                                <textarea id="contact_message" name="message" class="mp-form-textarea" rows="6" required 
                                          placeholder="How can we help you?"></textarea>
                            </div>

                            <div class="mp-message" id="mp-contact-message" style="display: none;"></div>

                            <button type="submit" class="mp-btn mp-btn-primary mp-btn-full" id="mp-contact-btn">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact Info -->
                <div style="flex: 1;">
                    <div class="mp-card" style="margin-bottom: var(--mp-spacing-lg);">
                        <h3 style="margin: 0 0 var(--mp-spacing-lg);">Contact Information</h3>
                        
                        <div style="margin-bottom: var(--mp-spacing-xl);">
                            <h4 style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-sm); text-transform: uppercase; letter-spacing: 0.05em;">General Inquiries</h4>
                            <a href="mailto:<?php echo esc_attr($company_email); ?>" style="font-size: var(--mp-font-size-lg); font-weight: 500;">
                                <?php echo esc_html($company_email); ?>
                            </a>
                        </div>

                        <div style="margin-bottom: var(--mp-spacing-xl);">
                            <h4 style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-sm); text-transform: uppercase; letter-spacing: 0.05em;">Support</h4>
                            <a href="mailto:<?php echo esc_attr($support_email); ?>" style="font-size: var(--mp-font-size-lg); font-weight: 500;">
                                <?php echo esc_html($support_email); ?>
                            </a>
                        </div>

                        <div>
                            <h4 style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-sm); text-transform: uppercase; letter-spacing: 0.05em;">Response Time</h4>
                            <p style="color: var(--mp-gray-600); margin: 0;">
                                We typically respond within 24-48 hours during business days.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Card -->
                    <div class="mp-card" style="background: var(--mp-gray-50); border: none;">
                        <h3 style="margin: 0 0 var(--mp-spacing-lg);">Quick Answers</h3>
                        
                        <div style="margin-bottom: var(--mp-spacing-lg);">
                            <h4 style="font-size: var(--mp-font-size-sm); color: var(--mp-dark-navy); margin-bottom: var(--mp-spacing-xs);">
                                How do I claim my business?
                            </h4>
                            <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin: 0;">
                                <a href="<?php echo esc_url($company_url); ?>/register?type=business">Register as a business</a> and follow the verification steps.
                            </p>
                        </div>

                        <div style="margin-bottom: var(--mp-spacing-lg);">
                            <h4 style="font-size: var(--mp-font-size-sm); color: var(--mp-dark-navy); margin-bottom: var(--mp-spacing-xs);">
                                How can I leave a review?
                            </h4>
                            <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin: 0;">
                                <a href="<?php echo esc_url($company_url); ?>/businesses">Browse businesses</a> and click "Write a Review" on any profile.
                            </p>
                        </div>

                        <div>
                            <h4 style="font-size: var(--mp-font-size-sm); color: var(--mp-dark-navy); margin-bottom: var(--mp-spacing-xs);">
                                How does the Trust System work?
                            </h4>
                            <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin: 0;">
                                <a href="<?php echo esc_url($company_url); ?>/about#trust-system">Learn about our Traffic Light Trust System</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
@media (max-width: 768px) {
    .mp-grid {
        display: block;
    }
}
</style>

<?php
get_footer();