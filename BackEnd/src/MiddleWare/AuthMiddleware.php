<?php
namespace App\Middleware;

use App\Services\AuthService;
use App\Middleware\JwtMiddleware;

class AuthMiddleware {
    private $jwtMiddleware;

    public function __construct(AuthService $authService) {
        $this->jwtMiddleware = new JwtMiddleware($authService);
    }

    // Método para autenticar e obter o ID do usuário
    public function authenticate($jwt) {
        $decoded = $this->jwtMiddleware->handle(); // Valida o token e obtém o payload
        return $decoded->sub; // Retorna o ID do usuário
    }
}


