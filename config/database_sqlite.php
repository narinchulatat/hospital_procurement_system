<?php
class Database {
    private $db_file = 'hospital_procurement.db';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("sqlite:" . __DIR__ . '/../' . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Enable foreign key constraints
            $this->conn->exec("PRAGMA foreign_keys = ON");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>