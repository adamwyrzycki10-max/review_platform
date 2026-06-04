<?php
/**
 * MyProtector Platform - WordPress Customizer Settings
 * 
 * Theme Customizer integration for frontend branding
 *
 * @package MyProtector\Modules\FrontendUI
 */

namespace MyProtector\Modules\FrontendUI;

// Prevent direct access
if (!defined('ABSPATH')) exit;

class CustomizerSettings {
    
    /**
     * Initialize customizer settings
     * 
     * @return void
     */
    public static function init(): void {
        add_action('customize_register', [self::class, 'addCustomizerSettings']);
        add_action('wp_head', [self::class, 'outputCustomizerStyles']);
    }

    /**
     * Add customizer settings
     * 
     * @param \WP_Customize_Manager $wp_customize
     * @return void
     */
    public static function addCustomizerSettings($wp_customize): void {
        
        // Add MyProtector Panel
        $wp_customize->add_panel('mp_branding_panel', [
            'title' => __('MyProtector Branding', 'myprotector-platform'),
            'description' => __('Customize the look and feel of your MyProtector frontend', 'myprotector-platform'),
            'priority' => 30,
        ]);

        // ==========================================
        // General Settings Section
        // ==========================================
        $wp_customize->add_section('mp_general_settings', [
            'title' => __('General Settings', 'myprotector-platform'),
            'panel' => 'mp_branding_panel',
            'priority' => 10,
        ]);

        // Primary Color
        $wp_customize->add_setting('mp_primary_color', [
            'default' => '#0A1F44',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'mp_primary_color', [
            'label' => __('Primary Color', 'myprotector-platform'),
            'section' => 'mp_general_settings',
            'settings' => 'mp_primary_color',
        ]));

        // Secondary Color
        $wp_customize->add_setting('mp_secondary_color', [
            'default' => '#1a3a6e',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'mp_secondary_color', [
            'label' => __('Secondary Color', 'myprotector-platform'),
            'section' => 'mp_general_settings',
            'settings' => 'mp_secondary_color',
        ]));

        // Accent Color
        $wp_customize->add_setting('mp_accent_color', [
            'default' => '#059669',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'mp_accent_color', [
            'label' => __('Accent Color (Green for Trust)', 'myprotector-platform'),
            'section' => 'mp_general_settings',
            'settings' => 'mp_accent_color',
        ]));

        // ==========================================
        // Company Information Section
        // ==========================================
        $wp_customize->add_section('mp_company_info', [
            'title' => __('Company Information', 'myprotector-platform'),
            'panel' => 'mp_branding_panel',
            'priority' => 20,
        ]);

        // Company Name
        $wp_customize->add_setting('mp_company_name', [
            'default' => 'MyProtector LLC',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control('mp_company_name', [
            'label' => __('Company Name', 'myprotector-platform'),
            'section' => 'mp_company_info',
            'type' => 'text',
        ]);

        // Company Email
        $wp_customize->add_setting('mp_company_email', [
            'default' => 'contact@myprotector.com',
            'sanitize_callback' => 'sanitize_email',
        ]);

        $wp_customize->add_control('mp_company_email', [
            'label' => __('Contact Email', 'myprotector-platform'),
            'section' => 'mp_company_info',
            'type' => 'email',
        ]);

        // Support Email
        $wp_customize->add_setting('mp_support_email', [
            'default' => 'support@myprotector.com',
            'sanitize_callback' => 'sanitize_email',
        ]);

        $wp_customize->add_control('mp_support_email', [
            'label' => __('Support Email', 'myprotector-platform'),
            'section' => 'mp_company_info',
            'type' => 'email',
        ]);

        // Company URL
        $wp_customize->add_setting('mp_company_url', [
            'default' => home_url(),
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_company_url', [
            'label' => __('Company URL', 'myprotector-platform'),
            'section' => 'mp_company_info',
            'type' => 'url',
        ]);

        // ==========================================
        // Social Links Section
        // ==========================================
        $wp_customize->add_section('mp_social_links', [
            'title' => __('Social Links', 'myprotector-platform'),
            'panel' => 'mp_branding_panel',
            'priority' => 30,
        ]);

        // LinkedIn
        $wp_customize->add_setting('mp_social_linkedin', [
            'default' => 'https://linkedin.com/company/myprotector',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_social_linkedin', [
            'label' => __('LinkedIn URL', 'myprotector-platform'),
            'section' => 'mp_social_links',
            'type' => 'url',
        ]);

        // Twitter/X
        $wp_customize->add_setting('mp_social_twitter', [
            'default' => 'https://twitter.com/myprotector',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_social_twitter', [
            'label' => __('Twitter/X URL', 'myprotector-platform'),
            'section' => 'mp_social_links',
            'type' => 'url',
        ]);

        // Facebook
        $wp_customize->add_setting('mp_social_facebook', [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_social_facebook', [
            'label' => __('Facebook URL', 'myprotector-platform'),
            'section' => 'mp_social_links',
            'type' => 'url',
        ]);

        // Instagram
        $wp_customize->add_setting('mp_social_instagram', [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_social_instagram', [
            'label' => __('Instagram URL', 'myprotector-platform'),
            'section' => 'mp_social_links',
            'type' => 'url',
        ]);

        // ==========================================
        // Founder Section
        // ==========================================
        $wp_customize->add_section('mp_founder', [
            'title' => __('Founder Information', 'myprotector-platform'),
            'panel' => 'mp_branding_panel',
            'priority' => 40,
        ]);

        // Founder Name
        $wp_customize->add_setting('mp_founder_name', [
            'default' => 'Adam Wyrzycki',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control('mp_founder_name', [
            'label' => __('Founder Name', 'myprotector-platform'),
            'section' => 'mp_founder',
            'type' => 'text',
        ]);

        // Founder Title
        $wp_customize->add_setting('mp_founder_title', [
            'default' => 'Co-Founder',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control('mp_founder_title', [
            'label' => __('Founder Title', 'myprotector-platform'),
            'section' => 'mp_founder',
            'type' => 'text',
        ]);

        // Founder LinkedIn
        $wp_customize->add_setting('mp_founder_linkedin', [
            'default' => 'https://linkedin.com/in/adamwyrzycki',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_founder_linkedin', [
            'label' => __('Founder LinkedIn URL', 'myprotector-platform'),
            'section' => 'mp_founder',
            'type' => 'url',
        ]);

        // Founder Bio
        $wp_customize->add_setting('mp_founder_bio', [
            'default' => 'With a passion for consumer protection and business transparency, Adam Wyrzycki founded MyProtector to help people make informed decisions when dealing with businesses.',
            'sanitize_callback' => 'sanitize_textarea_field',
        ]);

        $wp_customize->add_control('mp_founder_bio', [
            'label' => __('Founder Biography', 'myprotector-platform'),
            'section' => 'mp_founder',
            'type' => 'textarea',
        ]);

        // Founder Photo
        $wp_customize->add_setting('mp_founder_photo', [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'mp_founder_photo', [
            'label' => __('Founder Photo', 'myprotector-platform'),
            'section' => 'mp_founder',
            'settings' => 'mp_founder_photo',
        ]));

        // ==========================================
        // Legal Pages Section
        // ==========================================
        $wp_customize->add_section('mp_legal_pages', [
            'title' => __('Legal Pages', 'myprotector-platform'),
            'panel' => 'mp_branding_panel',
            'priority' => 50,
        ]);

        // Privacy Policy URL
        $wp_customize->add_setting('mp_privacy_url', [
            'default' => home_url('/privacy'),
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_privacy_url', [
            'label' => __('Privacy Policy URL', 'myprotector-platform'),
            'section' => 'mp_legal_pages',
            'type' => 'url',
        ]);

        // Terms of Service URL
        $wp_customize->add_setting('mp_terms_url', [
            'default' => home_url('/terms'),
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_terms_url', [
            'label' => __('Terms of Service URL', 'myprotector-platform'),
            'section' => 'mp_legal_pages',
            'type' => 'url',
        ]);

        // Cookie Policy URL
        $wp_customize->add_setting('mp_cookie_url', [
            'default' => home_url('/cookies'),
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('mp_cookie_url', [
            'label' => __('Cookie Policy URL', 'myprotector-platform'),
            'section' => 'mp_legal_pages',
            'type' => 'url',
        ]);
    }

    /**
     * Output customizer styles in head
     * 
     * @return void
     */
    public static function outputCustomizerStyles(): void {
        $primary = get_theme_mod('mp_primary_color', '#0A1F44');
        $secondary = get_theme_mod('mp_secondary_color', '#1a3a6e');
        $accent = get_theme_mod('mp_accent_color', '#059669');
        
        ?>
        <style id="mp-customizer-styles" type="text/css">
            :root {
                --mp-primary: <?php echo esc_html($primary); ?>;
                --mp-secondary: <?php echo esc_html($secondary); ?>;
                --mp-accent: <?php echo esc_html($accent); ?>;
            }
            
            /* Apply primary color to key elements */
            .mp-btn-primary,
            .mp-logo-icon,
            .mp-header {
                background-color: <?php echo esc_html($primary); ?>;
            }
            
            .mp-nav-link:hover,
            .mp-nav-link.active {
                color: <?php echo esc_html($primary); ?>;
            }
            
            .mp-nav-link::after {
                background: <?php echo esc_html($primary); ?>;
            }
            
            .mp-section-title,
            .mp-card-title {
                color: <?php echo esc_html($primary); ?>;
            }
            
            .mp-cta {
                background: linear-gradient(135deg, <?php echo esc_html($primary); ?> 0%, <?php echo esc_html($secondary); ?> 100%);
            }
            
            .mp-trust-badge-green {
                background-color: <?php echo esc_html($accent); ?>;
            }
        </style>
        <?php
    }

    /**
     * Get a customizer setting value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        return get_theme_mod('mp_' . $key, $default);
    }
}

// Initialize customizer settings
CustomizerSettings::init();

// Define constants for easy access
if (!defined('MYPROTECTOR_COMPANY_NAME')) {
    define('MYPROTECTOR_COMPANY_NAME', CustomizerSettings::get('company_name', 'MyProtector LLC'));
}

if (!defined('MYPROTECTOR_COMPANY_EMAIL')) {
    define('MYPROTECTOR_COMPANY_EMAIL', CustomizerSettings::get('company_email', 'contact@myprotector.com'));
}

if (!defined('MYPROTECTOR_SUPPORT_EMAIL')) {
    define('MYPROTECTOR_SUPPORT_EMAIL', CustomizerSettings::get('support_email', 'support@myprotector.com'));
}

if (!defined('MYPROTECTOR_FOUNDER_NAME')) {
    define('MYPROTECTOR_FOUNDER_NAME', CustomizerSettings::get('founder_name', 'Adam Wyrzycki'));
}

if (!defined('MYPROTECTOR_FOUNDER_LINKEDIN')) {
    define('MYPROTECTOR_FOUNDER_LINKEDIN', CustomizerSettings::get('founder_linkedin', 'https://linkedin.com/in/adamwyrzycki'));
}

if (!defined('MYPROTECTOR_SOCIAL_LINKEDIN')) {
    define('MYPROTECTOR_SOCIAL_LINKEDIN', CustomizerSettings::get('social_linkedin', 'https://linkedin.com/company/myprotector'));
}

if (!defined('MYPROTECTOR_SOCIAL_TWITTER')) {
    define('MYPROTECTOR_SOCIAL_TWITTER', CustomizerSettings::get('social_twitter', 'https://twitter.com/myprotector'));
}

if (!defined('MYPROTECTOR_PRIVACY_URL')) {
    define('MYPROTECTOR_PRIVACY_URL', CustomizerSettings::get('privacy_url', home_url('/privacy')));
}

if (!defined('MYPROTECTOR_TERMS_URL')) {
    define('MYPROTECTOR_TERMS_URL', CustomizerSettings::get('terms_url', home_url('/terms')));
}