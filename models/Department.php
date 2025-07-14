<?php
class Department {
    private $conn;
    private $table_name = "departments";

    public $id;
    public $name;
    public $code;
    public $description;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
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
            $this->code = $row['code'];
            $this->description = $row['description'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name = :name, code = :code, description = :description";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':description', $this->description);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, code = :code, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function getSubDepartments() {
        $query = "SELECT * FROM sub_departments WHERE department_id = :department_id ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function readAllWithPagination($search = '', $perPage = 10, $offset = 0) {
        // Base query for counting
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        // Base query for data
        $dataQuery = "SELECT * FROM " . $this->table_name;
        
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (name LIKE :search OR code LIKE :search OR description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        // Get total count
        $countStmt = $this->conn->prepare($countQuery . $whereClause);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get data with pagination
        $dataQuery .= $whereClause . " ORDER BY name LIMIT :offset, :per_page";
        $dataStmt = $this->conn->prepare($dataQuery);
        
        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
        $dataStmt->execute();
        
        return [
            'data' => $dataStmt,
            'total' => $total
        ];
    }

    public function checkCodeExists($code, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE code = :code";
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function checkNameExists($name, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE name = :name";
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

class SubDepartment {
    private $conn;
    private $table_name = "sub_departments";

    public $id;
    public $department_id;
    public $name;
    public $code;
    public $description;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT sd.*, d.name as department_name 
                  FROM " . $this->table_name . " sd
                  LEFT JOIN departments d ON sd.department_id = d.id
                  ORDER BY d.name, sd.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByDepartment($department_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE department_id = :department_id ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT sd.*, d.name as department_name 
                  FROM " . $this->table_name . " sd
                  LEFT JOIN departments d ON sd.department_id = d.id
                  WHERE sd.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->department_id = $row['department_id'];
            $this->name = $row['name'];
            $this->code = $row['code'];
            $this->description = $row['description'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET department_id = :department_id, name = :name, code = :code, description = :description";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':description', $this->description);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET department_id = :department_id, name = :name, code = :code, description = :description 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>