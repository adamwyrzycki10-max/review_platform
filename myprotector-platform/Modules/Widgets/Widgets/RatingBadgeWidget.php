<?php
/**
 * MyProtector Platform - Rating Badge Widget
 * 
 * WordPress widget for displaying rating badges
 * 
 * @package MyProtector\Modules\Widgets\Widgets
 * @version 1.0.0
 */

namespace MyProtector\Modules\Widgets\Widgets;

class RatingBadgeWidget extends \WP_Widget {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'mp_rating_badge',
            __('MyProtector Rating Badge', 'myprotector-platform'),
            [
                'description' => __('Display a rating badge for a business', 'myprotector-platform'),
                'classname' => 'mp-widget-rating-badge',
            ]
        );
    }

    /**
     * Widget output
     * 
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance): void {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $business_id = $instance['business_id'] ?? 0;
        $style = $instance['style'] ?? 'compact';
        
        echo '<div class="mp-rating-badge-widget" data-business-id="' . esc_attr($business_id) . '" data-style="' . esc_attr($style) . '">';
        echo '<p>' . esc_html__('Rating badge will be displayed here.', 'myprotector-platform') . '</p>';
        echo '</div>';
        
        echo $args['after_widget'];
    }

    /**
     * Widget form in admin
     * 
     * @param array $instance
     * @return void
     */
    public function form($instance): void {
        $title = $instance['title'] ?? '';
        $business_id = $instance['business_id'] ?? 0;
        $style = $instance['style'] ?? 'compact';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'myprotector-platform'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('business_id'); ?>"><?php esc_html_e('Business ID:', 'myprotector-platform'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('business_id'); ?>" name="<?php echo $this->get_field_name('business_id'); ?>" type="number" value="<?php echo esc_attr($business_id); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('style'); ?>"><?php esc_html_e('Style:', 'myprotector-platform'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
                <option value="compact" <?php selected($style, 'compact'); ?>><?php esc_html_e('Compact', 'myprotector-platform'); ?></option>
                <option value="full" <?php selected($style, 'full'); ?>><?php esc_html_e('Full', 'myprotector-platform'); ?></option>
                <option value="badge" <?php selected($style, 'badge'); ?>><?php esc_html_e('Badge', 'myprotector-platform'); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Update widget instance
     * 
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance): array {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['business_id'] = absint($new_instance['business_id'] ?? 0);
        $instance['style'] = sanitize_text_field($new_instance['style'] ?? 'compact');
        return $instance;
    }
}