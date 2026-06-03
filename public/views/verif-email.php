<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
/** @var string $token */
?>
<!doctype html>
<html lang="pt">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verificar email</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-light">
<div class="container py-5" style="max-width: 520px;">
<h1 class="h3 mb-3">Verificar email e definir password</h1>


<?php //require __DIR__ . '/partials/toasts.php'; ?>



<form method="post" action="/verify-email" class="card card-body shadow-sm">
<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">


<div class="mb-3">
<label class="form-label">Nova password</label>
<input class="form-control" type="password" name="password" required autocomplete="new-password">
</div>



<button class="btn btn-primary w-100" type="submit">Confirmar</button>
</form>



<p class="text-muted small mt-3 mb-0">
O link expira em 5 minutos. Se expirar, pede um novo link.
</p>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>