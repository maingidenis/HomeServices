<?php
/**
 * DatabaseSQLite.php
 * SQLite Database Configuration for FrankenPHP / macOS
 * Location: config/DatabaseSQLite.php
 */

class DatabaseSQLite {
    private $conn;
    private $db_path;

    public function __construct() {
        // Path to your SQLite database file
        $this->db_path = __DIR__ . '/../data/home_services.sqlite';

        // Verify the file exists
        if (!file_exists($this->db_path)) {
            die("Error: Database file not found at: " . $this->db_path);
        }

        try {
            // Connect using PDO with SQLite driver
            $this->conn = new PDO('sqlite:' . $this->db_path);

            // Set error mode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Enable foreign key constraints
            $this->conn->exec("PRAGMA foreign_keys = ON;");
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Returns the PDO connection object.
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Execute a prepared query with parameters.
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     * @throws Exception
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch all rows from a query.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single row from a query.
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the ID of the last inserted row.
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}

// Create a global instance for convenience
if (!isset($GLOBALS['db'])) {
    $GLOBALS['db'] = new DatabaseSQLite();
}
$db = $GLOBALS['db'];
?>
