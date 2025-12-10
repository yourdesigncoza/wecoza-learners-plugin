<?php
/**
 * AJAX Handlers for Learners Management
 * 
 * This file consolidates all AJAX operations for the learners functionality.
 * All handlers include proper security checks and error handling.
 * 
 * @package WeCoza_Learners
 * @subpackage AJAX
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
                    <a href="%s" class="btn bg-discovery-subtle">View</a>
                    <a href="%s" class="btn bg-warning-subtle">Edit</a>
                    <button class="btn btn-sm bg-danger-subtle delete-learner-btn" data-id="%s">Delete</button>
                </div>',
                esc_url(home_url('/app/view-learner/?learner_id=' . ($learner->id ?? ''))),
                esc_url(home_url('/app/update-learners/?learner_id=' . ($learner->id ?? ''))),
                esc_attr($learner->id ?? '')
            );

            // Create full name with title
            $title_with_period = !empty($learner->title) ? $learner->title . '. ' : '';
            $full_name = trim($title_with_period . ($learner->first_name ?? '') . ' ' . ($learner->surname ?? ''));
            
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
                esc_html($full_name),
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
    }
}


/**
 * Update learner information
 * 
 * AJAX action: update_learner
 */
function update_learner() {
    try {
        // Verify nonce
        if (!check_ajax_referer('learners_nonce', 'nonce', false)) {
            throw new Exception('Security check failed');
        }

        // Collect and validate data
        $data = [];
        $fields = [
            'id', 'title', 'first_name', 'second_name', 'initials', 'surname', 'gender', 'race',
            'sa_id_no', 'passport_number', 'tel_number', 'alternative_tel_number',
            'email_address', 'address_line_1', 'address_line_2', 'city_town_id',
            'province_region_id', 'postal_code', 'highest_qualification',
            'assessment_status', 'placement_assessment_date', 'numeracy_level',
            'communication_level', 'employment_status', 'employer_id',
            'disability_status', 'scanned_portfolio'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = sanitize_text_field($_POST[$field]);
            }
        }

        // Validate required fields
        if (empty($data['id'])) {
            throw new Exception('Learner ID is required');
        }

        // Update learner
        $db = new learner_DB();
        $result = $db->update_learner($data);

        if ($result) {
            wp_send_json_success(['message' => 'Learner updated successfully']);
        } else {
            throw new Exception('Failed to update learner');
        }

    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

/**
 * Delete learner
 * 
 * AJAX action: delete_learner
 */
function handle_delete_learner() {
    try {
        // Security checks
        if (!wp_verify_nonce($_POST['nonce'], 'learners_nonce_action')) {
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
            wp_send_json_success(['message' => 'Learner deleted successfully']);
        } else {
            throw new Exception('Failed to delete learner');
        }

    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

/**
 * Fetch learners data for display
 * 
 * AJAX action: fetch_learners_data
 */
function fetch_learners_data() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'learners_nonce_action')) {
        wp_send_json_error('Security check failed.');
        return;
    }

    try {
        // error_log('WeCoza Learners: Starting fetch_learners_data');
        
        $db = new learner_DB();
        $learners = $db->get_learners_mappings();

        // error_log('WeCoza Learners: Retrieved ' . count($learners) . ' learners');

        if (empty($learners)) {
            // error_log('WeCoza Learners: No learners found in database');
            throw new Exception('No learners found.');
        }

        $rows = generate_learner_table_rows($learners);
        // error_log('WeCoza Learners: Successfully generated table rows');
        wp_send_json_success($rows);

    } catch (Exception $e) {
        // error_log('WeCoza Learners: Error in fetch_learners_data - ' . $e->getMessage());
        wp_send_json_error($e->getMessage());
    }
}

/**
 * Fetch dropdown data for forms
 * 
 * AJAX action: fetch_learners_dropdown_data
 */
function fetch_learners_dropdown_data() {
    $db = new learner_DB();

    try {
        $locations = $db->get_locations();
        $qualifications = $db->get_qualifications();
        $employers = $db->get_employers();
        $placement_level = $db->get_placement_level();

        // Structure placement levels with filters
        $placement_levels_data = [
            'numeracy_levels' => array_values(array_map(function($level) {
                return ['id' => $level['placement_level_id'], 'name' => $level['level']];
            }, array_filter($placement_level, function($level) {
                return strpos($level['level'], 'N') === 0;
            }))),
            'communication_levels' => array_values(array_map(function($level) {
                return ['id' => $level['placement_level_id'], 'name' => $level['level']];
            }, array_filter($placement_level, function($level) {
                return strpos($level['level'], 'C') === 0;
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
}

/**
 * Handle portfolio deletion
 * 
 * AJAX action: delete_learner_portfolio
 */
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
}

/**
 * Register all AJAX handlers
 */
function register_learners_ajax_handlers() {
    $ajax_actions = [
        'update_learner' => 'update_learner',
        'delete_learner' => 'handle_delete_learner',
        'fetch_learners_data' => 'fetch_learners_data',
        'fetch_learners_dropdown_data' => 'fetch_learners_dropdown_data',
        'delete_learner_portfolio' => 'handle_portfolio_deletion'
    ];

    foreach ($ajax_actions as $action => $callback) {
        add_action("wp_ajax_{$action}", $callback);
        add_action("wp_ajax_nopriv_{$action}", $callback);
    }
}

// Register handlers on init
add_action('init', 'register_learners_ajax_handlers');