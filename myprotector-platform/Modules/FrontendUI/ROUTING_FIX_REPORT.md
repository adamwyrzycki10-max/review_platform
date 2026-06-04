# FrontendUI.php - WordPress Routing Fix Report

## Executive Summary

**Problem**: All custom URLs (`/login`, `/register`, `/dashboard`, etc.) return default WordPress pages or 404 errors despite the plugin loading successfully.

**Root Cause**: Multiple critical bugs in the WordPress routing implementation, primarily:
1. `register_activation_hook()` was called inside the `init` hook callback (too late)
2. Duplicate `query_vars` filters registered in multiple places
3. Incorrect flush timing and hook priorities

---

## Bug Analysis

### BUG #1: register_activation_hook() Called at Runtime (CRITICAL)
**File**: `FrontendUI.php`
**Original Line**: 414
**Code**:
```php
protected function initPageRouting(): void {
    register_activation_hook(MYPROTECTOR_BASENAME, [$this, 'createPages']);
```

**Problem**: `register_activation_hook()` MUST be called at plugin load time, NOT inside any hook callbacks. By the time `initOnFirstLoad()` runs on the `init` hook, the plugin activation event has already passed. The hook registration was silently failing.

**Fix**: Move activation hook registration to `boot()` method, which runs when the module is loaded.

---

### BUG #2: Duplicate query_vars Filters
**File**: `FrontendUI.php`
**Original Lines**: 460-464 and 622-626

**Problem**: The `setupRouting()` method added a `query_vars` filter, then `addRewriteRules()` added ANOTHER `query_vars` filter. This creates duplicate filters that can cause unpredictable behavior.

**Fix**: Consolidate query_vars registration into a single method `addQueryVars()` called from `setupRouting()`.

---

### BUG #3: Incorrect flush_rewrite_rules() Placement
**File**: `FrontendUI.php`
**Original Line**: 450

**Problem**: `flush_rewrite_rules()` was called inside `createPages()` during plugin activation. This can cause issues because:
1. Other plugins may not have registered their hooks yet
2. The flush happens before our rewrite rules are fully registered
3. Multiple calls to `flush_rewrite_rules()` are expensive

**Fix**: Replace with a staged flush mechanism using transient options.

---

### BUG #4: Wrong Hook Priority for query_vars
**File**: `FrontendUI.php`
**Original Line**: 460

**Problem**: `query_vars` filter was registered with default priority (10). WordPress processes query vars at priority 0, so our custom vars weren't available when needed.

**Fix**: Register `query_vars` filter at priority 0 to ensure vars are available before WordPress matches rewrite rules.

---

### BUG #5: Missing Deactivation Hook
**File**: `FrontendUI.php`

**Problem**: No `register_deactivation_hook()` to clean up rewrite rules when plugin is deactivated.

**Fix**: Add deactivation handler to clear flush flags.

---

### BUG #6: No Debug Logging
**File**: `FrontendUI.php`

**Problem**: No way to verify rewrite rules were actually registered or to debug query_vars.

**Fix**: Add comprehensive debug logging (conditional on `WP_DEBUG`).

---

### BUG #7: Silent Template Failures
**File**: `FrontendUI.php`
**Original Lines**: 517-523

**Problem**: If template file doesn't exist, the code silently returns the original template with no error indication.

**Fix**: Add fallback to error template and debug logging for missing templates.

---

### BUG #8: Duplicate setupRouting() Method
**File**: `FrontendUI.php`
**Original Lines**: 558-579 (Second definition)

**Problem**: The class had TWO `setupRouting()` methods defined. Due to PHP's late binding, the second (broken) definition was overriding the first.

**Fix**: Remove duplicate method entirely.

---

## Fixed Code Snippets

### Fixed boot() Method (Lines 387-396)
```php
public function boot(): void {
    // FIX BUG #1: Register activation hook at load time, NOT inside init callback
    register_activation_hook(MYPROTECTOR_BASENAME, [$this, 'onPluginActivate']);
    register_deactivation_hook(MYPROTECTOR_BASENAME, [$this, 'onPluginDeactivate']);
    
    // FIX BUG #2: Register ALL hooks at init time
    // Use priority 0 for query_vars to register BEFORE rewrite rules
    add_action('init', [$this, 'initOnFirstLoad'], 0);
}
```

### Fixed initOnFirstLoad() Method (Lines 433-464)
```php
public function initOnFirstLoad(): void {
    // FIX BUG #3: Check for flush flag from activation
    if (get_option('mp_flush_rewrite_rules')) {
        delete_option('mp_flush_rewrite_rules');
        // Use transient to schedule flush for next request
        set_transient('mp_flush_rules_on_next_request', true, 60);
    }
    
    // Check if we need to flush rewrite rules this request
    if (get_transient('mp_flush_rules_on_next_request')) {
        delete_transient('mp_flush_rules_on_next_request');
        add_action('shutdown', 'flush_rewrite_rules');
    }
    
    // Initialize models and setup routing
    if (!isset($this->_initialized)) {
        $this->reviewModel = new ReviewModel();
        $this->businessModel = new BusinessModel();
        $this->trafficService = new TrafficSignalService();
        $this->registerShortcodes();
        $this->setupRouting();
        $this->_initialized = true;
    }
}
```

### Fixed setupRouting() Method (Lines 474-496)
```php
public function setupRouting(): void {
    // FIX BUG #5: Register query_vars at PRIORITY 0 (before rewrite rules)
    add_filter('query_vars', [$this, 'addQueryVars'], 0);
    
    // FIX BUG #6: Register ALL rewrite rules in ONE place
    $this->addRewriteRules();
    
    // FIX BUG #7: Handle template loading with correct priority
    add_filter('template_include', [$this, 'handleTemplateInclude'], 1);
    add_filter('the_content', [$this, 'overridePageContent'], 1);
    
    // Debug: Log that routing was set up
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[MyProtector] FrontendUI: setupRouting() completed at priority 0');
    }
}
```

### New addQueryVars() Method (Lines 504-514)
```php
public function addQueryVars(array $vars): array {
    $vars[] = 'mp_page';
    $vars[] = 'mp_slug';
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[MyProtector] FrontendUI: addQueryVars() - mp_page and mp_slug registered');
    }
    
    return $vars;
}
```

### Fixed addRewriteRules() Method (Lines 682-717)
```php
public function addRewriteRules(): void {
    // FIX BUG: Don't add duplicate query_vars filter here!
    
    self::$rewrite_rules_registered = true;
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[MyProtector] FrontendUI: addRewriteRules() - rules registered');
    }
    
    // All rewrite rules registered at 'top' priority
    add_rewrite_rule('^$', 'index.php?mp_page=home', 'top');
    add_rewrite_rule('^dashboard/?$', 'index.php?mp_page=dashboard', 'top');
    add_rewrite_rule('^business-dashboard/?$', 'index.php?mp_page=business-dashboard', 'top');
    add_rewrite_rule('^reseller-dashboard/?$', 'index.php?mp_page=reseller-dashboard', 'top');
    add_rewrite_rule('^businesses/?$', 'index.php?mp_page=businesses', 'top');
    add_rewrite_rule('^business/([^/]+)/?$', 'index.php?mp_page=business&mp_slug=$matches[1]', 'top');
    add_rewrite_rule('^login/?$', 'index.php?mp_page=login', 'top');
    add_rewrite_rule('^register/?$', 'index.php?mp_page=register', 'top');
    add_rewrite_rule('^about/?$', 'index.php?mp_page=about', 'top');
    add_rewrite_rule('^contact/?$', 'index.php?mp_page=contact', 'top');
}
```

---

## Deployment Instructions

### Step 1: Backup
```bash
cp /path/to/your-site/wp-content/plugins/myprotector-platform/Modules/FrontendUI/FrontendUI.php \
   /path/to/your-site/wp-content/plugins/myprotector-platform/Modules/FrontendUI/FrontendUI.php.backup
```

### Step 2: Deploy Fixed File
Replace `/path/to/your-site/wp-content/plugins/myprotector-platform/Modules/FrontendUI/FrontendUI.php` with the fixed version.

### Step 3: Flush Rewrite Rules

**Option A: WP-CLI (Recommended)**
```bash
wp rewrite flush --hard
```

**Option B: Admin Dashboard**
1. Go to Settings > Permalinks
2. Click "Save Changes" (do not change the structure, just save)

**Option C: wp-cli with plugin activation**
```bash
wp plugin deactivate myprotector-platform && wp plugin activate myprotector-platform
```

### Step 4: Verify Rewrite Rules

Add this temporary debug to your theme's `functions.php` to verify rules are registered:

```php
add_action('init', function() {
    global $wp_rewrite;
    $rules = $wp_rewrite->rules ?? [];
    $mp_rules = array_filter($rules, function($rule, $key) {
        return strpos($key, 'mp_page') !== false || strpos($rule, 'mp_page') !== false;
    }, ARRAY_FILTER_USE_BOTH);
    
    if (!empty($mp_rules)) {
        error_log('[DEBUG] MyProtector rewrite rules: ' . print_r($mp_rules, true));
    }
});
```

### Step 5: Clear All Caches
```bash
# If using Object Cache
wp cache flush

# If using Opcode Cache (e.g., OPcache)
# Restart PHP-FPM or Apache

# If using CDN
# Purge CDN cache
```

---

## Route Testing Checklist

After deployment, test each route:

| Route | Expected Behavior | Debug Check |
|-------|-------------------|-------------|
| `/login` | Shows login page template | Check error_log for "mp_page=login" |
| `/register` | Shows registration page template | Check error_log for "mp_page=register" |
| `/dashboard` | Shows user dashboard template | Check error_log for "mp_page=dashboard" |
| `/business-dashboard` | Shows business dashboard template | Check error_log for "mp_page=business-dashboard" |
| `/reseller-dashboard` | Shows reseller dashboard template | Check error_log for "mp_page=reseller-dashboard" |
| `/businesses` | Shows business directory template | Check error_log for "mp_page=businesses" |
| `/business/techventures-solutions` | Shows business profile for slug | Check error_log for "mp_page=business" and "mp_slug=techventures-solutions" |
| `/about` | Shows about page template | Check error_log for "mp_page=about" |
| `/contact` | Shows contact page template | Check error_log for "mp_page=contact" |

---

## How to Enable Debug Logging

### Method 1: Enable WP_DEBUG
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Logs will appear in `/wp-content/debug.log`.

### Method 2: Check via Query Monitor
Install the Query Monitor plugin to see:
- All registered rewrite rules
- Query vars for any request
- Template hierarchy for each page

### Method 3: Check via WP-CLI
```bash
# List all rewrite rules
wp rewrite list --format=table | grep mp_page

# Verify query vars are registered
wp eval 'print_r(apply_filters("query_vars", []));'
```

---

## Expected Debug Log Output

When WP_DEBUG is enabled, you should see these log entries in order:

1. `[MyProtector] FrontendUI: setupRouting() completed at priority 0`
2. `[MyProtector] FrontendUI: addQueryVars() - mp_page and mp_slug registered`
3. `[MyProtector] FrontendUI: addRewriteRules() - rules registered`
4. `[MyProtector] FrontendUI: handleTemplateInclude() - query_vars: [...]`
5. `[MyProtector] FrontendUI: mp_page=<route_name>, looking for template`
6. `[MyProtector] FrontendUI: Loading template <path>`

---

## Troubleshooting

### Routes Still Returning 404

1. **Check if plugin is activated**: 
   ```bash
   wp plugin list | grep myprotector
   ```

2. **Verify rewrite rules are loaded**:
   ```bash
   wp rewrite list | grep mp_page
   ```

3. **Manually flush rules**:
   ```bash
   wp rewrite flush --hard
   ```

4. **Check .htaccess is writable**:
   ```bash
   ls -la .htaccess
   ```

### Template Not Found Error

1. Verify template files exist:
   ```bash
   ls -la Modules/FrontendUI/templates/pages/
   ```

2. Check the template path being logged

3. Ensure file permissions allow reading

### Query Vars Not Available

1. Add debug at the top of `handleTemplateInclude()`:
   ```php
   error_log('All query_vars: ' . print_r($wp_query->query_vars, true));
   ```

2. Verify `addQueryVars()` is being called by checking for the log entry

3. Check if another plugin is removing query vars:
   ```bash
   wp plugin list
   ```

---

## Files Modified

1. `/Modules/FrontendUI/FrontendUI.php` - Complete routing fix with debug logging

## Files That May Need Updates

1. `/Core/Activator.php` - May need to call `flush_rewrite_rules()`
2. `/Core/Deactivator.php` - May need to flush rules on deactivation

---

*Report generated: 2026-06-04*
*Fixed issues: 8 critical bugs in WordPress routing implementation*