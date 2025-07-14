<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password_hash;
    public $email;
    public $first_name;
    public $last_name;
    public $role_id;
    public $department_id;
    public $sub_department_id;
    public $is_active;
    public $last_login;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate($username, $password) {
        $query = "SELECT u.id, u.username, u.password_hash, u.first_name, u.last_name, 
                         u.role_id, u.department_id, u.sub_department_id, u.is_active,
                         r.name as role_name, d.name as department_name, sd.name as sub_department_name
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  LEFT JOIN departments d ON u.department_id = d.id
                  LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
                  WHERE u.username = :username AND u.is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password_hash'])) {
                $this->updateLastLogin($row['id']);
                return $row;
            }
        }
        return false;
    }

    public function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username = :username, password_hash = :password_hash, email = :email,
                      first_name = :first_name, last_name = :last_name, role_id = :role_id,
                      department_id = :department_id, sub_department_id = :sub_department_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':role_id', $this->role_id);
        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':sub_department_id', $this->sub_department_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT u.id, u.username, u.email, u.first_name, u.last_name, 
                         u.is_active, u.last_login, u.created_at,
                         r.name as role_name, d.name as department_name, sd.name as sub_department_name
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  LEFT JOIN departments d ON u.department_id = d.id
                  LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
                  ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByDepartment($department_id) {
        $query = "SELECT u.id, u.username, u.email, u.first_name, u.last_name, 
                         u.is_active, u.last_login, u.created_at,
                         r.name as role_name, d.name as department_name, sd.name as sub_department_name
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  LEFT JOIN departments d ON u.department_id = d.id
                  LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
                  WHERE u.department_id = :department_id
                  ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT u.id, u.username, u.email, u.first_name, u.last_name, 
                         u.role_id, u.department_id, u.sub_department_id, u.is_active,
                         r.name as role_name, d.name as department_name, sd.name as sub_department_name
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  LEFT JOIN departments d ON u.department_id = d.id
                  LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
                  WHERE u.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role_id = $row['role_id'];
            $this->department_id = $row['department_id'];
            $this->sub_department_id = $row['sub_department_id'];
            $this->is_active = $row['is_active'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email, first_name = :first_name,
                      last_name = :last_name, role_id = :role_id, department_id = :department_id,
                      sub_department_id = :sub_department_id, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':role_id', $this->role_id);
        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':sub_department_id', $this->sub_department_id);
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

    public function changePassword($new_password) {
        $query = "UPDATE " . $this->table_name . " SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>