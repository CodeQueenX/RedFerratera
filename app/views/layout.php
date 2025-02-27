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
    
    <!-- Página de estilos -->
    <link rel="stylesheet" href="/RedFerratera/public/css/style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/RedFerratera/public/css/bootstrap.min.css">

    <!-- Iconos Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>

    <!-- Google Maps -->
    <script>
        if (!window.mapaCargado) {
            document.addEventListener("DOMContentLoaded", function () {
                var script = document.createElement('script');
                script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAQI4xaz6p1EWwRV5GxoDthHt8YxELrO88";
                script.async = true;
                script.onload = function () {
                    if (typeof initMap === "function") {
                        initMap();
                    }
                };
                document.body.appendChild(script);
            });
        }
    </script>
</head>
<body>
    <!-- Barra de navegación (menú superior) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/RedFerratera/">Red Ferratera</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/ferratas">Ferratas</a></li>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/nuevas-ferratas">Nuevas Ferratas</a></li>
                    <li class="nav-item"><a class="nav-link" href="/RedFerratera/reportes">Reportes</a></li>

                    <?php if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])): ?>  
                        <li class="nav-item"><a class="nav-link" href="/RedFerratera/agregar-ferrata">Añadir Ferrata</a></li>
                        <li class="nav-item"><a class="nav-link" href="/RedFerratera/agregar-reporte">Añadir Reporte</a></li>

                        <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
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
        </div>
    </nav>

    <!-- Contenido dinámico -->
    <div class="container mt-4">
        <?php include($contenido); ?>
    </div>

    <!-- Pie de página -->
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p>&copy; <?php echo date("Y"); ?> Red Ferratera - Información y comunidad sobre vías ferratas en España</p>
        <div>
            <a href="https://www.instagram.com/mai_elda" target="_blank" class="text-white me-3"><i class="lucide lucide-instagram"></i> Instagram</a>
            <a href="https://www.facebook.com/maielda" target="_blank" class="text-white me-3"><i class="lucide lucide-facebook"></i> Facebook</a>
            <a href="https://www.tiktok.com/@mai_elda" target="_blank" class="text-white"><i class="lucide lucide-play-circle"></i> TikTok</a>
        </div>
    </footer>

    <script src="/RedFerratera/public/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS -->
</body>
</html>

