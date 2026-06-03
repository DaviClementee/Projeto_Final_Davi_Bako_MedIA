<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/EmailVerificationDAO.php';
//require_once __DIR__ . '/../middleware/AuthMiddlewareWeb.php';

class UserController {

    private function view($name, $data = []) {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../public/views/' . $name . '.php';
    }

    public function profile($userId) {
        if (!AuthMiddlewareWeb::isLogin()) {
            header('Location: /login');
            exit;
        }

        $user = (new UserDAO())->findById($userId);

        if (!$user) {
            http_response_code(404);
            echo '404 - Utilizador não encontrado';
            exit;
        }

        $this->view('user/profile', ['user' => $user]);
    }
}
