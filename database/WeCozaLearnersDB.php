<?php
/**
 * WeCoza Learners PostgreSQL Database Service
 *
 * Singleton database service for WeCoza Learners Plugin
 * Handles all PostgreSQL database operations for learner management
 *
 * @package WeCoza_Learners
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WeCozaLearnersDB')) {
    class WeCozaLearnersDB {
        /**
         * Singleton instance
         */
        private static $instance = null;
    
    /**
     * PDO instance
     */
    private $pdo;
    
    /**
     * Constructor - private to prevent direct instantiation
     */
    private function __construct() {
        // Only connect to PostgreSQL - no MySQL connections
        // Connection will be established when needed via lazy loading
        $this->pdo = null;
    }
    
    /**
     * Get singleton instance
     *
     * @return WeCozaLearnersDB
     */
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (Exception $e) {
                error_log('WeCoza Learners Plugin: Failed to get database instance: ' . $e->getMessage());
                throw $e;
            }
        }
        return self::$instance;
    }
    
    /**
     * Connect to PostgreSQL database - lazy loading
     */
    private function connect() {
        // Skip connection if already connected
        if ($this->pdo !== null) {
            return;
        }

        try {
            // Only connect if WordPress functions are available
            if (!function_exists('get_option')) {
                error_log('WeCoza Learners Plugin: WordPress functions not available - deferring database connection');
                return;
            }

            $config = $this->getConfig();
            $pgHost = $config['host'];
            $pgPort = $config['port'];
            $pgName = $config['dbname'];
            $pgUser = $config['user'];
            $pgPass = $config['password'];
            $pgSchema = $config['schema'];

            // Check if password is configured
            if (empty($pgPass)) {
                error_log('WeCoza Learners Plugin: PostgreSQL password not configured. Please set wecoza_postgres_password option.');
                // Don't throw exception - just log and continue
                return;
            }

            // Create PDO instance for PostgreSQL - NO MySQL connections!
            $this->pdo = new PDO(
                "pgsql:host=$pgHost;port=$pgPort;dbname=$pgName;sslmode=require" . ($pgSchema ? ";options='-c search_path=$pgSchema'" : ''),
                $pgUser,
                $pgPass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                // error_log('WeCoza Learners Plugin: PostgreSQL connection successful');
            }
            
        } catch (PDOException $e) {
            error_log('WeCoza Learners Plugin: PostgreSQL connection error: ' . $e->getMessage());
            // Don't throw exception during plugin loading - just log
            $this->pdo = null;
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Database connection error: ' . $e->getMessage());
            $this->pdo = null;
        }
    }

    /**
     * Build PostgreSQL connection configuration from WordPress options
     *
     * @return array
     */
    private function getConfig() {
        $host = get_option('wecoza_postgres_host', '');
        $portValue = get_option('wecoza_postgres_port', '');
        $dbname = get_option('wecoza_postgres_dbname', '');
        $user = get_option('wecoza_postgres_user', '');
        $password = get_option('wecoza_postgres_password', '');
        $schema = get_option('wecoza_postgres_schema', 'public');

        $port = (int) $portValue;
        if ($port <= 0) {
            $port = 5432;
        }

        return [
            'host' => $host,
            'port' => $port,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'schema' => $schema !== '' ? $schema : 'public',
        ];
    }
    
    /**
     * Get PDO instance - with lazy connection
     *
     * @return PDO|null
     */
    public function getPdo() {
        // Lazy connect when actually needed
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }
    
    /**
     * Execute a query with parameters
     *
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            throw new Exception('Database connection not available');
        }
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('WeCoza Learners Plugin: Database query error: ' . $e->getMessage());
            error_log('WeCoza Learners Plugin: SQL: ' . $sql);
            error_log('WeCoza Learners Plugin: Params: ' . print_r($params, true));
            throw $e;
        }
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            throw new Exception('Database connection not available');
        }
        return $pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            throw new Exception('Database connection not available');
        }
        return $pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            throw new Exception('Database connection not available');
        }
        return $pdo->rollBack();
    }
    
    /**
     * Check if in transaction
     *
     * @return bool
     */
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }
    
    /**
     * Get last insert ID
     *
     * @param string $sequence_name Sequence name for PostgreSQL
     * @return string
     */
    public function lastInsertId($sequence_name = null) {
        return $this->pdo->lastInsertId($sequence_name);
    }
    
    /**
     * Prepare a statement
     *
     * @param string $sql SQL query
     * @return PDOStatement
     */
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    
    /**
     * Execute a statement
     *
     * @param string $sql SQL query
     * @return int Number of affected rows
     */
    public function exec($sql) {
        return $this->pdo->exec($sql);
    }
    
    /**
     * Quote a string for use in a query
     *
     * @param string $string String to quote
     * @param int $parameter_type Parameter type
     * @return string Quoted string
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR) {
        return $this->pdo->quote($string, $parameter_type);
    }
    
    /**
     * Test database connection
     *
     * @return bool
     */
    // public function testConnection() {
    //     try {
    //         $stmt = $this->pdo->query('SELECT 1');
    //         return $stmt !== false;
    //     } catch (Exception $e) {
    //         error_log('WeCoza Learners Plugin: Database connection test failed: ' . $e->getMessage());
    //         return false;
    //     }
    // }
    
    /**
     * Check if table exists
     *
     * @param string $tableName Table name
     * @return bool
     */
    public function tableExists($tableName) {
        try {
            $sql = "SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = ?
            )";
            $stmt = $this->query($sql, [$tableName]);
            $result = $stmt->fetch();
            return $result['exists'] ?? false;
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Error checking table existence: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get table columns
     *
     * @param string $tableName Table name
     * @return array
     */
    public function getTableColumns($tableName) {
        try {
            $sql = "SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns 
                    WHERE table_schema = 'public' 
                    AND table_name = ?
                    ORDER BY ordinal_position";
            $stmt = $this->query($sql, [$tableName]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Error getting table columns: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get database connection info (for debugging)
     *
     * @return array
     */
    public function getConnectionInfo() {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return ['debug' => 'disabled'];
        }
        
        return [
            'host' => get_option('wecoza_postgres_host', 'not_set'),
            'port' => get_option('wecoza_postgres_port', 'not_set'),
            'database' => get_option('wecoza_postgres_dbname', 'not_set'),
            'user' => get_option('wecoza_postgres_user', 'not_set'),
            'connected' => $this->pdo ? 'yes' : 'no'
        ];
    }
    
    /**
     * Get database version
     *
     * @return string
     */
    public function getVersion() {
        try {
            $stmt = $this->pdo->query('SELECT version()');
            $result = $stmt->fetch();
            return $result['version'] ?? 'unknown';
        } catch (Exception $e) {
            error_log('WeCoza Learners Plugin: Error getting database version: ' . $e->getMessage());
            return 'error';
        }
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    } // End of WeCozaLearnersDB class
} // End of class_exists check
