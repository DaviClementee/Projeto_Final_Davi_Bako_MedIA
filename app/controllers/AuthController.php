<?php

require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/EmailVerificationDAO.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/Mailer.php';

class AuthController
{
    private function view($name, $data = [])
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../public/views/' . $name . '.php';
    }

    public function loginWeb() {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Email e password são obrigatórios';
            header('Location: /login');
            exit;
        }

        $user = (new UserDAO())->findByEmail($email);

        if (!$user) {
            $_SESSION['flash_error'] = 'Email ou password inválidos';
            header('Location: /login');
            exit;
        }

        if (password_verify($password, $user->getPassword())) {
            $_SESSION['token'] = [
                'id'       => $user->getId(),
                'username' => $user->getUsername(),
                'email'    => $user->getEmail(),
                'is_admin' => $user->isAdmin()
            ];

            $_SESSION['toast'] = [
                'type'    => 'success',
                'message' => 'Bem-vindo de volta, ' . $user->getUsername() . '!'
            ];

            if ($user->isAdmin()) {
                header('Location: /admin');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            $_SESSION['toast'] = [
                'type'    => 'error',
                'message' => 'Dados de login inválidos'
            ];
            header('Location: /login');
            exit;
        }
    }

    public function signupWeb()
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');

        if (empty($username) || empty($email)) {
            throw new Exception('Todos os campos são obrigatórios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        $userDao = new UserDAO();

        if ($userDao->findByEmail($email)) {
            throw new Exception('Email já existe');
        }

        $userId = $userDao->createPending($username, $email);

        $verDao = new EmailVerificationDAO();
        $token  = $verDao->createForUser($userId, 300);

        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $scheme . '://' . $host;
        $link    = $baseUrl . '/verify-email?token=' . urlencode($token);

        $subject = 'Verifica o teu email (expira em 5 min)';
        $html    = "
            <div style='font-family: Arial, sans-serif;'>
            <h2>Olá, " . htmlspecialchars($username) . "!</h2>
            <p>Para ativares a tua conta e definires a tua password, clica no link abaixo (válido por <b>5 minutos</b>):</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Se o link expirar, faz signup novamente.</p>
            </div>
        ";

        (new Mailer())->send($email, $subject, $html);

        $_SESSION['flash_success'] = 'Conta criada. Enviámos um email para verificares (link expira em 5 min).';
        header('Location: /login');
        exit;
    }

    public function editProfileWeb()
    {
        if (!isset($_SESSION['token'])) {
            header('Location: /login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username)) {
            $_SESSION['flash_error'] = 'Username é obrigatório';
            header('Location: /editprofile');
            exit;
        }

        $userId = (int)$_SESSION['token']['id'];
        $pdo    = DatabaseSingle::connect();
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

        $_SESSION['token']['username'] = $username;

        $_SESSION['toast'] = [
            'type'    => 'success',
            'message' => 'Perfil atualizado com sucesso!'
        ];

        header('Location: /editprofile');
        exit;
    }

    public function verifyEmailForm()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            header('Location: /bad-request');
            exit;
        }

        $this->view('verify-email', ['token' => $token]);
    }

    public function verifyEmailSubmit()
    {
        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($token) || empty($password)) {
            throw new Exception('Dados inválidos');
        }

        $verDAO = new EmailVerificationDAO();
        $userId = $verDAO->validateToken($token);

        if (!$userId) {
            throw new Exception('Token inválido ou expirado');
        }

        $hash    = password_hash($password, PASSWORD_DEFAULT);
        $userDao = new UserDAO();
        $userDao->setPasswordAndVerify($userId, $hash);
        $verDAO->markUsed($token);

        $_SESSION['flash_success'] = 'Email verificado e password definida. Já podes fazer login.';
        header('Location: /login');
        exit;
    }

    public function logoutWeb()
    {
        unset($_SESSION['token']);
        $_SESSION['toast'] = [
            'type'    => 'success',
            'message' => 'Sessão terminada com sucesso!'
        ];
        header('Location: /');
        exit;
    }
}
