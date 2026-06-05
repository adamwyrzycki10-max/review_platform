<?php
/**
 * MyProtector Platform - Admin Settings Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('MyProtector Settings', 'myprotector-platform'); ?></h1>
    
    <form method="post" style="max-width: 600px;">
        <?php wp_nonce_field('mp_settings'); ?>
        
        <!-- Subscription Settings -->
        <h2><?php _e('Subscription Settings', 'myprotector-platform'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="mp_subscription_price"><?php _e('Monthly Subscription Price', 'myprotector-platform'); ?></label></th>
                <td>
                    <input type="number" name="mp_subscription_price" id="mp_subscription_price" 
                           value="<?php echo esc_attr($settings['mp_subscription_price']); ?>" 
                           class="regular-text" step="0.01" min="0">
                    <p class="description"><?php _e('Price per month for business subscription.', 'myprotector-platform'); ?></p>
                </td>
            </tr>
        </table>
        
        <!-- Review Settings -->
        <h2><?php _e('Review Settings', 'myprotector-platform'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="mp_review_approval_required"><?php _e('Require Approval', 'myprotector-platform'); ?></label></th>
                <td>
                    <input type="checkbox" name="mp_review_approval_required" id="mp_review_approval_required" 
                           value="1" <?php checked($settings['mp_review_approval_required'], 1); ?>>
                    <label for="mp_review_approval_required"><?php _e('Reviews must be approved before publishing', 'myprotector-platform'); ?></label>
                </td>
            </tr>
        </table>
        
        <!-- Trust Signal Settings -->
        <h2><?php _e('Trust Signal Settings', 'myprotector-platform'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="mp_min_reviews"><?php _e('Minimum Reviews', 'myprotector-platform'); ?></label></th>
                <td>
                    <input type="number" name="mp_min_reviews" id="mp_min_reviews" 
                           value="<?php echo esc_attr($settings['mp_min_reviews']); ?>" 
                           class="regular-text" min="0">
                    <p class="description"><?php _e('Minimum reviews required for GREEN status.', 'myprotector-platform'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="mp_min_rating"><?php _e('Minimum Rating', 'myprotector-platform'); ?></label></th>
                <td>
                    <input type="number" name="mp_min_rating" id="mp_min_rating" 
                           value="<?php echo esc_attr($settings['mp_min_rating']); ?>" 
                           class="regular-text" step="0.1" min="0" max="5">
                    <p class="description"><?php _e('Minimum average rating for GREEN status.', 'myprotector-platform'); ?></p>
                </td>
            </tr>
        </table>
        
        <!-- Email Settings -->
        <h2><?php _e('Email Settings', 'myprotector-platform'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="mp_email_notifications"><?php _e('Email Notifications', 'myprotector-platform'); ?></label></th>
                <td>
                    <input type="checkbox" name="mp_email_notifications" id="mp_email_notifications" 
                           value="1" <?php checked($settings['mp_email_notifications'], 1); ?>>
                    <label for="mp_email_notifications"><?php _e('Send email notifications for important events', 'myprotector-platform'); ?></label>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Save Settings', 'myprotector-platform')); ?>
    </form>
</div>