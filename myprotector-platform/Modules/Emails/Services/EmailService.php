<?php
/**
 * MyProtector Platform - Email Service
 * 
 * Handles email sending and template management
 * 
 * @package MyProtector\Modules\Emails\Services
 * @version 1.0.0
 */

namespace MyProtector\Modules\Emails\Services;

class EmailService {
    /**
     * WordPress database object
     * 
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Send an email
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array $headers
     * @return bool
     */
    public function send(string $to, string $subject, string $message, array $headers = []): bool {
        // Add default headers
        $default_headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>',
        ];
        
        $headers = array_merge($default_headers, $headers);
        
        $result = wp_mail($to, $subject, $message, $headers);
        
        // Log the email
        $this->logEmail($to, $subject, $message, $result ? 'sent' : 'failed');
        
        return $result;
    }

    /**
     * Send template email
     * 
     * @param string $to
     * @param string $template_key
     * @param array $variables
     * @return bool
     */
    public function sendTemplate(string $to, string $template_key, array $variables = []): bool {
        $template = $this->getTemplate($template_key);
        
        if (!$template) {
            return false;
        }
        
        // Replace variables
        $subject = $this->replaceVariables($template->template_subject, $variables);
        $message = $this->replaceVariables($template->template_body, $variables);
        
        return $this->send($to, $subject, nl2br($message));
    }

    /**
     * Get email template
     * 
     * @param string $key
     * @return object|null
     */
    public function getTemplate(string $key) {
        // Check cache first
        $cache_key = 'mp_email_template_' . $key;
        $cached = wp_cache_get($cache_key, 'mp_email_templates');
        
        if ($cached !== false) {
            return $cached;
        }
        
        $template = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}mp_email_templates WHERE template_key = %s AND is_active = 1",
                $key
            )
        );
        
        if ($template) {
            wp_cache_set($cache_key, $template, 'mp_email_templates', 3600);
        }
        
        return $template;
    }

    /**
     * Replace template variables
     * 
     * @param string $content
     * @param array $variables
     * @return string
     */
    public function replaceVariables(string $content, array $variables): string {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Log email
     * 
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $status
     * @return void
     */
    protected function logEmail(string $to, string $subject, string $body, string $status): void {
        $this->wpdb->insert(
            $this->wpdb->prefix . 'mp_email_logs',
            [
                'email_id' => wp_generate_uuid4(),
                'recipient_email' => $to,
                'email_subject' => $subject,
                'email_body_html' => $body,
                'email_template' => 'custom',
                'send_status' => $status,
                'sent_at' => current_time('mysql'),
                'created_at' => current_time('mysql'),
            ]
        );
    }

    /**
     * Send review submitted notification
     * 
     * @param int $review_id
     * @return void
     */
    public function onReviewSubmitted(int $review_id): void {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $review = $reviewModel->get($review_id);
        
        if (!$review) {
            return;
        }
        
        $businessModel = new \MyProtector\Models\BusinessModel();
        $business = $businessModel->get($review->business_id);
        
        // Notify business owner
        if ($business && $business->user_id) {
            $user = get_user_by('id', $business->user_id);
            
            if ($user) {
                $this->sendTemplate($user->user_email, 'business_new_review', [
                    'user_name' => $user->display_name,
                    'business_name' => $business->business_name,
                    'review_title' => $review->review_title,
                    'review_rating' => $review->review_rating,
                    'reviewer_name' => $review->reviewer_name ?? 'A customer',
                ]);
            }
        }
    }

    /**
     * Send review approved notification
     * 
     * @param int $review_id
     * @return void
     */
    public function onReviewApproved(int $review_id): void {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $review = $reviewModel->get($review_id);
        
        if (!$review) {
            return;
        }
        
        $user = get_user_by('id', $review->user_id);
        
        if ($user) {
            $businessModel = new \MyProtector\Models\BusinessModel();
            $business = $businessModel->get($review->business_id);
            
            $this->sendTemplate($user->user_email, 'review_approved', [
                'user_name' => $user->display_name,
                'business_name' => $business ? $business->business_name : 'The business',
                'review_title' => $review->review_title,
            ]);
        }
    }

    /**
     * Send review rejected notification
     * 
     * @param int $review_id
     * @return void
     */
    public function onReviewRejected(int $review_id): void {
        $reviewModel = new \MyProtector\Models\ReviewModel();
        $review = $reviewModel->get($review_id);
        
        if (!$review) {
            return;
        }
        
        $user = get_user_by('id', $review->user_id);
        
        if ($user) {
            $businessModel = new \MyProtector\Models\BusinessModel();
            $business = $businessModel->get($review->business_id);
            
            $this->sendTemplate($user->user_email, 'review_rejected', [
                'user_name' => $user->display_name,
                'business_name' => $business ? $business->business_name : 'The business',
                'review_title' => $review->review_title,
            ]);
        }
    }

    /**
     * Send business registered notification
     * 
     * @param int $business_id
     * @return void
     */
    public function onBusinessRegistered(int $business_id): void {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $business = $businessModel->get($business_id);
        
        if (!$business || !$business->user_id) {
            return;
        }
        
        $user = get_user_by('id', $business->user_id);
        
        if ($user) {
            $this->sendTemplate($user->user_email, 'business_registered', [
                'user_name' => $user->display_name,
                'business_name' => $business->business_name,
            ]);
        }
    }

    /**
     * Send trust status update notification
     * 
     * @param int $business_id
     * @param string $new_status
     * @return void
     */
    public function onTrustStatusUpdate(int $business_id, string $new_status): void {
        $businessModel = new \MyProtector\Models\BusinessModel();
        $business = $businessModel->get($business_id);
        
        if (!$business || !$business->user_id) {
            return;
        }
        
        $user = get_user_by('id', $business->user_id);
        
        if ($user) {
            $status_labels = [
                'green' => 'Shopping Safe',
                'amber' => 'Walking Safe',
                'red' => 'Caution',
            ];
            
            $this->sendTemplate($user->user_email, 'trust_status_update', [
                'user_name' => $user->display_name,
                'business_name' => $business->business_name,
                'trust_status' => $status_labels[$new_status] ?? $new_status,
            ]);
        }
    }
}