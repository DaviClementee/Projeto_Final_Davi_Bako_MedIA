<?php
/**
 * API: Gestao de Conversas
 * 
 * GET /api/conversas.php - Lista todas as conversas do utilizador
 * POST /api/conversas.php - Cria uma nova conversa
 * 
 * Headers: Authorization: Bearer {token}
 * 
 * POST Body (opcional):
 * - titulo: string (opcional, default: "Nova Conversa")
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/ConversaDAO.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'media_secret_key_2025';

try {
    // Validar token JWT
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        throw new Exception('Token nao fornecido');
    }

    $token = $matches[1];
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId = $decoded->data->user_id;

    $conversaDAO = new ConversaDAO();

    // GET - Listar conversas
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $conversas = $conversaDAO->findByUserId($userId);
        
        $result = [];
        foreach ($conversas as $conversa) {
            $result[] = $conversa->toArray();
        }

        echo json_encode([
            'success' => true,
            'conversas' => $result,
            'total' => count($result)
        ]);
        exit;
    }

    // POST - Criar nova conversa
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = trim($_POST['titulo'] ?? 'Nova Conversa');
        
        if (empty($titulo)) {
            $titulo = 'Nova Conversa';
        }

        $conversaId = $conversaDAO->create($userId, $titulo);
        $conversa = $conversaDAO->findById($conversaId);

        echo json_encode([
            'success' => true,
            'message' => 'Conversa criada com sucesso',
            'conversa' => $conversa->toArray()
        ]);
        exit;
    }

    throw new Exception('Metodo nao permitido');

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}