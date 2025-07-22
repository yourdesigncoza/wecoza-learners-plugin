# WeCoza Learners Plugin Conversion Guide

This guide provides step-by-step instructions for converting the organized learners code into an active WordPress plugin.

## Pre-Conversion Checklist

- [ ] All learners functionality is working in the theme
- [ ] Database tables exist and contain data
- [ ] File upload directory has proper permissions
- [ ] All dependencies are documented

## Conversion Steps

### Step 1: Prepare the Plugin Directory

1. Copy the entire `@learners` folder to `wp-content/plugins/`
2. Rename the folder to `wecoza-learners-plugin`
```bash
cp -r /path/to/theme/@learners /path/to/wp-content/plugins/wecoza-learners-plugin
```

### Step 2: Update File Paths

#### In `learners-plugin.php`:
1. Uncomment the last line to activate the plugin class
2. Verify all constant definitions use `plugin_dir_path(__FILE__)`

#### In `includes/learners-functions.php`:
Replace all instances:
```php
// Old (theme-based)
WECOZA_CHILD_DIR . '/assets/learners/...'
WECOZA_CHILD_URL . '/assets/learners/...'

// New (plugin-based)
WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php'
WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-capture-shortcode.php'
```

#### Update file paths in:
- `database/learners-db.php` - Update any theme-specific paths
- All shortcode files - Update include paths
- `ajax/learners-ajax-handlers.php` - Update any file references

### Step 3: Update Asset URLs

#### In JavaScript files:
Update any hardcoded URLs to use the localized variables:
```javascript
// Use the localized plugin URL
wecoza_learners.plugin_url + 'assets/images/...'
```

#### In CSS files:
Update any background image URLs:
```css
/* Old */
background-image: url('../../../assets/learners/images/...');

/* New */
background-image: url('../images/...');
```

### Step 4: Database Updates

1. Implement the `create_tables()` method in `learners-plugin.php`
2. Add table creation SQL from `database/schema.sql`
3. Handle table prefixes properly:
```php
$table_name = $wpdb->prefix . 'wecoza_learners';
```

### Step 5: Update Namespaces and Autoloading

1. Update namespace declarations if moving to plugin namespace
2. Add composer autoload if using composer:
```json
{
    "autoload": {
        "psr-4": {
            "WeCoza\\Learners\\": "src/"
        }
    }
}
```

### Step 6: Handle Dependencies

1. Check for required classes/functions from theme:
   - `Wecoza3_DB` class
   - Any theme-specific functions
   
2. Either:
   - Copy required classes to plugin
   - Add dependency checks
   - Create abstraction layer

### Step 7: Update AJAX Handlers

Update AJAX action names if needed to avoid conflicts:
```php
// Consider prefixing actions
add_action('wp_ajax_wecoza_learners_fetch_data', 'fetch_learners_data');
```

### Step 8: Add Admin Menu (Optional)

Not Needed

### Step 9: Testing Checklist

After conversion, test:
- [ ] Plugin activation/deactivation
- [ ] All shortcodes render correctly
- [ ] AJAX operations work
- [ ] File uploads function properly
- [ ] Database operations succeed
- [ ] CSS and JS load correctly
- [ ] No PHP errors in debug.log

### Step 10: Final Cleanup

1. Remove any debug code
2. Update version numbers
3. Add proper plugin headers
4. Create a changelog
5. Test on a fresh WordPress installation

## Common Issues and Solutions

### Issue: Class 'Wecoza3_DB' not found
**Solution**: Copy the database class or create a new PDO wrapper

### Issue: Assets not loading
**Solution**: Check enqueue paths and ensure proper URL generation

### Issue: AJAX not working
**Solution**: Verify nonce names and AJAX URLs are correct

### Issue: Shortcodes not rendering
**Solution**: Ensure shortcodes are registered on 'init' hook

## Post-Conversion Enhancements

1. Add settings page for configuration
2. Implement uninstall routine
3. Add data export/import functionality
4. Create user documentation
5. Add WordPress.org readme.txt

## Support Files to Create

1. `readme.txt` - WordPress.org plugin readme
2. `changelog.md` - Version history
3. `uninstall.php` - Cleanup on uninstall
4. `.gitignore` - Version control ignores
5. `languages/` - Translation files