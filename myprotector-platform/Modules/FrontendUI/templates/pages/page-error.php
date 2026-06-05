<?php
/**
 * Error template for MyProtector FrontendUI
 * 
 * This template is shown when a route is matched but the template file is missing.
 * 
 * @package MyProtector\Modules\FrontendUI
 */
if (!defined('ABSPATH')) {
    exit;
}

$mp_page = isset($wp_query->query_vars['mp_page']) ? $wp_query->query_vars['mp_page'] : 'unknown';
$mp_slug = isset($wp_query->query_vars['mp_slug']) ? $wp_query->query_vars['mp_slug'] : '';
$template_path = isset($template_path) ? $template_path : 'unknown';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template Error - MyProtector</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .error-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error-header {
            color: #dc3545;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .error-details {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }
        .error-details code {
            display: block;
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .debug-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .debug-info pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-header">Template File Not Found</h1>
        
        <p>The route <code><?php echo esc_html($mp_page); ?></code> was matched, but the template file is missing.</p>
        
        <div class="error-details">
            <strong>Route:</strong> <?php echo esc_html($mp_page); ?><br>
            <?php if (!empty($mp_slug)): ?>
            <strong>Slug:</strong> <?php echo esc_html($mp_slug); ?><br>
            <?php endif; ?>
            <strong>Expected Template:</strong> <code><?php echo esc_html($template_path); ?></code>
        </div>
        
        <h3>Troubleshooting Steps:</h3>
        <ol>
            <li>Verify that all template files exist in <code>/Modules/FrontendUI/templates/pages/</code></li>
            <li>Check that the plugin was activated properly</li>
            <li>Flush rewrite rules by visiting Settings > Permalinks > Save Changes</li>
            <li>Check the error log for more details</li>
        </ol>
        
        <h3>Quick Fix:</h3>
        <pre>wp rewrite flush --hard</pre>
        
        <div class="debug-info">
            <strong>Debug Info:</strong>
            <pre><?php echo esc_html(print_r([
                'mp_page' => $mp_page,
                'mp_slug' => $mp_slug,
                'template_path' => $template_path,
                'query_vars' => $wp_query->query_vars ?? []
            ], true)); ?></pre>
        </div>
    </div>
</body>
</html>