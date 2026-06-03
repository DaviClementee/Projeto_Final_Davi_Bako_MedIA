<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/UserDAO.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'media_secret_key_2025';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método não permitido');
    }

    $headers    = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token      = '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    if (!$token) {
        throw new Exception('Token não fornecido');
    }

    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId  = (int)$decoded->data->user_id;

    $user = (new UserDAO())->findById($userId);

    if (!$user) {
        throw new Exception('Utilizador não encontrado');
    }

    echo json_encode([
        'success' => true,
        'data'    => [
            'user' => [
                'id'       => $user->getId(),
                'username' => $user->getUsername(),
                'name'     => $user->getUsername(), 
                'email'    => $user->getEmail(),
                'is_admin' => $user->isAdmin(),
            ],
        ],
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
