<?php
/**
 * MyProtector Platform - Admin Dashboard Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('MyProtector Dashboard', 'myprotector-platform'); ?></h1>
    
    <?php if (isset($_GET['message'])): ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html($_GET['message']); ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="mp-admin-stats">
        <div class="mp-stat-card">
            <h3><?php echo esc_html($stats['total_reviews']); ?></h3>
            <p><?php _e('Total Reviews', 'myprotector-platform'); ?></p>
        </div>
        <div class="mp-stat-card mp-stat-warning">
            <h3><?php echo esc_html($stats['pending_reviews']); ?></h3>
            <p><?php _e('Pending Reviews', 'myprotector-platform'); ?></p>
        </div>
        <div class="mp-stat-card">
            <h3><?php echo esc_html($stats['total_businesses']); ?></h3>
            <p><?php _e('Total Businesses', 'myprotector-platform'); ?></p>
        </div>
        <div class="mp-stat-card mp-stat-success">
            <h3><?php echo esc_html($stats['active_businesses']); ?></h3>
            <p><?php _e('Active Businesses', 'myprotector-platform'); ?></p>
        </div>
    </div>
    
    <!-- Recent Pending Reviews -->
    <div class="mp-admin-section">
        <h2><?php _e('Pending Reviews', 'myprotector-platform'); ?></h2>
        
        <?php if (empty($recent_reviews)): ?>
        <p><?php _e('No pending reviews.', 'myprotector-platform'); ?></p>
        <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Business', 'myprotector-platform'); ?></th>
                    <th><?php _e('Reviewer', 'myprotector-platform'); ?></th>
                    <th><?php _e('Rating', 'myprotector-platform'); ?></th>
                    <th><?php _e('Date', 'myprotector-platform'); ?></th>
                    <th><?php _e('Actions', 'myprotector-platform'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_reviews as $review): ?>
                <tr>
                    <td><?php echo esc_html($review->business_name ?? 'N/A'); ?></td>
                    <td><?php echo esc_html($review->reviewer_name ?? 'Anonymous'); ?></td>
                    <td><?php echo esc_html($review->review_rating); ?>/5</td>
                    <td><?php echo esc_html(date_i18n('M j, Y', strtotime($review->created_at))); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('mp_admin_nonce'); ?>
                            <input type="hidden" name="review_id" value="<?php echo esc_attr($review->review_id); ?>">
                            <input type="hidden" name="mp_action" value="approve">
                            <button type="submit" class="button button-primary"><?php _e('Approve', 'myprotector-platform'); ?></button>
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('mp_admin_nonce'); ?>
                            <input type="hidden" name="review_id" value="<?php echo esc_attr($review->review_id); ?>">
                            <input type="hidden" name="mp_action" value="reject">
                            <button type="submit" class="button"><?php _e('Reject', 'myprotector-platform'); ?></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="<?php echo admin_url('admin.php?page=mp-reviews&status=pending'); ?>" class="button"><?php _e('View All Pending', 'myprotector-platform'); ?></a></p>
        <?php endif; ?>
    </div>
    
    <!-- Quick Links -->
    <div class="mp-admin-section">
        <h2><?php _e('Quick Links', 'myprotector-platform'); ?></h2>
        <div class="mp-admin-links">
            <a href="<?php echo admin_url('admin.php?page=mp-reviews'); ?>" class="mp-admin-link">
                <span class="dashicons dashicons-star-filled"></span>
                <span><?php _e('Moderate Reviews', 'myprotector-platform'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=mp-businesses'); ?>" class="mp-admin-link">
                <span class="dashicons dashicons-building"></span>
                <span><?php _e('Manage Businesses', 'myprotector-platform'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=mp-traffic-signals'); ?>" class="mp-admin-link">
                <span class="dashicons dashicons-shield"></span>
                <span><?php _e('Trust Signals', 'myprotector-platform'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=mp-resellers'); ?>" class="mp-admin-link">
                <span class="dashicons dashicons-groups"></span>
                <span><?php _e('Resellers', 'myprotector-platform'); ?></span>
            </a>
        </div>
    </div>
</div>

<style>
.mp-admin-wrap { padding: 20px 0; }
.mp-admin-stats { display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap; }
.mp-stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); min-width: 150px; }
.mp-stat-card h3 { font-size: 32px; margin: 0 0 5px; color: #1d2327; }
.mp-stat-card p { margin: 0; color: #646970; }
.mp-stat-card.mp-stat-warning h3 { color: #d63638; }
.mp-stat-card.mp-stat-success h3 { color: #00a32a; }
.mp-admin-section { background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.mp-admin-section h2 { margin-top: 0; }
.mp-admin-links { display: flex; gap: 15px; flex-wrap: wrap; }
.mp-admin-link { display: flex; align-items: center; gap: 8px; padding: 15px 20px; background: #f6f7f7; border-radius: 6px; text-decoration: none; color: #1d2327; }
.mp-admin-link:hover { background: #dcdcde; }
.mp-admin-link .dashicons { font-size: 24px; width: 24px; height: 24px; }
</style>