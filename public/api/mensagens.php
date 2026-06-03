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
    $headers    = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        throw new Exception('Token nao fornecido');
    }

    $token   = $matches[1];
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId  = $decoded->data->user_id;

    $conversaDAO = new ConversaDAO();
    $mensagemDAO = new MensagemDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $conversaId = (int)($_GET['conversa_id'] ?? 0);

        if ($conversaId <= 0) {
            throw new Exception('conversa_id e obrigatorio');
        }

        if (!$conversaDAO->belongsToUser($conversaId, $userId)) {
            http_response_code(403);
            throw new Exception('Acesso negado a esta conversa');
        }

        $mensagens = $mensagemDAO->findByConversaId($conversaId);
        $result    = [];
        foreach ($mensagens as $msg) {
            $result[] = $msg->toArray();
        }

        echo json_encode([
            'success'   => true,
            'mensagens' => $result,
            'total'     => count($result)
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Aceita tanto JSON como form-data
        $body       = json_decode(file_get_contents('php://input'), true) ?? [];
        $mensagem   = trim($body['mensagem']     ?? $_POST['mensagem']     ?? '');
        $resposta   = trim($body['resposta']     ?? $_POST['resposta']     ?? '');
        $conversaId = (int)($body['conversa_id'] ?? $_POST['conversa_id'] ?? 0);

        if (empty($mensagem)) {
            throw new Exception('mensagem e obrigatoria');
        }

        // Verificar estado tem_conversa do utilizador
        $pdo  = DatabaseSingle::connect();
        $stmt = $pdo->prepare("SELECT tem_conversa FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception('Utilizador nao encontrado');
        }

        $temConversa = (bool)$user['tem_conversa'];

        if (!$temConversa) {
            // Primeira conversa 
            $pdo->beginTransaction();
            try {
                $conversaId = $conversaDAO->create($userId, 'Nova Conversa');
                $pdo->prepare("UPDATE users SET tem_conversa = 1, updated_at = NOW() WHERE id = ?")
                    ->execute([$userId]);
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } else {
            if ($conversaId <= 0) {
                throw new Exception('conversa_id e obrigatorio quando tem_conversa e true');
            }

            if (!$conversaDAO->belongsToUser($conversaId, $userId)) {
                http_response_code(403);
                throw new Exception('Acesso negado a esta conversa');
            }
        }

        // Guardar mensagem do utilizador
        $userMsgId = $mensagemDAO->create($conversaId, 'user', $mensagem);

        $response = [
            'success'      => true,
            'message'      => 'Mensagem adicionada com sucesso',
            'conversa_id'  => $conversaId,
            'tem_conversa' => true,
            'pergunta'     => [
                'id'          => $userMsgId,
                'conversa_id' => $conversaId,
                'role'        => 'user',
                'conteudo'    => $mensagem
            ]
        ];

        if (!empty($resposta)) {
            $assistantMsgId = $mensagemDAO->create($conversaId, 'assistant', $resposta);
            $response['resposta'] = [
                'id'          => $assistantMsgId,
                'conversa_id' => $conversaId,
                'role'        => 'assistant',
                'conteudo'    => $resposta
            ];
        }

        echo json_encode($response);
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
        'error'   => $e->getMessage()
    ]);
}