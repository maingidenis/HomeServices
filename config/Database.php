<?php
class Database {
    private $host = "localhost";
    private $dbname = "home_services";
    private $username = "root";
    private $password = "";
    public $conn = null;
    private $mode = null; // 'mysql' or 'sqlite'

    public function __construct() {
        // Detect DB mode: SQLite if SQLite DB file exists or environment variable 'DB_MODE' = 'sqlite'
        $sqlitePath = __DIR__ . '/../data/home_services.sqlite';
        if (getenv('DB_MODE') === 'sqlite' || file_exists($sqlitePath)) {
            $this->mode = 'sqlite';
            $this->connectSQLite($sqlitePath);
        } else {
            $this->mode = 'mysql';
            $this->connectMySQL();
        }
    }

    private function connectMySQL() {
        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname,
                                  $this->username,
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("MySQL Connection error: " . $e->getMessage());
        }
    }

    private function connectSQLite($db_path) {
        try {
            $this->conn = new PDO("sqlite:" . $db_path); 
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("PRAGMA foreign_keys = ON;");
        } catch(PDOException $e) {
            die("SQLite Connection error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        if ($this->conn === null) {
            if ($this->mode === 'sqlite') {
                $this->connectSQLite(__DIR__ . '/../data/home_services.sqlite');
            } else {
                $this->connectMySQL();
            }
        }
        return $this->conn;
    }

    public function getMode() {
        return $this->mode;
    }
}
?>
