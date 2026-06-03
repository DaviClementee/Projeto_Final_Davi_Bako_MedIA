<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../../app/config/Database.php';
$db = DatabaseSingle::connect();

// Estatísticas reais
$totalUsers     = (int)$db->query('SELECT COUNT(*) FROM users WHERE is_verified = 1 AND deleted_at IS NULL')->fetchColumn();
$totalPending   = (int)$db->query('SELECT COUNT(*) FROM users WHERE is_verified = 0')->fetchColumn();
$totalConversas = (int)$db->query('SELECT COUNT(*) FROM conversas')->fetchColumn();
$totalMensagens = (int)$db->query('SELECT COUNT(*) FROM mensagens')->fetchColumn();
$mensagensHoje  = (int)$db->query("SELECT COUNT(*) FROM mensagens WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$conversasHoje  = (int)$db->query("SELECT COUNT(*) FROM conversas WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Acessos últimos 7 dias (conversas por dia como proxy)
$stmt = $db->query("
    SELECT DATE(created_at) as dia, COUNT(*) as total
    FROM conversas
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY dia ORDER BY dia ASC
");
$acessosDias   = [];
$acessosValores = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $acessosDias[]    = date('D', strtotime($row['dia']));
    $acessosValores[] = (int)$row['total'];
}
// preencher dias sem dados
if (empty($acessosDias)) {
    $acessosDias    = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];
    $acessosValores = [0,0,0,0,0,0,0];
}

// Todos os utilizadores
$stmtUsers = $db->query('SELECT id, username, email, is_admin, is_verified, created_at FROM users WHERE deleted_at IS NULL ORDER BY id DESC');
$allUsers  = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Últimas mensagens (log)
$stmtLog = $db->query('
    SELECT m.id, m.role, m.conteudo, m.created_at,
           u.username, c.titulo as conversa_titulo
    FROM mensagens m
    JOIN conversas c ON c.id = m.conversa_id
    JOIN users u ON u.id = c.user_id
    ORDER BY m.created_at DESC LIMIT 20
');
$logs = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

// Conversas
$stmtConv = $db->query('
    SELECT c.id, c.titulo, c.created_at,
           u.username, u.email,
           COUNT(m.id) as total_mensagens
    FROM conversas c
    JOIN users u ON u.id = c.user_id
    LEFT JOIN mensagens m ON m.conversa_id = c.id
    GROUP BY c.id ORDER BY c.created_at DESC LIMIT 50
');
$conversas = $stmtConv->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedIA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7f9; }
        .sidebar {
            background: #fff;
            border-right: 1px solid #e9ecef;
            width: 220px;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar h5 { color: #BF1922; font-weight: 700; margin-bottom: 1.5rem; }
        .sidebar .nav-link { color: #444; border-radius: 8px; padding: .5rem .75rem; cursor: pointer; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { background: #fff0f0; color: #BF1922; }
        .sidebar .nav-link i { margin-right: .5rem; }
        .main-content { margin-left: 220px; padding: 2rem; }
        .topbar { background: #fff; border-bottom: 1px solid #e9ecef; padding: .75rem 2rem; margin-left: 220px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
        .tab-section { display: none; }
        .tab-section.active { display: block; }
        .stat-card { border: 0; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h5><i class="bi bi-activity me-2"></i>MedIA Admin</h5>
    <ul class="nav flex-column gap-1" id="adminNav">
        <li><a class="nav-link active" data-tab="dashboard"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
        <li><a class="nav-link" data-tab="utilizadores"><i class="bi bi-people"></i>Utilizadores</a></li>
        <li><a class="nav-link" data-tab="chats"><i class="bi bi-chat-dots"></i>Chats</a></li>
        <li><a class="nav-link" data-tab="logs"><i class="bi bi-journal-text"></i>Logs</a></li>
    </ul>
    <div class="mt-auto pt-3">
        <hr>
        <a href="/logout" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
        <a href="/" class="btn btn-outline-secondary btn-sm w-100 mt-2"><i class="bi bi-arrow-left me-1"></i>Voltar ao site</a>
    </div>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <span class="fw-semibold text-muted" id="topbarTitle">Painel de Administração</span>
    <span class="text-danger fw-bold">
        <i class="bi bi-person-circle me-1"></i>
        <?= htmlspecialchars($_SESSION['token']['username'] ?? 'Admin') ?>
    </span>
</div>

<div class="main-content">

    <!-- ===== DASHBOARD ===== -->
    <div id="tab-dashboard" class="tab-section active">
        <h2 class="fw-bold mb-3">Dashboard Geral</h2>

        <div class="alert alert-warning">⚠️ VPS N8N necessita de atualizar credenciais.</div>

        <div class="d-flex gap-2 mb-4">
            <span class="badge bg-danger fs-6 px-3 py-2">Chatbot offline</span>
            <span class="badge bg-danger fs-6 px-3 py-2">n8n desativado</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Conversas Hoje</h6>
                        <h3 class="fw-bold text-danger"><?= $conversasHoje ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Mensagens Hoje</h6>
                        <h3 class="fw-bold text-danger"><?= $mensagensHoje ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Conversas</h6>
                        <h3 class="fw-bold text-danger"><?= $totalConversas ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Utilizadores</h6>
                        <h3 class="fw-bold text-danger"><?= $totalUsers ?></h3>
                        <small class="text-warning"><?= $totalPending ?> pendentes</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICO -->
        <div class="card stat-card mb-4 p-4">
            <h5 class="fw-bold">Conversas nos últimos 7 dias</h5>
            <canvas id="chartAcessos" height="100"></canvas>
        </div>

        <!-- ESTADO DO SISTEMA -->
        <div class="card stat-card p-4 mb-4">
            <h5 class="fw-bold">Estado do Sistema</h5>
            <p class="mb-1 mt-2">Utilizadores verificados</p>
            <div class="progress mb-3">
                <?php $pct = ($totalUsers + $totalPending) > 0 ? round($totalUsers / ($totalUsers + $totalPending) * 100) : 0; ?>
                <div class="progress-bar bg-danger" style="width:<?= $pct ?>%"><?= $pct ?>%</div>
            </div>
            <p class="mb-1">Mensagens hoje vs total</p>
            <div class="progress">
                <?php $pct2 = $totalMensagens > 0 ? min(100, round($mensagensHoje / $totalMensagens * 100)) : 0; ?>
                <div class="progress-bar bg-warning" style="width:<?= $pct2 ?>%"><?= $pct2 ?>%</div>
            </div>
        </div>

        <!-- ÚLTIMAS MENSAGENS -->
        <div class="card stat-card p-4">
            <h5 class="fw-bold">Últimas Mensagens</h5>
            <table class="table table-striped mt-3">
                <thead><tr><th>Utilizador</th><th>Mensagem</th><th>Role</th><th>Data</th></tr></thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                    <td class="text-muted small" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= htmlspecialchars(mb_substr($log['conteudo'], 0, 80)) ?>
                    </td>
                    <td><?= $log['role'] === 'user' ? '<span class="badge bg-primary">user</span>' : '<span class="badge bg-danger">assistant</span>' ?></td>
                    <td class="text-muted small"><?= date('d/m H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Sem mensagens ainda.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== UTILIZADORES ===== -->
    <div id="tab-utilizadores" class="tab-section">
        <h2 class="fw-bold mb-4">Utilizadores</h2>
        <div class="card stat-card p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID</th><th>Utilizador</th><th>Email</th>
                            <th>Role</th><th>Estado</th><th>Registo</th><th class="pe-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allUsers as $u): ?>
                    <tr>
                        <td class="ps-3 text-muted small">#<?= $u['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($u['username']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['is_admin'] ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-secondary">Utilizador</span>' ?></td>
                        <td><?= $u['is_verified'] ? '<span class="badge bg-success">Verificado</span>' : '<span class="badge bg-warning text-dark">Pendente</span>' ?></td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-end pe-3">
                            <?php if ($u['id'] != ($_SESSION['token']['id'] ?? 0)): ?>
                            <form method="POST" action="/admin/toggle-admin" class="d-inline" onsubmit="return confirm('Alterar role?')">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button class="btn btn-sm <?= $u['is_admin'] ? 'btn-outline-secondary' : 'btn-outline-danger' ?>">
                                    <?= $u['is_admin'] ? '<i class="bi bi-shield-minus"></i> Remover Admin' : '<i class="bi bi-shield-plus"></i> Tornar Admin' ?>
                                </button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">(você)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($allUsers)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Sem utilizadores.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== CHATS ===== -->
    <div id="tab-chats" class="tab-section">
        <h2 class="fw-bold mb-4">Conversas</h2>
        <div class="card stat-card p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-3">ID</th><th>Título</th><th>Utilizador</th><th>Mensagens</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($conversas as $c): ?>
                    <tr>
                        <td class="ps-3 text-muted small">#<?= $c['id'] ?></td>
                        <td class="small fw-semibold"><?= htmlspecialchars(mb_substr($c['titulo'] ?: 'Sem título', 0, 50)) ?></td>
                        <td>
                            <div class="small"><?= htmlspecialchars($c['username']) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= $c['total_mensagens'] ?></span></td>
                        <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($conversas)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Sem conversas ainda.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== LOGS ===== -->
    <div id="tab-logs" class="tab-section">
        <h2 class="fw-bold mb-4">Logs do Sistema</h2>
        <div class="card stat-card p-0">
            <div class="card-header bg-white text-muted small fw-semibold">Últimas 20 mensagens</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-3">ID</th><th>Utilizador</th><th>Conversa</th><th>Role</th><th>Mensagem</th><th>Data/Hora</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="ps-3 text-muted small">#<?= $log['id'] ?></td>
                        <td class="small fw-semibold"><?= htmlspecialchars($log['username']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($log['conversa_titulo'] ?: '-', 0, 30)) ?></td>
                        <td><?= $log['role'] === 'user' ? '<span class="badge bg-primary">user</span>' : '<span class="badge bg-danger">assistant</span>' ?></td>
                        <td class="small text-muted" style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= htmlspecialchars(mb_substr($log['conteudo'], 0, 100)) ?>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Sem logs ainda.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Tabs
const navLinks = document.querySelectorAll('#adminNav .nav-link');
const sections = document.querySelectorAll('.tab-section');
const titles = { dashboard:'Painel de Administração', utilizadores:'Gestão de Utilizadores', chats:'Conversas', logs:'Logs do Sistema' };

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        const tab = link.dataset.tab;
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        sections.forEach(s => s.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('topbarTitle').textContent = titles[tab] || '';
    });
});

// Gráfico com dados reais da BD
new Chart(document.getElementById('chartAcessos'), {
    type: 'line',
    data: {
        labels: <?= json_encode($acessosDias) ?>,
        datasets: [{
            label: 'Conversas',
            data: <?= json_encode($acessosValores) ?>,
            borderColor: '#BF1922',
            backgroundColor: 'rgba(191,25,34,0.08)',
            borderWidth: 3,
            tension: 0.3,
            fill: true
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

// Toastr
const _toast = <?= json_encode($_SESSION['toast'] ?? null) ?>;
<?php unset($_SESSION['toast']); ?>
if (_toast) {
    toastr.options = { positionClass: 'toast-top-right', timeOut: 4000 };
    toastr[_toast.type](_toast.message);
}
</script>
</body>
</html>
