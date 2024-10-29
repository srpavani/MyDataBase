<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\User\UserModel;
use App\Helpers\TokenHelper;
use App\Services\AuthService;
use Exception;


class UserController {
    private $user;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new UserModel($this->db);;
    }

    public function register($username, $email, $password) {
        // Sanitização
        $username = htmlspecialchars(strip_tags($username));
        $email = htmlspecialchars(strip_tags($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['message' => 'Formato de e-mail inválido']);
            return;
        }

        if (strlen($password) < 8) {
            echo json_encode(['message' => 'A senha deve ter pelo menos 8 caracteres']);
            return;
        }

        // Validação de nome (opcional: verificar se contém apenas letras, etc.)
        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            echo json_encode(['message' => 'username contém caracteres inválidos']);
            return;
        }

        $this->user->setName($username);
        $this->user->setEmail($email);
        $this->user->setPassword($password);



        if ($this->user->create($username, $email, $password)) {
            echo json_encode(['message' => 'Usuário Criado Com Sucesso']);
        } else {
            echo json_encode(['message' => 'Erro ao criar usuário']);
        }
    }

    

    public function login($email, $password) {
        try {
            // Sanitização de e-mail
            $email = htmlspecialchars(strip_tags($email));

            if ($this->user->checkLogin($email, $password)) {
                // Assumindo que a classe AuthService foi adequadamente incluída e configurada
                $authService = new AuthService( $this->db);
                $jwt = $authService->createToken($this->user);  // Supondo que createToken agora aceite um objeto UserModel
                
                echo json_encode([
                    'message' => 'Login bem-sucedido',
                    'jwt' => $jwt  // Enviando o JWT para o usuário
                ]);
            } else {
                throw new Exception('Falha no login');
            }
        } catch (Exception $e) {
            echo json_encode(['message' => 'Login falhou: erro:: '.$e]);
        }
    }

    public function logout() {
        try {
            $authService = new AuthService($this->db);
            $jwt = TokenHelper::getBearerToken();  // Obtém o token JWT do cabeçalho Authorization

            if (!$jwt) {
                http_response_code(400);
                echo json_encode(['error' => 'Token not provided']);
                return;
            }

            // Executa o logout (remove o token do banco de dados)
            $authService->logout($jwt);

            // Resposta de logout bem-sucedido
            http_response_code(200);
            echo json_encode(['message' => 'Logout successful']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Logout failed: ' . $e->getMessage()]);
        }
    }
}

