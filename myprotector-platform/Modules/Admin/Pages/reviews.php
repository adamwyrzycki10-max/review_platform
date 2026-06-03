<?php
/**
 * MyProtector Platform - Admin Reviews Page
 * 
 * @package MyProtector\Modules\Admin\Pages
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap mp-admin-wrap">
    <h1><?php _e('Reviews Management', 'myprotector-platform'); ?></h1>
    
    <!-- Filters -->
    <form method="get" style="margin: 20px 0;">
        <input type="hidden" name="page" value="mp-reviews">
        <select name="status">
            <option value=""><?php _e('All Status', 'myprotector-platform'); ?></option>
            <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'myprotector-platform'); ?></option>
            <option value="approved" <?php selected($status, 'approved'); ?>><?php _e('Approved', 'myprotector-platform'); ?></option>
            <option value="rejected" <?php selected($status, 'rejected'); ?>><?php _e('Rejected', 'myprotector-platform'); ?></option>
        </select>
        <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Search reviews...', 'myprotector-platform'); ?>">
        <button type="submit" class="button"><?php _e('Filter', 'myprotector-platform'); ?></button>
    </form>
    
    <!-- Bulk Actions -->
    <form id="mp-reviews-form" method="post">
        <?php wp_nonce_field('mp_admin_nonce'); ?>
        <div class="mp-bulk-actions" style="margin: 10px 0;">
            <select name="bulk_action" id="bulk_action">
                <option value=""><?php _e('Bulk Actions', 'myprotector-platform'); ?></option>
                <option value="approve"><?php _e('Approve', 'myprotector-platform'); ?></option>
                <option value="delete"><?php _e('Delete', 'myprotector-platform'); ?></option>
            </select>
            <button type="button" class="button" id="mp-bulk-apply"><?php _e('Apply', 'myprotector-platform'); ?></button>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="manage-column column-cb check-column">
                        <input type="checkbox" id="mp-select-all">
                    </th>
                    <th><?php _e('Business', 'myprotector-platform'); ?></th>
                    <th><?php _e('Reviewer', 'myprotector-platform'); ?></th>
                    <th><?php _e('Title', 'myprotector-platform'); ?></th>
                    <th><?php _e('Rating', 'myprotector-platform'); ?></th>
                    <th><?php _e('Status', 'myprotector-platform'); ?></th>
                    <th><?php _e('Date', 'myprotector-platform'); ?></th>
                    <th><?php _e('Actions', 'myprotector-platform'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;"><?php _e('No reviews found.', 'myprotector-platform'); ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" name="review_ids[]" value="<?php echo esc_attr($review->review_id); ?>">
                    </th>
                    <td><?php echo esc_html($review->business_name ?? 'N/A'); ?></td>
                    <td><?php echo esc_html($review->reviewer_name ?? 'Anonymous'); ?></td>
                    <td><?php echo esc_html($review->review_title); ?></td>
                    <td><?php echo esc_html($review->review_rating); ?>/5</td>
                    <td>
                        <span class="mp-status mp-status-<?php echo esc_attr($review->review_status); ?>">
                            <?php echo ucfirst(esc_html($review->review_status)); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(date_i18n('M j, Y', strtotime($review->created_at))); ?></td>
                    <td>
                        <?php if ($review->review_status === 'pending'): ?>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('mp_admin_nonce'); ?>
                            <input type="hidden" name="review_id" value="<?php echo esc_attr($review->review_id); ?>">
                            <input type="hidden" name="mp_action" value="approve">
                            <button type="submit" class="button button-primary button-small"><?php _e('Approve', 'myprotector-platform'); ?></button>
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('mp_admin_nonce'); ?>
                            <input type="hidden" name="review_id" value="<?php echo esc_attr($review->review_id); ?>">
                            <input type="hidden" name="mp_action" value="reject">
                            <input type="text" name="rejection_reason" placeholder="<?php _e('Reason', 'myprotector-platform'); ?>" style="width: 100px;">
                            <button type="submit" class="button button-secondary button-small"><?php _e('Reject', 'myprotector-platform'); ?></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
    
    <!-- Pagination -->
    <?php if ($total > $per_page): ?>
    <div class="mp-pagination" style="margin-top: 20px;">
        <?php
        $total_pages = ceil($total / $per_page);
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($page === $i) ? 'button-primary' : '';
            echo '<a href="' . add_query_arg('paged', $i) . '" class="button ' . esc_attr($active) . '">' . $i . '</a> ';
        }
        ?>
    </div>
    <?php endif; ?>
</div>

<style>
.mp-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
.mp-status-pending { background: #f0f6fc; color: #d63638; }
.mp-status-approved { background: #d4edda; color: #00a32a; }
.mp-status-rejected { background: #f8d7da; color: #721c24; }
.mp-pagination { display: flex; gap: 5px; }
</style>

<script>
jQuery(document).ready(function($) {
    $('#mp-select-all').on('change', function() {
        $('input[name="review_ids[]"]').prop('checked', $(this).prop('checked'));
    });
    
    $('#mp-bulk-apply').on('click', function() {
        var action = $('#bulk_action').val();
        if (!action) {
            alert('<?php _e('Please select an action', 'myprotector-platform'); ?>');
            return;
        }
        
        var checked = $('input[name="review_ids[]"]:checked').length;
        if (checked === 0) {
            alert('<?php _e('Please select items', 'myprotector-platform'); ?>');
            return;
        }
        
        if (confirm('<?php _e('Are you sure?', 'myprotector-platform'); ?>')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mp_admin_bulk_action',
                    nonce: '<?php echo wp_create_nonce('mp_admin_nonce'); ?>',
                    bulk_action: action,
                    review_ids: $('input[name="review_ids[]"]:checked').map(function() { return $(this).val(); }).get()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });
});
</script>