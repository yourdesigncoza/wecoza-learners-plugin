<?php
/**
 * Learners Management System
 *
 * This file contains core functionality for managing learners including file loading,
 * asset enqueueing, and AJAX handlers.
 *
 * @package WeCoza
 * @subpackage Learners
 * @since 1.0.0
 * 
 * PLUGIN CONVERSION NOTES:
 * When converting to plugin, replace:
 * - WECOZA_CHILD_DIR with WECOZA_LEARNERS_PLUGIN_DIR
 * - WECOZA_CHILD_URL with WECOZA_LEARNERS_PLUGIN_URL
 * - Update all file paths to use the new plugin directory structure
 */

/**
 * Load all required learner-related files (Plugin Version)
 *
 * @since 1.0.0
 * @return void
 */
if (!function_exists('load_learners_files_plugin')) {
    function load_learners_files_plugin() {
        // Note: Plugin version doesn't need to load files this way
        // Files are loaded via the main plugin class includes() method
        // This function exists for compatibility but does nothing
        return true;
    }
}

// Don't auto-load files here - they're loaded by the main plugin class
// load_learners_files_plugin();

/**
 * Enqueue necessary JavaScript and CSS files for learners functionality
 *
 * @since 1.0.0
 * @return void
 */
if (!function_exists('enqueue_learners_assets')) {
    function enqueue_learners_assets() {
    // Enqueue main learners JavaScript file
    wp_enqueue_script(
        'learners-app',
        WECOZA_CHILD_URL . '/assets/learners/js/learners-app.js',
        array('jquery'),
        WECOZA_PLUGIN_VERSION,
        true
    );

    // Get WordPress uploads directory information
    $uploads_dir = wp_upload_dir();

    // Localize script with necessary data
    wp_localize_script('learners-app', 'learners_nonce', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('learners_nonce'),
        'uploads_url' => $uploads_dir['baseurl'],
        'is_admin' => current_user_can('manage_options')
    ));
    } // End enqueue_learners_assets function
} // End function_exists check

// Register enqueue action if function exists
if (function_exists('enqueue_learners_assets')) {
    add_action('wp_enqueue_scripts', 'enqueue_learners_assets');
}


/**
 * NOTE: AJAX handler registration moved to ajax/learners-ajax-handlers.php
 * This duplicate registration has been removed to prevent conflicts.
 */

/**
 * Handle learner deletion via AJAX
 *
 * @since 1.0.0
 * @return void
 */
if (!function_exists('handle_delete_learner')) {
    function handle_delete_learner() {
    try {
        // Security checks
        if (!check_ajax_referer('learners_nonce', 'nonce', false)) {
            throw new Exception('Security check failed');
        }

        // Validate learner ID
        $learner_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$learner_id) {
            throw new Exception('Invalid learner ID');
        }

        // Delete learner
        $db = new learner_DB();
        $result = $db->delete_learner($learner_id);

        if ($result) {
            wp_send_json_success(array('message' => 'Learner deleted successfully'));
        } else {
            throw new Exception('Failed to delete learner');
        }

    } catch (Exception $e) {
        error_log('Error in delete_learner: ' . $e->getMessage());
        wp_send_json_error(array('message' => $e->getMessage()));
    }
    wp_die();
    } // End handle_delete_learner function
} // End function_exists check


/**
 * Handle portfolio deletion
 *
 * @since 1.0.0
 * @return void
 */
if (!function_exists('handle_portfolio_deletion')) {
    function handle_portfolio_deletion() {
    try {
        // Security checks
        if (!check_ajax_referer('learners_nonce', 'nonce', false)) {
            throw new Exception('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            throw new Exception('Unauthorized access');
        }

        // Validate input parameters
        $portfolio_id = filter_input(INPUT_POST, 'portfolio_id', FILTER_VALIDATE_INT);
        $learner_id = filter_input(INPUT_POST, 'learner_id', FILTER_VALIDATE_INT);

        if (!$portfolio_id || !$learner_id) {
            throw new Exception('Invalid portfolio or learner ID');
        }

        // Delete portfolio
        $db = new learner_DB();
        if ($db->deletePortfolioFile($portfolio_id)) {
            wp_send_json_success('Portfolio deleted successfully');
        } else {
            throw new Exception('Failed to delete portfolio');
        }

    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
    } // End handle_portfolio_deletion function
} // End function_exists check

// Register AJAX handler if function exists
if (function_exists('handle_portfolio_deletion')) {
    add_action('wp_ajax_delete_learner_portfolio', 'handle_portfolio_deletion');
}



/**
 * NOTE: fetch_learners_data AJAX handler is now registered in ajax/learners-ajax-handlers.php
 * This duplicate registration has been removed to prevent conflicts.
 */

/**
 * Generate HTML table rows for learners data
 *
 * @param array $learners Array of learner objects
 * @return string HTML string of table rows
 */
if (!function_exists('generate_learner_table_rows')) {
    function generate_learner_table_rows($learners) {
    $rows = '';
    foreach ($learners as $learner) {
        $buttons = sprintf(
            '<div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn bg-discovery-subtle view-details" data-id="%s">View</button>
                <a href="%s" class="btn bg-warning-subtle">Edit</a>
                <button class="btn btn-sm bg-danger-subtle delete-learner-btn" data-id="%s">Delete</button>
            </div>',
            esc_attr($learner->id ?? ''),
            esc_url(home_url('/update-learners/?learner_id=' . ($learner->id ?? ''))),
            esc_attr($learner->id ?? '')
        );

        $rows .= sprintf(
            '<tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td class="text-nowrap text-center">%s</td>
            </tr>',
            esc_html($learner->first_name ?? ''),
            esc_html($learner->surname ?? ''),
            esc_html($learner->gender ?? ''),
            esc_html($learner->race ?? ''),
            esc_html($learner->tel_number ?? ''),
            esc_html($learner->email_address ?? ''),
            esc_html($learner->city_town_name ?? ''),
            esc_html($learner->employment_status ?? ''),
            $buttons
        );
    }
    return $rows;
    } // End generate_learner_table_rows function
} // End function_exists check

/**
 * Fetch dropdown data needed for learners' forms and interactions.
 */
if (!function_exists('fetch_learners_dropdown_data')) {
    function fetch_learners_dropdown_data() {
    $db = new learner_DB();

    try {
        $locations = $db->get_locations();
        $qualifications = $db->get_qualifications();
        $employers = $db->get_employers();
        $placement_level = $db->get_placement_level();

        // Structure placement levels with filters
        $placement_levels_data = [
            // 'all_levels' => array_values(array_map(function($level) {
            //     return ['id' => $level['placement_level_id'], 'name' => $level['level']];
            // }, $placement_levels)),
            'numeracy_levels' => array_values(array_map(function($level) {
                return ['id' => $level['placement_level_id'], 'name' => $level['level']];
            }, array_filter($placement_level, function($level) {
                return strpos($level['level'], 'N') === 0; // Levels starting with 'N'
            }))),
            'communication_levels' => array_values(array_map(function($level) {
                return ['id' => $level['placement_level_id'], 'name' => $level['level']];
            }, array_filter($placement_level, function($level) {
                return strpos($level['level'], 'C') === 0; // Levels starting with 'C'
            })))
        ];



        $cities = array_map(function($city) {
            return ['id' => $city['location_id'], 'name' => $city['town']];
        }, $locations['cities']);

        $provinces = array_map(function($province) {
            return ['id' => $province['location_id'], 'name' => $province['province']];
        }, $locations['provinces']);

        $qualifications = array_map(function($qualification) {
            return ['id' => $qualification['id'], 'name' => $qualification['qualification']];
        }, $qualifications);

        $employers = array_map(function($employer) {
            return ['id' => $employer['employer_id'], 'name' => $employer['employer_name']];
        }, $employers);

        wp_send_json_success([
            'cities' => $cities,
            'provinces' => $provinces,
            'qualifications' => $qualifications,
            'employers' => $employers,
            'placement_levels' => $placement_levels_data,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
    } // End fetch_learners_dropdown_data function
} // End function_exists check

// Note: fetch_learners_dropdown_data AJAX registration moved to ajax/learners-ajax-handlers.php to prevent conflicts

// Plugin version doesn't need to include component files
// require_once WECOZA_CHILD_DIR . '/assets/learners/components/learner-detail.php';