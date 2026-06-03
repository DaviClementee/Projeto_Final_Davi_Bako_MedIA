<?php

class WebController
{
    private function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../public/views/' . $name . '.php';
    }

    public function index(): void
    {
        $this->view('home');
    }

    public function login(): void
    {
        $this->view('login');
    }

    public function signup(): void
    {
        $this->view('signup');
    }

    public function editProfile(): void
    {
        $this->view('editprofile');
    }

    public function admin(): void
    {
        $this->view('admin');
    }

    public function badRequest(): void
    {
        $this->view('errors/400');
    }
}
