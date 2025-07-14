<?php
class Status {
    private $conn;
    private $table_name = "statuses";

    public $id;
    public $name;
    public $description;
    public $sort_order;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY sort_order";
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
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->sort_order = $row['sort_order'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name = :name, description = :description, sort_order = :sort_order, is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':sort_order', $this->sort_order);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, sort_order = :sort_order, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':sort_order', $this->sort_order);
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

    public function getStatusName($status_id) {
        $query = "SELECT name FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $status_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['name'];
        }
        return null;
    }

    public function getStatusTranslation($status_name) {
        $translations = [
            'pending' => 'รอการอนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ปฏิเสธ',
            'procurement_pending' => 'รอจัดซื้อ',
            'document_preparation' => 'จัดทำเอกสาร',
            'quotation_request' => 'ขอใบเสนอราคา',
            'purchased' => 'จัดซื้อแล้ว',
            'delivery_pending' => 'รอส่งมอบ',
            'delivered' => 'ส่งมอบแล้ว'
        ];
        
        return isset($translations[$status_name]) ? $translations[$status_name] : $status_name;
    }

    public function getStatusBadgeClass($status_name) {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'procurement_pending' => 'bg-blue-100 text-blue-800',
            'document_preparation' => 'bg-purple-100 text-purple-800',
            'quotation_request' => 'bg-indigo-100 text-indigo-800',
            'purchased' => 'bg-teal-100 text-teal-800',
            'delivery_pending' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800'
        ];
        
        return isset($classes[$status_name]) ? $classes[$status_name] : 'bg-gray-100 text-gray-800';
    }

    public function getNextStatuses($current_status_id) {
        $status_flow = [
            1 => [2, 3], // pending -> approved, rejected
            2 => [4], // approved -> procurement_pending
            4 => [5], // procurement_pending -> document_preparation
            5 => [6], // document_preparation -> quotation_request
            6 => [7], // quotation_request -> purchased
            7 => [8], // purchased -> delivery_pending
            8 => [9], // delivery_pending -> delivered
        ];
        
        return isset($status_flow[$current_status_id]) ? $status_flow[$current_status_id] : [];
    }
}
?>