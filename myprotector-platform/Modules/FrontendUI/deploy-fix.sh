#!/bin/bash
# FrontendUI Routing Fix - Verification and Deployment Script
# 
# This script helps verify the fix and deploy it to a WordPress site.
# Run this from the plugin directory on the target WordPress installation.
#
# Usage:
#   ./deploy-fix.sh --verify     # Verify current state
#   ./deploy-fix.sh --deploy    # Deploy the fix
#   ./deploy-fix.sh --test      # Test routes after fix

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=============================================="
echo " MyProtector FrontendUI Routing Fix Script"
echo "=============================================="
echo ""

# Check if we're in a WordPress context
if [ -f "myprotector-platform.php" ]; then
    PLUGIN_DIR=$(pwd)
    echo -e "${GREEN}✓${NC} Plugin directory found: $PLUGIN_DIR"
elif [ -f "../myprotector-platform.php" ]; then
    PLUGIN_DIR=$(dirname $(pwd))
    echo -e "${GREEN}✓${NC} Plugin directory found: $PLUGIN_DIR"
else
    echo -e "${RED}✗${NC} Not in a WordPress plugin directory."
    echo "  Run this script from the myprotector-platform plugin directory."
    exit 1
fi

FRONTENDUI_FILE="$PLUGIN_DIR/Modules/FrontendUI/FrontendUI.php"

# Check if file exists
if [ ! -f "$FRONTENDUI_FILE" ]; then
    echo -e "${RED}✗${NC} FrontendUI.php not found at: $FRONTENDUI_FILE"
    exit 1
fi

echo ""

# Parse command line argument
case "${1:-}" in
    --verify)
        echo "=== VERIFYING CURRENT STATE ==="
        echo ""
        
        echo "1. Checking FrontendUI.php for critical fixes..."
        
        # Check BUG #1: Activation hook in boot()
        if grep -q "register_activation_hook(MYPROTECTOR_BASENAME, \[\\$this, 'onPluginActivate'\]);" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} BUG #1 FIXED: Activation hook in boot()"
        else
            echo -e "   ${RED}✗${NC} BUG #1 NOT FIXED: Activation hook not in boot()"
        fi
        
        # Check BUG #2: Single query_vars registration
        if grep -q "add_filter('query_vars', \[\$this, 'addQueryVars\], 0);" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} BUG #2 FIXED: query_vars at priority 0"
        else
            echo -e "   ${RED}✗${NC} BUG #2 NOT FIXED: query_vars filter issue"
        fi
        
        # Check BUG #3: No flush_rewrite_rules in createPages()
        if ! grep -q "flush_rewrite_rules();" "$FRONTENDUI_FILE" | grep -v "//" | grep -v "#"; then
            echo -e "   ${GREEN}✓${NC} BUG #3 FIXED: No flush_rewrite_rules in createPages()"
        else
            echo -e "   ${RED}✗${NC} BUG #3 NOT FIXED: flush_rewrite_rules in wrong place"
        fi
        
        # Check BUG #4: Debug logging present
        if grep -q "error_log.*FrontendUI.*handleTemplateInclude" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} BUG #4 FIXED: Debug logging added"
        else
            echo -e "   ${YELLOW}○${NC} BUG #4: Debug logging status unclear"
        fi
        
        # Check for deactivation hook
        if grep -q "register_deactivation_hook" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} Deactivation hook present"
        else
            echo -e "   ${YELLOW}○${NC} No deactivation hook found"
        fi
        
        echo ""
        echo "2. Checking for duplicate setupRouting() methods..."
        DUPLICATE_COUNT=$(grep -c "public function setupRouting" "$FRONTENDUI_FILE")
        if [ "$DUPLICATE_COUNT" -eq 1 ]; then
            echo -e "   ${GREEN}✓${NC} No duplicate setupRouting() methods"
        else
            echo -e "   ${RED}✗${NC} Found $DUPLICATE_COUNT setupRouting() methods (should be 1)"
        fi
        
        echo ""
        echo "3. Checking rewrite rules registration..."
        if grep -q "add_rewrite_rule.*mp_page=home" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} Rewrite rules defined"
        else
            echo -e "   ${RED}✗${NC} No rewrite rules found"
        fi
        
        echo ""
        echo "4. Checking addQueryVars() method exists..."
        if grep -q "public function addQueryVars" "$FRONTENDUI_FILE"; then
            echo -e "   ${GREEN}✓${NC} addQueryVars() method found"
        else
            echo -e "   ${RED}✗${NC} addQueryVars() method missing"
        fi
        
        echo ""
        echo "=== VERIFICATION COMPLETE ==="
        ;;
        
    --deploy)
        echo "=== DEPLOYING FIX ==="
        echo ""
        
        echo "1. Backing up current FrontendUI.php..."
        BACKUP_FILE="$FRONTENDUI_FILE.backup.$(date +%Y%m%d_%H%M%S)"
        cp "$FRONTENDUI_FILE" "$BACKUP_FILE"
        echo -e "   ${GREEN}✓${NC} Backup created: $BACKUP_FILE"
        
        echo ""
        echo "2. Deploy instructions:"
        echo ""
        echo "   To deploy this fix to your WordPress site:"
        echo ""
        echo "   a. Copy the fixed FrontendUI.php to your server:"
        echo "      scp FrontendUI.php user@your-server:/path/to/wp-content/plugins/myprotector-platform/Modules/FrontendUI/"
        echo ""
        echo "   b. Deactivate and reactivate the plugin to flush rewrite rules:"
        echo "      wp plugin deactivate myprotector-platform"
        echo "      wp plugin activate myprotector-platform"
        echo ""
        echo "   c. Or manually flush rewrite rules:"
        echo "      wp rewrite flush --hard"
        echo ""
        echo "   d. Enable debug logging in wp-config.php:"
        echo "      define('WP_DEBUG', true);"
        echo "      define('WP_DEBUG_LOG', true);"
        echo ""
        echo "3. Files modified:"
        echo "   - $FRONTENDUI_FILE"
        echo ""
        echo "=== DEPLOYMENT INSTRUCTIONS PROVIDED ==="
        ;;
        
    --test)
        echo "=== TESTING ROUTES ==="
        echo ""
        
        echo "1. Checking if WP-CLI is available..."
        if command -v wp &> /dev/null; then
            WP_CLI="wp"
        else
            echo -e "   ${YELLOW}○${NC} WP-CLI not found. Manual testing required."
            WP_CLI=""
        fi
        
        if [ -n "$WP_CLI" ]; then
            echo -e "   ${GREEN}✓${NC} WP-CLI found"
            
            echo ""
            echo "2. Checking plugin status..."
            $WP_CLI plugin list | grep myprotector || echo "   Plugin not found in list"
            
            echo ""
            echo "3. Verifying rewrite rules..."
            echo "   Running: wp rewrite list --format=table | grep mp_page"
            echo ""
            $WP_CLI rewrite list --format=table 2>/dev/null | grep mp_page || echo "   No mp_page rules found (this is a problem!)"
            
            echo ""
            echo "4. Checking query vars..."
            echo "   Verifying mp_page and mp_slug are registered..."
            $WP_CLI eval "echo implode(', ', apply_filters('query_vars', []));" 2>/dev/null | grep -q "mp_page" && echo -e "   ${GREEN}✓${NC} mp_page query var registered" || echo -e "   ${RED}✗${NC} mp_page query var NOT registered"
        fi
        
        echo ""
        echo "5. Manual Testing Instructions:"
        echo ""
        echo "   Visit these URLs and check for correct template loading:"
        echo "   - https://yoursite.com/login"
        echo "   - https://yoursite.com/register"
        echo "   - https://yoursite.com/dashboard"
        echo "   - https://yoursite.com/businesses"
        echo "   - https://yoursite.com/business/techventures-solutions"
        echo "   - https://yoursite.com/about"
        echo "   - https://yoursite.com/contact"
        echo ""
        echo "   Check debug.log for these entries:"
        echo "   - [MyProtector] FrontendUI: setupRouting() completed"
        echo "   - [MyProtector] FrontendUI: addQueryVars() - mp_page and mp_slug registered"
        echo "   - [MyProtector] FrontendUI: mp_page=<route>, looking for template"
        echo ""
        echo "=== TESTING INSTRUCTIONS PROVIDED ==="
        ;;
        
    --help|*)
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  --verify    Verify the fix is correctly applied"
        echo "  --deploy    Show deployment instructions"
        echo "  --test      Show testing instructions"
        echo "  --help      Show this help message"
        echo ""
        echo "Examples:"
        echo "  $0 --verify    # Verify the fix"
        echo "  $0 --deploy    # Get deployment instructions"
        echo "  $0 --test      # Get testing instructions"
        ;;
esac

echo ""
exit 0