<?php
namespace App\Middleware;
use App\Services\AuthService;
 


class JwtMiddleware {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function handle($request) {
        $jwt = getBearerToken();
        
        if (!$jwt) {
            http_response_code(401);
            echo json_encode(['error' => 'Token not provided']);
            exit;
        }

        $decoded = $this->authService->validateToken($jwt);
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        return $decoded;  // Continua o fluxo com o token decodificado
    }
}

function getBearerToken() {
    $header = null;

    // Captura headers dependendo da configuração do servidor
    if (isset($_SERVER['Authorization'])) {
        $header = $_SERVER['Authorization'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Servidores como Apache às vezes prefixam com HTTP_
        $header = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $header = $headers['Authorization'];
        }
    }

    // Extrai o token se o header Authorization estiver presente
    if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        return $matches[1];
    }

    return null;
}