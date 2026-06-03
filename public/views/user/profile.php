<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="mb-3">Perfil do Utilizador</h4>

          <?php if (AuthMiddlewareWeb::canEdit($user->getId())): ?>
            <form method="POST" action="/users/<?= $user->getId() ?>">
              <div class="mb-2">
                <label class="form-label">Username</label>
                <input name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" class="form-control" placeholder="Username" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Email</label>
                <input name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" type="email" class="form-control" placeholder="Email" required>
              </div>
              <button class="btn btn-primary w-100">Guardar</button>
            </form>
          <?php else: ?>
            <p><strong>Username:</strong> <?= htmlspecialchars($user->getUsername()) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user->getEmail()) ?></p>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
