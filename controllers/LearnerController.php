<?php
/**
 * LearnerController.php
 * 
 * Controller for handling learner-related operations
 * Integrates with existing shortcodes and AJAX handlers
 */

namespace WeCoza\Controllers;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class LearnerController {
    /**
     * Constructor
     */
    public function __construct() {
        // Register WordPress hooks
        add_action('init', [$this, 'init']);
        
        // Register AJAX handlers
        // Note: get_learner_data_by_id is handled in ajax/learners-ajax-handlers.php to avoid conflicts
        add_action('wp_ajax_update_learner', [$this, 'handleUpdateLearner']);
        // Note: delete_learner is handled in ajax/learners-ajax-handlers.php to avoid conflicts
        add_action('wp_ajax_delete_learner_portfolio', [$this, 'handleDeleteLearnerPortfolio']);
        
        // Note: fetch_learners_dropdown_data is handled in ajax/learners-ajax-handlers.php to avoid conflicts
    }

    /**
     * Initialize the controller
     */
    public function init() {
        // Register the MVC shortcodes (simple implementations)
        add_shortcode('wecoza_learner_capture', [$this, 'captureLearner']);
        add_shortcode('wecoza_learner_display', [$this, 'displayLearner']);
        add_shortcode('wecoza_learner_update', [$this, 'updateLearner']);
    }

    /**
     * Handle learner capture shortcode (MVC version)
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function captureLearner($atts = []) {
        $atts = shortcode_atts([
            'form_id' => 'wecoza_learner_form'
        ], $atts);
        
        ob_start();
        ?>
        <div class="wecoza-learner-mvc-form">
            <h3>WeCoza Learner Registration (MVC)</h3>
            <p>Basic MVC implementation - for full functionality use: [wecoza_learners_form]</p>
            <form id="<?php echo esc_attr($atts['form_id']); ?>" method="post">
                <?php wp_nonce_field('wecoza_learner_nonce', 'learner_nonce'); ?>
                
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email_address">Email:</label>
                    <input type="email" id="email_address" name="email_address" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Register Learner</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle learner display shortcode (MVC version)
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function displayLearner($atts = []) {
        $atts = shortcode_atts([
            'limit' => 10
        ], $atts);
        
        try {
            // Get learner data from database
            if (defined('WECOZA_LEARNERS_PLUGIN_DIR')) {
                require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php';
            } else {
                require_once dirname(__DIR__) . '/database/learners-db.php';
            }
            $learner_db = new \learner_DB();
            $learners = $learner_db->get_all_learners($atts['limit']);
            
            ob_start();
            ?>
            <div class="wecoza-learners-mvc-display">
                <h3>WeCoza Learners (MVC)</h3>
                <p>Basic MVC implementation - for full functionality use: [wecoza_display_learners]</p>
                
                <?php if (!empty($learners)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Email</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($learners as $learner): ?>
                                <tr>
                                    <td><?php echo esc_html($learner['id']); ?></td>
                                    <td><?php echo esc_html($learner['first_name']); ?></td>
                                    <td><?php echo esc_html($learner['surname']); ?></td>
                                    <td><?php echo esc_html($learner['email_address']); ?></td>
                                    <td><?php echo esc_html($learner['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No learners found.</p>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
            
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Error displaying learners: ' . $e->getMessage());
            return '<p>Error loading learner data. Please check the database connection.</p>';
        }
    }

    /**
     * Handle learner update shortcode (MVC version)
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function updateLearner($atts = []) {
        $atts = shortcode_atts([
            'learner_id' => 0
        ], $atts);
        
        ob_start();
        ?>
        <div class="wecoza-learner-mvc-update">
            <h3>WeCoza Learner Update (MVC)</h3>
            <p>Basic MVC implementation - for full functionality use: [wecoza_learners_update_form]</p>
            <p>Learner ID: <?php echo esc_html($atts['learner_id']); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler for getting learner data by ID
     */
    public function handleGetLearnerDataById() {
        // Verify nonce
        check_ajax_referer('learners_nonce', 'nonce');
        
        $learner_id = filter_input(INPUT_POST, 'learner_id', FILTER_VALIDATE_INT);
        
        if (!$learner_id) {
            wp_send_json_error(['message' => 'Invalid learner ID']);
            return;
        }
        
        try {
            if (defined('WECOZA_LEARNERS_PLUGIN_DIR')) {
                require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php';
            } else {
                require_once dirname(__DIR__) . '/database/learners-db.php';
            }
            $learner_db = new \learner_DB();
            $learner = $learner_db->get_learner_by_id($learner_id);
            
            if ($learner) {
                wp_send_json_success($learner);
            } else {
                wp_send_json_error(['message' => 'Learner not found']);
            }
            
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin AJAX Error: ' . $e->getMessage());
            wp_send_json_error(['message' => 'Database error']);
        }
    }

    /**
     * AJAX handler for updating learner
     */
    public function handleUpdateLearner() {
        // Verify nonce
        check_ajax_referer('learners_nonce', 'nonce');
        
        // This would integrate with the existing AJAX handler
        wp_send_json_success(['message' => 'MVC Update handler - use existing AJAX handlers for full functionality']);
    }

    /**
     * AJAX handler for deleting learner
     */
    public function handleDeleteLearner() {
        // Verify nonce
        check_ajax_referer('learners_nonce', 'nonce');
        
        // This would integrate with the existing AJAX handler
        wp_send_json_success(['message' => 'MVC Delete handler - use existing AJAX handlers for full functionality']);
    }

    /**
     * AJAX handler for fetching dropdown data
     */
    public function handleFetchDropdownData() {
        try {
            if (defined('WECOZA_LEARNERS_PLUGIN_DIR')) {
                require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/learners-db.php';
            } else {
                require_once dirname(__DIR__) . '/database/learners-db.php';
            }
            $learner_db = new \learner_DB();
            
            $data = [
                'locations' => $learner_db->get_locations(),
                'qualifications' => $learner_db->get_qualifications(),
                'levels' => $learner_db->get_placement_level(),
                'employers' => $learner_db->get_employers()
            ];
            
            wp_send_json_success($data);
            
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin AJAX Error: ' . $e->getMessage());
            wp_send_json_error(['message' => 'Database error']);
        }
    }

    /**
     * AJAX handler for deleting learner portfolio
     */
    public function handleDeleteLearnerPortfolio() {
        // Verify nonce
        check_ajax_referer('learners_nonce', 'nonce');
        
        // This would integrate with the existing AJAX handler
        wp_send_json_success(['message' => 'MVC Portfolio delete handler - use existing AJAX handlers for full functionality']);
    }
}
