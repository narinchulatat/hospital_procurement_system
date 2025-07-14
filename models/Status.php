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

class Attachment {
    private $conn;
    private $table_name = "attachments";

    public $id;
    public $procurement_request_id;
    public $original_filename;
    public $stored_filename;
    public $file_size;
    public $file_type;
    public $uploaded_by;
    public $uploaded_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByRequest($request_id) {
        $query = "SELECT a.*, u.first_name, u.last_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.uploaded_by = u.id
                  WHERE a.procurement_request_id = :request_id
                  ORDER BY a.uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT a.*, u.first_name, u.last_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.uploaded_by = u.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->procurement_request_id = $row['procurement_request_id'];
            $this->original_filename = $row['original_filename'];
            $this->stored_filename = $row['stored_filename'];
            $this->file_size = $row['file_size'];
            $this->file_type = $row['file_type'];
            $this->uploaded_by = $row['uploaded_by'];
            $this->uploaded_at = $row['uploaded_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET procurement_request_id = :procurement_request_id, original_filename = :original_filename,
                      stored_filename = :stored_filename, file_size = :file_size, file_type = :file_type,
                      uploaded_by = :uploaded_by";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':procurement_request_id', $this->procurement_request_id);
        $stmt->bindParam(':original_filename', $this->original_filename);
        $stmt->bindParam(':stored_filename', $this->stored_filename);
        $stmt->bindParam(':file_size', $this->file_size);
        $stmt->bindParam(':file_type', $this->file_type);
        $stmt->bindParam(':uploaded_by', $this->uploaded_by);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getFileIcon($file_type) {
        $icons = [
            'application/pdf' => 'fas fa-file-pdf text-red-500',
            'image/jpeg' => 'fas fa-file-image text-blue-500',
            'image/png' => 'fas fa-file-image text-blue-500',
            'image/gif' => 'fas fa-file-image text-blue-500',
            'application/msword' => 'fas fa-file-word text-blue-600',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-blue-600',
            'application/vnd.ms-excel' => 'fas fa-file-excel text-green-600',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel text-green-600',
        ];
        
        return isset($icons[$file_type]) ? $icons[$file_type] : 'fas fa-file text-gray-500';
    }
}

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