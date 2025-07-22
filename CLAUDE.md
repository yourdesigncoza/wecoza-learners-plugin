# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## WordPress Plugin Architecture

This is a **WeCoza Learners Management Plugin** - a comprehensive learner registration and portfolio management system with MVC architecture and external PostgreSQL database integration.

### Plugin Status & Activation
**STATUS**: The plugin is now ACTIVE and fully functional! The main plugin file has been activated and all core functionality is working.

**Database**: Connected to external PostgreSQL database via `WeCozaLearnersDB` service class.

### Core Architecture
- **MVC Structure**: Controllers, Models, Views with WordPress shortcode integration
- **External Database**: PostgreSQL (not WordPress MySQL) via `WeCozaLearnersDB` class
- **Namespace**: `WeCoza\` for plugin classes
- **Entry Point**: `learners-plugin.php` with singleton pattern

### Key Files Structure
```
learners-plugin.php              # Main plugin file (ACTIVE)
database/WeCozaLearnersDB.php     # New PostgreSQL service class
controllers/LearnerController.php # Enhanced MVC controller with AJAX handlers
models/LearnerModel.php          # Data model with getters/setters
database/learners-db.php         # PostgreSQL operations via learner_DB class
shortcodes/                      # WordPress shortcode implementations (3 active)
ajax/learners-ajax-handlers.php  # AJAX endpoints with nonce security
assets/js/learners-app.js        # Frontend JavaScript with Bootstrap 5
test-connection.php              # Comprehensive functionality test
legacy/                          # Complete reference implementation
```

### Database Integration

#### External PostgreSQL Database
- **Connection**: Via new `WeCozaLearnersDB` singleton service class
- **Primary Table**: `learners` with 20+ fields
- **Supporting Tables**: `learner_portfolios`, `learner_qualifications`, `learner_placement_level`, `locations`, `employers`
- **Caching**: WordPress transients (12-hour duration) for performance

#### Database Testing Commands
```bash
# Test database connection via new service
wp eval "echo WeCozaLearnersDB::getInstance()->testConnection() ? 'Connected' : 'Failed';"

# Test database service functionality  
wp eval "echo json_encode(WeCozaLearnersDB::getInstance()->getConnectionInfo());"

# Test learner database operations
wp eval "echo json_encode((new learner_DB())->get_locations());"

# Access comprehensive test suite
# Visit: /wp-content/plugins/wecoza-learners-plugin/test-connection.php
```

### Shortcodes & Endpoints

#### Active Shortcodes
```php
[wecoza_learners_form]           # Comprehensive registration form with file upload
[wecoza_display_learners]        # Responsive table with search/pagination
[wecoza_learners_update_form]    # Update existing learner data
```

#### MVC Shortcodes (Placeholder Implementation)
```php
[wecoza_learner_capture]         # Basic form (LearnerController)
[wecoza_learner_display]         # Basic display (LearnerController)  
[wecoza_learner_update]          # Basic update (LearnerController)
```

#### AJAX Endpoints (5 active endpoints)
```javascript
// Primary Operations
wp.ajax.post('get_learner_data_by_id', {learner_id: id})
wp.ajax.post('update_learner', learnerData)
wp.ajax.post('delete_learner', {learner_id: id})

// Supporting Operations  
wp.ajax.post('fetch_learners_dropdown_data', {})
wp.ajax.post('delete_learner_portfolio', {learner_id: id})
```

### Asset Management

#### Loading Strategy
Assets load via WordPress `wp_enqueue_scripts` hooks:
```php
// Frontend dependencies
wp_enqueue_script('jquery');
wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/');
wp_localize_script('learners-app-js', 'learners_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('learners_nonce')
]);
```

#### CSS Integration
**ALL CSS styles must be added to**: `/opt/lampp/htdocs/wecoza/wp-content/themes/wecoza_3_child_theme/includes/css/ydcoza-styles.css`

Never create separate CSS files in plugin directories.

### Development Workflows

#### Plugin Activation Workflow
```bash
# Activate plugin (after uncommenting line 226)
wp plugin activate wecoza-learners-plugin

# Verify table creation
wp eval "echo (new learner_DB())->verify_tables_exist() ? 'Tables exist' : 'Tables missing';"

# Test shortcode rendering
wp eval "echo do_shortcode('[wecoza_display_learners]');"
```

#### Database Operations Testing
```bash
# Test learner retrieval
wp eval "echo json_encode((new learner_DB())->get_all_learners());"

# Test portfolio uploads (requires activation)
wp eval "echo (new learner_DB())->get_learner_portfolios(1);"
```

### Legacy Reference Architecture

The `/legacy/` directory contains the complete **WeCoza Classes Plugin** implementation serving as architectural reference:

#### Key Reference Files
- `legacy/wecoza-classes-plugin.php` - Complete plugin structure with activation hooks
- `legacy/config/app.php` - Comprehensive MVC configuration system  
- `legacy/app/bootstrap.php` - PSR-4 autoloading and MVC initialization
- `legacy/app/Controllers/ClassController.php` - Full controller implementation
- `legacy/app/Services/Database/DatabaseService.php` - PostgreSQL service layer

#### Reference Patterns
- **Database Service**: Singleton pattern with connection pooling
- **Controller Structure**: WordPress hook integration with AJAX endpoints
- **View Rendering**: Component-based templating system
- **Asset Management**: Conditional loading based on shortcode presence
- **Security**: Capability-based access control with nonce verification

### Security Implementation

#### Form Security Pattern
```php
// Nonce verification on all forms
wp_verify_nonce($_POST['learners_nonce'], 'learners_nonce_action');

// AJAX nonce verification
check_ajax_referer('learners_nonce', 'nonce');

// Input sanitization
$name = sanitize_text_field($_POST['learner_name']);
```

#### File Upload Security
- PDF files only via `wp_check_filetype()`
- Upload directory: `wp-content/uploads/portfolios/`
- File validation with WordPress core functions

### Current Implementation Status

- ✅ **Plugin Core**: ACTIVE and fully functional
- ✅ **Database Layer**: Complete PostgreSQL integration with new `WeCozaLearnersDB` service
- ✅ **Database Tables**: All 6 tables created automatically on activation
- ✅ **Shortcodes**: Six functional shortcodes (3 original + 3 new MVC versions)
- ✅ **AJAX Handlers**: Five endpoints with security validation
- ✅ **Asset Loading**: WordPress standard with Bootstrap 5 integration
- ✅ **MVC Structure**: Enhanced controller with integrated AJAX handling
- ✅ **File Uploads**: Portfolio directory created and configured
- ✅ **WordPress Integration**: Proper hooks, activation, and credential management

### Important Development Notes

- **External Database**: All operations use PostgreSQL, not WordPress MySQL
- **No Build System**: Direct file editing with WordPress asset management
- **Ready for Production**: Plugin is active and all functionality is working
- **Test Suite Available**: Comprehensive test at `/test-connection.php`
- **Legacy Reference**: Complete MVC implementation available in `/legacy/` folder for reference
- **Security First**: All forms and AJAX use WordPress nonce verification
- **Asset Integration**: CSS goes to theme child file, JavaScript loads conditionally
- **Caching Strategy**: WordPress transients with 12-hour duration for performance

### Quick Start Guide

1. **Plugin Activation**: The plugin is already active and ready to use
2. **Database Connection**: Uses existing `wecoza_postgres_password` option
3. **Test Functionality**: Visit `/wp-content/plugins/wecoza-learners-plugin/test-connection.php`
4. **Use Shortcodes**: 
   - `[wecoza_learners_form]` - Full registration form
   - `[wecoza_display_learners]` - Learner data table
   - `[wecoza_learners_update_form]` - Update form
   - `[wecoza_learner_capture]` - Simple MVC form
   - `[wecoza_learner_display]` - Simple MVC display
   - `[wecoza_learner_update]` - Simple MVC update

### Production Notes

- **Database Credentials**: Configured via WordPress options (existing setup)
- **File Uploads**: Portfolio directory auto-created at `/wp-content/uploads/portfolios/`
- **Error Logging**: All database operations log errors to WordPress debug log
- **Performance**: Caching enabled via WordPress transients
- **Security**: Nonce verification on all AJAX operations