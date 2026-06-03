<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';

require '../app/controllers/WebController.php';
require '../app/controllers/AuthController.php';
require '../app/controllers/UserController.php';
require '../app/middleware/AuthMiddlewareWeb.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/' || $uri === '/index' || $uri === '/home') {
    (new WebController())->index();
}

// Login
elseif ($uri === '/login' && $method === 'GET') {
    if (AuthMiddlewareWeb::isLogin()) { header('Location: /'); exit; }
    (new WebController())->login();
}
elseif ($uri === '/login' && $method === 'POST') {
    (new AuthController())->loginWeb();
}

// Logout
elseif ($uri === '/logout' && $method === 'GET') {
    if (AuthMiddlewareWeb::isLogin()) {
        (new AuthController())->logoutWeb();
    } else {
        header('Location: /login'); exit;
    }
}

// Signup
elseif ($uri === '/signup' && $method === 'GET') {
    (new WebController())->signup();
}
elseif ($uri === '/signup' && $method === 'POST') {
    try {
        (new AuthController())->signupWeb();
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: /signup'); exit;
    }
}

// Verify Email
elseif ($uri === '/verify-email' && $method === 'GET') {
    (new AuthController())->verifyEmailForm();
}
elseif ($uri === '/verify-email' && $method === 'POST') {
    try {
        (new AuthController())->verifyEmailSubmit();
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: /login'); exit;
    }
}

// Rotas protegidas 

// Edit Profile
elseif ($uri === '/editprofile' && $method === 'GET') {
    if (!AuthMiddlewareWeb::isLogin()) { header('Location: /login'); exit; }
    (new WebController())->editProfile();
}
elseif ($uri === '/editprofile' && $method === 'POST') {
    if (!AuthMiddlewareWeb::isLogin()) { header('Location: /login'); exit; }
    (new AuthController())->editProfileWeb();
}

// Admin 
elseif ($uri === '/admin' && $method === 'GET') {
    if (!AuthMiddlewareWeb::isAdmin()) { header('Location: /login'); exit; }
    (new WebController())->admin();
}

// Admin 
elseif ($uri === '/admin/toggle-admin' && $method === 'POST') {
    if (!AuthMiddlewareWeb::isAdmin()) { header('Location: /login'); exit; }

    $targetId  = (int)($_POST['user_id'] ?? 0);
    $currentId = (int)($_SESSION['token']['id'] ?? 0);

    if ($targetId > 0 && $targetId !== $currentId) {
        require_once '../app/config/Database.php';
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row !== false) {
                $newRole = $row['is_admin'] ? 0 : 1;
                $pdo->prepare('UPDATE users SET is_admin = ? WHERE id = ?')->execute([$newRole, $targetId]);
                $_SESSION['toast'] = [
                    'type'    => 'success',
                    'message' => $newRole ? 'Utilizador promovido a Admin.' : 'Role de Admin removida.'
                ];
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }

    header('Location: /admin');
    exit;
}

// Perfil de utilizador /users/{id}
elseif (preg_match('#^/users/(\d+)$#', $uri, $m) && $method === 'GET') {
    (new UserController())->profile((int)$m[1]);
}

// Utilitários 

elseif ($uri === '/bad-request') {
    (new WebController())->badRequest();
}

// 404
else {
    http_response_code(404);
    echo '404 Not Found';
}
