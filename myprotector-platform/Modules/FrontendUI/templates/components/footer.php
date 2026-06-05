<?php
/**
 * MyProtector Platform - Footer Component
 * 
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$company_name = defined('MYPROTECTOR_COMPANY_NAME') ? MYPROTECTOR_COMPANY_NAME : 'MyProtector LLC';
$company_email = defined('MYPROTECTOR_COMPANY_EMAIL') ? MYPROTECTOR_COMPANY_EMAIL : 'contact@myprotector.com';
$privacy_url = defined('MYPROTECTOR_PRIVACY_URL') ? MYPROTECTOR_PRIVACY_URL : home_url('/privacy');
$terms_url = defined('MYPROTECTOR_TERMS_URL') ? MYPROTECTOR_TERMS_URL : home_url('/terms');
$linkedin_url = defined('MYPROTECTOR_SOCIAL_LINKEDIN') ? MYPROTECTOR_SOCIAL_LINKEDIN : 'https://linkedin.com/company/myprotector';
$twitter_url = defined('MYPROTECTOR_SOCIAL_TWITTER') ? MYPROTECTOR_SOCIAL_TWITTER : 'https://twitter.com/myprotector';
$founder_linkedin = defined('MYPROTECTOR_FOUNDER_LINKEDIN') ? MYPROTECTOR_FOUNDER_LINKEDIN : 'https://linkedin.com/in/adamwyrzycki';
$founder_name = defined('MYPROTECTOR_FOUNDER_NAME') ? MYPROTECTOR_FOUNDER_NAME : 'Adam Wyrzycki';
?>

<footer class="mp-footer">
    <div class="mp-container">
        <div class="mp-footer-grid">
            <div class="mp-footer-brand">
                <a href="<?php echo esc_url($company_url); ?>" class="mp-logo">
                    <div class="mp-logo-icon">MP</div>
                    <div class="mp-logo-text">My<span>Protector</span></div>
                </a>
                <p class="mp-footer-desc">
                    Building trust between businesses and customers through 
                    transparent verification and authentic reviews. Helping 
                    consumers make informed decisions with verified trust signals.
                </p>
                <div class="mp-footer-social">
                    <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" aria-label="X (Twitter)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" target="_blank" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" target="_blank" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="mp-footer-title">Platform</h4>
                <ul class="mp-footer-links">
                    <li><a href="<?php echo esc_url($company_url); ?>/businesses">Search Businesses</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/write-review">Write a Review</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/trust-signals">Trust Signals</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/dashboard">Dashboard</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="mp-footer-title">For Business</h4>
                <ul class="mp-footer-links">
                    <li><a href="<?php echo esc_url($company_url); ?>/claim-business">Claim Your Business</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/business-dashboard">Business Dashboard</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/verification">Verification</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/widgets">Widgets</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="mp-footer-title">Company</h4>
                <ul class="mp-footer-links">
                    <li><a href="<?php echo esc_url($company_url); ?>/about">About Us</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/how-it-works">How It Works</a></li>
                    <li><a href="mailto:<?php echo esc_attr($company_email); ?>">Contact</a></li>
                    <li><a href="<?php echo esc_url($company_url); ?>/founder/<?php echo esc_attr(strtolower(str_replace(' ', '-', $founder_name))); ?>">
                        Meet Our <a href="<?php echo esc_url($founder_linkedin); ?>" target="_blank"><?php echo esc_html($founder_name); ?></a>
                    </a></li>
                </ul>
            </div>
        </div>
        
        <div class="mp-footer-bottom">
            <p class="mp-footer-copyright">
                &copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. All rights reserved.
                <span class="mp-footer-audit"> | Page last verified: <?php echo date('F j, Y g:i A'); ?></span>
            </p>
            <div class="mp-footer-legal">
                <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>
                <a href="<?php echo esc_url($terms_url); ?>">Terms of Service</a>
                <a href="<?php echo esc_url($company_url); ?>/cookies">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
