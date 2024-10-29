<?php
namespace App\Helpers;

class TokenHelper {

    public static function getBearerToken() {
        $header = null;

        // Captura headers dependendo da configuração do servidor
        if (isset($_SERVER['Authorization'])) {
            $header = $_SERVER['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Servidores como Apache prefixam com HTTP_
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
}
