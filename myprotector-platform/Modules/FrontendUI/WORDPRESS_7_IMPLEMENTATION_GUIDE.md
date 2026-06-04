# MyProtector Frontend UI - WordPress 7.0 Implementation Guide

This guide provides comprehensive instructions for implementing the MyProtector Frontend UI module in WordPress 7.0. The frontend is built as a WordPress plugin module with full AJAX functionality, URL routing, and responsive design.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Plugin Structure](#plugin-structure)
4. [Page Routes and URLs](#page-routes-and-urls)
5. [Template System](#template-system)
6. [Frontend Functionality](#frontend-functionality)
7. [AJAX Handlers](#ajax-handlers)
8. [Customization](#customization)
9. [Troubleshooting](#troubleshooting)
10. [Deployment Checklist](#deployment-checklist)

---

## Prerequisites

- WordPress 7.0 or higher
- PHP 8.0 or higher
- MySQL 8.0 or higher
- HTTPS enabled (recommended for security)

---

## Installation

### Method 1: Plugin Installation

1. Upload the `myprotector-platform` folder to `/wp-content/plugins/`
2. Navigate to **Plugins** in WordPress admin
3. Activate "MyProtector Platform" plugin
4. Configure settings in **Settings > MyProtector**

### Method 2: Composer Installation (Development)

```bash
composer install
```

### Configuration

Add these constants to your `wp-config.php`:

```php
// MyProtector Configuration
define('MYPROTECTOR_COMPANY_URL', 'https://yourdomain.com');
define('MYPROTECTOR_FOUNDER_NAME', 'Adam Wyrzycki');
define('MYPROTECTOR_FOUNDER_LINKEDIN', 'https://linkedin.com/in/adamwyrzycki');
define('MYPROTECTOR_SUPPORT_EMAIL', 'support@yourdomain.com');
```

---

## Plugin Structure

```
myprotector-platform/
├── Modules/
│   └── FrontendUI/
│       ├── FrontendUI.php          # Main module class
│       ├── CustomizerSettings.php  # Customizer integration
│       ├── README.md              # Module documentation
│       ├── assets/
│       │   ├── css/
│       │   │   ├── style.css      # Main styles
│       │   │   └── frontend.css   # Additional styles
│       │   └── js/
│       │       ├── frontend.js     # Main JavaScript
│       │       └── app.js          # App initialization
│       └── templates/
│           ├── pages/             # Page templates
│           │   ├── page-home.php
│           │   ├── page-directory.php
│           │   ├── page-login.php
│           │   ├── page-register.php
│           │   ├── page-dashboard.php
│           │   ├── page-business-dashboard.php
│           │   ├── page-reseller-dashboard.php
│           │   ├── page-about.php
│           │   └── page-contact.php
│           ├── components/         # Reusable components
│           │   ├── header.php
│           │   ├── footer.php
│           │   ├── business-card.php
│           │   ├── trust-signal.php
│           │   ├── review-modal.php
│           │   ├── stars.php
│           │   └── rating-badge.php
│           └── layouts/
│               └── app.php
├── Controllers/
├── Core/
├── Models/
└── Services/
```

---

## Page Routes and URLs

The module automatically registers the following URL rewrite rules:

| Page | URL | Description |
|------|-----|-------------|
| Home | `/` | Landing page with hero, stats, trust signals |
| Directory | `/businesses` | Business listing with search and filters |
| Business Profile | `/business/{slug}` | Individual business page with reviews |
| Login | `/login` | User login form |
| Register | `/register` | User registration (individual/business) |
| Dashboard | `/dashboard` | User dashboard for reviews and settings |
| Business Dashboard | `/business-dashboard` | Business owner dashboard |
| Reseller Dashboard | `/reseller-dashboard` | Reseller management dashboard |
| About | `/about` | About page |
| Contact | `/contact` | Contact form |

### Rewrite Rules Activation

After activating the plugin, go to **Settings > Permalinks** and click "Save Changes" to flush rewrite rules.

---

## Template System

### Template Hierarchy

The module uses a custom template loading system:

1. Check for `mp_page` query var
2. Load corresponding template from `templates/pages/`
3. Fall back to WordPress theme templates

### Template Files

Each page template follows this structure:

```php
<?php
/**
 * Template Name: MyProtector [Page Name]
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get module instance for data
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$data = $frontend_ui->getMockData('key');

// Template content
?>

<div class="mp-frontend-ui">
    <!-- Page content -->
</div>

<?php
get_footer();
```

### Component Inclusion

Use `include` for template parts:

```php
// Include header component
include $frontend_ui->getPath('templates/components/header.php');

// Include custom component with data
$frontend_ui->getTemplatePart('components/trust-signal', [
    'business' => $business,
    'size' => 'large'
]);
```

---

## Frontend Functionality

### Working Buttons and Features

All buttons and interactive elements are fully functional:

#### 1. **Navigation Menu**
- Mobile hamburger menu toggle
- Desktop navigation links
- Active state highlighting

#### 2. **Search Forms**
- Hero search on homepage → redirects to `/businesses?search=query`
- Directory search with results filtering
- Category filtering

#### 3. **Filter Buttons (Directory)**
- Trust status filters: All, Green (🟢), Amber (🟡), Red (🔴)
- Updates URL query param: `?status=green`
- Client-side filtering with animation

#### 4. **Pagination**
- Numbered pagination buttons
- URL updates: `?page=2`
- Scroll to top on page change

#### 5. **Dashboard Navigation**
- Tab switching between sections
- Active nav highlighting
- Smooth scroll to top

#### 6. **Authentication Forms**
- Login form with AJAX submission
- Registration with individual/business toggle
- Lost password flow
- Password reset form

#### 7. **Share Buttons**
- Facebook share
- Twitter share
- Copy link to clipboard
- Email share

#### 8. **Write Review Modal**
- Star rating selection
- Review form submission
- Loading states
- Success/error messages

#### 9. **Settings Forms**
- Profile settings save
- Password change
- AJAX submission with notifications

### JavaScript API

The frontend exposes a global API:

```javascript
// Open review modal
MyProtectorFrontend.openReviewModal(businessId);

// Close review modal
MyProtectorFrontend.closeReviewModal();

// Show notification
MyProtectorFrontend.showMessage('Success!', 'success');

// Filter businesses
MyProtectorFrontend.filterBusinesses('green');

// Copy to clipboard
MyProtectorFrontend.copyToClipboard('https://example.com');
```

### Configuration Object

JavaScript receives configuration via `window.mpFrontendConfig`:

```javascript
{
    ajaxUrl: '/wp-admin/admin-ajax.php',
    nonce: 'abc123...',
    companyUrl: 'https://yourdomain.com',
    isLoggedIn: true,
    currentUserId: 123
}
```

---

## AJAX Handlers

### Available AJAX Actions

| Action | Description | Auth Required |
|--------|-------------|---------------|
| `mp_ajax_login` | User login | No |
| `mp_ajax_register` | User registration | No |
| `mp_ajax_lost_password` | Password reset request | No |
| `mp_ajax_reset_password` | Password reset | No |
| `mp_ajax_save_settings` | Save user settings | Yes |
| `mp_submit_review` | Submit review | Yes |
| `mp_search_businesses` | Search businesses | No |
| `mp_get_business_reviews` | Get reviews | No |
| `mp_mark_helpful` | Mark review helpful | Yes |
| `mp_respond_to_review` | Respond to review | Yes |
| `mp_contact_form` | Contact form submission | No |

### Form Implementation

Forms use hidden fields for action and nonce:

```html
<form id="my-form">
    <input type="hidden" name="action" value="mp_ajax_login">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mp_frontend_nonce'); ?>">
    <!-- Form fields -->
    <button type="submit">Submit</button>
</form>
```

### AJAX Response Format

**Success:**
```json
{
    "success": true,
    "data": {
        "message": "Operation successful",
        "redirect": "/dashboard"
    }
}
```

**Error:**
```json
{
    "success": false,
    "data": {
        "message": "Error description"
    }
}
```

---

## Customization

### CSS Customization

All design tokens are CSS custom properties:

```css
:root {
    /* Brand Colors */
    --mp-primary: #0A1F44;
    --mp-primary-light: #1A3A6E;
    
    /* Trust Colors */
    --mp-green: #00C853;
    --mp-amber: #FFB300;
    --mp-red: #D50000;
    
    /* Typography */
    --mp-font-family: 'Inter', sans-serif;
    --mp-font-size-base: 16px;
    
    /* Spacing */
    --mp-spacing-md: 1rem;
    --mp-spacing-lg: 1.5rem;
    
    /* Border Radius */
    --mp-radius-md: 0.5rem;
    --mp-radius-lg: 0.75rem;
}
```

### Customizer Settings

Navigate to **Appearance > Customize > MyProtector** to configure:
- Logo
- Brand colors
- Trust signal visibility
- Default business image
- Footer text

### Template Customization

Override templates by creating files in your theme:

```
your-theme/
└── myprotector/
    ├── page-home.php
    ├── page-directory.php
    └── components/
        └── business-card.php
```

---

## Troubleshooting

### Buttons Not Working

1. Check browser console for JavaScript errors
2. Verify jQuery is loaded
3. Ensure `wp_footer()` is called in theme
4. Check nonce validation in AJAX handlers

### Styles Not Loading

1. Verify plugin is activated
2. Check CSS file path in enqueueAssets()
3. Clear browser cache
4. Regenerate .htaccess (save permalinks)

### AJAX Not Working

1. Check `admin-ajax.php` is accessible
2. Verify nonce is being sent
3. Check PHP error logs
4. Ensure user is logged in for protected actions

### 404 Page Not Found

1. Go to **Settings > Permalinks**
2. Click "Save Changes"
3. Verify .htaccess is writable

---

## Deployment Checklist

### Pre-Deployment

- [ ] Update `MYPROTECTOR_COMPANY_URL` in wp-config.php
- [ ] Update founder info and links
- [ ] Configure support email
- [ ] Test all buttons and forms
- [ ] Verify mobile responsiveness
- [ ] Check all AJAX handlers

### Security

- [ ] Enable HTTPS
- [ ] Review file permissions (644 for files, 755 for directories)
- [ ] Disable PHP execution in uploads folder
- [ ] Implement rate limiting on AJAX endpoints
- [ ] Add CSRF protection

### Performance

- [ ] Minify CSS and JavaScript
- [ ] Enable browser caching
- [ ] Optimize images
- [ ] Enable GZIP compression
- [ ] Use CDN for static assets

### Testing

Test all pages and functionality:

- [ ] Home page with all buttons
- [ ] Directory search and filters
- [ ] Business profile pages
- [ ] Login/Register flows
- [ ] Dashboard navigation
- [ ] Review submission
- [ ] Contact form
- [ ] Mobile devices

---

## File Modifications Made

The following changes were made to fix button and functionality issues:

### 1. Template Context Fix
Updated templates to use singleton pattern for module access:

```php
// Before (broken)
$businesses = $this->getMockData('businesses');

// After (fixed)
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$businesses = $frontend_ui->getMockData('businesses');
```

### 2. Frontend JavaScript Enhanced
- Added `initBusinessActions()` for bookmark and write review
- Added `initShareButtons()` for social sharing
- Added `initPagination()` for directory pagination
- Added `showNotification()` for toast messages
- Fixed event delegation for dynamically added elements

### 3. Backend Handlers Added
- `ajaxSaveSettings()` for settings forms
- `addRewriteRules()` for URL routing
- `handleTemplateInclude()` for template loading
- `enqueueAssets()` for proper asset loading

### 4. CSS Improvements
- Added toast notification styles
- Added loading spinner animation
- Added utility classes
- Added modal improvements

---

## Support

For issues or questions:
- GitHub Issues: [Link to repository]
- Email: support@myprotector.com
- Documentation: [Link to docs]

---

*Last Updated: June 2026*
*Version: 1.0.0*