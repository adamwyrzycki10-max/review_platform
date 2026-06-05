<?php
/**
 * Compact traffic signal template
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
<div class="mp-traffic-signal mp-traffic-compact" title="<?php echo esc_attr($data['label']); ?>">
    <div class="mp-traffic-light-compact" style="background: <?php echo esc_attr($color); ?>;">
        <span><?php echo esc_html($data['icon']); ?></span>
    </div>
    <?php if ($args['show_score']): ?>
    <span class="mp-traffic-score-compact"><?php echo esc_html($data['score']); ?>%</span>
    <?php endif; ?>
</div>