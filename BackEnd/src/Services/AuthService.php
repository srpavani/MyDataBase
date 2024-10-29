<?php 
namespace App\Services;
use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Exception;



use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__FILE__,));
$dotenv->load();

class AuthService {
    private $db;
    private $key;
    private $alg;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->key = $_ENV['KEY'];
        $this->alg = 'HS256';
    }

    public function createToken($user) {
        $payload = [
            'iat' => time(),
            'exp' => time() + 7200, // Token expira em 2 horas
            'sub' => $user->getId(),
            'username' => $user->getName(),
            'admin' => $user->isAdmin()//sempre falso, mesmo se alterarem o payload vai ser sempre falso e erro de assinatura;
        ];

        $jwt = JWT::encode($payload, $this->key, $this->alg);

        // Salva o token no banco de dados
        $stmt = $this->db->prepare("INSERT INTO jwt_sessions (user_id, jwt_token, expires_at) VALUES (:user_id, :jwt_token, FROM_UNIXTIME(:expires_at))");
        $stmt->execute([
            ':user_id' => $user->getId(),
            ':jwt_token' => $jwt,
            ':expires_at' => $payload['exp']
        ]);

        return $jwt;
    }

    public function validateToken($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($this->key, $this->alg));

            // Verifica se o token está no banco de dados e não expirou
            $stmt = $this->db->prepare("SELECT * FROM jwt_sessions WHERE jwt_token = :jwt_token AND expires_at > NOW()");
            $stmt->execute([':jwt_token' => $jwt]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ? $decoded : null;
        } catch (SignatureInvalidException $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token signature']);
            exit;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token validation failed: ' . $e->getMessage()]);
            exit;
        }
    }
    public function logout($jwt) {
        // Remove o token do banco de dados
        $stmt = $this->db->prepare("DELETE FROM jwt_sessions WHERE jwt_token = :jwt_token");
        $stmt->execute([':jwt_token' => $jwt]);
    }

    public function getUserById($jwt) {
        $decoded = $this->validateToken($jwt);
        if (!$decoded) {
            return null; // Token inválido ou expirado
        }

        $userId = $decoded->sub; // ID do usuário a partir do token

        // Consulta para buscar o usuário no banco de dados
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null; // Retorna os dados do usuário ou null se não for encontrado
    }

    public function getOnlyId($jwt) {
        $decoded = $this->validateToken($jwt);
        if (!$decoded) {
            echo json_encode(['error' => 'TokenInvalido']);
        }

        $userId = $decoded->sub; // ID do usuário a partir do token
        echo json_encode(['idUser' => "{$userId}"]);
            exit;
        
    }


}





   

