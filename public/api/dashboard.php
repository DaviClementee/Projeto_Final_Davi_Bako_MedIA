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

    $pdo = DatabaseSingle::connect();

    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM users WHERE is_verified = 1 AND deleted_at IS NULL');
    $stmt->execute();
    $totalUsers = (int)$stmt->fetchColumn();

    $stmt2 = $pdo->prepare('SELECT COUNT(*) as total FROM users WHERE is_verified = 0');
    $stmt2->execute();
    $totalPending = (int)$stmt2->fetchColumn();

    $stmt3 = $pdo->prepare('SELECT COUNT(*) as total FROM email_verifications');
    $stmt3->execute();
    $totalTokens = (int)$stmt3->fetchColumn();

    $stmt4 = $pdo->prepare('SELECT COUNT(*) as total FROM email_verifications WHERE used_at IS NULL AND expires_at > NOW()');
    $stmt4->execute();
    $totalActiveTokens = (int)$stmt4->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => 'Dashboard data retrieved successfully',
        'data'    => [
            'total_users'         => $totalUsers,
            'total_pending'       => $totalPending,
            'total_tokens'        => $totalTokens,
            'total_active_tokens' => $totalActiveTokens,
        ],
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
