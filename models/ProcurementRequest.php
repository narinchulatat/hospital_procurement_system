<?php
class ProcurementRequest {
    private $conn;
    private $table_name = "procurement_requests";

    public $id;
    public $request_number;
    public $user_id;
    public $department_id;
    public $sub_department_id;
    public $item_id;
    public $quantity;
    public $unit_price;
    public $total_price;
    public $request_type;
    public $old_equipment_code;
    public $reason;
    public $budget_year_id;
    public $status_id;
    public $approved_by;
    public $approved_at;
    public $approval_notes;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                         sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                         s.name as status_name, b.year_be, approver.first_name as approver_first_name,
                         approver.last_name as approver_last_name
                  FROM " . $this->table_name . " pr
                  LEFT JOIN users u ON pr.user_id = u.id
                  LEFT JOIN departments d ON pr.department_id = d.id
                  LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                  LEFT JOIN items i ON pr.item_id = i.id
                  LEFT JOIN statuses s ON pr.status_id = s.id
                  LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                  LEFT JOIN users approver ON pr.approved_by = approver.id
                  ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByUser($user_id) {
        $query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                         sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                         s.name as status_name, b.year_be, approver.first_name as approver_first_name,
                         approver.last_name as approver_last_name
                  FROM " . $this->table_name . " pr
                  LEFT JOIN users u ON pr.user_id = u.id
                  LEFT JOIN departments d ON pr.department_id = d.id
                  LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                  LEFT JOIN items i ON pr.item_id = i.id
                  LEFT JOIN statuses s ON pr.status_id = s.id
                  LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                  LEFT JOIN users approver ON pr.approved_by = approver.id
                  WHERE pr.user_id = :user_id
                  ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function readByDepartment($department_id) {
        $query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                         sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                         s.name as status_name, b.year_be, approver.first_name as approver_first_name,
                         approver.last_name as approver_last_name
                  FROM " . $this->table_name . " pr
                  LEFT JOIN users u ON pr.user_id = u.id
                  LEFT JOIN departments d ON pr.department_id = d.id
                  LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                  LEFT JOIN items i ON pr.item_id = i.id
                  LEFT JOIN statuses s ON pr.status_id = s.id
                  LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                  LEFT JOIN users approver ON pr.approved_by = approver.id
                  WHERE pr.department_id = :department_id
                  ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->execute();
        return $stmt;
    }

    public function readByStatus($status_id) {
        $query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                         sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                         s.name as status_name, b.year_be, approver.first_name as approver_first_name,
                         approver.last_name as approver_last_name
                  FROM " . $this->table_name . " pr
                  LEFT JOIN users u ON pr.user_id = u.id
                  LEFT JOIN departments d ON pr.department_id = d.id
                  LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                  LEFT JOIN items i ON pr.item_id = i.id
                  LEFT JOIN statuses s ON pr.status_id = s.id
                  LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                  LEFT JOIN users approver ON pr.approved_by = approver.id
                  WHERE pr.status_id = :status_id
                  ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                         sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                         i.unit, i.specifications, s.name as status_name, b.year_be,
                         approver.first_name as approver_first_name, approver.last_name as approver_last_name
                  FROM " . $this->table_name . " pr
                  LEFT JOIN users u ON pr.user_id = u.id
                  LEFT JOIN departments d ON pr.department_id = d.id
                  LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                  LEFT JOIN items i ON pr.item_id = i.id
                  LEFT JOIN statuses s ON pr.status_id = s.id
                  LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                  LEFT JOIN users approver ON pr.approved_by = approver.id
                  WHERE pr.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->request_number = $row['request_number'];
            $this->user_id = $row['user_id'];
            $this->department_id = $row['department_id'];
            $this->sub_department_id = $row['sub_department_id'];
            $this->item_id = $row['item_id'];
            $this->quantity = $row['quantity'];
            $this->unit_price = $row['unit_price'];
            $this->total_price = $row['total_price'];
            $this->request_type = $row['request_type'];
            $this->old_equipment_code = $row['old_equipment_code'];
            $this->reason = $row['reason'];
            $this->budget_year_id = $row['budget_year_id'];
            $this->status_id = $row['status_id'];
            $this->approved_by = $row['approved_by'];
            $this->approved_at = $row['approved_at'];
            $this->approval_notes = $row['approval_notes'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function create() {
        // Generate request number
        $this->request_number = $this->generateRequestNumber();

        $query = "INSERT INTO " . $this->table_name . " 
                  SET request_number = :request_number, user_id = :user_id, 
                      department_id = :department_id, sub_department_id = :sub_department_id,
                      item_id = :item_id, quantity = :quantity, unit_price = :unit_price,
                      total_price = :total_price, request_type = :request_type,
                      old_equipment_code = :old_equipment_code, reason = :reason,
                      budget_year_id = :budget_year_id, status_id = :status_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':request_number', $this->request_number);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':sub_department_id', $this->sub_department_id);
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':request_type', $this->request_type);
        $stmt->bindParam(':old_equipment_code', $this->old_equipment_code);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':budget_year_id', $this->budget_year_id);
        $stmt->bindParam(':status_id', $this->status_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET department_id = :department_id, sub_department_id = :sub_department_id,
                      item_id = :item_id, quantity = :quantity, unit_price = :unit_price,
                      total_price = :total_price, request_type = :request_type,
                      old_equipment_code = :old_equipment_code, reason = :reason,
                      budget_year_id = :budget_year_id, status_id = :status_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':sub_department_id', $this->sub_department_id);
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':request_type', $this->request_type);
        $stmt->bindParam(':old_equipment_code', $this->old_equipment_code);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':budget_year_id', $this->budget_year_id);
        $stmt->bindParam(':status_id', $this->status_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function approve($approved_by, $approval_notes = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET approved_by = :approved_by, approved_at = NOW(), approval_notes = :approval_notes,
                      status_id = 2
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approved_by', $approved_by);
        $stmt->bindParam(':approval_notes', $approval_notes);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function reject($approved_by, $approval_notes) {
        $query = "UPDATE " . $this->table_name . " 
                  SET approved_by = :approved_by, approved_at = NOW(), approval_notes = :approval_notes,
                      status_id = 3
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approved_by', $approved_by);
        $stmt->bindParam(':approval_notes', $approval_notes);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function updateStatus($status_id) {
        $query = "UPDATE " . $this->table_name . " SET status_id = :status_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    private function generateRequestNumber() {
        $year = date('Y');
        $month = date('m');
        
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'] + 1;
        
        return sprintf('REQ-%04d%02d-%04d', $year, $month, $count);
    }

    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_requests,
                    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as approved_requests,
                    SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as rejected_requests,
                    SUM(total_price) as total_amount
                  FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatisticsByDepartment($department_id) {
        $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_requests,
                    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as approved_requests,
                    SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as rejected_requests,
                    SUM(total_price) as total_amount
                  FROM " . $this->table_name . " WHERE department_id = :department_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatisticsByBudgetYear($budget_year_id) {
        $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_requests,
                    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as approved_requests,
                    SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as rejected_requests,
                    SUM(total_price) as total_amount
                  FROM " . $this->table_name . " WHERE budget_year_id = :budget_year_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':budget_year_id', $budget_year_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkUserHasRequests($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
?>