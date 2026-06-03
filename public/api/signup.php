<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/dao/UserDAO.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!isset($_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    $email           = trim($_POST['email']);
    $username        = trim($_POST['username']);
    $password        = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if ($email === '' || $username === '' || $password === '' || $confirmPassword === '') {
        throw new Exception('Dados insuficientes. Preencha os dados corretamente');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    if ($password !== $confirmPassword) {
        throw new Exception('As passwords não coincidem');
    }

    $userDao = new UserDAO();

    if ($userDao->findByEmailAny($email)) {
        throw new Exception('Erro ao criar registo. Email já existente.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $userId       = $userDao->createUser($username, $email, $passwordHash);


    echo json_encode([
        'success' => true,
        'message' => 'Utilizador registado com sucesso',
        'user'    => [
            'id'       => $userId,
            'username' => $username,
            'email'    => $email,
        ],
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}