<?php
/**
 * MyProtector Platform - About Us Template
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_name = defined('MYPROTECTOR_COMPANY_NAME') ? MYPROTECTOR_COMPANY_NAME : 'MyProtector LLC';
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$company_email = defined('MYPROTECTOR_COMPANY_EMAIL') ? MYPROTECTOR_COMPANY_EMAIL : 'contact@myprotector.com';
$company_address = defined('MYPROTECTOR_COMPANY_ADDRESS') ? MYPROTECTOR_COMPANY_ADDRESS : '';
$company_phone = defined('MYPROTECTOR_COMPANY_PHONE') ? MYPROTECTOR_COMPANY_PHONE : '';

$founder_name = defined('MYPROTECTOR_FOUNDER_NAME') ? MYPROTECTOR_FOUNDER_NAME : 'Adam Wyrzycki';
$founder_title = defined('MYPROTECTOR_FOUNDER_TITLE') ? MYPROTECTOR_FOUNDER_TITLE : 'Co-Founder & Lead Developer';
$founder_linkedin = defined('MYPROTECTOR_FOUNDER_LINKEDIN') ? MYPROTECTOR_FOUNDER_LINKEDIN : 'https://linkedin.com/in/adamwyrzycki';
$founder_email = defined('MYPROTECTOR_FOUNDER_EMAIL') ? MYPROTECTOR_FOUNDER_EMAIL : 'adam@myprotector.com';

$cofounder_name = defined('MYPROTECTOR_COFOUNDER_NAME') ? MYPROTECTOR_COFOUNDER_NAME : 'Co-Founder';
$cofounder_title = defined('MYPROTECTOR_COFOUNDER_TITLE') ? MYPROTECTOR_COFOUNDER_TITLE : 'Co-Founder & CEO';
$cofounder_linkedin = defined('MYPROTECTOR_COFOUNDER_LINKEDIN') ? MYPROTECTOR_COFOUNDER_LINKEDIN : 'https://linkedin.com/in/cofounder';

$linkedin_url = defined('MYPROTECTOR_SOCIAL_LINKEDIN') ? MYPROTECTOR_SOCIAL_LINKEDIN : 'https://linkedin.com/company/myprotector';
$twitter_url = defined('MYPROTECTOR_SOCIAL_TWITTER') ? MYPROTECTOR_SOCIAL_TWITTER : 'https://twitter.com/myprotector';
$instagram_url = defined('MYPROTECTOR_SOCIAL_INSTAGRAM') ? MYPROTECTOR_SOCIAL_INSTAGRAM : 'https://instagram.com/myprotector';
?>

<div class="mp-page mp-about-page">
    <!-- Hero Section -->
    <section class="mp-page-hero mp-page-hero-sm" style="background: linear-gradient(135deg, var(--mp-primary) 0%, #1a365d 100%);">
        <div class="mp-container mp-text-center">
            <h1 style="color: #fff; margin-bottom: var(--mp-spacing-md);">About <?php echo esc_html($company_name); ?></h1>
            <p style="color: rgba(255,255,255,0.9); font-size: var(--mp-font-size-xl); max-width: 700px; margin: 0 auto;">
                Building trust between businesses and consumers through transparent verification and authentic reviews.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mp-section" style="padding: var(--mp-spacing-4xl) 0;">
        <div class="mp-container">
            <div class="mp-grid" style="grid-template-columns: 1fr 1fr; gap: var(--mp-spacing-3xl); align-items: center;">
                <div>
                    <h2 style="margin-bottom: var(--mp-spacing-lg);">Our Mission</h2>
                    <p style="font-size: var(--mp-font-size-lg); color: var(--mp-gray-700); margin-bottom: var(--mp-spacing-lg);">
                        We believe everyone deserves to know exactly who they're dealing with before making a purchase or signing a contract. 
                        MyProtector provides the transparency needed in today's digital marketplace.
                    </p>
                    <p style="color: var(--mp-gray-600);">
                        Our Traffic Light Trust System gives businesses a clear path to demonstrate their commitment to customer trust, 
                        while giving consumers the information they need to make confident decisions.
                    </p>
                </div>
                <div class="mp-card" style="padding: var(--mp-spacing-xl);">
                    <div class="mp-trust-system-preview">
                        <div style="display: flex; align-items: center; gap: var(--mp-spacing-md); margin-bottom: var(--mp-spacing-lg);">
                            <div class="mp-trust-light mp-trust-light-green" style="width: 60px; height: 60px;">
                                <span style="font-size: 24px;">🛒</span>
                            </div>
                            <div>
                                <strong style="color: var(--mp-green);">Shopping Safe</strong>
                                <p style="margin: 0; font-size: var(--mp-font-size-sm); color: var(--mp-gray-600);">Fully verified businesses</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--mp-spacing-md); margin-bottom: var(--mp-spacing-lg);">
                            <div class="mp-trust-light mp-trust-light-amber" style="width: 60px; height: 60px;">
                                <span style="font-size: 24px;">🚶</span>
                            </div>
                            <div>
                                <strong style="color: var(--mp-amber);">Walking Safe</strong>
                                <p style="margin: 0; font-size: var(--mp-font-size-sm); color: var(--mp-gray-600);">Partially verified</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--mp-spacing-md);">
                            <div class="mp-trust-light mp-trust-light-red" style="width: 60px; height: 60px;">
                                <span style="font-size: 24px;">⚠️</span>
                            </div>
                            <div>
                                <strong style="color: var(--mp-red);">Caution</strong>
                                <p style="margin: 0; font-size: var(--mp-font-size-sm); color: var(--mp-gray-600);">Proceed with care</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Founders Section -->
    <section class="mp-section mp-section-alt" style="padding: var(--mp-spacing-4xl) 0;">
        <div class="mp-container">
            <div class="mp-text-center" style="margin-bottom: var(--mp-spacing-3xl);">
                <h2 style="margin-bottom: var(--mp-spacing-md);">Meet Our Team</h2>
                <p style="color: var(--mp-gray-600); max-width: 600px; margin: 0 auto;">
                    Dedicated to transforming how businesses and consumers build trust online.
                </p>
            </div>
            
            <div class="mp-grid mp-grid-2" style="max-width: 900px; margin: 0 auto;">
                <!-- Founder Card -->
                <div class="mp-card mp-card-hover" style="padding: var(--mp-spacing-xl); text-align: center;">
                    <div class="mp-founder-avatar" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--mp-primary) 0%, #1a365d 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--mp-spacing-lg);">
                        <span style="font-size: 48px; color: #fff; font-weight: bold;">
                            <?php echo esc_html(substr($founder_name, 0, 1)); ?>
                        </span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-xs);"><?php echo esc_html($founder_name); ?></h3>
                    <p style="color: var(--mp-primary); font-weight: 600; margin-bottom: var(--mp-spacing-md);">
                        <?php echo esc_html($founder_title); ?>
                    </p>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-lg);">
                        Lead Developer driving the technical vision and implementation of the MyProtector platform. 
                        Passionate about building tools that help businesses and consumers connect with confidence.
                    </p>
                    <a href="<?php echo esc_url($founder_linkedin); ?>" target="_blank" class="mp-btn mp-btn-outline mp-btn-sm">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px;">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn Profile
                    </a>
                    <a href="mailto:<?php echo esc_attr($founder_email); ?>" class="mp-btn mp-btn-ghost mp-btn-sm" style="margin-left: var(--mp-spacing-sm);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Email
                    </a>
                </div>
                
                <!-- Co-Founder Card -->
                <div class="mp-card mp-card-hover" style="padding: var(--mp-spacing-xl); text-align: center;">
                    <div class="mp-founder-avatar" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--mp-green) 0%, #065f46 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--mp-spacing-lg);">
                        <span style="font-size: 48px; color: #fff; font-weight: bold;">
                            <?php echo esc_html(substr($cofounder_name, 0, 1)); ?>
                        </span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-xs);"><?php echo esc_html($cofounder_name); ?></h3>
                    <p style="color: var(--mp-green); font-weight: 600; margin-bottom: var(--mp-spacing-md);">
                        <?php echo esc_html($cofounder_title); ?>
                    </p>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm); margin-bottom: var(--mp-spacing-lg);">
                        Leading business strategy and growth initiatives. Committed to creating 
                        a marketplace where trust is the foundation of every business relationship.
                    </p>
                    <a href="<?php echo esc_url($cofounder_linkedin); ?>" target="_blank" class="mp-btn mp-btn-outline mp-btn-sm">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px;">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn Profile
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="mp-section" style="padding: var(--mp-spacing-4xl) 0;">
        <div class="mp-container">
            <div class="mp-text-center" style="margin-bottom: var(--mp-spacing-3xl);">
                <h2 style="margin-bottom: var(--mp-spacing-md);">Our Values</h2>
            </div>
            
            <div class="mp-grid mp-grid-3">
                <div class="mp-card" style="padding: var(--mp-spacing-xl);">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-light); border-radius: var(--mp-radius-lg); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🔍</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Transparency</h3>
                    <p style="color: var(--mp-gray-600);">
                        We believe in complete transparency. Every trust signal, verification status, 
                        and review is displayed openly so consumers can make informed decisions.
                    </p>
                </div>
                
                <div class="mp-card" style="padding: var(--mp-spacing-xl);">
                    <div style="width: 60px; height: 60px; background: var(--mp-green-bg); border-radius: var(--mp-radius-lg); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🛡️</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Trust</h3>
                    <p style="color: var(--mp-gray-600);">
                        Trust is earned, not given. We help businesses demonstrate their commitment 
                        to customer satisfaction through verified trust signals.
                    </p>
                </div>
                
                <div class="mp-card" style="padding: var(--mp-spacing-xl);">
                    <div style="width: 60px; height: 60px; background: var(--mp-amber-bg); border-radius: var(--mp-radius-lg); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">✅</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Verification</h3>
                    <p style="color: var(--mp-gray-600);">
                        We verify insurance, terms, and promises so you know exactly who you're 
                        dealing with before you commit to any transaction.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="mp-section mp-section-dark" style="padding: var(--mp-spacing-4xl) 0;">
        <div class="mp-container">
            <div class="mp-grid" style="grid-template-columns: 1fr 1fr; gap: var(--mp-spacing-3xl);">
                <div>
                    <h2 style="margin-bottom: var(--mp-spacing-lg); color: #fff;">Get In Touch</h2>
                    <p style="color: var(--mp-gray-300); margin-bottom: var(--mp-spacing-xl);">
                        Have questions about MyProtector? Want to partner with us? We'd love to hear from you.
                    </p>
                    
                    <div class="mp-contact-info">
                        <div style="display: flex; align-items: flex-start; gap: var(--mp-spacing-md); margin-bottom: var(--mp-spacing-lg);">
                            <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span>📍</span>
                            </div>
                            <div>
                                <strong style="color: #fff; display: block; margin-bottom: 4px;">Address</strong>
                                <span style="color: var(--mp-gray-300);"><?php echo esc_html($company_address); ?></span>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: var(--mp-spacing-md); margin-bottom: var(--mp-spacing-lg);">
                            <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span>📧</span>
                            </div>
                            <div>
                                <strong style="color: #fff; display: block; margin-bottom: 4px;">Email</strong>
                                <a href="mailto:<?php echo esc_attr($company_email); ?>" style="color: var(--mp-gray-300);"><?php echo esc_html($company_email); ?></a>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: var(--mp-spacing-md);">
                            <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span>📞</span>
                            </div>
                            <div>
                                <strong style="color: #fff; display: block; margin-bottom: 4px;">Phone</strong>
                                <span style="color: var(--mp-gray-300);"><?php echo esc_html($company_phone); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 style="color: #fff; margin-bottom: var(--mp-spacing-lg);">Connect With Us</h3>
                    <div style="display: flex; gap: var(--mp-spacing-md); margin-bottom: var(--mp-spacing-xl);">
                        <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; color: #fff; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; color: #fff; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: var(--mp-radius-md); display: flex; align-items: center; justify-content: center; color: #fff; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                    
                    <div class="mp-card" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <h4 style="color: #fff; margin-bottom: var(--mp-spacing-md);">Contact Form</h4>
                        <form action="#" method="POST" class="mp-form">
                            <div style="margin-bottom: var(--mp-spacing-md);">
                                <input type="text" name="name" placeholder="Your Name" required 
                                    style="width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--mp-radius-md); color: #fff; font-size: 14px;">
                            </div>
                            <div style="margin-bottom: var(--mp-spacing-md);">
                                <input type="email" name="email" placeholder="Your Email" required 
                                    style="width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--mp-radius-md); color: #fff; font-size: 14px;">
                            </div>
                            <div style="margin-bottom: var(--mp-spacing-md);">
                                <textarea name="message" rows="4" placeholder="Your Message" required 
                                    style="width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--mp-radius-md); color: #fff; font-size: 14px; resize: vertical;"></textarea>
                            </div>
                            <button type="submit" class="mp-btn mp-btn-primary" style="width: 100%;">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mp-footer">
        <div class="mp-container">
            <div class="mp-footer-bottom">
                <p class="mp-footer-copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. All rights reserved.
                    <span class="mp-footer-audit"> | Page last updated: <?php echo date('F j, Y g:i A'); ?></span>
                </p>
            </div>
        </div>
    </footer>
</div>

<style>
.mp-about-page .mp-page-hero {
    padding: var(--mp-spacing-4xl) 0;
}
.mp-about-page .mp-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
}
.mp-about-page input::placeholder,
.mp-about-page textarea::placeholder {
    color: rgba(255,255,255,0.5);
}
</style>