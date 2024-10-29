<?php 

require_once '../../vendor/autoload.php';
require_once '../Headers/headers.php';


use App\Config\Database;
use App\Services\AuthService;
use App\Middleware\JwtMiddleware;

// Inicializa o banco de dados e o AuthService
$database = new Database();
$dbCon = $database->getConnection();
$authService = new AuthService($dbCon);
$jwtMiddleware = new JwtMiddleware($authService);

// Função de Middleware: Verifica o JWT
function jwtMiddlewareCheck() {
    global $jwtMiddleware; // Aqui você poderia ajustar se precisar de outras informações na requisição

    // Executa o middleware e valida o token
    $user = $jwtMiddleware->handle();

    // Retorna o usuário decodificado se o token for válido
    return $user;
}


$user = jwtMiddlewareCheck();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $user) {
    echo json_encode(['message' => 'Verificado com sucesso', 'user' => $user]);
} else {
    http_response_code(405); 
    echo json_encode(['message' => 'Method not allowed']);
}