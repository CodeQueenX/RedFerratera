<!DOCTYPE html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Red Ferratera</title>

    <!-- Metadatos SEO -->
    <meta name="description" content="Descubre y comparte información sobre vías ferratas en España. Encuentra ferratas organizadas por dificultad y ubicación.">
    <meta name="keywords" content="vías ferratas, escalada, aventura, senderismo, montaña, deportes extremos">
    <meta name="author" content="Red Ferratera">
    <meta property="og:title" content="Red Ferratera - Información sobre vías ferratas">
    <meta property="og:description" content="Descubre y comparte información sobre vías ferratas en España.">
    <meta property="og:image" content="/RedFerratera/public/img/ferrata_background.jpg">
    <meta property="og:url" content="http://localhost/RedFerratera">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/RedFerratera/public/img/favicon.png">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/RedFerratera/public/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/RedFerratera/public/css/style.css">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-T4DHKKM3QC"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-T4DHKKM3QC');
    </script>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container position-relative">
        <!-- Logo y búsqueda -->
        <div class="d-flex align-items-center">
          <a class="navbar-brand" href="/RedFerratera/">Red Ferratera</a>
          <button id="toggleSearch" class="btn ms-2" style="background: none; border: none;">
            <i data-lucide="search" style="font-size: 1.5rem; color: white;"></i>
          </button>
        </div>

        <!-- Botón hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú -->
        <div class="collapse navbar-collapse text-end" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="/RedFerratera/">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="/RedFerratera/ferratas">Ferratas</a></li>
            <li class="nav-item"><a class="nav-link" href="/RedFerratera/nuevas-ferratas">Nuevas Ferratas</a></li>
            <li class="nav-item"><a class="nav-link" href="/RedFerratera/reportes">Reportes</a></li>
            <?php if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])): ?>  
                <?php if ($_SESSION['usuario']['verificado'] == 1): ?>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/agregar-ferrata">Añadir Ferrata</a></li>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/agregar-reporte">Añadir Reporte</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['usuario']['rol'] === 'admin' || $_SESSION['usuario']['rol'] === 'moderador'): ?>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/gestionar-ferratas">Gestionar Ferratas</a></li>
                <?php endif; ?>
                <li class="nav-item"><span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></span></li>
                <li class="nav-item"><a class="nav-link text-danger" href="/RedFerratera/logout">Cerrar Sesión</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="/RedFerratera/login">Iniciar Sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="/RedFerratera/registrar">Registrarse</a></li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Buscador desplegable -->
        <div id="searchContainer" class="position-absolute" style="display: none; left: 0; top: 60px; z-index: 1000;">
          <form action="/RedFerratera/index.php" method="GET" class="d-flex" style="background: rgba(0,0,0,0.7); padding: 5px; border-radius: 5px;">
            <input type="hidden" name="accion" value="buscarGlobal">
            <input class="form-control me-2" type="search" name="buscar" placeholder="Buscar..." aria-label="Buscar">
            <button class="btn btn-outline-light" type="submit">
              <i data-lucide="search"></i>
            </button>
          </form>
        </div>
      </div>
    </nav>

    <!-- Contenido principal -->
    <div class="wrapper">
        <div class="container mt-4 content">
            <?php 
            if (isset($contenido) && file_exists($contenido)) {
                include $contenido;
            } else {
                echo "<p>Error: No se pudo cargar la página.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Pie de página -->
    <footer class="bg-dark text-white text-center py-4 mt-4">
        <div class="container">
            <div class="row">
                <!-- Contacto -->
                <div class="col-md-3">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><a href="/RedFerratera/contacto" class="text-white">Formulario de Contacto</a></li>
                        <li><a href="mailto:megidorico@gmail.com" class="text-white">Enviar Email</a></li>
                    </ul>
                </div>

                <!-- Información Legal -->
                <div class="col-md-3">
                    <h5>Información Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="/RedFerratera/aviso-legal" class="text-white">Aviso Legal</a></li>
                        <li><a href="/RedFerratera/politica-privacidad" class="text-white">Política de Privacidad</a></li>
                        <li><a href="/RedFerratera/politica-cookies" class="text-white">Política de Cookies</a></li>
                    </ul>
                </div>

                <!-- Recursos -->
                <div class="col-md-3">
                    <h5>Recursos</h5>
                    <ul class="list-unstyled">
                        <li><a href="/RedFerratera/faq" class="text-white">Preguntas Frecuentes</a></li>
                        <li><a href="/RedFerratera/sitemap" class="text-white">Mapa del Sitio</a></li>
                    </ul>
                </div>

                <!-- Redes Sociales -->
                <div class="col-md-3">
                    <h5>Síguenos</h5>
                    <p><i data-lucide="instagram"></i> <a href="https://www.instagram.com/mai_elda" target="_blank" class="text-white">Instagram</a></p>
                    <p><i data-lucide="facebook"></i> <a href="https://www.facebook.com/maielda" target="_blank" class="text-white">Facebook</a></p>
                    <p><i data-lucide="play-circle"></i> <a href="https://www.tiktok.com/@mai_elda" target="_blank" class="text-white">TikTok</a></p>
                </div>
            </div>
            <p class="mt-3">&copy; <?= date("Y"); ?> Red Ferratera - Información y comunidad sobre vías ferratas en España</p>
        </div>
    </footer>

    <!-- Banner de cookies -->
    <?php include __DIR__ . '/cookies_banner.php'; ?>

    <!-- Scripts -->
    <script src="/RedFerratera/public/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="/RedFerratera/public/js/scripts.js"></script>
</body>
</html>
