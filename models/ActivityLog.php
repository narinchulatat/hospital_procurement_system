<?php
class ActivityLog {
    private $conn;
    private $table_name = "activity_logs";

    public $id;
    public $user_id;
    public $action;
    public $description;
    public $table_name_ref;
    public $record_id;
    public $ip_address;
    public $user_agent;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($limit = 100) {
        $query = "SELECT al.*, u.first_name, u.last_name
                  FROM " . $this->table_name . " al
                  LEFT JOIN users u ON al.user_id = u.id
                  ORDER BY al.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readByUser($user_id, $limit = 50) {
        $query = "SELECT al.*, u.first_name, u.last_name
                  FROM " . $this->table_name . " al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE al.user_id = :user_id
                  ORDER BY al.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readByTable($table_name, $record_id) {
        $query = "SELECT al.*, u.first_name, u.last_name
                  FROM " . $this->table_name . " al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE al.table_name = :table_name AND al.record_id = :record_id
                  ORDER BY al.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':table_name', $table_name);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, action = :action, description = :description,
                      table_name = :table_name, record_id = :record_id, ip_address = :ip_address,
                      user_agent = :user_agent";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':action', $this->action);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':table_name', $this->table_name_ref);
        $stmt->bindParam(':record_id', $this->record_id);
        $stmt->bindParam(':ip_address', $this->ip_address);
        $stmt->bindParam(':user_agent', $this->user_agent);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function deleteOldLogs($days = 365) {
        $query = "DELETE FROM " . $this->table_name . " WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>