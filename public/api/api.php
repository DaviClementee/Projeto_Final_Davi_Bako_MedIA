<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/UserDAO.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'media_secret_key_2025';

$rota = $_GET['rota'] ?? '';

// Helper para resposta de erro
function errorResponse($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// Helper para validar JWT e retornar userId
function getAuthUserId($secretKey) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    if (!$token) {
        errorResponse('Token não fornecido', 401);
    }

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return (int)$decoded->data->user_id;
    } catch (Exception $e) {
        errorResponse('Token inválido ou expirado', 401);
    }
}

switch ($rota) {

    case 'signup':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            errorResponse('Método não permitido', 405);
        }

        $username        = trim($_POST['username'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (!$username || !$email || !$password || !$confirmPassword) {
            errorResponse('Dados insuficientes. Preencha os dados corretamente');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            errorResponse('Email inválido');
        }

        if ($password !== $confirmPassword) {
            errorResponse('As passwords não coincidem');
        }

        $userDao = new UserDAO();

        if ($userDao->findByEmailAny($email)) {
            errorResponse('Email já existente.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $userDao->createUser($username, $email, $passwordHash);

        echo json_encode([
            'success' => true,
            'message' => 'Utilizador registado com sucesso',
            'user'    => ['id' => $userId, 'username' => $username, 'email' => $email],
        ]);
        break;

    case 'getprofile':
        $userId = getAuthUserId($secretKey);
        $user   = (new UserDAO())->findById($userId);

        if (!$user) {
            errorResponse('Utilizador não encontrado', 404);
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
        break;

    case 'utilizadores':
        $userId = getAuthUserId($secretKey);

        $db   = DatabaseSingle::connect();
        $stmt = $db->query("SELECT id, username, email FROM users WHERE is_verified = 1 AND deleted_at IS NULL");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => ['users' => $users]]);
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            errorResponse('Método não permitido', 405);
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            errorResponse('Email e password são obrigatórios');
        }

        $user = (new UserDAO())->findByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            errorResponse('Email ou password inválidos', 401);
        }

        $payload = [
            'iss'  => 'media-api',
            'iat'  => time(),
            'exp'  => time() + 3600,
            'data' => ['user_id' => $user->getId()]
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        echo json_encode([
            'success' => true,
            'token'   => $jwt,
            'user'    => [
                'id'       => $user->getId(),
                'username' => $user->getUsername(),
                'email'    => $user->getEmail(),
            ]
        ]);
        break;

    default:
        errorResponse('Rota não encontrada', 404);
        break;
}
