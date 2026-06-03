<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedIA — Criar Conta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            display: grid;
            place-items: center;
            min-height: 100vh;
            background: #f6f7f9;
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }
        .card {
            width: min(440px, 100% - 2rem);
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        }
        h2 { margin: 0; color: #BF1922; font-size: 1.8rem; }
        h1 { font-size: 1.15rem; margin: .4rem 0 1.25rem; color: #444; font-weight: 400; }
        label { display: block; font-size: .9rem; margin: .5rem 0 .25rem; color: #555; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: .65rem .75rem;
            border: 1px solid #ccd1d7; border-radius: 8px; font-size: 1rem;
            transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #BF1922; box-shadow: 0 0 0 3px rgba(191,25,34,.1); }
        .btn-media {
            width: 100%; padding: .75rem; border: 0; border-radius: 8px;
            background: #BF1922; color: #fff; font-weight: 600;
            cursor: pointer; font-size: 1rem; transition: opacity .2s; margin-top: 1rem;
        }
        .btn-media:hover { opacity: 0.88; }
        .link-media { color: #BF1922; text-decoration: none; }
        .link-media:hover { text-decoration: underline; }
        .info-box { background:#fff8f8; border:1px solid #f5c6c6; border-radius:8px; padding:.75rem 1rem; font-size:.85rem; color:#666; margin-bottom:1rem; }
        .back-link {
            position: fixed; top: 1rem; left: 1rem;
            color: #BF1922; text-decoration: none; font-size: .9rem;
            display: flex; align-items: center; gap: .25rem;
        }
        .input-with-toggle { position: relative; }
        .toggle {
            position: absolute; right: .5rem; top: 50%; transform: translateY(-50%);
            border: 0; background: none; cursor: pointer; color: #BF1922; font-size: .85rem;
        }
        .has-toggle { padding-right: 5rem; }
    </style>
</head>

<body>

<a class="back-link" href="/">
    <i class="bi bi-arrow-left"></i> Voltar ao site
</a>

<main class="card">
    <h2>MedIA</h2>
    <h1>Criar Conta</h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="info-box">
        <i class="bi bi-envelope-check me-1 text-danger"></i>
        Após o registo receberás um email para verificar a conta e definir a tua password.
    </div>

    <form method="POST" action="/signup">
        <label for="username">Nome de utilizador</label>
        <input id="username" name="username" type="text" required placeholder="O teu nome" autocomplete="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required placeholder="exemplo@email.com" autocomplete="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <button type="submit" class="btn-media">
            <i class="bi bi-person-plus me-2"></i>Criar Conta
        </button>
    </form>

    <div class="text-center mt-3" style="font-size:.9rem;">
        Já tens conta?
        <a class="link-media" href="/login">Iniciar sessão</a>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const _toast = <?= json_encode($_SESSION['toast'] ?? null) ?>;
    <?php unset($_SESSION['toast']); ?>
    if (_toast) {
        toastr.options = { positionClass: 'toast-top-right', timeOut: 4000 };
        toastr[_toast.type](_toast.message);
    }
</script>

</body>
</html>
