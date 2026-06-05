# MyProtector Platform - Complete WordPress Implementation Guide

## Table of Contents
1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Database Setup](#database-setup)
5. [Plugin Configuration](#plugin-configuration)
6. [WooCommerce Integration](#woocommerce-integration)
7. [Using Shortcodes](#using-shortcodes)
8. [REST API Documentation](#rest-api-documentation)
9. [Admin Panel Guide](#admin-panel-guide)
10. [Troubleshooting](#troubleshooting)

---

## Overview

**MyProtector Platform** is a Trustpilot-style SaaS review platform for WordPress with:
- Traffic Light Trust System (GREEN/AMBER/RED)
- Business reviews with moderation
- WooCommerce subscription integration ($50/month)
- REST API for developers
- Admin dashboard for management

---

## Prerequisites

- WordPress 6.0+
- PHP 8.0+
- WooCommerce (optional, for subscriptions)
- MySQL 5.7+ or MariaDB 10.3+

---

## Installation

### Method 1: Upload via WordPress Admin

1. **Download the plugin** as a ZIP file
2. Go to **WordPress Admin > Plugins > Add New**
3. Click **Upload Plugin** at the top
4. Select the ZIP file and click **Install Now**
5. Activate the plugin

### Method 2: FTP/SFTP Upload

1. Upload the `myprotector-platform` folder to `/wp-content/plugins/`
2. Go to **WordPress Admin > Plugins**
3. Find "MyProtector Platform" and click **Activate**

### Method 3: WP-CLI

```bash
wp plugin install /path/to/myprotector-platform.zip --activate
```

---

## Database Setup

### Option A: Auto-Installation (Recommended)

When you activate the plugin, it automatically creates all required tables:
- `wp_mp_reviews`
- `wp_mp_businesses`
- `wp_mp_resellers`
- `wp_mp_traffic_signals`
- `wp_mp_commissions`
- `wp_mp_notifications`
- `wp_mp_email_logs`

### Option B: Manual Installation

1. Open phpMyAdmin or MySQL CLI
2. Select your WordPress database
3. Import the SQL file:

```bash
mysql -u username -p database_name < database/step-1-tables.sql
```

Or run the SQL commands from `database/step-1-tables.sql` manually.

---

## Plugin Configuration

### 1. Access Settings

Navigate to: **WordPress Admin > MyProtector > Settings**

### 2. General Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Subscription Price | Monthly cost for business plan | $50 |
| Minimum Reviews | Reviews needed for GREEN status | 5 |
| Minimum Rating | Avg rating needed for GREEN status | 3.5 |
| Require Approval | Reviews need admin approval | Yes |
| Email Notifications | Send email on events | Yes |

### 3. WooCommerce Setup (Required for Subscriptions)

1. Install WooCommerce if not already installed
2. Go to **MyProtector > WooCommerce**
3. The plugin auto-creates a subscription product
4. Note the product ID for customizations

---

## WooCommerce Integration

### Creating Subscription Product Manually

If WooCommerce Subscriptions is installed:

1. Go to **Products > Add New**
2. Product Type: **Subscription**
3. Name: "MyProtector Business Subscription"
4. Price: $50/month
5. Save the product ID

### Connecting to Plugin

Update the option or add to `wp-config.php`:

```php
// Option 1: Database
update_option('mp_woocommerce_subscription_product_id', YOUR_PRODUCT_ID);

// Option 2: wp-config.php
define('MP_WOOCOMMERCE_SUBSCRIPTION_PRODUCT_ID', YOUR_PRODUCT_ID);
```

### Subscription Hooks

The plugin hooks into WooCommerce events:

| Event | Action |
|-------|--------|
| `woocommerce_subscription_status_active` | Updates traffic signal to GREEN (if requirements met) |
| `woocommerce_subscription_status_cancelled` | Recalculates traffic signal |
| `woocommerce_subscription_status_expired` | Sends expiration notification |
| `woocommerce_order_completed` | Triggers review invitation email |

---

## Using Shortcodes

### 1. Business Profile Page

Display a complete business profile with reviews and trust signal:

```
[mp_business_profile id="123"]
[mp_business_profile slug="business-name"]
```

**Parameters:**
- `id` - Business ID (integer)
- `slug` - Business URL slug (string)

### 2. Business Directory/List

Show a list of all verified businesses:

```
[mp_business_list category="" limit="12" orderby="avg_rating" order="DESC"]
```

**Parameters:**
- `category` - Category ID (integer, optional)
- `limit` - Number of businesses (default: 12)
- `orderby` - Sort field: `avg_rating`, `total_reviews`, `created_at`
- `order` - Sort direction: `DESC`, `ASC`
- `show_filters` - Show category/rating filters: `true`, `false`

### 3. Reviews List

Display reviews for a specific business:

```
[mp_reviews business_id="123" limit="10" sort="recent"]
```

**Parameters:**
- `business_id` - Business ID (required)
- `limit` - Number of reviews (default: 10)
- `sort` - Sort order: `recent`, `highest`, `lowest`, `helpful`

### 4. Trust Signal Widget

Show only the traffic light trust indicator:

```
[mp_trust_signal business_id="123" style="standard" show_checklist="true"]
```

**Parameters:**
- `business_id` - Business ID (required)
- `style` - Display style: `standard`, `compact`, `badge_only`
- `show_checklist` - Show requirements checklist: `true`, `false`

### 5. Rating Badge

Compact rating display:

```
[mp_rating_badge business_id="123" style="compact" size="medium"]
```

**Parameters:**
- `business_id` - Business ID (required)
- `style` - Style: `compact`, `full`, `badge`
- `size` - Size: `small`, `medium`, `large`

### 6. Search Widget

Search businesses:

```
[mp_search placeholder="Search businesses..." show_category_filter="true"]
```

**Parameters:**
- `placeholder` - Input placeholder text
- `show_category_filter` - Show category dropdown: `true`, `false`

---

## REST API Documentation

### Base URL
```
https://yoursite.com/wp-json/mp/v1/
```

### Authentication
- Public endpoints: No authentication required
- Protected endpoints: WordPress cookie authentication or Application Password

### Reviews

#### Get All Reviews
```
GET /reviews?status=approved&per_page=20&page=1
```

#### Create Review
```
POST /reviews
Headers: Content-Type: application/json
Body: {
  "nonce": "xxx",
  "business_id": 123,
  "rating": 5,
  "review_title": "Great service!",
  "review_content": "Detailed review content here..."
}
```

#### Approve Review (Admin)
```
POST /reviews/approve
Body: { "review_id": 123 }
```

#### Reject Review (Admin)
```
POST /reviews/reject
Body: { "review_id": 123, "reason": "Inappropriate content" }
```

### Businesses

#### Get All Businesses
```
GET /businesses?category=1&min_rating=4&trust=green&per_page=20
```

#### Get Single Business
```
GET /businesses/123
```

#### Update Business
```
POST /businesses/123
Body: {
  "business_name": "New Name",
  "insurance_url": "https://...",
  "terms_url": "https://...",
  "promise_page_url": "https://..."
}
```

#### Get Business by Slug
```
GET /businesses/slug/business-name
```

### Traffic Signals

#### Get Traffic Signal
```
GET /traffic-signals/123
```

#### Override Traffic Signal (Admin)
```
POST /traffic-signals/123/override
Body: {
  "status": "green",
  "reason": "Manual verification completed"
}
```

### Dashboard

#### Get User's Reviews
```
GET /dashboard/reviews
```

#### Get Business Stats
```
GET /dashboard/business-stats/123
```

---

## Admin Panel Guide

### Accessing Admin Panel

Navigate to: **WordPress Admin > MyProtector**

### Dashboard

Overview with:
- Total reviews / Pending reviews
- Total businesses / Active businesses
- Recent pending reviews requiring moderation

### Reviews Management

**URL:** `admin.php?page=mp-reviews`

Features:
- Filter by status (Pending/Approved/Rejected)
- Search reviews
- Bulk actions (Approve/Delete)
- Individual approve/reject with reason

### Businesses Management

**URL:** `admin.php?page=mp-businesses`

Features:
- View all businesses
- Filter by status
- Verify businesses
- Suspend businesses (with reason)

### Trust Signals

**URL:** `admin.php?page=mp-traffic-signals`

Features:
- View all traffic signals
- See requirement checklist
- Admin override with reason
- View improvement tips

### Resellers

**URL:** `admin.php?page=mp-resellers`

Features:
- View reseller list
- Commission tier display
- Referral tracking

### Commissions

**URL:** `admin.php?page=mp-commissions`

Features:
- View all commission records
- Pending total display
- Commission type/status

### Settings

**URL:** `admin.php?page=mp-settings`

Configurable options:
- Subscription price
- Minimum reviews/rating for GREEN
- Review approval requirement
- Email notifications toggle

---

## Troubleshooting

### Plugin Not Activating

**Error:** "Plugin could not be activated"

**Solutions:**
1. Check PHP version (requires 8.0+)
2. Check WordPress version (requires 6.0+)
3. Verify MySQL user has CREATE TABLE permissions

### Database Tables Not Created

Run the activation manually:

```php
// Add to theme's functions.php or use WP-CLI
require_once WP_PLUGIN_DIR . '/myprotector-platform/Core/Activator.php';
\MyProtector\Core\Activator::activate();
```

### Reviews Not Showing

1. Check reviews are approved (status = 'approved')
2. Verify business_id matches
3. Check review_rating >= minimum setting

### Traffic Signal Not Updating

1. Ensure business has all requirements:
   - Insurance URL
   - Terms URL
   - Promise Page URL
   - Active WooCommerce subscription

2. Manually recalculate:
```php
$service = new \MyProtector\Services\TrafficSignal\TrafficSignalService();
$service->calculate(BUSINESS_ID);
```

### WooCommerce Not Detected

1. Install WooCommerce plugin
2. Install WooCommerce Subscriptions extension
3. Check for JavaScript errors in browser console

### API Returns 403 Forbidden

1. Ensure user is logged in for protected endpoints
2. Check nonce is valid and not expired
3. Verify user has required capabilities

---

## Customization

### Adding Custom Trust Requirements

Edit `TrafficSignalService.php` weights:

```php
protected $weights = [
    'has_min_reviews' => 20,
    'has_min_rating' => 20,
    'has_verified_domain' => 15,
    'has_insurance' => 20,
    'has_terms' => 15,
    'has_promise_page' => 10,
    // Add your custom requirement here:
    'has_ssl_certificate' => 5,
];
```

### Custom Email Templates

Add to `wp_mp_email_templates` table:

```sql
INSERT INTO wp_mp_email_templates 
(template_key, template_name, template_subject, template_body, template_type, is_active)
VALUES 
('custom_trigger', 'Custom Email', 'Subject', 'Body with {{variable}}', 'notification', 1);
```

### Hooks & Filters

Available WordPress hooks:

```php
// Review hooks
do_action('mp_review_submitted', $review_id);
do_action('mp_review_approved', $review_id);
do_action('mp_review_rejected', $review_id);

// Trust signal hooks
do_action('mp_trust_status_updated', $business_id, $new_status);

// Subscription hooks
do_action('mp_subscription_activated', $subscription_id);
do_action('mp_subscription_cancelled', $subscription_id);

// Example usage in theme/plugin:
add_action('mp_review_approved', function($review_id) {
    // Send push notification, Slack message, etc.
});
```

---

## Support

For issues or feature requests, please contact the development team.

---

*Document Version: 1.0.0*
*Last Updated: 2026-06-03*