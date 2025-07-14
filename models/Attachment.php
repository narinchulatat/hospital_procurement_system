<?php
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
?>