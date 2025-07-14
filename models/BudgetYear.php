<?php
class BudgetYear {
    private $conn;
    private $table_name = "budget_years";

    public $id;
    public $year_be;
    public $year_ad;
    public $start_date;
    public $end_date;
    public $request_start_date;
    public $request_end_date;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY year_be DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readActive() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY year_be DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->year_be = $row['year_be'];
            $this->year_ad = $row['year_ad'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->request_start_date = $row['request_start_date'];
            $this->request_end_date = $row['request_end_date'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET year_be = :year_be, year_ad = :year_ad, start_date = :start_date, 
                      end_date = :end_date, request_start_date = :request_start_date,
                      request_end_date = :request_end_date, is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':year_be', $this->year_be);
        $stmt->bindParam(':year_ad', $this->year_ad);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':request_start_date', $this->request_start_date);
        $stmt->bindParam(':request_end_date', $this->request_end_date);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET year_be = :year_be, year_ad = :year_ad, start_date = :start_date, 
                      end_date = :end_date, request_start_date = :request_start_date,
                      request_end_date = :request_end_date, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':year_be', $this->year_be);
        $stmt->bindParam(':year_ad', $this->year_ad);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':request_start_date', $this->request_start_date);
        $stmt->bindParam(':request_end_date', $this->request_end_date);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function getCurrentBudgetYear() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE CURDATE() BETWEEN start_date AND end_date 
                  ORDER BY year_be DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->year_be = $row['year_be'];
            $this->year_ad = $row['year_ad'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->request_start_date = $row['request_start_date'];
            $this->request_end_date = $row['request_end_date'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function isRequestPeriodOpen($budget_year_id = null) {
        $id = $budget_year_id ? $budget_year_id : $this->id;
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id = :id AND CURDATE() BETWEEN request_start_date AND request_end_date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function yearExists($year_be) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE year_be = :year_be";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_be', $year_be);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getRequestPeriodStatus($budget_year_id = null) {
        $id = $budget_year_id ? $budget_year_id : $this->id;
        $query = "SELECT request_start_date, request_end_date, 
                         CASE 
                            WHEN CURDATE() < request_start_date THEN 'not_started'
                            WHEN CURDATE() BETWEEN request_start_date AND request_end_date THEN 'open'
                            ELSE 'closed'
                         END as status
                  FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>