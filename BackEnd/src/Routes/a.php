<?php
// Simples roteador baseado em query string
$route = $_GET['route'] ?? '/'; // Pega a rota da URL ou define como padrão para home

switch ($route) {
    case '/':
        require 'home.php';
        break;
    case '/about':
        require 'about.php';
        break;
    case '/contact':
        require 'contact.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
