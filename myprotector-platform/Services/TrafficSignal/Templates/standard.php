<?php
/**
 * Standard traffic signal template
 * 
 * @var array $data
 * @var array $args
 */

if (!defined('ABSPATH')) exit;

$status_colors = [
    'green' => '#10B981',
    'amber' => '#F59E0B',
    'red' => '#EF4444',
];
$color = $status_colors[$data['status']] ?? '#EF4444';
?>
<div class="mp-traffic-signal mp-traffic-<?php echo esc_attr($data['status']); ?> mp-traffic-<?php echo esc_attr($args['size'] ?? 'medium'); ?>">
    <div class="mp-traffic-signal-header">
        <div class="mp-traffic-light-icon" style="background: <?php echo esc_attr($color); ?>;">
            <span><?php echo esc_html($data['icon']); ?></span>
        </div>
        <div class="mp-traffic-signal-info">
            <span class="mp-traffic-label"><?php echo esc_html($data['label']); ?></span>
            <?php if ($args['show_score']): ?>
            <span class="mp-traffic-score"><?php echo esc_html($data['score']); ?>%</span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($args['show_checklist']): ?>
    <div class="mp-traffic-checklist">
        <div class="mp-traffic-checklist-item <?php echo $data['requirements']['insurance'] ? 'mp-checked' : 'mp-unchecked'; ?>">
            <span class="mp-check-icon"><?php echo $data['requirements']['insurance'] ? '✓' : '✗'; ?></span>
            <span>Insurance</span>
        </div>
        <div class="mp-traffic-checklist-item <?php echo $data['requirements']['terms'] ? 'mp-checked' : 'mp-unchecked'; ?>">
            <span class="mp-check-icon"><?php echo $data['requirements']['terms'] ? '✓' : '✗'; ?></span>
            <span>Terms & Conditions</span>
        </div>
        <div class="mp-traffic-checklist-item <?php echo $data['requirements']['promise_page'] ? 'mp-checked' : 'mp-unchecked'; ?>">
            <span class="mp-check-icon"><?php echo $data['requirements']['promise_page'] ? '✓' : '✗'; ?></span>
            <span>Promise Page</span>
        </div>
        <div class="mp-traffic-checklist-item <?php echo $data['requirements']['subscription'] ? 'mp-checked' : 'mp-unchecked'; ?>">
            <span class="mp-check-icon"><?php echo $data['requirements']['subscription'] ? '✓' : '✗'; ?></span>
            <span>Active Subscription</span>
        </div>
        <div class="mp-traffic-checklist-item <?php echo $data['requirements']['min_reviews'] ? 'mp-checked' : 'mp-unchecked'; ?>">
            <span class="mp-check-icon"><?php echo $data['requirements']['min_reviews'] ? '✓' : '✗'; ?></span>
            <span>Minimum Reviews</span>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="mp-traffic-progress">
        <div class="mp-traffic-progress-bar" style="width: <?php echo esc_attr($data['percentage']); ?>%; background: <?php echo esc_attr($color); ?>;"></div>
    </div>
    <div class="mp-traffic-meta">
        <?php echo esc_html($data['fulfilled']); ?>/<?php echo esc_html($data['total']); ?> requirements met
    </div>
</div>