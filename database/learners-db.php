<?php
/**
 * Learner Database Handler
 *
 * This class handles all database operations for learner management including:
 * - CRUD operations for learner records
 * - Portfolio file management
 * - Data validation and sanitization
 * - Transaction management
 * - Caching with WordPress transients
 *
 * Key Features:
 * - Secure database interactions using prepared statements
 * - Comprehensive error handling and logging
 * - Support for file uploads and storage
 * - Data integrity checks
 * - Transaction rollback on failures
 *
 * @package Wecoza
 * @subpackage Learners
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the PostgreSQL database service
if (defined('WECOZA_LEARNERS_PLUGIN_DIR')) {
    require_once WECOZA_LEARNERS_PLUGIN_DIR . 'database/WeCozaLearnersDB.php';
} else {
    // Fallback for when called outside plugin context (e.g., from test files)
    require_once dirname(__FILE__) . '/WeCozaLearnersDB.php';
}

if (!class_exists('learner_DB')) {
    class learner_DB {
        private $db;

        public function __construct() {
            $this->db = WeCozaLearnersDB::getInstance();
        }


    /**
     * Check if database supports DISTINCT ON
     */
    private function supports_distinct_on() {
        try {
            $pdo = $this->db->getPdo();
            return $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
        } catch (Exception $e) {
            return false;
        }
    }



    /**
     * Get distinct locations for dropdowns
     * Returns separate arrays for cities and provinces without duplicates
     */
    public function get_locations() {
        try {
            $pdo = $this->db->getPdo();

            // Get distinct cities
            $cities_query = "
                SELECT DISTINCT ON (LOWER(town))
                    location_id,
                    town
                FROM locations
                WHERE town IS NOT NULL
                    AND town != ''
                ORDER BY LOWER(town), location_id ASC
            ";

            // Get distinct provinces
            $provinces_query = "
                SELECT DISTINCT ON (LOWER(province))
                    location_id,
                    province
                FROM locations
                WHERE province IS NOT NULL
                    AND province != ''
                ORDER BY LOWER(province), location_id ASC
            ";

            // For databases that don't support DISTINCT ON, use this alternative:
            if (!$this->supports_distinct_on()) {
                $cities_query = "
                    SELECT MIN(location_id) as location_id, town
                    FROM locations
                    WHERE town IS NOT NULL
                        AND town != ''
                    GROUP BY LOWER(town)
                    ORDER BY LOWER(town) ASC
                ";

                $provinces_query = "
                    SELECT MIN(location_id) as location_id, province
                    FROM locations
                    WHERE province IS NOT NULL
                        AND province != ''
                    GROUP BY LOWER(province)
                    ORDER BY LOWER(province) ASC
                ";
            }

            // Execute queries
            $cities_stmt = $pdo->prepare($cities_query);
            $provinces_stmt = $pdo->prepare($provinces_query);

            $cities_stmt->execute();
            $provinces_stmt->execute();

            return [
                'cities' => $cities_stmt->fetchAll(PDO::FETCH_ASSOC),
                'provinces' => $provinces_stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching locations: " . $e->getMessage());
            return [
                'cities' => [],
                'provinces' => []
            ];
        }
    }

    /*------------------YDCOZA-----------------------*/
    /* Fetch qualifications for dropdown select in learner form capture      */
    /*-----------------------------------------------*/
    public function get_qualifications() {
        try {
            $qua = $this->db->getPdo()->query("SELECT id, qualification FROM learner_qualifications");
            return $qua->fetchAll(\PDO::FETCH_ASSOC);
            $qua->execute();
        } catch (\PDOException $e) {
            error_log("Failed to fetch learner_qualifications: " . $e->getMessage());
            return [];
        }
    }
    /*------------------YDCOZA-----------------------*/
    /* Fetch Placement Levels for dropdown select in learner form capture      */
    /*-----------------------------------------------*/
    public function get_placement_level() {
        try {
            $lev = $this->db->getPdo()->query("SELECT placement_level_id, level FROM learner_placement_level ORDER BY level ASC");
            return $lev->fetchAll(\PDO::FETCH_ASSOC);
            $qua->execute();
        } catch (\PDOException $e) {
            error_log("Failed to fetch learner_numeracy_level: " . $e->getMessage());
            return [];
        }
    }


    public function get_employers() {
        $lrner = $this->db->getPdo()->prepare("SELECT employer_id, employer_name FROM employers ORDER BY employer_name ASC");
        $lrner->execute();
        return $lrner->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all learners with optional limit
     */
    public function get_all_learners($limit = null) {
        try {
            $sql = "SELECT id, title, first_name, surname, email_address, created_at FROM learners ORDER BY created_at DESC";
            
            if ($limit && is_numeric($limit)) {
                $sql .= " LIMIT " . intval($limit);
            }
            
            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Error getting all learners: ' . $e->getMessage());
            return [];
        }
    }

    /*------------------YDCOZA-----------------------*/
    /* Capture new learner      */
    /*-----------------------------------------------*/
    public function insert_learner($data) {
        try {
            $pdo = $this->db->getPdo();

            // Start transaction
            $pdo->beginTransaction();

            // Validate highest_qualification
            $qualification_check = $pdo->prepare("SELECT COUNT(*) FROM learner_qualifications WHERE id = :id");
            $qualification_check->execute(['id' => $data['highest_qualification']]);
            $qualification_exists = $qualification_check->fetchColumn() > 0;

            if (!$qualification_exists) {
                throw new Exception("Invalid highest qualification ID: " . $data['highest_qualification']);
            }

            // Prepare and execute the insert query
            $stmt = $pdo->prepare("
                INSERT INTO learners (
                    title, first_name, second_name, initials, surname, gender, race,
                    sa_id_no, passport_number, tel_number, alternative_tel_number,
                    email_address, address_line_1, address_line_2, city_town_id,
                    province_region_id, postal_code, highest_qualification,
                    assessment_status, placement_assessment_date, numeracy_level, communication_level,
                    employment_status, employer_id, disability_status, scanned_portfolio,
                    created_at, updated_at
                ) VALUES (
                    :title, :first_name, :second_name, :initials, :surname, :gender, :race,
                    :sa_id_no, :passport_number, :tel_number, :alternative_tel_number,
                    :email_address, :address_line_1, :address_line_2, :city_town_id,
                    :province_region_id, :postal_code, :highest_qualification,
                    :assessment_status, :placement_assessment_date, :numeracy_level, :communication_level,
                    :employment_status, :employer_id, :disability_status, :scanned_portfolio,
                    :created_at, :updated_at
                ) RETURNING id;
            ");

            $stmt->execute($data);

            // Clear the transient cache
            delete_transient('learner_db_get_learners_mappings');

            // Get the inserted ID
            $learner_id = $stmt->fetchColumn();

            // Commit transaction
            $pdo->commit();

            return $learner_id;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Error inserting learner: " . $e->getMessage());
            return false;
        }
    }


    /*------------------YDCOZA-----------------------*/
    /* Fetch all learners with qualifications         */
    /* Maps the highest_qualification to the actual   */
    /* qualification name from the learner_qualifications */
    /* Also maps location details (suburb, town,      */
    /* province, postal_code) from the locations table */
    /*-----------------------------------------------*/

public function get_learners_mappings() {

    // Check if the transient exists
    $cached_results = get_transient( 'learner_db_get_learners_mappings' );

    if ($cached_results !== false) {
        // error_log('WeCoza Learners DB: Returning cached results (' . count($cached_results) . ' learners)');
        return $cached_results;
    }

    try {
        $db = $this->db->getPdo();
        // error_log('WeCoza Learners DB: Database connection established');
    } catch (Exception $e) {
        error_log('WeCoza Learners DB: Failed to get database connection - ' . $e->getMessage());
        throw new Exception('Database connection failed: ' . $e->getMessage());
    }

$query = "
    WITH portfolio_info AS (
        SELECT
            learner_id,
            string_agg(file_path || '|' || upload_date::text, ', ' ORDER BY upload_date DESC) as portfolio_details
        FROM learner_portfolios
        GROUP BY learner_id
    )
    SELECT
        learners.*,
        learner_qualifications.qualification AS highest_qualification,
        locations.suburb AS suburb,
        locations.town AS city_town_name,
        locations.province AS province_region_name,
        locations.postal_code AS postal_code,
        employers.employer_name AS employer_name,
        numeracy_level_table.level AS numeracy_level,
        communication_level_table.level AS communication_level,
        CASE WHEN learners.employment_status = true THEN 'Employed' ELSE 'Unemployed' END AS employment_status,
        CASE WHEN learners.disability_status = true THEN 'Yes' ELSE 'No' END AS disability_status,
        portfolio_info.portfolio_details
    FROM learners
    LEFT JOIN learner_qualifications
        ON learners.highest_qualification = learner_qualifications.id
    LEFT JOIN locations
        ON learners.city_town_id = locations.location_id
    LEFT JOIN employers
        ON learners.employer_id = employers.employer_id
    LEFT JOIN learner_placement_level AS numeracy_level_table
        ON learners.numeracy_level = numeracy_level_table.placement_level_id
    LEFT JOIN learner_placement_level AS communication_level_table
        ON learners.communication_level = communication_level_table.placement_level_id
    LEFT JOIN portfolio_info
        ON learners.id = portfolio_info.learner_id
";



    try {
        // error_log('WeCoza Learners DB: Executing learners mappings query');
        $lrner = $db->prepare($query);
        $lrner->execute();

        $results = $lrner->fetchAll(PDO::FETCH_OBJ);
        // error_log('WeCoza Learners DB: Query executed successfully, retrieved ' . count($results) . ' raw results');

        // Process portfolio details for each learner
        foreach ($results as $learner) {
            if (!empty($learner->portfolio_details)) {
                $portfolios = [];
                $dates = [];

                // Split the combined portfolio details
                $portfolio_items = explode(', ', $learner->portfolio_details);
                foreach ($portfolio_items as $item) {
                    list($path, $date) = explode('|', $item);
                    $portfolios[] = $path;
                    $dates[] = $date;
                }

                $learner->scanned_portfolio = implode(', ', $portfolios);
                $learner->portfolio_upload_dates = $dates;
            } else {
                $learner->scanned_portfolio = '';
                $learner->portfolio_upload_dates = [];
            }
        }

        // Store the results in a transient valid 12 hours
        set_transient( 'learner_db_get_learners_mappings', $results, 12 * HOUR_IN_SECONDS );
        // error_log('WeCoza Learners DB: Results cached, returning ' . count($results) . ' processed learners');

        return $results;

    } catch (PDOException $e) {
        error_log('WeCoza Learners DB: Database Query Error - ' . $e->getMessage());
        error_log('WeCoza Learners DB: Failed query was: ' . $query);
        throw new Exception('Database query failed: ' . $e->getMessage());
    }
}



        /*------------------YDCOZA-----------------------*/
        /* Fetch single learner with all mappings         */
        /* Returns learner data with qualification name,   */
        /* location details, and employer information      */
        /*-----------------------------------------------*/
public function get_learner_by_id($id) {
    $db = $this->db->getPdo();

    $query = "
        WITH portfolio_info AS (
            SELECT
                learner_id,
                string_agg(file_path || '|' || upload_date::text, ', ' ORDER BY upload_date DESC) as portfolio_details
            FROM learner_portfolios
            GROUP BY learner_id
        )
        SELECT
            learners.*,
            learner_qualifications.qualification AS highest_qualification,
            locations.suburb AS suburb,
            locations.town AS city_town_name,
            locations.province AS province_region_name,
            locations.postal_code AS postal_code,
            employers.employer_name AS employer_name,
            numeracy_level_table.level AS numeracy_level,
            communication_level_table.level AS communication_level,
            CASE WHEN learners.employment_status = true THEN 'Employed' ELSE 'Unemployed' END AS employment_status,
            CASE WHEN learners.disability_status = true THEN 'Yes' ELSE 'No' END AS disability_status,
            portfolio_info.portfolio_details
        FROM learners
        LEFT JOIN learner_qualifications
            ON learners.highest_qualification = learner_qualifications.id
        LEFT JOIN locations
            ON learners.city_town_id = locations.location_id
        LEFT JOIN employers
            ON learners.employer_id = employers.employer_id
        LEFT JOIN learner_placement_level AS numeracy_level_table
            ON learners.numeracy_level = numeracy_level_table.placement_level_id
        LEFT JOIN learner_placement_level AS communication_level_table
            ON learners.communication_level = communication_level_table.placement_level_id
        LEFT JOIN portfolio_info
            ON learners.id = portfolio_info.learner_id
        WHERE learners.id = :id
    ";

    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $learner = $stmt->fetch(PDO::FETCH_OBJ);

        if ($learner) {
            // Process portfolio details for the learner
            if (!empty($learner->portfolio_details)) {
                $portfolios = [];
                $dates = [];

                // Split the combined portfolio details
                $portfolio_items = explode(', ', $learner->portfolio_details);
                foreach ($portfolio_items as $item) {
                    list($path, $date) = explode('|', $item);
                    $portfolios[] = $path;
                    $dates[] = $date;
                }

                $learner->scanned_portfolio = implode(', ', $portfolios);
                $learner->portfolio_upload_dates = $dates;
            } else {
                $learner->scanned_portfolio = '';
                $learner->portfolio_upload_dates = [];
            }
        }

        return $learner;
    } catch (PDOException $e) {
        error_log('Database Query Error in get_learner_by_id: ' . $e->getMessage());
        return false;
    }
}



        /*------------------YDCOZA-----------------------*/
        /* Update Learner Function                        */
        /* Updates learner details in the database using  */
        /* the provided data. It binds each field and     */
        /* ensures the 'updated_at' field is updated with */
        /* the current timestamp.                         */
        /*-----------------------------------------------*/

        /**
         * Update learner information in the database
         *
         * @param array $data Associative array of learner data
         * @return bool True on success, False on failure
         */
        public function update_learner($data) {
            try {
                // Get PDO connection
                $pdo = $this->db->getPdo();
                // Log the update attempt

                // Build the SQL query dynamically based on provided fields
                $sql_parts = [];
                $params = [];

                // Map the form fields to database columns
                $field_mappings = [
                    'title' => 'title',
                    'first_name' => 'first_name',
                    'second_name' => 'second_name',
                    'initials' => 'initials',
                    'surname' => 'surname',
                    'gender' => 'gender',
                    'race' => 'race',
                    'sa_id_no' => 'sa_id_no',
                    'passport_number' => 'passport_number',
                    'tel_number' => 'tel_number',
                    'alternative_tel_number' => 'alternative_tel_number',
                    'email_address' => 'email_address',
                    'address_line_1' => 'address_line_1',
                    'address_line_2' => 'address_line_2',
                    'city_town_id' => 'city_town_id',
                    'province_region_id' => 'province_region_id',
                    'postal_code' => 'postal_code',
                    'highest_qualification' => 'highest_qualification',
                    'assessment_status' => 'assessment_status',
                    'placement_assessment_date' => 'placement_assessment_date',
                    'numeracy_level' => 'numeracy_level',
                    'employment_status' => 'employment_status',
                    'employer_id' => 'employer_id',
                    'disability_status' => 'disability_status',
                    'scanned_portfolio' => 'scanned_portfolio',
                    'updated_at' => 'updated_at'
                ];

                // Build SQL parts and params array
                foreach ($field_mappings as $form_field => $db_column) {
                    if (isset($data[$form_field])) {
                        $sql_parts[] = "\"$db_column\" = :$form_field";
                        $params[$form_field] = $data[$form_field];
                    }
                }

                // Add ID to params
                $params['id'] = $data['id'];

                // Construct the final SQL query
                $sql = "UPDATE learners SET " . implode(', ', $sql_parts) . " WHERE id = :id";

                    // Log the final SQL query (for debugging)

                // Prepare and execute the query
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($params);

                if ($result) {
                    // Clear the transient cache
                    delete_transient('learner_db_get_learners_mappings');
                    return true;
                } else {
                    error_log("Failed to update learner ID: " . $data['id']);
                    error_log("Database error: " . print_r($stmt->errorInfo(), true));
                    return false;
                }

            } catch (PDOException $e) {
                error_log("PDO Exception while updating learner: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                return false;
            } catch (Exception $e) {
                error_log("General Exception while updating learner: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                return false;
            }
        }

        /**
         * Validate a learner ID exists in the database
         *
         * @param int $id Learner ID to validate
         * @return bool True if learner exists, False otherwise
         */
        public function validate_learner_exists($id) {
            try {
                $pdo = $this->db->getPdo();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM learners WHERE id = :id");
                $stmt->execute(['id' => $id]);

                return (bool)$stmt->fetchColumn();
            } catch (Exception $e) {
                error_log("Error validating learner existence: " . $e->getMessage());
                return false;
            }
        }



        /*------------------YDCOZA-----------------------*/
        /* Delete learner data from the database         */
        /* Removes a learner's record based on their ID  */
        /*-----------------------------------------------*/
        public function delete_learner($learner_id) {
            try {
                $pdo = $this->db->getPdo();

                // Start transaction
                $pdo->beginTransaction();

                // Log deletion attempt

                // Delete learner
                $query = "DELETE FROM learners WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['id' => $learner_id]);

                $rowCount = $stmt->rowCount();

                if ($rowCount > 0) {

                    // Commit transaction
                    $pdo->commit();

                    // Clear the transient cache
                    delete_transient('learner_db_get_learners_mappings');

                    return true;
                } else {
                    $pdo->rollBack();
                    error_log("No learner found with ID: " . $learner_id);
                    return false;
                }

            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Database error deleting learner: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("General error deleting learner: " . $e->getMessage());
                return false;
            }
        }


    public function saveLearnerPortfolios($learner_id, $files) {
    try {
        $pdo = $this->db->getPdo();
        $upload_dir = wp_upload_dir();
        $portfolio_dir = $upload_dir['basedir'] . '/portfolios/';
        $portfolio_paths = [];

        // Ensure the portfolios directory exists
        if (!file_exists($portfolio_dir)) {
            wp_mkdir_p($portfolio_dir);
        }

        // Start transaction
        $pdo->beginTransaction();

        // Check if files were actually uploaded
        if (!is_array($files['name']) || empty($files['name'][0])) {
            throw new Exception('No files were uploaded.');
        }

        // Process each uploaded file
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $filename = $files['name'][$i];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if ($file_ext === 'pdf') {
                    $new_filename = uniqid('portfolio_', true) . '.pdf';
                    $file_path = $portfolio_dir . $new_filename;
                    $relative_path = 'portfolios/' . $new_filename;

                    if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
                        $portfolio_paths[] = $relative_path;

                        // Insert into learner_portfolios
                        $stmt = $pdo->prepare("
                            INSERT INTO learner_portfolios (
                                learner_id,
                                file_path
                            ) VALUES (
                                :learner_id,
                                :file_path
                            )
                        ");

                        $stmt->execute([
                            ':learner_id' => $learner_id,
                            ':file_path' => $relative_path
                        ]);
                    }
                }
            }
        }

        // Update the learners table with concatenated portfolio paths
        if (!empty($portfolio_paths)) {
            // Convert array of paths to comma-separated string
            $portfolio_list = implode(', ', $portfolio_paths);

            // Log the update attempt

            // Update learners table
            $update_stmt = $pdo->prepare("
                UPDATE learners
                SET scanned_portfolio = :portfolio_paths
                WHERE id = :learner_id
            ");

            $update_result = $update_stmt->execute([
                ':portfolio_paths' => $portfolio_list,
                ':learner_id' => $learner_id
            ]);

            // Note: Removed verbose logging to keep debug.log clean
            // Update result is handled by transaction success/failure below
        }

        // Commit transaction
        $pdo->commit();

        return [
            'success' => true,
            'message' => 'Files uploaded and paths updated successfully',
            'paths' => $portfolio_paths
        ];

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error in saveLearnerPortfolios: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error processing files: ' . $e->getMessage()
        ];
    }
    }

    // Add a helper function to verify the update
    public function verifyPortfolioUpdate($learner_id) {
        try {
            $pdo = $this->db->getPdo();
            $stmt = $pdo->prepare("
                SELECT scanned_portfolio
                FROM learners
                WHERE id = :learner_id
            ");

            $stmt->execute([':learner_id' => $learner_id]);
            $result = $stmt->fetch(PDO::FETCH_COLUMN);


            return $result;
        } catch (Exception $e) {
            error_log("Error verifying portfolio update: " . $e->getMessage());
            return false;
        }
    }


        // learners-db.php
        public function get_learner_portfolios($learner_id) {
            $pdo = $this->db->getPdo();

            $stmt = $pdo->prepare("
                SELECT portfolio_id, file_path, upload_date
                FROM learner_portfolios
                WHERE learner_id = :learner_id
                ORDER BY upload_date DESC
            ");

            $stmt->execute([':learner_id' => $learner_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }



        // Use Proper File Permissions
        // sudo chmod -R 666 /opt/lampp/htdocs/wecoza/wp-content/uploads/portfolios/*
        // sudo chmod 777 /opt/lampp/htdocs/wecoza/wp-content/uploads/portfolios

        public function deletePortfolioFile($portfolio_id) {
            try {
                $pdo = $this->db->getPdo();

                // Start transaction
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
                    SELECT file_path, learner_id
                    FROM learner_portfolios
                    WHERE portfolio_id = :portfolio_id
                ");
                $stmt->execute([':portfolio_id' => $portfolio_id]);
                $file = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($file) {
                    // Get WordPress upload directory info
                    $upload_dir = wp_upload_dir();
                    $file_path = $upload_dir['basedir'] . '/' . $file['file_path'];


                    // Try to delete file if it exists
                    if (file_exists($file_path)) {
                        try {
                            unlink($file_path);
                        } catch (Exception $e) {
                            error_log("Could not delete file: " . $e->getMessage());
                            // Continue with database updates even if file deletion fails
                        }
                    } else {
                        error_log("File not found at: " . $file_path);
                    }

                    // Delete from learner_portfolios table
                    $deleteStmt = $pdo->prepare("
                        DELETE FROM learner_portfolios
                        WHERE portfolio_id = :portfolio_id
                    ");
                    $deleteStmt->execute([':portfolio_id' => $portfolio_id]);

                    // Get remaining portfolios for this learner
                    $remainingStmt = $pdo->prepare("
                        SELECT file_path
                        FROM learner_portfolios
                        WHERE learner_id = :learner_id
                        ORDER BY upload_date DESC
                    ");
                    $remainingStmt->execute([':learner_id' => $file['learner_id']]);
                    $remaining_portfolios = $remainingStmt->fetchAll(PDO::FETCH_COLUMN);

                    // Update learners table
                    $updateLearnerStmt = $pdo->prepare("
                        UPDATE learners
                        SET scanned_portfolio = :portfolio_paths
                        WHERE id = :learner_id
                    ");
                    $updateLearnerStmt->execute([
                        ':portfolio_paths' => !empty($remaining_portfolios) ? implode(', ', $remaining_portfolios) : null,
                        ':learner_id' => $file['learner_id']
                    ]);

                    // Commit transaction
                    $pdo->commit();
                    return true;
                }

                $pdo->rollBack();
                return false;

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Error in deletePortfolioFile: " . $e->getMessage());
                return false;
            }
        }

    } // End of learner_DB class
} // End of class_exists check
