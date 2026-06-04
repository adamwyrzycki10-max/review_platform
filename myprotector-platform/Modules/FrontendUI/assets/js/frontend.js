/**
 * MyProtector Platform - Frontend JavaScript
 * Interactive components for the frontend
 *
 * @package MyProtector\Modules\FrontendUI
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Global configuration
    const mpConfig = window.mpFrontendConfig || {
        ajaxUrl: '',
        nonce: '',
        companyUrl: ''
    };

    /**
     * Initialize all components on document ready
     */
    $(document).ready(function() {
        initMobileMenu();
        initSearchForm();
        initAuthForms();
        initFilterButtons();
        initReviewModal();
        initStarRating();
        initSmoothScroll();
        initDashboardNav();
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const $toggle = $('.mp-mobile-menu-toggle');
        const $nav = $('.mp-nav');
        const $body = $('body');

        $toggle.on('click', function() {
            $nav.toggleClass('mp-nav-open');
            $body.toggleClass('mp-menu-open');
        });

        // Close menu on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.mp-header').length) {
                $nav.removeClass('mp-nav-open');
                $body.removeClass('mp-menu-open');
            }
        });
    }

    /**
     * Search Form Handler
     */
    function initSearchForm() {
        const $form = $('.mp-hero-search form, .mp-directory-search form');
        
        $form.on('submit', function(e) {
            e.preventDefault();
            
            const $input = $(this).find('input[name="search"]');
            const query = $input.val().trim();
            
            if (query.length > 0) {
                // Redirect to directory with search query
                const searchUrl = mpConfig.companyUrl + '/businesses?search=' + encodeURIComponent(query);
                window.location.href = searchUrl;
            }
        });
    }

    /**
     * Authentication Forms Handler
     */
    function initAuthForms() {
        // Login Form
        $('#mp-login-form').on('submit', handleLoginSubmit);
        
        // Register Form
        $('#mp-register-form').on('submit', handleRegisterSubmit);
        
        // Lost Password Form
        $('#mp-lost-password-form').on('submit', handleLostPasswordSubmit);
        
        // Toggle between login and lost password
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

        // Toggle password visibility
        $('.mp-toggle-password').on('click', function(e) {
            e.preventDefault();
            const $this = $(this);
            const $input = $this.closest('.mp-form-group').find('input');
            const isPassword = $input.attr('type') === 'password';
            
            $input.attr('type', isPassword ? 'text' : 'password');
            $this.find('.mp-icon-eye').toggleClass('mp-icon-eye-slash');
        });

        // User type toggle for registration
        $('.mp-user-type-btn').on('click', function() {
            const type = $(this).data('type');
            
            $('.mp-user-type-btn').removeClass('active');
            $(this).addClass('active');
            
            $('#user_type').val(type);
            
            // Show/hide additional fields based on type
            if (type === 'business') {
                $('.mp-business-fields').show();
            } else {
                $('.mp-business-fields').hide();
            }
        });
    }

    /**
     * Handle Login Form Submission
     */
    function handleLoginSubmit(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#mp-login-btn');
        const $message = $('#mp-login-message');
        
        // Disable button and show loading
        $btn.prop('disabled', true).html('<span class="mp-spinner"></span> Signing in...');
        $message.hide();
        
        $.ajax({
            url: mpConfig.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    $message.removeClass('error').addClass('success').html(response.data.message).show();
                    
                    // Redirect after short delay
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 500);
                } else {
                    $message.removeClass('success').addClass('error').html(response.data.message).show();
                    $btn.prop('disabled', false).text('Sign In');
                }
            },
            error: function() {
                $message.removeClass('success').addClass('error').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Sign In');
            }
        });
    }

    /**
     * Handle Register Form Submission
     */
    function handleRegisterSubmit(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#mp-register-btn');
        const $message = $('#mp-register-message');
        
        // Validate passwords match
        const password = $form.find('[name="password"]').val();
        const confirmPassword = $form.find('[name="confirm_password"]').val();
        
        if (password !== confirmPassword) {
            $message.removeClass('success').addClass('error').html('Passwords do not match.').show();
            return;
        }
        
        // Disable button and show loading
        $btn.prop('disabled', true).html('<span class="mp-spinner"></span> Creating account...');
        $message.hide();
        
        $.ajax({
            url: mpConfig.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    $message.removeClass('error').addClass('success').html(response.data.message).show();
                    
                    // Redirect after short delay
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 500);
                } else {
                    $message.removeClass('success').addClass('error').html(response.data.message).show();
                    $btn.prop('disabled', false).text('Create Account');
                }
            },
            error: function() {
                $message.removeClass('success').addClass('error').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Create Account');
            }
        });
    }

    /**
     * Handle Lost Password Form Submission
     */
    function handleLostPasswordSubmit(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#mp-lost-password-btn');
        const $message = $('#mp-lost-password-message');
        
        $btn.prop('disabled', true).html('<span class="mp-spinner"></span> Sending...');
        $message.hide();
        
        $.ajax({
            url: mpConfig.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                $message.removeClass('error').addClass('success').html(response.data.message).show();
                $btn.prop('disabled', false).text('Send Reset Link');
            },
            error: function() {
                $message.removeClass('success').addClass('error').html('Connection error. Please try again.').show();
                $btn.prop('disabled', false).text('Send Reset Link');
            }
        });
    }

    /**
     * Filter Buttons Handler
     */
    function initFilterButtons() {
        $('.mp-filter-btn').on('click', function() {
            const $this = $(this);
            const filter = $this.data('filter');
            const $container = $this.closest('.mp-directory-filters');
            
            // Toggle active state
            $container.find('.mp-filter-btn').removeClass('active');
            $this.addClass('active');
            
            // Filter businesses
            filterBusinesses(filter);
        });
    }

    /**
     * Filter Businesses Display
     */
    function filterBusinesses(filter) {
        const $cards = $('.mp-business-card');
        
        if (filter === 'all') {
            $cards.show();
        } else {
            $cards.each(function() {
                const $card = $(this);
                const status = $card.data('trust-status');
                
                if (status === filter) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }
    }

    /**
     * Review Modal Handler
     */
    function initReviewModal() {
        // Open modal
        $('.mp-write-review-btn, [data-action="write-review"]').on('click', function(e) {
            e.preventDefault();
            const businessId = $(this).data('business-id');
            openReviewModal(businessId);
        });

        // Close modal
        $('.mp-modal-close, .mp-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });

        // Close on ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReviewModal();
            }
        });

        // Star rating interaction
        $('.mp-star-rating .mp-star').on('click', function() {
            const rating = $(this).data('rating');
            setStarRating(rating);
        });
    }

    /**
     * Open Review Modal
     */
    function openReviewModal(businessId) {
        const $modal = $('#mp-review-modal');
        
        if (businessId) {
            $modal.find('[name="business_id"]').val(businessId);
        }
        
        $modal.addClass('mp-modal-open').fadeIn();
        $('body').css('overflow', 'hidden');
    }

    /**
     * Close Review Modal
     */
    function closeReviewModal() {
        const $modal = $('#mp-review-modal');
        
        $modal.removeClass('mp-modal-open').fadeOut();
        $('body').css('overflow', '');
        
        // Reset form
        $modal.find('form')[0].reset();
        setStarRating(0);
    }

    /**
     * Star Rating Handler
     */
    function initStarRating() {
        $('.mp-star-rating .mp-star').on('mouseenter', function() {
            const rating = $(this).data('rating');
            highlightStars(rating);
        });

        $('.mp-star-rating').on('mouseleave', function() {
            const currentRating = $(this).find('input[name="rating"]').val() || 0;
            highlightStars(currentRating);
        });
    }

    /**
     * Set Star Rating
     */
    function setStarRating(rating) {
        const $container = $('.mp-star-rating');
        $container.find('input[name="rating"]').val(rating);
        highlightStars(rating);
    }

    /**
     * Highlight Stars
     */
    function highlightStars(rating) {
        $('.mp-star-rating .mp-star').each(function() {
            const starRating = parseInt($(this).data('rating'));
            if (starRating <= rating) {
                $(this).addClass('mp-star-filled');
            } else {
                $(this).removeClass('mp-star-filled');
            }
        });
    }

    /**
     * Smooth Scroll
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                const offset = 80; // Header height
                $('html, body').animate({
                    scrollTop: target.offset().top - offset
                }, 500);
            }
        });
    }

    /**
     * Dashboard Navigation
     */
    function initDashboardNav() {
        $('.mp-dashboard-nav-link').on('click', function(e) {
            const href = $(this).attr('href');
            
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = $(href);
                
                if (target.length) {
                    // Hide all sections
                    $('.mp-dashboard-section').hide();
                    
                    // Show target section
                    target.show();
                    
                    // Update active nav
                    $('.mp-dashboard-nav-link').removeClass('active');
                    $(this).addClass('active');
                }
            }
        });
    }

    /**
     * AJAX Helper Function
     */
    function mpAjax(action, data, successCallback, errorCallback) {
        const ajaxData = $.extend({
            action: action,
            nonce: mpConfig.nonce
        }, data);

        $.ajax({
            url: mpConfig.ajaxUrl,
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success) {
                    if (typeof successCallback === 'function') {
                        successCallback(response.data);
                    }
                } else {
                    if (typeof errorCallback === 'function') {
                        errorCallback(response.data || { message: 'An error occurred.' });
                    }
                }
            },
            error: function(xhr, status, error) {
                if (typeof errorCallback === 'function') {
                    errorCallback({ message: 'Connection error. Please try again.' });
                }
            }
        });
    }

    /**
     * Show Message
     */
    function showMessage(element, message, type) {
        const $el = $(element);
        $el.removeClass('success error info').addClass('mp-message-' + type).html(message).show();
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            $el.fadeOut();
        }, 5000);
    }

    /**
     * Debounce Function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Expose public methods
    window.MyProtectorFrontend = {
        openReviewModal: openReviewModal,
        closeReviewModal: closeReviewModal,
        showMessage: showMessage,
        filterBusinesses: filterBusinesses
    };

})(jQuery);