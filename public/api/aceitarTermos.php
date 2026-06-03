<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'media_secret_key_2025';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metodo nao permitido');
    }

    $headers    = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        throw new Exception('Token nao fornecido');
    }

    $token   = $matches[1];
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId  = $decoded->data->user_id;

    $pdo = DatabaseSingle::connect();
    $pdo->beginTransaction();
    try {
        $sql  = "UPDATE users SET termos_aceites = 1, termos_aceites_at = NOW(), updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Utilizador nao encontrado');
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Termos de uso aceites com sucesso'
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
