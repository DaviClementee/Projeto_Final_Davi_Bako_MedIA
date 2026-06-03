<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/UserDAO.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'media_secret_key_2025';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        throw new Exception('Email e password são obrigatórios');
    }

    $user = (new UserDAO())->findByEmail($email);

    if (!$user || !password_verify($password, $user->getPassword())) {
        throw new Exception('Email ou password inválidos');
    }

    // Criar JWT
    $payload = [
        'iss'  => 'media-api',
        'aud'  => 'media-api',
        'iat'  => time(),
        'exp'  => time() + 3600,
        'data' => [
            'user_id' => $user->getId(),
        ]
    ];

    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    // Resposta final no formato que o android precisa
    echo json_encode([
        'success' => true,
        'message' => 'Login efetuado com sucesso',
        'data' => [
            'user' => [
                'id'        => $user->getId(),
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'isAdmin'   => $user->isAdmin(),
                'createdAt' => $user->getCreatedAt(),
                'updatedAt' => $user->getUpdatedAt(),
                'deletedAt' => $user->getDeletedAt()
            ],
            'jwt' => $jwt
        ]
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
