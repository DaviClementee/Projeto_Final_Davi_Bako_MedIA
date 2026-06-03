<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/ConversaDAO.php';
require_once __DIR__ . '/../../app/dao/MensagemDAO.php';

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

    // Obter ID da conversa
    $conversaId = (int)($_GET['id'] ?? 0);
    if ($conversaId <= 0) {
        throw new Exception('ID da conversa e obrigatorio');
    }

    $conversaDAO = new ConversaDAO();
    $mensagemDAO = new MensagemDAO();

    // Verificar se a conversa pertence ao utilizador
    if (!$conversaDAO->belongsToUser($conversaId, $userId)) {
        http_response_code(403);
        throw new Exception('Acesso negado a esta conversa');
    }

    // GET - Buscar conversa com mensagens
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $conversa = $conversaDAO->findById($conversaId);
        $mensagens = $mensagemDAO->findByConversaId($conversaId);

        $mensagensArray = [];
        foreach ($mensagens as $msg) {
            $mensagensArray[] = $msg->toArray();
        }

        echo json_encode([
            'success' => true,
            'conversa' => $conversa->toArray(),
            'mensagens' => $mensagensArray,
            'total_mensagens' => count($mensagensArray)
        ]);
        exit;
    }

    // DELETE - Apagar conversa
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $conversaDAO->delete($conversaId);

        echo json_encode([
            'success' => true,
            'message' => 'Conversa apagada com sucesso'
        ]);
        exit;
    }

    // PUT - Atualizar titulo
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $putData);
        $titulo = trim($putData['titulo'] ?? '');

        if (empty($titulo)) {
            throw new Exception('Titulo e obrigatorio');
        }

        $conversaDAO->updateTitulo($conversaId, $titulo);
        $conversa = $conversaDAO->findById($conversaId);

        echo json_encode([
            'success' => true,
            'message' => 'Conversa atualizada com sucesso',
            'conversa' => $conversa->toArray()
        ]);
        exit;
    }

    throw new Exception('Metodo nao permitido');

} catch (Exception $e) {
    $code = http_response_code();
    if ($code === 200) {
        http_response_code(400);
    }
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}