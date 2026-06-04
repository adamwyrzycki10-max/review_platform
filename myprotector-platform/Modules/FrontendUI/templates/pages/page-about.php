<?php
/**
 * MyProtector Platform - About Page Template
 * 
 * Uses WordPress theme header and footer
 * Includes founder section
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

get_header();

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$founder_name = defined('MYPROTECTOR_FOUNDER_NAME') ? MYPROTECTOR_FOUNDER_NAME : 'Adam Wyrzycki';
$founder_linkedin = defined('MYPROTECTOR_FOUNDER_LINKEDIN') ? MYPROTECTOR_FOUNDER_LINKEDIN : 'https://linkedin.com/in/adamwyrzycki';
$company_email = defined('MYPROTECTOR_COMPANY_EMAIL') ? MYPROTECTOR_COMPANY_EMAIL : 'contact@myprotector.com';
$stats = $this->getMockData('stats');
?>

<div class="mp-frontend-ui">
    <!-- Page Header -->
    <section class="mp-hero" style="padding: var(--mp-spacing-3xl) 0;">
        <div class="mp-container mp-hero-content">
            <h1>About MyProtector</h1>
            <p class="mp-hero-subtitle">
                Building trust between businesses and customers through transparent 
                verification and authentic reviews. Our mission is to make the 
                marketplace safer for everyone.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-grid" style="max-width: 1000px; margin: 0 auto; align-items: center;">
                <div style="flex: 1;">
                    <h2 class="mp-section-title">Our Mission</h2>
                    <p style="font-size: var(--mp-font-size-lg); color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                        At MyProtector, we believe everyone deserves to make informed decisions 
                        when dealing with businesses. The internet should be a safe place where 
                        consumers can trust the companies they interact with.
                    </p>
                    <p style="color: var(--mp-gray-600);">
                        That's why we created the Traffic Light Trust System – a simple, visual way 
                        to understand how transparent and trustworthy a business is. We verify 
                        multiple criteria so you don't have to spend hours researching.
                    </p>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 80px; margin-bottom: var(--mp-spacing-md);">🎯</div>
                    <h3 style="color: var(--mp-primary);">Trust Through Transparency</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="mp-section mp-section-light">
        <div class="mp-container">
            <div class="mp-stats">
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_businesses'] ?? 1250); ?>+</div>
                    <div class="mp-stat-label">Verified Businesses</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_reviews'] ?? 28400); ?>+</div>
                    <div class="mp-stat-label">Customer Reviews</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_users'] ?? 18200); ?>+</div>
                    <div class="mp-stat-label">Active Members</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['trust_score'] ?? 96); ?>%</div>
                    <div class="mp-stat-label">Trust Score</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust System Explained -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">How the Trust System Works</h2>
                <p class="mp-section-subtitle">
                    Our Traffic Light system evaluates businesses across 5 key criteria.
                </p>
            </div>
            
            <div class="mp-grid mp-grid-3" style="max-width: 1000px; margin: 0 auto;">
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">📋</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Insurance Verification</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        We verify that businesses carry appropriate insurance coverage to protect 
                        their customers in case of accidents or damages.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">📜</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Terms & Conditions</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Businesses must have clear, accessible terms of service that outline 
                        their policies, refund procedures, and customer rights.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🤝</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Promise Pledges</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Verified businesses can pledge specific promises to their customers, 
                        demonstrating commitment to quality service.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">⭐</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Customer Reviews</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Real reviews from verified customers help you understand the actual 
                        experience of dealing with each business.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">✅</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Identity Verification</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        We verify business identity through multiple channels to ensure 
                        they're legitimate and can be held accountable.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🛡️</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Trust Score</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Our algorithm calculates a comprehensive trust score based on 
                        all verified criteria, updated in real-time.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="mp-section mp-section-light">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">Meet Our Founder</h2>
                <p class="mp-section-subtitle">
                    The vision and passion behind MyProtector's mission.
                </p>
            </div>
            
            <div class="mp-grid" style="max-width: 700px; margin: 0 auto;">
                <div class="mp-founder-card" style="box-shadow: var(--mp-shadow-lg);">
                    <div class="mp-founder-photo">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($founder_name); ?>&background=0A1F44&color=fff&size=200" 
                             alt="<?php echo esc_attr($founder_name); ?>">
                    </div>
                    <h3 class="mp-founder-name"><?php echo esc_html($founder_name); ?></h3>
                    <p class="mp-founder-title">Co-Founder</p>
                    <p class="mp-founder-bio">
                        With a passion for consumer protection and business transparency, 
                        <?php echo esc_html($founder_name); ?> founded MyProtector to help people make 
                        informed decisions when dealing with businesses. After seeing countless 
                        cases where consumers were scammed or misled, the Traffic Light Trust 
                        System was created to bring clarity and confidence to online transactions.
                    </p>
                    <p class="mp-founder-bio">
                        "I believe that by making business transparency visible, we can build 
                        a marketplace where trust is the norm, not the exception. Every consumer 
                        deserves to know exactly who they're dealing with."
                    </p>
                    <div class="mp-founder-social">
                        <a href="<?php echo esc_url($founder_linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="mp-cta">
        <div class="mp-container">
            <h2 class="mp-cta-title">Join the Trust Revolution</h2>
            <p class="mp-cta-description">
                Whether you're a consumer looking for trustworthy businesses or a business 
                wanting to demonstrate your commitment to transparency, MyProtector is here for you.
            </p>
            <div class="mp-cta-actions">
                <a href="<?php echo esc_url($company_url); ?>/register" class="mp-btn mp-btn-primary mp-btn-lg">
                    Get Started Free
                </a>
                <a href="mailto:<?php echo esc_attr($company_email); ?>" class="mp-btn mp-btn-secondary mp-btn-lg" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</div>

<?php
get_footer();