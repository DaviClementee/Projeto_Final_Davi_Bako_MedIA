<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';

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

    $pdo  = DatabaseSingle::connect();
    $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE is_verified = 1 AND deleted_at IS NULL ORDER BY id ASC');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Utilizadores obtidos com sucesso',
        'data'    => ['users' => $users],
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
