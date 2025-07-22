# WeCoza Learners Management System

This folder contains all learners-related functionality organized for future plugin development.

## Structure Overview

```
@learners/
├── learners-plugin.php      # Main plugin file (inactive)
├── includes/                # Core functionality
│   └── learners-functions.php
├── database/               # Database operations
│   └── learners-db.php
├── shortcodes/             # Shortcode implementations
│   ├── learners-capture-shortcode.php
│   ├── learners-display-shortcode.php
│   └── learners-update-shortcode.php
├── models/                 # Data models (MVC)
│   └── LearnerModel.php
├── controllers/            # Controllers (MVC)
│   └── LearnerController.php
├── views/                  # Views (MVC)
│   └── learner-form.view.php
├── components/             # Reusable components
│   ├── learner-assesment.php
│   ├── learner-class-info.php
│   ├── learner-detail.php
│   ├── learner-header.php
│   ├── learner-info.php
│   ├── learner-poe.php
│   └── learner-tabs.php
├── assets/                 # Frontend assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   │   ├── learners-app.js
│   │   └── learners-display-shortcode.js
│   └── images/            # Images
├── ajax/                   # AJAX handlers
├── admin/                  # Admin functionality
└── templates/              # Template files
```

## Available Shortcodes

### Active Shortcodes
1. `[wecoza_display_learners]` - Displays all learners in a responsive table
2. `[wecoza_learners_form]` - Comprehensive learner registration form
3. `[wecoza_learners_update_form]` - Form for updating existing learner information

### MVC-Based Shortcodes (Placeholders)
1. `[wecoza_learner_capture]` - Learner capture form
2. `[wecoza_learner_display]` - Learner display functionality
3. `[wecoza_learner_update]` - Learner update form

## Key Features

### Learner Management
- Complete CRUD operations for learner records
- Portfolio file management (PDF uploads)
- Comprehensive learner data capture including:
  - Personal information
  - Contact details
  - Educational qualifications
  - Employment status
  - Assessment results
  - Disability status

### Database Operations
- Secure database interactions using prepared statements
- Transaction management for data integrity
- Caching with WordPress transients
- Support for PostgreSQL and MySQL

### AJAX Functionality
- Dynamic dropdown population
- Real-time data fetching
- Asynchronous form submissions
- Portfolio file management

### Security Features
- Nonce verification
- Data sanitization and validation
- Secure file upload handling
- Permission checks

## Converting to Plugin

To convert this to an active WordPress plugin:

1. **Move the folder**: Copy the entire `@learners` folder to `wp-content/plugins/wecoza-learners/`

2. **Update file paths**: Replace all instances of:
   - `WECOZA_CHILD_DIR` with `WECOZA_LEARNERS_PLUGIN_DIR`
   - `WECOZA_CHILD_URL` with `WECOZA_LEARNERS_PLUGIN_URL`

3. **Update includes**: Modify the `learners-functions.php` file to use the new plugin paths

4. **Update namespaces**: Ensure all namespace references are correct

5. **Activate**: Uncomment the last line in `learners-plugin.php` and activate the plugin in WordPress admin

## Dependencies

- WordPress 5.0+
- PHP 7.2+
- Bootstrap 5 (for styling)
- jQuery (for JavaScript functionality)
- Wecoza3_DB class (database connection)

## CSS Styles

Currently, learner-related CSS styles are stored in:
`/opt/lampp/htdocs/wecoza/wp-content/themes/wecoza_3_child_theme/includes/css/ydcoza-styles.css`

When converting to a plugin, create a dedicated CSS file:
`@learners/assets/css/learners-style.css`

## Notes

- All functionality is preserved from the original implementation
- The code is organized following WordPress plugin best practices
- Database table creation SQL needs to be implemented based on your schema
- Additional admin pages and settings can be added in the `admin/` directory