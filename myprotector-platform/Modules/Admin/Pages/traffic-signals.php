<?php
/**
 * MyProtector Platform - Admin Traffic Signals Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('Trust Signals Management', 'myprotector-platform'); ?></h1>
    
    <p><?php _e('View and manage trust signals for all businesses. You can override signals manually if needed.', 'myprotector-platform'); ?></p>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Business', 'myprotector-platform'); ?></th>
                <th><?php _e('Status', 'myprotector-platform'); ?></th>
                <th><?php _e('Score', 'myprotector-platform'); ?></th>
                <th><?php _e('Requirements', 'myprotector-platform'); ?></th>
                <th><?php _e('Manual Override', 'myprotector-platform'); ?></th>
                <th><?php _e('Actions', 'myprotector-platform'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($signals)): ?>
            <tr>
                <td colspan="6" style="text-align: center;"><?php _e('No traffic signals found.', 'myprotector-platform'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($signals as $business_id => $signal): 
                $business = $businessModel->get($business_id);
                if (!$business) continue;
            ?>
            <tr>
                <td><strong><?php echo esc_html($business->business_name); ?></strong></td>
                <td>
                    <span class="mp-trust-indicator mp-trust-<?php echo esc_attr($signal['status']); ?>">
                        <?php 
                        $icons = ['green' => '🛒', 'amber' => '🚶', 'red' => '⚠️'];
                        echo ($icons[$signal['status']] ?? '❓') . ' ';
                        echo esc_html($signal['label'] ?? '');
                        ?>
                    </span>
                </td>
                <td>
                    <div class="mp-score-bar">
                        <div class="mp-score-fill mp-score-<?php echo esc_attr($signal['status']); ?>" 
                             style="width: <?php echo esc_attr($signal['score']); ?>%"></div>
                    </div>
                    <span class="mp-score-text"><?php echo esc_html($signal['score']); ?>%</span>
                </td>
                <td>
                    <?php echo esc_html($signal['fulfilled']); ?>/<?php echo esc_html($signal['total']); ?>
                    <?php if (!empty($signal['improvement_tips'])): ?>
                    <details style="margin-top: 5px;">
                        <summary style="cursor: pointer; font-size: 12px;"><?php _e('View tips', 'myprotector-platform'); ?></summary>
                        <ul style="margin: 5px 0 0 15px; font-size: 12px;">
                            <?php foreach ($signal['improvement_tips'] as $tip): ?>
                            <li><?php echo esc_html($tip); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($signal['manual_override']): ?>
                    <span class="mp-override-badge"><?php _e('Overridden', 'myprotector-platform'); ?></span>
                    <?php if (!empty($signal['override_reason'])): ?>
                    <br><small><?php echo esc_html($signal['override_reason']); ?></small>
                    <?php endif; ?>
                    <?php else: ?>
                    <span style="color: #646970;"><?php _e('Auto', 'myprotector-platform'); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" class="button button-small mp-override-btn" 
                            data-business-id="<?php echo esc_attr($business_id); ?>"
                            data-current-status="<?php echo esc_attr($signal['status']); ?>">
                        <?php _e('Override', 'myprotector-platform'); ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Override Modal -->
<div id="mp-override-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border: 1px solid #ccc; z-index: 10000; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
    <h2><?php _e('Override Trust Signal', 'myprotector-platform'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('mp_admin_nonce'); ?>
        <input type="hidden" name="business_id" id="mp-override-business-id" value="">
        <input type="hidden" name="mp_signal_override" value="1">
        
        <p>
            <label><?php _e('Status:', 'myprotector-platform'); ?></label><br>
            <select name="trust_status" id="mp-override-status" style="width: 100%;">
                <option value="green">🛒 Green - Shopping Safe</option>
                <option value="amber">🚶 Amber - Walking Safe</option>
                <option value="red">⚠️ Red - Caution</option>
            </select>
        </p>
        <p>
            <label><?php _e('Reason:', 'myprotector-platform'); ?></label><br>
            <input type="text" name="override_reason" id="mp-override-reason" style="width: 100%;" required>
        </p>
        <p>
            <button type="submit" class="button button-primary"><?php _e('Apply Override', 'myprotector-platform'); ?></button>
            <button type="button" class="button mp-close-modal"><?php _e('Cancel', 'myprotector-platform'); ?></button>
        </p>
    </form>
</div>

<style>
.mp-score-bar { width: 100px; height: 8px; background: #dcdcde; border-radius: 4px; display: inline-block; vertical-align: middle; }
.mp-score-fill { height: 100%; border-radius: 4px; }
.mp-score-green { background: #00a32a; }
.mp-score-amber { background: #d63638; }
.mp-score-red { background: #1d2327; }
.mp-score-text { font-size: 12px; margin-left: 5px; }
.mp-override-badge { background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; }
</style>

<script>
jQuery(document).ready(function($) {
    $('.mp-override-btn').on('click', function() {
        var businessId = $(this).data('business-id');
        var currentStatus = $(this).data('current-status');
        
        $('#mp-override-business-id').val(businessId);
        $('#mp-override-status').val(currentStatus);
        $('#mp-override-modal').show();
    });
    
    $('.mp-close-modal').on('click', function() {
        $('#mp-override-modal').hide();
    });
});
</script>