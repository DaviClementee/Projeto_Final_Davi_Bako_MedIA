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

    $headers    = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token      = '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    } elseif (!empty($_POST['token'])) {
        $token = $_POST['token'];
    }

    if (!$token) {
        throw new Exception('Token não fornecido');
    }

    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId  = (int)$decoded->data->user_id;

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '') {
        throw new Exception('Username é obrigatório');
    }

    $pdo = DatabaseSingle::connect();
    $pdo->beginTransaction();
    try {
        if ($password !== '') {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username = ?, password = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$username, $hashedPassword, $userId]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$username, $userId]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

    $stmtSelect = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ?');
    $stmtSelect->execute([$userId]);
    $updatedUser = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Perfil atualizado com sucesso',
        'user'    => $updatedUser,
        'token'   => $token,
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
