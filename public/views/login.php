<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedIA — Iniciar Sessão</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }

        body {
            display: grid;
            place-items: center;
            min-height: 100vh;
            background: #f6f7f9;
            margin: 0;
        }

        .card {
            width: min(420px, 100% - 2rem);
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        }

        h2 { margin: 0; color: #BF1922; font-size: 1.8rem; }
        h1 { font-size: 1.15rem; margin: .4rem 0 1.25rem; color: #444; font-weight: 400; }

        label {
            display: block;
            font-size: .9rem;
            margin: .5rem 0 .25rem;
            color: #555;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: .65rem .75rem;
            border: 1px solid #ccd1d7;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color .2s;
        }

        input:focus {
            outline: none;
            border-color: #BF1922;
            box-shadow: 0 0 0 3px rgba(191,25,34,.1);
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: .5rem 0 1rem;
            font-size: .9rem;
        }

        .btn-media {
            width: 100%;
            padding: .75rem;
            border: 0;
            border-radius: 8px;
            background: #BF1922;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity .2s;
        }
        .btn-media:hover { opacity: 0.88; }

        .input-with-toggle { position: relative; }
        .toggle {
            position: absolute; right: .5rem; top: 50%; transform: translateY(-50%);
            border: 0; background: none; cursor: pointer; color: #BF1922; font-size: .85rem;
        }
        .has-toggle { padding-right: 5rem; }

        .divider { text-align:center; color:#999; margin: 1rem 0; font-size:.85rem; }
        .link-media { color: #BF1922; text-decoration: none; }
        .link-media:hover { text-decoration: underline; }

        .back-link {
            position: fixed; top: 1rem; left: 1rem;
            color: #BF1922; text-decoration: none; font-size: .9rem;
            display: flex; align-items: center; gap: .25rem;
        }
    </style>
</head>

<body>

<a class="back-link" href="/">
    <i class="bi bi-arrow-left"></i> Voltar ao site
</a>

<main class="card">
    <h2>MedIA</h2>
    <h1>Iniciar Sessão</h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success py-2 mb-3"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <form method="POST" action="/login">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required placeholder="exemplo@email.com" autocomplete="email">

        <label for="pwd">Password</label>
        <div class="input-with-toggle">
            <input id="pwd" name="password" type="password" class="has-toggle" required placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="toggle" id="togglePwd">mostrar</button>
        </div>

        <div class="actions">
            <label>
                <input type="checkbox" name="remember"> Manter sessão
            </label>
        </div>

        <button type="submit" class="btn-media">Entrar</button>
    </form>

    <div class="divider">— ou —</div>

    <div class="text-center" style="font-size:.9rem;">
        Não tens conta?
        <a class="link-media" href="/signup">Registar aqui</a>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const toggle = document.getElementById('togglePwd');
    const pwd    = document.getElementById('pwd');
    toggle.addEventListener('click', () => {
        const visible = pwd.type === 'text';
        pwd.type = visible ? 'password' : 'text';
        toggle.textContent = visible ? 'mostrar' : 'ocultar';
    });
    const _toast = <?= json_encode($_SESSION['toast'] ?? null) ?>;
    <?php unset($_SESSION['toast']); ?>
    if (_toast) {
        toastr.options = { positionClass: 'toast-top-right', timeOut: 4000 };
        toastr[_toast.type](_toast.message);
    }
</script>

</body>
</html>
