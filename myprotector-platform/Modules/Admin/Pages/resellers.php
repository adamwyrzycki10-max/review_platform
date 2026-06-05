<?php
/**
 * MyProtector Platform - Admin Resellers Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('Reseller Management', 'myprotector-platform'); ?></h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Company', 'myprotector-platform'); ?></th>
                <th><?php _e('Contact', 'myprotector-platform'); ?></th>
                <th><?php _e('Referral Code', 'myprotector-platform'); ?></th>
                <th><?php _e('Tier', 'myprotector-platform'); ?></th>
                <th><?php _e('Referrals', 'myprotector-platform'); ?></th>
                <th><?php _e('Earnings', 'myprotector-platform'); ?></th>
                <th><?php _e('Status', 'myprotector-platform'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($resellers)): ?>
            <tr>
                <td colspan="7" style="text-align: center;"><?php _e('No resellers found.', 'myprotector-platform'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($resellers as $reseller): ?>
            <tr>
                <td><strong><?php echo esc_html($reseller->company_name ?? 'N/A'); ?></strong></td>
                <td>
                    <?php echo esc_html($reseller->user_name ?? 'N/A'); ?><br>
                    <small><?php echo esc_html($reseller->user_email ?? ''); ?></small>
                </td>
                <td><code><?php echo esc_html($reseller->referral_code); ?></code></td>
                <td>
                    <span class="mp-tier mp-tier-<?php echo esc_attr($reseller->commission_tier); ?>">
                        <?php echo ucfirst(esc_html($reseller->commission_tier)); ?>
                    </span>
                    <br><small><?php echo esc_html($reseller->commission_rate); ?>% commission</small>
                </td>
                <td>
                    <?php echo esc_html($reseller->total_referrals); ?> referrals<br>
                    <small><?php echo esc_html($reseller->total_clicks); ?> clicks</small>
                </td>
                <td>
                    <?php echo wc_price($reseller->total_earnings); ?><br>
                    <small>Pending: <?php echo wc_price($reseller->pending_earnings); ?></small>
                </td>
                <td>
                    <span class="mp-status mp-status-<?php echo esc_attr($reseller->reseller_status); ?>">
                        <?php echo ucfirst(esc_html($reseller->reseller_status)); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.mp-tier { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
.mp-tier-standard { background: #f0f6fc; color: #1d2327; }
.mp-tier-silver { background: #c0c0c0; color: #333; }
.mp-tier-gold { background: #ffd700; color: #333; }
.mp-tier-platinum { background: #e5e4e2; color: #1d2327; }
</style>