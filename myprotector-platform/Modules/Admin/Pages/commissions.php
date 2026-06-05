<?php
/**
 * MyProtector Platform - Admin Commissions Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('Commission Management', 'myprotector-platform'); ?></h1>
    
    <div class="mp-commission-stats" style="margin: 20px 0; display: flex; gap: 20px;">
        <div class="mp-stat-card">
            <h3><?php echo wc_price($pending_total ?? 0); ?></h3>
            <p><?php _e('Pending Commissions', 'myprotector-platform'); ?></p>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Reseller', 'myprotector-platform'); ?></th>
                <th><?php _e('Business', 'myprotector-platform'); ?></th>
                <th><?php _e('Type', 'myprotector-platform'); ?></th>
                <th><?php _e('Amount', 'myprotector-platform'); ?></th>
                <th><?php _e('Rate', 'myprotector-platform'); ?></th>
                <th><?php _e('Status', 'myprotector-platform'); ?></th>
                <th><?php _e('Date', 'myprotector-platform'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($commissions)): ?>
            <tr>
                <td colspan="7" style="text-align: center;"><?php _e('No commissions found.', 'myprotector-platform'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($commissions as $commission): ?>
            <tr>
                <td><?php echo esc_html($commission->reseller_name ?? 'N/A'); ?></td>
                <td><?php echo esc_html($commission->business_name ?? 'N/A'); ?></td>
                <td><?php echo ucfirst(esc_html($commission->commission_type)); ?></td>
                <td><?php echo wc_price($commission->commission_amount); ?></td>
                <td><?php echo esc_html($commission->commission_rate); ?>%</td>
                <td>
                    <span class="mp-status mp-status-<?php echo esc_attr($commission->commission_status); ?>">
                        <?php echo ucfirst(esc_html($commission->commission_status)); ?>
                    </span>
                </td>
                <td><?php echo esc_html(date_i18n('M j, Y', strtotime($commission->created_at))); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>