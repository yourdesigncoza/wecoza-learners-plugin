<?php
/*------------------YDCOZA-----------------------*/
/* Wecoza Learners Update Form Shortcode          */
/* This shortcode renders a form to update         */
/* existing learner information with validation    */
/* and file handling.                             */
/*-----------------------------------------------*/

function wecoza_learners_update_form_shortcode($atts) {
    
    global $wpdb;

    // Retrieve the saved URL for the redirect
    $redirect_update_url = get_option('wecoza_learners_update_form_url');

    // Initialize variables for form errors and data
    $form_error = false;
    $error_messages = [];
    $data = [];

    // Get learner ID from URL
    $learner_id = isset($_GET['learner_id']) ? intval($_GET['learner_id']) : 0;
    
    // Validate learner ID exists
    if (!$learner_id) {
        return '<div class="alert alert-subtle-danger">Invalid learner ID</div>';
    }

    // Initialize DB and get learner data
    $db = new learner_DB();
    $learner = $db->get_learner_by_id($learner_id);

    // print_r($learner);

    if (!$learner) {
        return '<div class="alert alert-subtle-danger">Learner not found</div>';
    }

            // Fetch locations, qualifications and employers for dropdowns
            $locations = $db->get_locations();
            $employers = $db->get_employers();
            $qualifications = $db->get_qualifications();
            // $communication_level = $db->get_placement_level();
            $portfolios = $db->get_learner_portfolios($learner_id);


    // Disability status, Booolean, needs a bit of work in PHP 
    if ($learner !== false) {
        $learner->disability_status = isset($learner->disability_status) ? (bool) $learner->disability_status : false;
        // error_log("Disability Status (after conversion): " . var_export($learner->disability_status, true));
    }


    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wecoza_learners_update_form_nonce']) && 
        wp_verify_nonce($_POST['wecoza_learners_update_form_nonce'], 'submit_learners_update_form')) {

        // Handle file upload if new file is provided
        $upload_dir = wp_upload_dir();
        $portfolios_dir = $upload_dir['basedir'] . '/portfolios/';
        
        if (!file_exists($portfolios_dir)) {
            wp_mkdir_p($portfolios_dir);
        }


        // Sanitize and prepare form inputs
        $data = [
            'id' => $learner_id,
            'first_name' => sanitize_text_field($_POST['first_name']),
            'initials' => sanitize_text_field($_POST['initials']),
            'surname' => sanitize_text_field($_POST['surname']),
            'gender' => sanitize_text_field($_POST['gender']),
            'race' => sanitize_text_field($_POST['race']),
            'sa_id_no' => sanitize_text_field($_POST['sa_id_no']),
            'passport_number' => sanitize_text_field($_POST['passport_number']),
            'tel_number' => sanitize_text_field($_POST['tel_number']),
            'alternative_tel_number' => sanitize_text_field($_POST['alternative_tel_number']),
            'email_address' => sanitize_email($_POST['email_address']),
            'address_line_1' => sanitize_text_field($_POST['address_line_1']),
            'address_line_2' => sanitize_text_field($_POST['address_line_2']),
            'city_town_id' => intval($_POST['city_town_id']),
            'province_region_id' => intval($_POST['province_region_id']),
            'postal_code' => sanitize_text_field($_POST['postal_code']),
            'highest_qualification' => sanitize_text_field($_POST['highest_qualification']),
            'assessment_status' => sanitize_text_field($_POST['assessment_status']),
            'placement_assessment_date' => sanitize_text_field($_POST['placement_assessment_date']),
            'communication_level' => intval($_POST['communication_level']),
            'employment_status' => isset($_POST['employment_status']) ? (int)filter_var($_POST['employment_status'], FILTER_VALIDATE_BOOLEAN) : 0,
            'employer_id' => intval($_POST['employer_id']),
            'disability_status' => isset($_POST['disability_status']) ? (int)filter_var($_POST['disability_status'], FILTER_VALIDATE_BOOLEAN) : 0,
            'scanned_portfolio' => $learner->scanned_portfolio,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Server-side validation disabled - using frontend validation only
        // All validation is handled by JavaScript and Bootstrap validation


    // Proceed only if there are no errors
    if (!$form_error) {

        // Update learner data
        if ($db->update_learner($data)) {
            // Handle new file uploads if present
            if (isset($_FILES['scanned_portfolio']) && !empty($_FILES['scanned_portfolio']['name'][0])) {
                $upload_result = $db->saveLearnerPortfolios($learner_id, $_FILES['scanned_portfolio']);

                    if ($upload_result['success']) {

                        // Show success message and redirect after a delay
                        echo '<div class="alert alert-subtle-success alert-dismissible fade show" role="alert">
                            Learner updated successfully! Files have been uploaded.
                            <div class="mt-2">
                                <small class="text-muted">Redirecting you to the learners list...</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                        
                        // Add JavaScript for delayed redirect with progress
                        echo '<script>
                            setTimeout(function() {
                                window.location.href = "' . esc_url($redirect_update_url) . '/?updated=true";
                            }, 2000); // 2 second delay
                        </script>';
                        return;
                    } else {
                        echo '<div class="alert alert-subtle-warning alert-dismissible fade show" role="alert">
                            Learner information updated, but some files could not be uploaded: ' . esc_html($upload_result['message']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    }

                // For the case when no new files were uploaded:
                } else {
                    // Show success message and redirect after a delay
                    echo '<div class="alert alert-subtle-success alert-dismissible fade show" role="alert">
                        Learner information has been updated successfully!
                        <div class="mt-2">
                            <small class="text-muted">Redirecting you to the learners list...</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "' . esc_url($redirect_update_url) . '/?updated=true";
                        }, 2000); // 1 second delay
                    </script>';
                    return;
                }

                
        } else {
            $error_messages[] = 'There was an error updating the learner. Please try again.';
        }
    } else {
        // Display any validation error messages
        foreach ($error_messages as $message) {
            echo '<div class="alert alert-subtle-danger">' . esc_html($message) . '</div>';
        }
    }


    }

    // Start building the form HTML
    ob_start();
    
    // Display any error messages
    if (!empty($error_messages)) {
        foreach ($error_messages as $message) {
            echo '<div class="alert alert-subtle-danger">' . esc_html($message) . '</div>';
        }
    }
    ?>
    <form id="learners-update-form" class="needs-validation ydcoza-compact-form" novalidate method="POST" enctype="multipart/form-data">
    <div class="container container-md ms-0">
        <?php wp_nonce_field('submit_learners_update_form', 'wecoza_learners_update_form_nonce'); ?>
        <input type="hidden" name="learner_id" value="<?php echo esc_attr($learner_id); ?>">
        <div class="row">
            <div class="col-md-4">
                <!-- Personal Information Section -->
                <div class="mb-1">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control form-control-sm" required 
                           value="<?php echo esc_attr($learner->first_name); ?>">
                    <div class="invalid-feedback">Please provide a first name.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label for="initials" class="form-label">Initials <span class="text-danger">*</span></label>
                    <input type="text" id="initials" name="initials" class="form-control form-control-sm" required 
                           value="<?php echo esc_attr($learner->initials); ?>">
                    <div class="invalid-feedback">Please provide initials.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label for="surname" class="form-label">Surname <span class="text-danger">*</span></label>
                    <input type="text" id="surname" name="surname" class="form-control form-control-sm" required 
                           value="<?php echo esc_attr($learner->surname); ?>">
                    <div class="invalid-feedback">Please provide a surname.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <!-- Contact Information -->
                <div class="mb-1">
                    <label for="tel_number" class="form-label">Telephone Number <span class="text-danger">*</span></label>
                    <input type="text" id="tel_number" name="tel_number" class="form-control form-control-sm" required
                           value="<?php echo esc_attr($learner->tel_number); ?>">
                    <div class="invalid-feedback">Please provide a valid telephone number.</div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Race Field -->
                <div class="mb-1">
                    <label for="race" class="form-label">Race <span class="text-danger">*</span></label>
                    <select id="race" name="race" class="form-select form-select-sm" required>
                        <option value="">Select Race</option>
                        <?php
                        $races = ['Black', 'White', 'Coloured', 'Indian'];
                        foreach ($races as $race) {
                            echo '<option value="' . esc_attr($race) . '"' . 
                                 selected($learner->race, $race, false) . '>' . 
                                 esc_html($race) . '</option>';
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Please select a race.</div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Gender -->
                <div class="mb-1">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select id="gender" name="gender" class="form-select form-select-sm" required>
                        <option value="">Select Gender</option>
                        <option value="Male"<?php selected($learner->gender, 'Male'); ?>>Male</option>
                        <option value="Female"<?php selected($learner->gender, 'Female'); ?>>Female</option>
                    </select>
                    <div class="invalid-feedback">Please select a gender.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <!-- Identification Section -->
                <div class="mb-1">
                    <label class="form-label">Identification Type <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="id_type" id="sa_id_option" value="sa_id" 
                                       <?php checked(!empty($learner->sa_id_no)); ?> required>
                                <label class="form-check-label" for="sa_id_option">SA ID</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="id_type" id="passport_option" value="passport"
                                       <?php checked(!empty($learner->passport_number)); ?> required>
                                <label class="form-check-label" for="passport_option">Passport</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div id="sa_id_field" class="mb-3 <?php echo empty($learner->sa_id_no) ? 'd-none' : ''; ?>">
                    <label for="sa_id_no" class="form-label">SA ID Number</label>
                    <input type="text" id="sa_id_no" name="sa_id_no" class="form-control form-control-sm"
                           value="<?php echo esc_attr($learner->sa_id_no); ?>">
                </div>

                <div id="passport_field" class="mb-3 <?php echo empty($learner->passport_number) ? 'd-none' : ''; ?>">
                    <label for="passport_number" class="form-label">Passport Number</label>
                    <input type="text" id="passport_number" name="passport_number" class="form-control form-control-sm"
                           value="<?php echo esc_attr($learner->passport_number); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-1">
                    <label for="alternative_tel_number" class="form-label">Alternative Telephone Number</label>
                    <input type="text" id="alternative_tel_number" name="alternative_tel_number" class="form-control form-control-sm"
                           value="<?php echo esc_attr($learner->alternative_tel_number); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-1">
                    <label for="email_address" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="email_address" name="email_address" class="form-control form-control-sm" required
                           value="<?php echo esc_attr($learner->email_address); ?>">
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- Address Information -->
                <div class="mb-1">
                    <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                    <input type="text" id="address_line_1" name="address_line_1" class="form-control form-control-sm" required
                           value="<?php echo esc_attr($learner->address_line_1); ?>">
                    <div class="invalid-feedback">Please provide a valid address.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-1">
                    <label for="address_line_2" class="form-label">Address Line 2</label>
                    <input type="text" id="address_line_2" name="address_line_2" class="form-control form-control-sm"
                           value="<?php echo esc_attr($learner->address_line_2); ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <!-- City/Town Dropdown -->
                <div class="mb-1">
                    <label for="city_town_id" class="form-label">City/Town <span class="text-danger">*</span></label>
                    <select id="city_town_id" name="city_town_id" class="form-select form-select-sm" required>
                        <option value="">Select City/Town</option>
                        <?php foreach ($locations['cities'] as $city): ?>
                            <option value="<?php echo esc_attr($city['location_id']); ?>"
                                    <?php selected($learner->city_town_id, $city['location_id']); ?>>
                                <?php echo esc_html($city['town']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a valid city or town.</div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Province/Region Dropdown -->
                <div class="mb-1">
                    <label for="province_region_id" class="form-label">Province/Region <span class="text-danger">*</span></label>
                    <select id="province_region_id" name="province_region_id" class="form-select form-select-sm" required>
                        <option value="">Select Province/Region</option>
                        <?php foreach ($locations['provinces'] as $province): ?>
                            <option value="<?php echo esc_attr($province['location_id']); ?>"
                                    <?php selected($learner->province_region_id, $province['location_id']); ?>>
                                <?php echo esc_html($province['province']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a valid province or region.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control form-control-sm" required
                           value="<?php echo esc_attr($learner->postal_code); ?>">
                    <div class="invalid-feedback">Please provide a valid postal code.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
        <!-- Assessment Information -->
        <div class="mb-1">
            <label for="highest_qualification" class="form-label">Highest Qualification <span class="text-danger">*</span></label>
            <select id="highest_qualification" name="highest_qualification" class="form-select form-select-sm">
                <option value="">Select Highest Qualification</option>
                <?php foreach ($qualifications as $qualification): ?>
                    <option value="<?php echo esc_attr($qualification['id']); ?>"
                            <?php selected($learner->highest_qualification, $qualification['qualification']); ?>>
                        <?php echo esc_attr($qualification['qualification']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select an employer.</div>
        </div>
            </div>
            <div class="col-md-4">
                <!-- Disability Status -->
                <div class="mb-1">
                    <label for="disability_status" class="form-label">Disability Status <span class="text-danger">*</span></label>
                    <select id="disability_status" name="disability_status" class="form-select form-select-sm" required>
                        <option value="0" <?php echo $learner->disability_status == 0 ? 'selected' : ''; ?>>Not Disabled</option>
                        <option value="1" <?php echo $learner->disability_status == 1 ? 'selected' : ''; ?>>Has Disability</option>
                    </select>
                    <div class="invalid-feedback">Please provide your disability status.</div>
                </div>
            </div>
            <div class="col-md-4">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-1">
                    <label for="assessment_status" class="form-label">Assessment Status <span class="text-danger">*</span></label>
                    <select id="assessment_status" name="assessment_status" class="form-select form-select-sm" required>
                        <option value="">Select Assessment Status</option>
                        <option value="Assessed" <?php selected($learner->assessment_status, 'Assessed'); ?>>Assessed</option>
                        <option value="Not Assessed" <?php selected($learner->assessment_status, 'Not Assessed'); ?>>Not Assessed</option>
                    </select>
                    <div class="invalid-feedback">Please select an assessment status.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1 placement_date_outerdiv" id="placement_date_div" <?php echo $learner->assessment_status !== 'Assessed' ? 'style="display:none;"' : ''; ?>>
                    <label for="placement_assessment_date" class="form-label">Placement Assessment Date <span class="text-danger">*</span></label>
                    <input type="date" id="placement_assessment_date" name="placement_assessment_date" class="form-control form-control-sm"
                           value="<?php echo esc_attr($learner->placement_assessment_date); ?>">
                    <div class="invalid-feedback">Please select the placement assessment date.</div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Communication Placement Level -->
                <div class="mb-1 placement_date_outerdiv" id="placement_level_div" <?php echo $learner->assessment_status !== 'Assessed' ? 'style="display:none;"' : ''; ?>>
                    <label for="communication_level" class="form-label">Assessment Communication Level <span class="text-danger">*</span></label>
                    <select id="communication_level" name="communication_level" class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="invalid-feedback">Please select a Assessment Communication Level.</div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Numeracy Placement Level -->
                <div class="mb-1 placement_date_outerdiv" id="placement_level_div" <?php echo $learner->assessment_status !== 'Assessed' ? 'style="display:none;"' : ''; ?>>
                    <label for="numeracy_level" class="form-label">Assessment Numeracy Level <span class="text-danger">*</span></label>
                    <select id="numeracy_level" name="numeracy_level" class="form-select form-select-sm">
                    <option value="">Loading...</option>
                    </select>
                    <div class="invalid-feedback">Please select a Assessment Communication Level.</div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <!-- Portfolio Upload -->
                <div class="mb-1">
                    <label for="scanned_portfolio" class="form-label">Scanned Portfolio (PDF only)</label>
                    <?php 
                    if (!empty($portfolios)): ?>
                        <div class="mb-1">
                            <?php foreach ($portfolios as $index => $portfolio): ?>
                                <div class="d-flex align-items-center mb-0 portfolio-item">
                                    <span class="me-2 small" style="font-size:12px">Portfolio <?php echo $index + 1; ?></span>
                                    <span class="text-muted me-2 small" style="font-size:12px">
                                        (Uploaded: <?php echo date('Y-m-d', strtotime($portfolio['upload_date'])); ?>)
                                    </span>
                                    <button type="button"
                                            class="delete-portfolio badge rounded-pill bg-danger"
                                            style="padding-top:3px; padding-bottom:3px"
                                            data-portfolio-id="<?php echo esc_attr($portfolio['portfolio_id']); ?>"
                                            data-learner-id="<?php echo esc_attr($learner_id); ?>">
                                        Delete
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-0">
                        <label class="form-label">Add New Portfolio Files:</label>
                        <input type="file" 
                               id="scanned_portfolio" 
                               name="scanned_portfolio[]" 
                               class="form-control form-control-sm" 
                               accept="application/pdf"
                               multiple>
                        <div class="form-text small" style="font-size:12px">You can select multiple PDF files to upload.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Employment Information -->
                <div class="mb-1">
                    <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
                    <select id="employment_status" name="employment_status" class="form-select form-select-sm" required>
                        <option value="">Select Employment Status</option>

                        print_r($learner->employment_status);
                        <option value="1" <?php selected($learner->employment_status, "Employed"); ?>>Employed</option>
                        <option value="0" <?php selected($learner->employment_status, "Unemployed"); ?>>Unemployed</option>
                    </select>
                    <div class="invalid-feedback">Please select an employment status.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div id="employer_field" class="mb-1" <?php echo !$learner->employment_status ? 'style="display:none;"' : ''; ?>>
                    <label for="employer_id" class="form-label">Employer <span class="text-danger">*</span></label>
                    <select id="employer_id" name="employer_id" class="form-select form-select-sm">
                        <option value="">Select Employer</option>
                        <?php foreach ($employers as $employer): ?>
                            <option value="<?php echo esc_attr($employer['employer_id']); ?>"
                                    <?php selected($learner->employer_id, $employer['employer_id']); ?>>
                                <?php echo esc_html($employer['employer_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select an employer.</div>
                </div>
            </div>
        </div>


        <!-- Form Actions -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-sm">Update Learner</button>
            <a href="<?php echo home_url(); ?>/update-learners/" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
    </div> <!-- ./container container-md -->
    </form>

    <script>
    jQuery(document).ready(function($) {
        // Form validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch dropdown data via AJAX
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=fetch_learners_dropdown_data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate Communication Levels
                    const communicationSelect = document.getElementById('communication_level');
                    communicationSelect.innerHTML = data.data.placement_levels.communication_levels.map(level => 
                        `<option value="${level.id}" ${level.name == <?php echo json_encode($learner->communication_level); ?> ? 'selected' : ''}>
                            ${level.name}
                        </option>`
                    ).join('');

                    // Populate Numeracy Levels
                    const numeracySelect = document.getElementById('numeracy_level');
                    numeracySelect.innerHTML = data.data.placement_levels.numeracy_levels.map(level => 
                        `<option value="${level.id}" ${level.name == <?php echo json_encode($learner->numeracy_level); ?> ? 'selected' : ''}>
                            ${level.name}
                        </option>`
                    ).join('');
                } else {
                    console.error('Failed to load placement levels:', data);
                }
            })
            .catch(error => console.error('Error fetching dropdown data:', error));
    });
    </script>


    <?php
    return ob_get_clean();
}
add_shortcode('wecoza_learners_update_form', 'wecoza_learners_update_form_shortcode');
