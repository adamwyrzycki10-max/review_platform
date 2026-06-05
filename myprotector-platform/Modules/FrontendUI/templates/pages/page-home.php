<?php
/**
 * MyProtector Platform - Homepage Template
 * 
 * Uses WordPress theme header and footer
 * Full production-ready landing page
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Get theme header
get_header();

// Get FrontendUI module instance for mock data
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$businesses = $frontend_ui->getMockData('businesses');
$stats = $frontend_ui->getMockData('stats');
$reviews = $frontend_ui->getMockData('reviews');
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$founder_name = defined('MYPROTECTOR_FOUNDER_NAME') ? MYPROTECTOR_FOUNDER_NAME : 'Adam Wyrzycki';
$founder_linkedin = defined('MYPROTECTOR_FOUNDER_LINKEDIN') ? MYPROTECTOR_FOUNDER_LINKEDIN : 'https://linkedin.com/in/adamwyrzycki';
?>

<div class="mp-frontend-ui">
    <!-- Hero Section -->
    <section class="mp-hero">
        <div class="mp-container mp-hero-content">
            <h1>Trust the Businesses<br>You Choose</h1>
            <p class="mp-hero-subtitle">
                MyProtector helps you make informed decisions with verified reviews 
                and our unique Traffic Light Trust System. Know exactly who you're 
                dealing with before you spend a single dollar.
            </p>
            
            <!-- Hero Search -->
            <div class="mp-hero-search">
                <form action="<?php echo esc_url($company_url); ?>/businesses" method="GET">
                    <div class="mp-search">
                        <span class="mp-search-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" class="mp-form-input mp-search-input" placeholder="Search businesses by name or category...">
                        <button type="submit" class="mp-btn mp-btn-primary mp-search-btn">Search</button>
                    </div>
                </form>
            </div>
            
            <div class="mp-hero-actions">
                <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-btn mp-btn-primary mp-btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    Search Businesses
                </a>
                <a href="#featured-reviews" class="mp-btn mp-btn-outline mp-btn-lg" style="background: transparent; color: #fff; border-color: #fff;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    Read Reviews
                </a>
                <a href="<?php echo esc_url($company_url); ?>/register?type=business" class="mp-btn mp-btn-secondary mp-btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Claim Your Business
                </a>
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

    <!-- Trust Signals Explanation -->
    <section class="mp-section" id="trust-signals">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">Our Traffic Light Trust System</h2>
                <p class="mp-section-subtitle">
                    We verify businesses against 5 key trust criteria. 
                    See instantly how trustworthy a business is before you engage.
                </p>
            </div>
            
            <div class="mp-grid mp-grid-3" style="max-width: 1000px; margin: 0 auto;">
                <!-- Green - Shopping Safe -->
                <div class="mp-card" style="border-top: 4px solid var(--mp-green);">
                    <div class="mp-trust-light mp-trust-light-green" style="width: 80px; height: 80px; margin: 0 auto var(--mp-spacing-lg);">
                        <span class="mp-trust-icon">🛒</span>
                    </div>
                    <h3 style="text-align: center; color: var(--mp-green);">Shopping Safe</h3>
                    <p style="text-align: center; color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-md);">
                        4-5 trust criteria met. This business has demonstrated high transparency 
                        and commitment to customer trust.
                    </p>
                    <ul class="mp-trust-checklist" style="color: var(--mp-gray-700);">
                        <li><span class="mp-icon-check">✓</span> Insurance verified</li>
                        <li><span class="mp-icon-check">✓</span> Terms & conditions posted</li>
                        <li><span class="mp-icon-check">✓</span> Promise pledge made</li>
                        <li><span class="mp-icon-check">✓</span> Business verified</li>
                    </ul>
                </div>
                
                <!-- Amber - Walking Safe -->
                <div class="mp-card" style="border-top: 4px solid var(--mp-amber);">
                    <div class="mp-trust-light mp-trust-light-amber" style="width: 80px; height: 80px; margin: 0 auto var(--mp-spacing-lg);">
                        <span class="mp-trust-icon">🚶</span>
                    </div>
                    <h3 style="text-align: center; color: var(--mp-amber);">Walking Safe</h3>
                    <p style="text-align: center; color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-md);">
                        2-3 trust criteria met. Exercise normal caution. 
                        Request additional verification if needed.
                    </p>
                    <ul class="mp-trust-checklist" style="color: var(--mp-gray-700);">
                        <li><span class="mp-icon-warning">⚠</span> Partial verification</li>
                        <li><span class="mp-icon-check">✓</span> Some transparency</li>
                        <li><span class="mp-icon-warning">⚠</span> May need more info</li>
                        <li><span class="mp-icon-warning">⚠</span> Standard caution advised</li>
                    </ul>
                </div>
                
                <!-- Red - Caution -->
                <div class="mp-card" style="border-top: 4px solid var(--mp-red);">
                    <div class="mp-trust-light mp-trust-light-red" style="width: 80px; height: 80px; margin: 0 auto var(--mp-spacing-lg);">
                        <span class="mp-trust-icon">⚠️</span>
                    </div>
                    <h3 style="text-align: center; color: var(--mp-red);">Caution</h3>
                    <p style="text-align: center; color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-md);">
                        0-1 trust criteria met. We recommend extreme caution. 
                        Do your own research before engaging.
                    </p>
                    <ul class="mp-trust-checklist" style="color: var(--mp-gray-700);">
                        <li><span class="mp-icon-cross">✗</span> No insurance verified</li>
                        <li><span class="mp-icon-cross">✗</span> Missing terms</li>
                        <li><span class="mp-icon-cross">✗</span> No promise pledge</li>
                        <li><span class="mp-icon-warning">⚠</span> Do thorough research</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Businesses -->
    <section class="mp-section mp-section-light" id="businesses">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">Featured Verified Businesses</h2>
                <p class="mp-section-subtitle">
                    Browse our most trusted businesses, all verified against our strict criteria.
                </p>
            </div>
            
            <div class="mp-featured-businesses">
                <?php foreach (array_slice($businesses, 0, 3) as $business): ?>
                <div class="mp-business-card" data-trust-status="<?php echo esc_attr($business['trust_status']); ?>">
                    <div class="mp-business-card-header">
                        <div class="mp-business-card-logo">
                            <?php if (!empty($business['logo'])): ?>
                            <img src="<?php echo esc_url($business['logo']); ?>" alt="<?php echo esc_attr($business['name']); ?>">
                            <?php else: ?>
                            <?php echo esc_html(substr($business['name'], 0, 2)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="mp-business-card-info">
                            <h3 class="mp-business-card-name"><?php echo esc_html($business['name']); ?></h3>
                            <span class="mp-business-card-category"><?php echo esc_html($business['category']); ?></span>
                        </div>
                        <span class="mp-trust-badge mp-trust-badge-<?php echo esc_attr($business['trust_status']); ?>">
                            <?php echo strtoupper($business['trust_status']); ?>
                        </span>
                    </div>
                    <div class="mp-business-card-rating">
                        <div class="mp-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="mp-star <?php echo $i <= round($business['rating']) ? 'mp-star-filled' : 'mp-star-empty'; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                            <?php echo number_format($business['rating'], 1); ?> (<?php echo number_format($business['total_reviews']); ?> reviews)
                        </span>
                    </div>
                    <p class="mp-business-card-description"><?php echo esc_html($business['description']); ?></p>
                    <div class="mp-business-card-footer">
                        <span style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm);">
                            📍 <?php echo esc_html($business['location']); ?>
                        </span>
                        <a href="<?php echo esc_url($company_url); ?>/business/<?php echo esc_attr($business['slug']); ?>" class="mp-btn mp-btn-sm mp-btn-outline">
                            View Profile
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mp-text-center" style="margin-top: var(--mp-spacing-2xl);">
                <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-btn mp-btn-primary mp-btn-lg">
                    Browse All Businesses
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="mp-section" id="how-it-works">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">How It Works</h2>
                <p class="mp-section-subtitle">
                    Getting started with MyProtector is simple. Here's how we help you make better decisions.
                </p>
            </div>
            
            <div class="mp-grid mp-grid-4 mp-how-it-works">
                <div class="mp-step">
                    <div class="mp-step-number">1</div>
                    <h3 class="mp-step-title">Search Businesses</h3>
                    <p class="mp-step-description">Find the business you're looking for using our search and filter tools.</p>
                </div>
                <div class="mp-step">
                    <div class="mp-step-number">2</div>
                    <h3 class="mp-step-title">Check Trust Signals</h3>
                    <p class="mp-step-description">Review the Traffic Light Trust System to understand their transparency level.</p>
                </div>
                <div class="mp-step">
                    <div class="mp-step-number">3</div>
                    <h3 class="mp-step-title">Read Verified Reviews</h3>
                    <p class="mp-step-description">Browse authentic reviews from real customers who have used their services.</p>
                </div>
                <div class="mp-step">
                    <div class="mp-step-number">4</div>
                    <h3 class="mp-step-title">Make Informed Decisions</h3>
                    <p class="mp-step-description">Use all the information to make confident, informed choices.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Reviews -->
    <section class="mp-section mp-section-light" id="featured-reviews">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">What Our Members Say</h2>
                <p class="mp-section-subtitle">
                    Real stories from real people who trust MyProtector to make better decisions.
                </p>
            </div>
            
            <div class="mp-featured-reviews">
                <?php foreach (array_slice($reviews, 0, 4) as $review): ?>
                <div class="mp-review-card">
                    <div class="mp-review-header">
                        <div class="mp-review-avatar"><?php echo esc_html(substr($review['author'], 0, 1)); ?></div>
                        <div class="mp-review-meta">
                            <h4 class="mp-review-author"><?php echo esc_html($review['author']); ?></h4>
                            <span class="mp-review-date"><?php echo esc_html($review['date']); ?></span>
                        </div>
                        <div class="mp-review-rating">
                            <div class="mp-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="mp-star <?php echo $i <= $review['rating'] ? 'mp-star-filled' : 'mp-star-empty'; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <h4 class="mp-review-title"><?php echo esc_html($review['title']); ?></h4>
                    <p class="mp-review-content"><?php echo esc_html($review['content']); ?></p>
                    <span class="mp-review-verified">✓ Verified Customer</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">Meet Our Founder</h2>
                <p class="mp-section-subtitle">
                    The vision behind MyProtector's mission to build trust in the marketplace.
                </p>
            </div>
            
            <div class="mp-grid" style="max-width: 600px; margin: 0 auto;">
                <div class="mp-founder-card">
                    <div class="mp-founder-photo">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($founder_name); ?>&background=0A1F44&color=fff&size=200" 
                             alt="<?php echo esc_attr($founder_name); ?>">
                    </div>
                    <h3 class="mp-founder-name"><?php echo esc_html($founder_name); ?></h3>
                    <p class="mp-founder-title">Co-Founder</p>
                    <p class="mp-founder-bio">
                        With a passion for consumer protection and business transparency, 
                        <?php echo esc_html($founder_name); ?> founded MyProtector to help people make 
                        informed decisions when dealing with businesses. The Traffic Light Trust System 
                        was created to bring clarity and confidence to online transactions.
                    </p>
                    <div class="mp-founder-social">
                        <a href="<?php echo esc_url($founder_linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
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
            <h2 class="mp-cta-title">Ready to Get Started?</h2>
            <p class="mp-cta-description">
                Join thousands of businesses and customers who trust MyProtector 
                for transparent, verified business reviews.
            </p>
            <div class="mp-cta-actions">
                <a href="<?php echo esc_url($company_url); ?>/register" class="mp-btn mp-btn-primary mp-btn-lg">
                    Create Free Account
                </a>
                <a href="<?php echo esc_url($company_url); ?>/about" class="mp-btn mp-btn-secondary mp-btn-lg" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
                    Learn More
                </a>
            </div>
        </div>
    </section>
</div>

<?php
// Get theme footer
get_footer();