<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Projeto PAP sobre MedIA - Inteligência Artificial aplicada à Medicina">
    <meta name="author" content="Davi Bakó">
    <title>MedIA | Inteligência Artificial na Medicina</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body { font-family: 'Open Sans', sans-serif; }
        h1, h2, h5 { font-family: 'Montserrat', sans-serif; }
        .navbar { padding: 12px 0; }
        .navbar-brand strong { display: block; font-size: 0.75rem; color: #dc3545; }
        #hero-carousel { margin-top: 70px; }
        #medCarousel .carousel-item { height: 90vh; min-height: 400px; }
        #medCarousel .carousel-item img { object-fit: cover; height: 100%; width: 100%; filter: brightness(0.55); }
        .carousel-caption h5 { font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 10px rgba(0,0,0,0.6); }
        .carousel-caption p  { font-size: 1.2rem; text-shadow: 1px 1px 8px rgba(0,0,0,0.5); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important; }
        .tech-img { width: 64px; height: 64px; object-fit: contain; margin-bottom: 10px; }
        .featured-circle { width: 280px; height: 280px; border-radius: 50%; margin: 0 auto; }
        .featured-text { text-align: center; }
        .featured-number { font-family: 'Montserrat', sans-serif; font-size: 3rem; font-weight: 700; color: #dc3545; display: block; }
        section { padding: 80px 0; }
        #about { background: #f8f9fa; }
        #reviews { background: #f8f9fa; }
        #tecnologias { background: #f8f9fa; }
        footer { background: #222; color: #fff; padding: 50px 0; }
        #translate-buttons button { transition: all 0.3s ease; }
        #translate-buttons button:hover { opacity: 0.8; transform: scale(1.1); }
    </style>
</head>

<body id="top">

<div id="translate-buttons" style="position:fixed; top:10px; right:10px; z-index:9999;">
    <button onclick="setLang('pt')" style="margin-right:5px; padding:5px 12px; border-radius:5px; border:none; background:#890505; color:white; cursor:pointer; font-weight:bold;">🇵🇹 PT</button>
    <button onclick="setLang('en')" style="padding:5px 12px; border-radius:5px; border:none; background:#3B3B6D; color:white; cursor:pointer; font-weight:bold;">🇺🇸 EN</button>
</div>

<main>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm" data-aos="fade-down" data-aos-duration="1000">
    <div class="container">
        <a class="navbar-brand mx-auto d-lg-none" href="/">MedIA<strong>PAP</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link fw-semibold" href="#hero-carousel" data-i18n="nav_inicio">Início</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#about"         data-i18n="nav_projeto">Projeto</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#timeline"      data-i18n="nav_evolucao">Evolução</a></li>
                <li class="nav-item d-none d-lg-block mx-3">
                    <a class="navbar-brand" href="/">MedIA<strong data-i18n="nav_brand">Inteligência Artificial</strong></a>
                </li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#reviews"     data-i18n="nav_impacto">Impacto</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#criador"     data-i18n="nav_criador">Criador</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#tecnologias" data-i18n="nav_tec">Tecnologias</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#contact"     data-i18n="nav_contacto">Contacto</a></li>

                <?php if (isset($_SESSION['token'])): ?>
                    <?php if ($_SESSION['token']['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link text-danger fw-bold" href="/admin">
                                <i class="bi bi-shield-lock"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['token']['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="/login">
                            <i class="bi bi-person"></i> Entrar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold" href="/signup">
                            <i class="bi bi-person-plus"></i> Registar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- CARROSSEL -->
<section id="hero-carousel">
    <div id="medCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#medCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#medCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#medCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1400&q=80" class="d-block w-100" alt="">
                <div class="carousel-caption d-none d-md-block">
                    <h5 data-i18n="slide1_title">Diagnóstico Inteligente</h5>
                    <p data-i18n="slide1_text">Aplicando Inteligência Artificial à Medicina.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400&q=80" class="d-block w-100" alt="">
                <div class="carousel-caption d-none d-md-block">
                    <h5 data-i18n="slide2_title">Inovação Tecnológica</h5>
                    <p data-i18n="slide2_text">Soluções avançadas para hospitais e clínicas.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1551190822-a9333d879b1f?w=1400&q=80" class="d-block w-100" alt="">
                <div class="carousel-caption d-none d-md-block">
                    <h5 data-i18n="slide3_title">Eficiência Clínica</h5>
                    <p data-i18n="slide3_text">Redução de erros e otimização de processos.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#medCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#medCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 col-12" data-aos="fade-right">
                <h2 class="mb-3" data-i18n="about_title">O que é a MedIA?</h2>
                <p class="lead" data-i18n="about_lead">A MedIA é um chatbot médico inteligente desenvolvido para apoiar a triagem inicial de sintomas através de automação e Inteligência Artificial.</p>
                <p data-i18n="about_text">O sistema integra n8n, base de dados estruturada e painel administrativo para monitorização do desempenho.</p>
                <div class="d-flex gap-3 mt-4">
                    <div class="text-center"><h3 class="text-danger fw-bold">n8n</h3><small class="text-muted" data-i18n="about_n8n">Automação</small></div>
                    <div class="text-center"><h3 class="text-danger fw-bold">IA</h3><small class="text-muted" data-i18n="about_ia">Inteligência</small></div>
                    <div class="text-center"><h3 class="text-danger fw-bold">Android</h3><small class="text-muted" data-i18n="about_android">Plataforma</small></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-12 mx-auto mt-4 mt-md-0" data-aos="fade-left">
                <div class="featured-circle bg-white shadow-lg d-flex justify-content-center align-items-center card-hover">
                    <p class="featured-text mb-0">
                        <span class="featured-number">IA</span>
                        <span data-i18n="about_circle">na Saúde</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TIMELINE -->
<section id="timeline" style="background:#fff;">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up" data-i18n="timeline_title">Evolução da MedIA</h2>
        <div class="row text-center g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-hover p-4 bg-light rounded shadow h-100">
                    <h5 data-i18n="tl1_title">Planeamento</h5>
                    <p class="text-muted" data-i18n="tl1_text">Definição da arquitetura e modelo ER da base de dados.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-hover p-4 bg-light rounded shadow h-100">
                    <h5 data-i18n="tl2_title">Desenvolvimento</h5>
                    <p class="text-muted" data-i18n="tl2_text">Integração com n8n e criação do chatbot inteligente.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-hover p-4 bg-light rounded shadow h-100">
                    <h5 data-i18n="tl3_title">Implementação</h5>
                    <p class="text-muted" data-i18n="tl3_text">Criação do painel administrativo e app Android.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- IMPACTO -->
<section id="reviews">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up" data-i18n="impact_title">Impacto da MedIA</h2>
        <div class="row text-center g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-hover p-4 bg-white rounded shadow h-100">
                    <h5 data-i18n="imp1_title">Precisão</h5>
                    <p class="text-muted" data-i18n="imp1_text">Redução de erros médicos através de IA treinada.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-hover p-4 bg-white rounded shadow h-100">
                    <h5 data-i18n="imp2_title">Eficiência</h5>
                    <p class="text-muted" data-i18n="imp2_text">Processos automatizados e resposta em tempo real.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-hover p-4 bg-white rounded shadow h-100">
                    <h5 data-i18n="imp3_title">Inovação</h5>
                    <p class="text-muted" data-i18n="imp3_text">Nova abordagem tecnológica na área da saúde.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Formulário de Cliente</h2>

                <form id="clienteForm">
                    <input type="hidden" id="clienteId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" id="nome" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nif" class="form-label">NIF</label>
                            <input type="text" id="nif" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" id="telefone" class="form-control">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="morada" class="form-label">Morada</label>
                            <input type="text" id="morada" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Guardar</button>
                        <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                    </div>
                </form>

                <div id="mensagem" class="mt-3"></div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Lista de Clientes</h2>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>NIF</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Morada</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaClientes">
                            <tr>
                                <td colspan="7" class="text-center">A carregar clientes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
<!-- CRIADOR -->
<section id="criador" style="background:#fff;">
    <div class="container">
        <h2 class="text-center mb-2" data-aos="fade-up" data-i18n="criador_title">Criador do Projeto</h2>
        <p class="text-center text-muted mb-5" data-aos="fade-up" data-i18n="criador_sub">Desenvolvido no âmbito da Prova de Aptidão Profissional</p>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 col-12 text-center" data-aos="fade-up">
                <div class="card-hover p-4 rounded shadow-lg border-0">
                    <img src="/davi.jpeg"
                         onerror="this.src='https://ui-avatars.com/api/?name=Davi+Bakó+Clemente&size=180&background=dc3545&color=fff&bold=true&font-size=0.6'"
                         alt="Davi Bakó Clemente"
                         class="rounded-circle mb-4 border border-4 border-danger shadow-lg"
                         style="width:180px; height:180px; object-fit:cover;">
                    <h4 class="fw-bold mb-1" style="font-family:'Montserrat',sans-serif;">Davi Bakó Clemente</h4>
                    <p class="text-danger fw-semibold mb-1" data-i18n="criador_curso">Técnico de Gestão e Programação<br>de Sistemas Informáticos</p>
                    <p class="text-muted mb-3">12TGPSI  •  2025/2026</p>
                    <span class="badge bg-danger px-3 py-2 mb-4" style="font-size:0.85rem;" data-i18n="criador_badge">Prova de Aptidão Profissional</span>
                    <hr class="my-3">
                    <div class="d-flex justify-content-center gap-4 text-muted" style="font-size:0.9rem;">
                        <div><i class="bi bi-mortarboard-fill text-danger"></i><span class="ms-1" data-i18n="criador_escola">Escola</span></div>
                        <div><i class="bi bi-laptop-fill text-danger"></i><span class="ms-1" data-i18n="criador_tec">Tecnologia</span></div>
                        <div><i class="bi bi-robot text-danger"></i><span class="ms-1" data-i18n="criador_ia">IA</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TECNOLOGIAS -->
<section id="tecnologias">
    <div class="container">
        <h2 class="text-center mb-2" data-aos="fade-up" data-i18n="tec_title">Tecnologias Utilizadas</h2>
        <p class="text-center text-muted mb-5" data-aos="fade-up" data-i18n="tec_sub">Linguagens e software usados no desenvolvimento da MedIA</p>

        <h5 class="fw-bold mb-4 text-danger" data-aos="fade-right">
            <i class="bi bi-code-slash me-2"></i><span data-i18n="tec_lang">Linguagens de Programação</span>
        </h5>
        <div class="row g-3 mb-5">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="tech-img">
                    <h6 class="fw-bold mb-1">Java</h6>
                    <small class="text-muted" data-i18n="lang_java">App Android</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="150">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML" class="tech-img">
                    <h6 class="fw-bold mb-1">HTML</h6>
                    <small class="text-muted" data-i18n="lang_html">Estrutura Web</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS" class="tech-img">
                    <h6 class="fw-bold mb-1">CSS</h6>
                    <small class="text-muted" data-i18n="lang_css">Estilo Web</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="250">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript" class="tech-img">
                    <h6 class="fw-bold mb-1">JavaScript</h6>
                    <small class="text-muted" data-i18n="lang_js">Interatividade</small>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 mb-5">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" class="tech-img">
                    <h6 class="fw-bold mb-1">PHP</h6>
                    <small class="text-muted">Back-end</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="350">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="SQL" class="tech-img">
                    <h6 class="fw-bold mb-1">SQL</h6>
                    <small class="text-muted" data-i18n="lang_sql">Base de dados</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/json/json-original.svg" alt="JSON" class="tech-img">
                    <h6 class="fw-bold mb-1">JSON</h6>
                    <small class="text-muted" data-i18n="lang_json">API / Dados</small>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="fw-bold mb-4 text-danger" data-aos="fade-right">
            <i class="bi bi-tools me-2"></i><span data-i18n="tec_soft">Software Utilizado</span>
        </h5>
        <div class="row g-3">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/androidstudio/androidstudio-original.svg" alt="Android Studio" class="tech-img">
                    <h6 class="fw-bold mb-1">Android Studio</h6>
                    <small class="text-muted" data-i18n="soft_android">App Móvel</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="150">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg" alt="VS Code" class="tech-img">
                    <h6 class="fw-bold mb-1">VS Code</h6>
                    <small class="text-muted" data-i18n="soft_vscode">Editor Web</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg" alt="GitHub" class="tech-img">
                    <h6 class="fw-bold mb-1">GitHub</h6>
                    <small class="text-muted" data-i18n="soft_github">Controlo Versões</small>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="250">
                <div class="card-hover text-center p-3 bg-white rounded shadow-sm h-100">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="Docker" class="tech-img">
                    <h6 class="fw-bold mb-1">Docker</h6>
                    <small class="text-muted">Containerização</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer id="contact">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12" data-aos="fade-right">
                <h5 class="text-white" data-i18n="footer_title">Projeto PAP — MedIA</h5>
                <p class="text-white-50">Davi Bakó Clemente  •  12TGPSI  •  2025/2026</p>
                <p class="text-white-50" data-i18n="footer_dir">Diretora de Curso: Maria Luísa Parente</p>
            </div>
            <div class="col-lg-6 col-12 text-lg-end mt-3 mt-lg-0" data-aos="fade-left">
                <p class="text-white-50 mb-0" data-i18n="footer_copy">© 2025 MedIA | Projeto de Aptidão Profissional</p>
                <p class="text-white-50" data-i18n="footer_curso">Curso Profissional – Área Tecnológica</p>
            </div>
        </div>
    </div>
</footer>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ once: true, duration: 1000 });

// Toast via PHP session
const _toast = <?= json_encode($_SESSION['toast'] ?? null) ?>;
<?php unset($_SESSION['toast']); ?>
if (_toast) {
    toastr.options = { positionClass: 'toast-top-right', timeOut: 4000 };
    toastr[_toast.type](_toast.message);
}

// i18n
const traducoes = {
    pt: {
        nav_inicio:"Início",nav_projeto:"Projeto",nav_evolucao:"Evolução",nav_brand:"Inteligência Artificial",nav_impacto:"Impacto",nav_criador:"Criador",nav_tec:"Tecnologias",nav_contacto:"Contacto",
        slide1_title:"Diagnóstico Inteligente",slide1_text:"Aplicando Inteligência Artificial à Medicina.",slide2_title:"Inovação Tecnológica",slide2_text:"Soluções avançadas para hospitais e clínicas.",slide3_title:"Eficiência Clínica",slide3_text:"Redução de erros e otimização de processos.",
        about_title:"O que é a MedIA?",about_lead:"A MedIA é um chatbot médico inteligente desenvolvido para apoiar a triagem inicial de sintomas através de automação e Inteligência Artificial.",about_text:"O sistema integra n8n, base de dados estruturada e painel administrativo para monitorização do desempenho.",about_n8n:"Automação",about_ia:"Inteligência",about_android:"Plataforma",about_circle:"na Saúde",
        timeline_title:"Evolução da MedIA",tl1_title:"Planeamento",tl1_text:"Definição da arquitetura e modelo ER da base de dados.",tl2_title:"Desenvolvimento",tl2_text:"Integração com n8n e criação do chatbot inteligente.",tl3_title:"Implementação",tl3_text:"Criação do painel administrativo e app Android.",
        impact_title:"Impacto da MedIA",imp1_title:"Precisão",imp1_text:"Redução de erros médicos através de IA treinada.",imp2_title:"Eficiência",imp2_text:"Processos automatizados e resposta em tempo real.",imp3_title:"Inovação",imp3_text:"Nova abordagem tecnológica na área da saúde.",
        criador_title:"Criador do Projeto",criador_sub:"Desenvolvido no âmbito da Prova de Aptidão Profissional",criador_curso:"Técnico de Gestão e Programação<br>de Sistemas Informáticos",criador_badge:"Prova de Aptidão Profissional",criador_escola:"Escola",criador_tec:"Tecnologia",criador_ia:"IA",
        tec_title:"Tecnologias Utilizadas",tec_sub:"Linguagens e software usados no desenvolvimento da MedIA",tec_lang:"Linguagens de Programação",tec_soft:"Software Utilizado",lang_java:"App Android",lang_html:"Estrutura Web",lang_css:"Estilo Web",lang_js:"Interatividade",lang_sql:"Base de Dados",lang_json:"API / Dados",soft_android:"App Móvel",soft_n8n:"Automação IA",soft_vscode:"Editor Web",soft_github:"Controlo Versões",
        footer_title:"Projeto PAP — MedIA",footer_dir:"Diretora de Curso: Maria Luísa Parente",footer_copy:"© 2025 MedIA | Projeto de Aptidão Profissional",footer_curso:"Curso Profissional – Área Tecnológica",
    },
    en: {
        nav_inicio:"Home",nav_projeto:"Project",nav_evolucao:"Evolution",nav_brand:"Artificial Intelligence",nav_impacto:"Impact",nav_criador:"Creator",nav_tec:"Technologies",nav_contacto:"Contact",
        slide1_title:"Intelligent Diagnosis",slide1_text:"Applying Artificial Intelligence to Medicine.",slide2_title:"Technological Innovation",slide2_text:"Advanced solutions for hospitals and clinics.",slide3_title:"Clinical Efficiency",slide3_text:"Reducing errors and optimizing processes.",
        about_title:"What is MedIA?",about_lead:"MedIA is an intelligent medical chatbot developed to support the initial triage of symptoms through automation and Artificial Intelligence.",about_text:"The system integrates n8n, a structured database, and an administrative panel for performance monitoring.",about_n8n:"Automation",about_ia:"Intelligence",about_android:"Platform",about_circle:"in Healthcare",
        timeline_title:"MedIA Evolution",tl1_title:"Planning",tl1_text:"Definition of the architecture and ER model of the database.",tl2_title:"Development",tl2_text:"Integration with n8n and creation of the intelligent chatbot.",tl3_title:"Implementation",tl3_text:"Creation of the admin panel and Android app.",
        impact_title:"MedIA Impact",imp1_title:"Accuracy",imp1_text:"Reduction of medical errors through trained AI.",imp2_title:"Efficiency",imp2_text:"Automated processes and real-time response.",imp3_title:"Innovation",imp3_text:"New technological approach in the healthcare sector.",
        criador_title:"Project Creator",criador_sub:"Developed as part of the Professional Aptitude Test",criador_curso:"Technician in IT Systems Management<br>and Programming",criador_badge:"Professional Aptitude Test",criador_escola:"School",criador_tec:"Technology",criador_ia:"AI",
        tec_title:"Technologies Used",tec_sub:"Languages and software used in the development of MedIA",tec_lang:"Programming Languages",tec_soft:"Software Used",lang_java:"Android App",lang_html:"Web Structure",lang_css:"Web Styling",lang_js:"Interactivity",lang_sql:"Database",lang_json:"API / Data",soft_android:"Mobile App",soft_n8n:"AI Automation",soft_vscode:"Web Editor",soft_github:"Version Control",
        footer_title:"PAP Project — MedIA",footer_dir:"Course Director: Maria Luísa Parente",footer_copy:"© 2025 MedIA | Professional Aptitude Project",footer_curso:"Professional Course – Technology Area",
    }
};

function setLang(lang) {
    const t = traducoes[lang];
    document.querySelectorAll('[data-i18n]').forEach(function(el) {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) el.innerHTML = t[key];
    });
    document.documentElement.lang = lang;
}
setLang('pt');
</script>

</body>
</html>
