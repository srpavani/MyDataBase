<?php
namespace App\Controllers;

use App\Models\RequirementModel\RequirementModel;
use PDO;

class RequirementController {

    private $model;

    public function __construct(PDO $db) {
        $this->model = new RequirementModel($db);
    }

    // Cria uma nova descrição de requisito
    public function createDescription($description, $idUser) {
        if ($this->model->createDescription($description, $idUser)) {
            http_response_code(201);
            echo json_encode(['message' => 'Requirement created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create requirement']);
        }
    }

    // Obtém um requisito pelo ID
    public function getRequirementByID($id) {
        $requirement = $this->model->getRequirementsByID($id);

        if ($requirement) {
            http_response_code(200);
            echo json_encode($requirement);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Requirement not found']);
        }
    }

    // Obtém todos os requisitos de um usuário específico
    public function getAllRequirementsByIdUser($idUser) {
        $requirements = $this->model->getAllRequirementsByIdUser($idUser);

        if ($requirements) {
            http_response_code(200);
            echo json_encode($requirements);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No requirements found for this user']);
        }
    }

    // Exclui um requisito pelo ID
    public function deleteRequirementByID($id) {
        if ($this->model->deleteByID($id)) {
            http_response_code(200);
            echo json_encode(['message' => 'Requirement deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Requirement not found or could not be deleted']);
        }
    }
}
