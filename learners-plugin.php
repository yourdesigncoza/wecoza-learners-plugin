<?php
/**
 * Plugin Name: WeCoza Learners Plugin
 * Plugin URI: https://yourdesign.co.za/
 * Description: Comprehensive learners management system for WeCoza including capture, display, update, and portfolio management functionality.
 * Version: 1.0.0
 * Author: YourDesign.co.za
 * Author URI: https://yourdesign.co.za/
 * Text Domain: wecoza-learners-plugin
 * Domain Path: /languages
 * 
 * NOTE: This is a prepared plugin structure. To activate as a plugin:
 * 1. Move this entire @learners folder to wp-content/plugins/wecoza-learners-plugin/
 * 2. Update all file paths and includes to use plugin_dir_path()
 * 3. Update all URLs to use plugin_dir_url()
 * 4. Activate the plugin in WordPress admin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WECOZA_LEARNERS_VERSION', date('YmdHis')); // Dynamic timestamp version e.g., 20250723143045
define('WECOZA_LEARNERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WECOZA_LEARNERS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WECOZA_LEARNERS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WECOZA_LEARNERS_DEBUG', WP_DEBUG); // Use WordPress debug setting


/**
 * Main plugin class
 */
class WeCoza_Learners_Plugin {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Main instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        try {
            $this->init_hooks();
            $this->includes_debug();
            
        } catch (Exception $e) {
            // error_log('WeCoza Learners Plugin: FATAL ERROR in constructor: ' . $e->getMessage());
            // error_log('WeCoza Learners Plugin: Error file: ' . $e->getFile());
            // error_log('WeCoza Learners Plugin: Error line: ' . $e->getLine());
            // Add admin notice instead of fatal error
            add_action('admin_notices', array($this, 'show_error_notice'));
            throw $e; // Re-throw to cause plugin activation failure
        }
    }
    
    /**
     * Show error notice in admin
     */
    public function show_error_notice() {
        ?>
        <div class="notice notice-error">
            <p><strong>WeCoza Learners Plugin Error:</strong> The plugin could not be loaded properly. Please check the error log for details.</p>
            <p>Common issues:</p>
            <ul>
                <li>Missing database credentials (check wecoza_postgres_password option)</li>
                <li>Database connection issues</li>
                <li>Missing plugin files</li>
            </ul>
        </div>
        <?php
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        try {
            // Activation/Deactivation hooks
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));
            
            // Init action
            add_action('init', array($this, 'init'));
            
            // Enqueue scripts
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            
        } catch (Exception $e) {
            // error_log('WeCoza Learners Plugin: FATAL ERROR in init_hooks(): ' . $e->getMessage());
            // error_log('WeCoza Learners Plugin: Error file: ' . $e->getFile());
            // error_log('WeCoza Learners Plugin: Error line: ' . $e->getLine());
            throw $e;
        }
    }
    
    /**
     * Include required files
     */
    private function includes() {
        try {
            // Core includes - Skip learners-functions.php to avoid conflicts with theme
            // $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'includes/learners-functions.php', 'Core functions');
            
            // Database
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php', 'Database class');
            
            // Shortcodes
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-capture-shortcode.php', 'Capture shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-display-shortcode.php', 'Display shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-update-shortcode.php', 'Update shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learner-single-display-shortcode.php', 'Single learner display shortcode');
            
            // AJAX handlers
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'ajax/learners-ajax-handlers.php', 'AJAX handlers');
            
            // Models
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'models/LearnerModel.php', 'Learner model');
            
            // Controllers
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'controllers/LearnerController.php', 'Learner controller');
            
            // Initialize controllers
            if (class_exists('\WeCoza\Controllers\LearnerController')) {
                new \WeCoza\Controllers\LearnerController();
            }
            
        } catch (Exception $e) {
            // error_log('WeCoza Learners Plugin: Error in includes(): ' . $e->getMessage());
            // Don't throw here, let plugin continue with partial functionality
        }
    }

    /**
     * Debug version of includes - test each file individually
     */
    private function includes_debug() {
        try {
            // Database class
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php', 'Database class');
            
            // Models
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'models/LearnerModel.php', 'Learner model');
            
            // Controllers
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'controllers/LearnerController.php', 'Learner controller');
            
            // Shortcodes
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-capture-shortcode.php', 'Capture shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-display-shortcode.php', 'Display shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-update-shortcode.php', 'Update shortcode');
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learner-single-display-shortcode.php', 'Single learner display shortcode');
            
            // AJAX handlers
            $this->safe_include(WECOZA_LEARNERS_PLUGIN_DIR . 'ajax/learners-ajax-handlers.php', 'AJAX handlers');
            
            // Initialize controllers
            if (class_exists('\WeCoza\Controllers\LearnerController')) {
                new \WeCoza\Controllers\LearnerController();
            }
            
        } catch (Exception $e) {
            // error_log('WeCoza Learners Plugin: FATAL ERROR in includes_debug(): ' . $e->getMessage());
            // error_log('WeCoza Learners Plugin: Error file: ' . $e->getFile());
            // error_log('WeCoza Learners Plugin: Error line: ' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Safely include a file with error handling
     */
    private function safe_include($file_path, $description) {
        if (file_exists($file_path)) {
            try {
                require_once $file_path;
            } catch (Exception $e) {
                // error_log("WeCoza Learners Plugin: Error loading $description from $file_path: " . $e->getMessage());
                throw $e;
            }
        } else {
            // error_log("WeCoza Learners Plugin: File not found - $description at $file_path");
            throw new Exception("Required file not found: $file_path");
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        try {
            // Set up database credentials if not already configured
            $this->setup_database_credentials();
            
            // // Create database tables if needed
            // $this->create_tables();
            
            // Create upload directories
            $this->create_directories();
            
            // Flush rewrite rules
            flush_rewrite_rules();
            
        } catch (Exception $e) {
            // Log the full error
            // error_log('WeCoza Learners Plugin: ACTIVATION FAILED - ' . $e->getMessage());
            // error_log('WeCoza Learners Plugin: Stack trace: ' . $e->getTraceAsString());
            
            // Show user-friendly error message
            wp_die(
                '<h1>WeCoza Learners Plugin Activation Error</h1>' .
                '<p><strong>Error:</strong> ' . esc_html($e->getMessage()) . '</p>' .
                '<p>Please check the error log for more details and ensure:</p>' .
                '<ul>' .
                '<li>Database credentials are properly configured</li>' .
                '<li>PostgreSQL database is accessible</li>' .
                '<li>All plugin files are present</li>' .
                '</ul>' .
                '<p><a href="' . admin_url('plugins.php') . '">← Back to Plugins</a></p>',
                'WeCoza Learners Plugin Activation Error',
                array('back_link' => true)
            );
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Init function
     */
    public function init() {
        // Load plugin textdomain
        load_plugin_textdomain('wecoza-learners-plugin', false, dirname(WECOZA_LEARNERS_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue CSS with timestamp versioning
        $css_file = WECOZA_LEARNERS_PLUGIN_DIR . 'assets/css/learners-style.css';
        wp_enqueue_style(
            'wecoza-learners-style',
            WECOZA_LEARNERS_PLUGIN_URL . 'assets/css/learners-style.css',
            array(),
            WECOZA_LEARNERS_VERSION
        );
        
        // Enqueue JavaScript with timestamp versioning
        $app_js_file = WECOZA_LEARNERS_PLUGIN_DIR . 'assets/js/learners-app.js';
        wp_enqueue_script(
            'wecoza-learners-app',
            WECOZA_LEARNERS_PLUGIN_URL . 'assets/js/learners-app.js',
            array('jquery'),
            WECOZA_LEARNERS_VERSION,
            true
        );
        
        
        // Localize script with proper naming convention
        wp_localize_script('wecoza-learners-app', 'WeCozaLearners', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('learners_nonce_action'),
            'plugin_url' => WECOZA_LEARNERS_PLUGIN_URL,
            'uploads_url' => wp_upload_dir()['baseurl'],
            'home_url' => home_url(),
            'display_learners_url' => home_url('app/all-learners'),
            'view_learner_url' => home_url('app/view-learner'),
            'update_learner_url' => home_url('app/update-learners')
        ));
        
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {
        // Admin specific scripts/styles can be added here
    }
    
    /**
     * Set up database credentials
     */
    private function setup_database_credentials() {
        // Set default PostgreSQL credentials if not already configured  
        $credentials = [
            'wecoza_postgres_host' => 'db-wecoza-3-do-user-17263152-0.m.db.ondigitalocean.com',
            'wecoza_postgres_port' => '25060',
            'wecoza_postgres_dbname' => 'defaultdb',
            'wecoza_postgres_user' => 'doadmin'
        ];
        
        foreach ($credentials as $option_name => $default_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $default_value);
            }
        }
        
        // Log warning if password is not set
        if (empty(get_option('wecoza_postgres_password'))) {
            // error_log('WeCoza Learners Plugin: Database password not configured. Please set the wecoza_postgres_password option in WordPress admin.');
        }
    }
    
    /**
     * Create necessary database tables
     */
    private function create_tables() {
        try {
            // Include the database service
            require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/WeCozaLearnersDB.php';
            $db = WeCozaLearnersDB::getInstance();
            
            // Create learners table
            $learners_sql = "
                CREATE TABLE IF NOT EXISTS learners (
                    id SERIAL PRIMARY KEY,
                    first_name VARCHAR(100) NOT NULL,
                    initials VARCHAR(10),
                    surname VARCHAR(100) NOT NULL,
                    gender VARCHAR(20),
                    race VARCHAR(50),
                    sa_id_no VARCHAR(20),
                    passport_number VARCHAR(50),
                    tel_number VARCHAR(20),
                    alternative_tel_number VARCHAR(20),
                    email_address VARCHAR(255),
                    address_line_1 VARCHAR(255),
                    address_line_2 VARCHAR(255),
                    city_town_id INTEGER,
                    province_region_id INTEGER,
                    postal_code VARCHAR(10),
                    highest_qualification INTEGER,
                    assessment_status VARCHAR(50),
                    placement_assessment_date DATE,
                    numeracy_level INTEGER,
                    communication_level INTEGER,
                    employment_status BOOLEAN DEFAULT FALSE,
                    employer_id INTEGER,
                    disability_status BOOLEAN DEFAULT FALSE,
                    scanned_portfolio TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            
            // Create learner_portfolios table
            $portfolios_sql = "
                CREATE TABLE IF NOT EXISTS learner_portfolios (
                    portfolio_id SERIAL PRIMARY KEY,
                    learner_id INTEGER NOT NULL,
                    file_path VARCHAR(500) NOT NULL,
                    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            
            // Create learner_qualifications table
            $qualifications_sql = "
                CREATE TABLE IF NOT EXISTS learner_qualifications (
                    id SERIAL PRIMARY KEY,
                    qualification VARCHAR(255) NOT NULL UNIQUE
                )
            ";
            
            // Create learner_placement_level table
            $placement_level_sql = "
                CREATE TABLE IF NOT EXISTS learner_placement_level (
                    placement_level_id SERIAL PRIMARY KEY,
                    level VARCHAR(50) NOT NULL UNIQUE
                )
            ";
            
            // Create locations table
            $locations_sql = "
                CREATE TABLE IF NOT EXISTS locations (
                    location_id SERIAL PRIMARY KEY,
                    suburb VARCHAR(255),
                    town VARCHAR(255),
                    province VARCHAR(255),
                    postal_code VARCHAR(10)
                )
            ";
            
            // Create employers table
            $employers_sql = "
                CREATE TABLE IF NOT EXISTS employers (
                    employer_id SERIAL PRIMARY KEY,
                    employer_name VARCHAR(255) NOT NULL
                )
            ";
            
            // Execute table creation statements
            $tables = [
                'learner_qualifications' => $qualifications_sql,
                'learner_placement_level' => $placement_level_sql,
                'locations' => $locations_sql,
                'employers' => $employers_sql,
                'learners' => $learners_sql,
                'learner_portfolios' => $portfolios_sql
            ];
            
            foreach ($tables as $table_name => $sql) {
                try {
                    $db->exec($sql);
                } catch (Exception $e) {
                    // error_log("WeCoza Learners Plugin: Error creating table $table_name: " . $e->getMessage());
                    throw $e;
                }
            }
            
            // Create indexes
            $indexes = [
                "CREATE INDEX IF NOT EXISTS idx_learners_email ON learners(email_address)",
                "CREATE INDEX IF NOT EXISTS idx_learners_id_number ON learners(sa_id_no)",
                "CREATE INDEX IF NOT EXISTS idx_learners_surname ON learners(surname)",
                "CREATE INDEX IF NOT EXISTS idx_learner_portfolios_learner_id ON learner_portfolios(learner_id)",
                "CREATE INDEX IF NOT EXISTS idx_locations_town ON locations(LOWER(town))",
                "CREATE INDEX IF NOT EXISTS idx_locations_province ON locations(LOWER(province))"
            ];
            
            foreach ($indexes as $index_sql) {
                try {
                    $db->exec($index_sql);
                } catch (Exception $e) {
                    // error_log("WeCoza Learners Plugin: Error creating index: " . $e->getMessage());
                    // Continue with other indexes even if one fails
                }
            }
            
        } catch (Exception $e) {
            // error_log('WeCoza Learners Plugin: Error creating database tables: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create necessary directories
     */
    private function create_directories() {
        $upload_dir = wp_upload_dir();
        $portfolios_dir = $upload_dir['basedir'] . '/portfolios';
        
        if (!file_exists($portfolios_dir)) {
            wp_mkdir_p($portfolios_dir);
        }
    }
}

/**
 * Main function to get plugin instance
 */
function wecoza_learners() {
    try {
        return WeCoza_Learners_Plugin::instance();
    } catch (Exception $e) {
        // error_log('WeCoza Learners Plugin: Fatal error in main function: ' . $e->getMessage());
        
        // Show admin notice if in admin context
        if (is_admin()) {
            add_action('admin_notices', function() use ($e) {
                ?>
                <div class="notice notice-error">
                    <p><strong>WeCoza Learners Plugin Fatal Error:</strong></p>
                    <p><?php echo esc_html($e->getMessage()); ?></p>
                    <p>Please check the error log and ensure all requirements are met.</p>
                </div>
                <?php
            });
        }
        
        return false;
    }
}

// Check plugin requirements before initialization
function wecoza_learners_check_requirements() {
    $errors = array();
    
    // Check if required files exist
    $required_files = array(
        // 'includes/learners-functions.php', // Skipped to avoid conflicts with theme
        'database/WeCozaLearnersDB.php',
        'database/learners-db.php',
        'controllers/LearnerController.php'
    );
    
    foreach ($required_files as $file) {
        if (!file_exists(WECOZA_LEARNERS_PLUGIN_DIR . $file)) {
            $errors[] = "Required file missing: $file";
        }
    }
    
    // Check database password
    if (empty(get_option('wecoza_postgres_password'))) {
        $errors[] = "Database password not configured (wecoza_postgres_password option)";
    }
    
    return $errors;
}

// Add deactivation notice function
function wecoza_learners_deactivate_with_notice($message) {
    deactivate_plugins(plugin_basename(__FILE__));
    wp_die(
        '<h1>WeCoza Learners Plugin - Activation Failed</h1>' .
        '<p><strong>Error:</strong> ' . esc_html($message) . '</p>' .
        '<p><a href="' . admin_url('plugins.php') . '">← Back to Plugins</a></p>',
        'Plugin Activation Error',
        array('back_link' => true)
    );
}

// Initialize plugin with comprehensive error checking
$requirement_errors = wecoza_learners_check_requirements();

if (!empty($requirement_errors)) {
    // error_log('WeCoza Learners Plugin: Requirement errors found: ' . implode('; ', $requirement_errors));
    add_action('admin_init', function() use ($requirement_errors) {
        if (is_plugin_active(plugin_basename(__FILE__))) {
            wecoza_learners_deactivate_with_notice(implode('; ', $requirement_errors));
        }
    });
} else {
    // Initialize plugin only if requirements are met
    try {
        wecoza_learners();
    } catch (Exception $e) {
        // error_log('WeCoza Learners Plugin: Exception during initialization: ' . $e->getMessage());
        // error_log('WeCoza Learners Plugin: Exception file: ' . $e->getFile());
        // error_log('WeCoza Learners Plugin: Exception line: ' . $e->getLine());
        add_action('admin_init', function() use ($e) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                wecoza_learners_deactivate_with_notice($e->getMessage());
            }
        });
    } catch (Error $e) {
        // error_log('WeCoza Learners Plugin: Fatal PHP Error during initialization: ' . $e->getMessage());
        // error_log('WeCoza Learners Plugin: Fatal error file: ' . $e->getFile());
        // error_log('WeCoza Learners Plugin: Fatal error line: ' . $e->getLine());
        // Catch PHP 7+ fatal errors
        add_action('admin_init', function() use ($e) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                wecoza_learners_deactivate_with_notice('Fatal PHP Error: ' . $e->getMessage());
            }
        });
    }
}