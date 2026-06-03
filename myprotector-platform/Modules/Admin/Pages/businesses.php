<?php
/**
 * MyProtector Platform - Admin Businesses Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('Business Management', 'myprotector-platform'); ?></h1>
    
    <!-- Filters -->
    <form method="get" style="margin: 20px 0;">
        <input type="hidden" name="page" value="mp-businesses">
        <select name="status">
            <option value=""><?php _e('All Status', 'myprotector-platform'); ?></option>
            <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'myprotector-platform'); ?></option>
            <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'myprotector-platform'); ?></option>
            <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'myprotector-platform'); ?></option>
        </select>
        <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Search businesses...', 'myprotector-platform'); ?>">
        <button type="submit" class="button"><?php _e('Filter', 'myprotector-platform'); ?></button>
    </form>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Business Name', 'myprotector-platform'); ?></th>
                <th><?php _e('Owner', 'myprotector-platform'); ?></th>
                <th><?php _e('Reviews', 'myprotector-platform'); ?></th>
                <th><?php _e('Rating', 'myprotector-platform'); ?></th>
                <th><?php _e('Status', 'myprotector-platform'); ?></th>
                <th><?php _e('Trust', 'myprotector-platform'); ?></th>
                <th><?php _e('Actions', 'myprotector-platform'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($businesses)): ?>
            <tr>
                <td colspan="7" style="text-align: center;"><?php _e('No businesses found.', 'myprotector-platform'); ?></td>
            </tr>
            <?php else: ?>
            <?php 
            $trafficService = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
            foreach ($businesses as $business): 
                $signal = $trafficService->getSignal($business->business_id);
                $trust_color = $signal ? $signal->trust_status : 'red';
            ?>
            <tr>
                <td>
                    <strong><?php echo esc_html($business->business_name); ?></strong>
                    <?php if ($business->is_verified): ?>
                    <span class="mp-verified" title="<?php _e('Verified', 'myprotector-platform'); ?>">✓</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    $owner = get_user_by('id', $business->user_id);
                    echo esc_html($owner ? $owner->display_name : 'N/A');
                    ?>
                </td>
                <td><?php echo esc_html($business->total_reviews); ?></td>
                <td><?php echo number_format($business->avg_rating, 1); ?>/5</td>
                <td>
                    <span class="mp-status mp-status-<?php echo esc_attr($business->business_status); ?>">
                        <?php echo ucfirst(esc_html($business->business_status)); ?>
                    </span>
                </td>
                <td>
                    <span class="mp-trust-indicator mp-trust-<?php echo esc_attr($trust_color); ?>">
                        <?php 
                        $icons = ['green' => '🛒', 'amber' => '🚶', 'red' => '⚠️'];
                        echo $icons[$trust_color] ?? '❓';
                        ?>
                    </span>
                </td>
                <td>
                    <?php if ($business->business_status !== 'active'): ?>
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('mp_admin_nonce'); ?>
                        <input type="hidden" name="business_id" value="<?php echo esc_attr($business->business_id); ?>">
                        <input type="hidden" name="mp_business_action" value="verify">
                        <button type="submit" class="button button-primary button-small"><?php _e('Verify', 'myprotector-platform'); ?></button>
                    </form>
                    <?php endif; ?>
                    <?php if ($business->business_status === 'active'): ?>
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('mp_admin_nonce'); ?>
                        <input type="hidden" name="business_id" value="<?php echo esc_attr($business->business_id); ?>">
                        <input type="hidden" name="mp_business_action" value="suspend">
                        <input type="text" name="suspension_reason" placeholder="<?php _e('Reason', 'myprotector-platform'); ?>" style="width: 100px;">
                        <button type="submit" class="button button-secondary button-small"><?php _e('Suspend', 'myprotector-platform'); ?></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.mp-verified { color: #00a32a; margin-left: 5px; }
.mp-trust-indicator { font-size: 18px; }
</style>