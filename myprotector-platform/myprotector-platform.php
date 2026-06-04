<?php
/**
 * Plugin Name: MyProtector Platform
 * Plugin URI: https://myprotector.com
 * Description: Trust verification platform for businesses - Protect, Verify, Trust. A comprehensive review and trust signal system for WordPress.
 * Version: 1.0.0
 * Author: MyProtector Team
 * Author URI: https://myprotector.com
 * License: Proprietary
 * Text Domain: myprotector-platform
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load configuration
require_once __DIR__ . '/config.php';

// Autoloader
require_once __DIR__ . '/myprotector-platform-loader.php';

// Register activation hook
register_activation_hook(__FILE__, function () {
    require_once __DIR__ . '/Core/Activator.php';
    \MyProtector\Core\Activator::activate();
    
    // FIX: Trigger FrontendUI page creation on activation
    // This runs during plugin activation, before any other hooks
    do_action('mp_frontend_create_pages');
});

// Register deactivation hook
register_deactivation_hook(__FILE__, function () {
    require_once __DIR__ . '/Core/Deactivator.php';
    \MyProtector\Core\Deactivator::deactivate();
});

// Bootstrap the plugin
use MyProtector\Core\MyProtector;

function myprotector(): MyProtector {
    return MyProtector::getInstance();
}

// Load Frontend UI Module
$frontend_ui = plugin_dir_path(__FILE__) . 'Modules/FrontendUI/FrontendUI.php';

if (file_exists($frontend_ui)) {
    require_once $frontend_ui;
}

// Initialize - AFTER FrontendUI is loaded
myprotector()->run();