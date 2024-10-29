<?php
namespace App\Controllers\Requirements;

use App\Models\Requirement\RequirementModel;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use PDO;

class RequirementController {
    private $model;
    private $authMiddleware;
    private $idUser;

    public function __construct(PDO $db) {
        $this->model = new RequirementModel($db);
        $authService = new AuthService($db); // Instancia o AuthService para injetar no middleware
        $this->authMiddleware = new AuthMiddleware($authService); // Middleware de autenticação
    }

    // Método para configurar o ID do usuário autenticado a partir do JWT
    private function setAuthenticatedUser($jwt) {
        $this->idUser = $this->authMiddleware->authenticate($jwt); // Usa o middleware para obter o ID
    }

    // Cria uma nova descrição de requisito
    public function createDescription($description, $jwt) {
        $this->setAuthenticatedUser($jwt); // Define o idUser a partir do JWT
        
        if ($this->model->createDescription($description, $this->idUser)) {
            http_response_code(201);
            echo json_encode(['message' => 'Requirement created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create requirement']);
        }
    }

    // Obtém um requisito pelo ID
    public function getRequirementByID($id, $jwt) {
        $this->setAuthenticatedUser($jwt); // Define o idUser a partir do JWT

        $requirement = $this->model->getRequirementsByID($id);

        if ($requirement && $requirement['user_id'] === $this->idUser) { // Verifica propriedade do usuário
            http_response_code(200);
            echo json_encode($requirement);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Requirement not found or access denied']);
        }
    }

    // Obtém todos os requisitos de um usuário específico
    public function getAllRequirementsByIdUser($jwt) {
        $this->setAuthenticatedUser($jwt); // Define o idUser a partir do JWT

        $requirements = $this->model->getAllRequirementsByIdUser($this->idUser);

        if ($requirements) {
            http_response_code(200);
            echo json_encode($requirements);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No requirements found for this user']);
        }
    }

    // Exclui um requisito pelo ID
    public function deleteRequirementByID($id, $jwt) {
        $this->setAuthenticatedUser($jwt); // Define o idUser a partir do JWT

        if ($this->model->deleteByID($id, $this->idUser)) {
            http_response_code(200);
            echo json_encode(['message' => 'Requirement deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Requirement not found or could not be deleted']);
        }
    }
}
