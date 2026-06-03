# MedIA — Backend PHP

Projeto PAP — Backend PHP com autenticação, verificação de email e API REST.

## Estrutura

```
mydev.papdatabase.com/
├── app/
│   ├── config/
│   │   ├── Database.php       # Ligação PDO ao MySQL
│   │   └── mail.php           # Config SMTP (Mailtrap)
│   ├── controllers/
│   │   ├── AuthController.php # Login, Signup, Verify Email, Logout
│   │   ├── UserController.php # Perfil de utilizador
│   │   └── WebController.php  # Páginas públicas
│   ├── dao/
│   │   ├── UserDAO.php              # CRUD de utilizadores
│   │   └── EmailVerificationDAO.php # Tokens de verificação
│   ├── middleware/
│   │   └── AuthMiddlewareWeb.php    # isLogin(), isAdmin(), canEdit()
│   ├── models/
│   │   └── User.php           # Modelo de utilizador
│   └── services/
│       └── Mailer.php         # Envio de emails via PHPMailer
├── public/
│   ├── index.php              # Router principal
│   ├── api/api.php            # API REST com JWT
│   ├── views/
│   │   ├── home.php
│   │   ├── login.php
│   │   ├── signup.php
│   │   ├── verify-email.php
│   │   ├── admin.php
│   │   └── user/profile.php
│   └── includes/
│       ├── header.php
│       └── footer.php
├── schema.sql                 # Script SQL para criar a BD
└── composer.json
```

## Configuração

1. Importar `schema.sql` no MySQL (cria a BD `mydevpapdatabase`)
2. Ajustar credenciais em `app/config/Database.php`
3. Ajustar SMTP em `app/config/mail.php` (Mailtrap por defeito)
4. Apontar o vhost para `public/`

## Rotas Web

| Rota              | Método | Descrição                        |
|-------------------|--------|----------------------------------|
| `/`               | GET    | Página principal                 |
| `/login`          | GET    | Formulário de login              |
| `/login`          | POST   | Processar login                  |
| `/logout`         | GET    | Terminar sessão                  |
| `/signup`         | GET    | Formulário de registo            |
| `/signup`         | POST   | Processar registo                |
| `/verify-email`   | GET    | Formulário de verificação        |
| `/verify-email`   | POST   | Processar verificação            |
| `/admin`          | GET    | Dashboard admin (requer is_admin)|
| `/users/{id}`     | GET    | Perfil de utilizador             |

## API REST (`/api/api.php?rota=...`)

| Rota            | Método | Auth  | Descrição              |
|-----------------|--------|-------|------------------------|
| `login`         | POST   | Não   | Retorna JWT            |
| `utilizadores`  | GET    | JWT   | Lista utilizadores     |
