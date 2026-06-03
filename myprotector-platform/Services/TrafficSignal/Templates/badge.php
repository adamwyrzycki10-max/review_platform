<?php
/**
 * Badge only traffic signal template
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
<span class="mp-traffic-badge" style="background: <?php echo esc_attr($color); ?>;">
    <?php echo esc_html($data['icon']); ?>
</span>