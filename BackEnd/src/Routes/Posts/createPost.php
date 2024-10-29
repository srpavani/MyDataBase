<?php

require_once '../../../vendor/autoload.php';
require_once '../../Headers/headers.php';

use App\Controllers\Requirements\RequirementController;
use App\Middleware\JwtMiddleware;
use App\Config\Database;

$database = new Database();
$db = $database->getConnection();
$authService = new App\Services\AuthService($db);
$jwtMiddleware = new JwtMiddleware($authService);

// Executa o middleware
$user = $jwtMiddleware->handle();

// Processa a rota apenas se a requisição for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $data = json_decode(file_get_contents("php://input"), true);
    if(!$data['description']){
        echo json_encode(['message' => 'need description']);
        exit();
    }else{
        $RequirementPost = new RequirementController($db);
        $RequirementPost->createDescription($data['description'], $user->sub);
    }
    
} else {
    http_response_code(405); 
    echo json_encode(['message' => 'Method not allowed']);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $user) {
    $RequirementPost = new RequirementController($db);
    $RequirementPost->getAllRequirementsByIdUser($user->sub); 
}else{
    http_response_code(405); 
    echo json_encode(['message' => 'Method not allowed']);
}
