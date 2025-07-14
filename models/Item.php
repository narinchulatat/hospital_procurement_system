<?php
class Item {
    private $conn;
    private $table_name = "items";

    public $id;
    public $code;
    public $name;
    public $description;
    public $unit;
    public $unit_price;
    public $category;
    public $specifications;
    public $budget_year_id;
    public $is_available;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT i.*, b.year_be, b.year_ad 
                  FROM " . $this->table_name . " i
                  LEFT JOIN budget_years b ON i.budget_year_id = b.id
                  ORDER BY i.category, i.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByBudgetYear($budget_year_id) {
        $query = "SELECT i.*, b.year_be, b.year_ad 
                  FROM " . $this->table_name . " i
                  LEFT JOIN budget_years b ON i.budget_year_id = b.id
                  WHERE i.budget_year_id = :budget_year_id AND i.is_available = 1
                  ORDER BY i.category, i.name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':budget_year_id', $budget_year_id);
        $stmt->execute();
        return $stmt;
    }

    public function readByCategory($category, $budget_year_id = null) {
        $query = "SELECT i.*, b.year_be, b.year_ad 
                  FROM " . $this->table_name . " i
                  LEFT JOIN budget_years b ON i.budget_year_id = b.id
                  WHERE i.category = :category AND i.is_available = 1";
        
        if ($budget_year_id) {
            $query .= " AND i.budget_year_id = :budget_year_id";
        }
        
        $query .= " ORDER BY i.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        if ($budget_year_id) {
            $stmt->bindParam(':budget_year_id', $budget_year_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT i.*, b.year_be, b.year_ad 
                  FROM " . $this->table_name . " i
                  LEFT JOIN budget_years b ON i.budget_year_id = b.id
                  WHERE i.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->code = $row['code'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->unit = $row['unit'];
            $this->unit_price = $row['unit_price'];
            $this->category = $row['category'];
            $this->specifications = $row['specifications'];
            $this->budget_year_id = $row['budget_year_id'];
            $this->is_available = $row['is_available'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET code = :code, name = :name, description = :description, unit = :unit,
                      unit_price = :unit_price, category = :category, specifications = :specifications,
                      budget_year_id = :budget_year_id, is_available = :is_available";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':unit', $this->unit);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':specifications', $this->specifications);
        $stmt->bindParam(':budget_year_id', $this->budget_year_id);
        $stmt->bindParam(':is_available', $this->is_available);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET code = :code, name = :name, description = :description, unit = :unit,
                      unit_price = :unit_price, category = :category, specifications = :specifications,
                      budget_year_id = :budget_year_id, is_available = :is_available
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':unit', $this->unit);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':specifications', $this->specifications);
        $stmt->bindParam(':budget_year_id', $this->budget_year_id);
        $stmt->bindParam(':is_available', $this->is_available);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function getCategories($budget_year_id = null) {
        $query = "SELECT DISTINCT category FROM " . $this->table_name . " WHERE is_available = 1";
        
        if ($budget_year_id) {
            $query .= " AND budget_year_id = :budget_year_id";
        }
        
        $query .= " ORDER BY category";
        
        $stmt = $this->conn->prepare($query);
        if ($budget_year_id) {
            $stmt->bindParam(':budget_year_id', $budget_year_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function codeExists($code) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE code = :code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function search($keyword, $budget_year_id = null) {
        $query = "SELECT i.*, b.year_be, b.year_ad 
                  FROM " . $this->table_name . " i
                  LEFT JOIN budget_years b ON i.budget_year_id = b.id
                  WHERE i.is_available = 1 AND (
                      i.name LIKE :keyword OR 
                      i.description LIKE :keyword OR 
                      i.code LIKE :keyword OR 
                      i.category LIKE :keyword
                  )";
        
        if ($budget_year_id) {
            $query .= " AND i.budget_year_id = :budget_year_id";
        }
        
        $query .= " ORDER BY i.category, i.name";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        if ($budget_year_id) {
            $stmt->bindParam(':budget_year_id', $budget_year_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readAllWithPagination($search = '', $category = '', $budget_year_id = '', $perPage = 10, $offset = 0) {
        // Base query for counting
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table_name . " i
                      LEFT JOIN budget_years b ON i.budget_year_id = b.id";
        
        // Base query for data
        $dataQuery = "SELECT i.*, b.year_be, b.year_ad 
                      FROM " . $this->table_name . " i
                      LEFT JOIN budget_years b ON i.budget_year_id = b.id";
        
        $whereClause = "";
        $params = [];
        $conditions = [];
        
        if (!empty($search)) {
            $conditions[] = "(i.name LIKE :search OR i.code LIKE :search OR i.description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($category)) {
            $conditions[] = "i.category = :category";
            $params[':category'] = $category;
        }
        
        if (!empty($budget_year_id)) {
            $conditions[] = "i.budget_year_id = :budget_year_id";
            $params[':budget_year_id'] = $budget_year_id;
        }
        
        if (!empty($conditions)) {
            $whereClause = " WHERE " . implode(' AND ', $conditions);
        }
        
        // Get total count
        $countStmt = $this->conn->prepare($countQuery . $whereClause);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get data with pagination
        $dataQuery .= $whereClause . " ORDER BY i.category, i.name LIMIT :offset, :per_page";
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
?>