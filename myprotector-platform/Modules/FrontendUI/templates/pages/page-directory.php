<?php
/**
 * MyProtector Platform - Directory Page Template
 * 
 * Business listing with search, filters, and pagination
 * Uses WordPress theme header and footer
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get FrontendUI module instance for mock data
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$businesses = $frontend_ui->getMockData('businesses');
$categories = $frontend_ui->getMockData('categories');
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
?>

<div class="mp-frontend-ui">
    <!-- Directory Header -->
    <section class="mp-directory-header">
        <div class="mp-container">
            <h1 class="mp-directory-title">Browse Verified Businesses</h1>
            <p style="color: rgba(255,255,255,0.9); margin-bottom: var(--mp-spacing-lg);">
                Explore our directory of trusted businesses, all verified against our Traffic Light Trust System.
            </p>
            
            <!-- Search Form -->
            <form action="<?php echo esc_url($company_url); ?>/businesses" method="GET" style="max-width: 600px;">
                <div class="mp-search">
                    <span class="mp-search-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" class="mp-form-input mp-search-input" 
                           placeholder="Search businesses by name or category..."
                           value="<?php echo esc_attr($search_query); ?>">
                    <button type="submit" class="mp-btn mp-btn-primary mp-search-btn">Search</button>
                </div>
            </form>
            
            <!-- Filters -->
            <div class="mp-directory-filters">
                <span class="mp-filter-label">Filter by Trust Status:</span>
                <div class="mp-filter-group">
                    <button class="mp-filter-btn <?php echo $filter_status === 'all' ? 'active' : ''; ?>" data-filter="all">
                        All Businesses
                    </button>
                    <button class="mp-filter-btn mp-filter-btn-green <?php echo $filter_status === 'green' ? 'active' : ''; ?>" data-filter="green">
                        🟢 Green
                    </button>
                    <button class="mp-filter-btn mp-filter-btn-amber <?php echo $filter_status === 'amber' ? 'active' : ''; ?>" data-filter="amber">
                        🟡 Amber
                    </button>
                    <button class="mp-filter-btn mp-filter-btn-red <?php echo $filter_status === 'red' ? 'active' : ''; ?>" data-filter="red">
                        🔴 Red
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="mp-section">
        <div class="mp-container">
            <!-- Results Header -->
            <div class="mp-flex mp-items-center mp-justify-between mp-mb-lg">
                <p style="color: var(--mp-gray-600); margin: 0;">
                    Showing <strong><?php echo count($businesses); ?></strong> businesses
                    <?php if ($search_query): ?>
                    for "<strong><?php echo esc_html($search_query); ?></strong>"
                    <?php endif; ?>
                </p>
                <div class="mp-flex mp-items-center mp-gap-sm">
                    <label style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">Sort by:</label>
                    <select class="mp-form-select" style="width: auto; padding: 0.5rem 1rem;">
                        <option value="rating">Highest Rated</option>
                        <option value="reviews">Most Reviews</option>
                        <option value="newest">Newest First</option>
                        <option value="name">Name (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <!-- Business Grid -->
            <div class="mp-grid mp-grid-3" id="mp-businesses-grid">
                <?php foreach ($businesses as $business): ?>
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
                    
                    <!-- Trust Signal Mini -->
                    <div class="mp-trust-signal-card mp-trust-signal-card-<?php echo esc_attr($business['trust_status']); ?>" 
                         style="margin-bottom: var(--mp-spacing-md); padding: var(--mp-spacing-sm) var(--mp-spacing-md);">
                        <div class="mp-flex mp-items-center mp-gap-sm">
                            <div class="mp-trust-light mp-trust-light-<?php echo esc_attr($business['trust_status']); ?>" 
                                 style="width: 24px; height: 24px; font-size: 12px;">
                                <?php if ($business['trust_status'] === 'green'): ?>
                                ✓
                                <?php elseif ($business['trust_status'] === 'amber'): ?>
                                !
                                <?php else: ?>
                                ✗
                                <?php endif; ?>
                            </div>
                            <span style="font-size: var(--mp-font-size-sm); font-weight: 500;">
                                Trust Score: <?php echo number_format($business['trust_score']); ?>%
                            </span>
                        </div>
                    </div>
                    
                    <div class="mp-business-card-footer">
                        <span style="color: var(--mp-gray-500); font-size: var(--mp-font-size-sm);">
                            📍 <?php echo esc_html($business['location']); ?>
                        </span>
                        <a href="<?php echo esc_url($company_url); ?>/business/<?php echo esc_attr($business['slug']); ?>" 
                           class="mp-btn mp-btn-sm mp-btn-primary">
                            View Profile
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <div class="mp-pagination">
                <button class="mp-pagination-btn" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                </button>
                <button class="mp-pagination-btn active">1</button>
                <button class="mp-pagination-btn">2</button>
                <button class="mp-pagination-btn">3</button>
                <span style="color: var(--mp-gray-400); padding: 0 8px;">...</span>
                <button class="mp-pagination-btn">10</button>
                <button class="mp-pagination-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="mp-section mp-section-light">
        <div class="mp-container mp-text-center">
            <h2 style="margin-bottom: var(--mp-spacing-md);">Own a Business?</h2>
            <p style="color: var(--mp-gray-600); max-width: 600px; margin: 0 auto var(--mp-spacing-xl);">
                Join our verified directory and build trust with potential customers. 
                Get your Traffic Light Trust badge and stand out from the competition.
            </p>
            <a href="<?php echo esc_url($company_url); ?>/register?type=business" class="mp-btn mp-btn-primary mp-btn-lg">
                Claim Your Business
            </a>
        </div>
    </section>
</div>

<script>
(function($) {
    'use strict';
    
    // Filter functionality
    $('.mp-filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        const $cards = $('#mp-businesses-grid').find('.mp-business-card');
        
        // Update active state
        $('.mp-filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filter cards
        if (filter === 'all') {
            $cards.show();
        } else {
            $cards.hide().filter('[data-trust-status="' + filter + '"]').show();
        }
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('status', filter);
        window.history.pushState({}, '', url);
    });
    
})(jQuery);
</script>

<?php
get_footer();