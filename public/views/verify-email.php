<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedIA — Verificar Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            display: grid;
            place-items: center;
            min-height: 100vh;
            background: #f6f7f9;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }
        .card {
            width: min(460px, 100% - 2rem);
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        }
        h2 { color: #BF1922; }
        input[type="password"] {
            width: 100%; padding: .65rem .75rem;
            border: 1px solid #ccd1d7; border-radius: 8px; font-size: 1rem;
        }
        input[type="password"]:focus { outline: none; border-color: #BF1922; box-shadow: 0 0 0 3px rgba(191,25,34,.1); }
        .btn-media {
            width: 100%; padding: .75rem; border: 0; border-radius: 8px;
            background: #BF1922; color: #fff; font-weight: 600;
            cursor: pointer; font-size: 1rem; margin-top: 1rem;
        }
    </style>
</head>

<body>
<main class="card">
    <h2 class="mb-1"><i class="bi bi-envelope-check me-2"></i>MedIA</h2>
    <h5 class="text-muted mb-4">Verificar email e definir password</h5>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form method="POST" action="/verify-email">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

        <label class="form-label">Nova password</label>
        <input type="password" name="password" required autocomplete="new-password" placeholder="••••••••">

        <button type="submit" class="btn-media">
            <i class="bi bi-check-circle me-2"></i>Confirmar e Ativar Conta
        </button>
    </form>

    <p class="text-muted small mt-3 mb-0">
        <i class="bi bi-clock me-1"></i>O link expira em 5 minutos. Se expirar, faz o registo novamente.
    </p>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
