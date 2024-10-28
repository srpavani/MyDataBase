<?php
namespace App\Models\User;

use PDO;

class UserModel {
    private $conn;
    private $table_name = "users";

    private $id;
    private $username;
    private $email;
    private $password;
    private $is_admin = false;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function isAdmin() {
        return $this->is_admin;
    }

    public function setName($username) {
        $this->username = htmlspecialchars(strip_tags($username));
    }

    public function setEmail($email) {
        $this->email = htmlspecialchars(strip_tags($email));
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setIsAdmin($is_admin) {
        $this->is_admin = $is_admin;
    }

    public function create($username, $email, $password) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, is_admin) VALUES (:username, :email, :password, false)";
        $stmt = $this->conn->prepare($query);;
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    private function updateLastLogin($id) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    public function checkLogin($email, $password) {
        $query = "SELECT id, username, password, is_admin FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->password = $row['password'];
                $this->is_admin = $row['is_admin'];
                $this->updateLastLogin($row['id']);
                return true;
            }
        }
        return false;
    }


    
    

    
}