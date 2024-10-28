<?php

require_once '../../vendor/autoload.php';
use App\Controllers\UserController;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $controller = new UserController();
    $controller->login($data['email'], $data['password']);
} else {
    http_response_code(405); 
    echo json_encode(['message' => 'Method not allowed']);
}



