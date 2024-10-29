<?php

require_once '../../vendor/autoload.php';
require_once '../Headers/headers.php';

use App\Controllers\RequirementController;
use App\Helpers\TokenHelper;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $RequirementPost = new RequirementController($db);
    $jwt = TokenHelper::getBearerToken();
    $RequirementPost->createDescription($data['description'], $jwt);
} else {
    http_response_code(405); 
    echo json_encode(['message' => 'Method not allowed']);
}



