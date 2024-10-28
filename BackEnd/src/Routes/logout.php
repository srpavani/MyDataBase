<?php

require_once '../../vendor/autoload.php';
require_once '../Headers/headers.php';

use App\Controllers\UserController;


if ($_SERVER) {
    $controller = new UserController();
    $controller->logout();
} else {
    http_response_code(405); 
    echo json_encode(['message' => 'Logout ja feito']);
}



