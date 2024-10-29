<?php
namespace App\Middleware;

use App\Services\AuthService;
use App\Helpers\TokenHelper;

class JwtMiddleware {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function handle() {
        $jwt = TokenHelper::getBearerToken(); // Obtém o token JWT com o helper
        
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

        return $decoded; // Continua o fluxo com o token decodificado
    }
}

   


