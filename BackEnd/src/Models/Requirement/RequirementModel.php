<?php
namespace App\Models\RequirementModel;

use PDO;

class RequirementModel {

    private $conn;
    private $table_name = "requirements";
    private $description;

    private $idUser;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    public function setDescription($description) {
        $this->description = htmlspecialchars(strip_tags($description));
    }


    public function createDescription($description, $idUser) {
        $this->setDescription($description);
        $this->setIdUser($idUser);
        $query = "INSERT INTO " . $this->table_name . " (description, user_id) VALUES (:description, :idUser)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':idUser', $this->idUser); 

        return $stmt->execute(); 
    }

    public function getRequirementsByID($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Corrige o parâmetro

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna o resultado
        }
        return false;
    }

    public function getAllRequirementsByIdUser($idUser) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :idUser";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUser', $idUser); // Bind do parâmetro correto

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todos os resultados
        }
        return false;
    }

   
    public function deleteByID($id, $idUser) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :idUser";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':idUser', $idUser); 
        return $stmt->execute(); 
    }
}
