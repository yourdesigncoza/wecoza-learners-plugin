<?php
/**
 * Plugin Name: WeCoza Learners Management
 * Plugin URI: https://yourdesign.co.za/
 * Description: Comprehensive learners management system for WeCoza including capture, display, update, and portfolio management functionality.
 * Version: 1.0.0
 * Author: YourDesign.co.za
 * Author URI: https://yourdesign.co.za/
 * Text Domain: wecoza-learners-plugin
 * Domain Path: /languages
 * 
 * @package WeCoza_Learners
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
define('WECOZA_LEARNERS_VERSION', '1.0.0');
define('WECOZA_LEARNERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WECOZA_LEARNERS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WECOZA_LEARNERS_PLUGIN_BASENAME', plugin_basename(__FILE__));

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
        $this->init_hooks();
        $this->includes();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Init action
        add_action('init', array($this, 'init'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core includes
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'includes/learners-functions.php';
        
        // Database
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php';
        
        // Shortcodes
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-capture-shortcode.php';
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-display-shortcode.php';
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'shortcodes/learners-update-shortcode.php';
        
        // Models
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'models/LearnerModel.php';
        
        // Controllers
        require_once WECOZA_LEARNERS_PLUGIN_DIR . 'controllers/LearnerController.php';
        
        // Initialize controllers
        new \WeCoza\Controllers\LearnerController();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Create upload directories
        $this->create_directories();
        
        // Flush rewrite rules
        flush_rewrite_rules();
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
        // Enqueue CSS
        wp_enqueue_style(
            'wecoza-learners-style',
            WECOZA_LEARNERS_PLUGIN_URL . 'assets/css/learners-style.css',
            array(),
            WECOZA_LEARNERS_VERSION
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'wecoza-learners-app',
            WECOZA_LEARNERS_PLUGIN_URL . 'assets/js/learners-app.js',
            array('jquery'),
            WECOZA_LEARNERS_VERSION,
            true
        );
        
        wp_enqueue_script(
            'wecoza-learners-display',
            WECOZA_LEARNERS_PLUGIN_URL . 'assets/js/learners-display-shortcode.js',
            array('jquery'),
            WECOZA_LEARNERS_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('wecoza-learners-app', 'wecoza_learners', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('learners_nonce'),
            'plugin_url' => WECOZA_LEARNERS_PLUGIN_URL,
            'uploads_url' => wp_upload_dir()['baseurl']
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {
        // Admin specific scripts/styles can be added here
    }
    
    /**
     * Create necessary database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // SQL for creating tables would go here
        // This is just a placeholder - actual table creation depends on your database structure
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Example table creation (adjust according to your needs):
        /*
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wecoza_learners (
            id int(11) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            surname varchar(100) NOT NULL,
            -- Add other fields as needed
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        dbDelta($sql);
        */
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
    return WeCoza_Learners_Plugin::instance();
}

// Initialize plugin (commented out - will be active when moved to plugins directory)
// wecoza_learners();