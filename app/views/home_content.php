<div class="inicio-container">
    <!-- Fondo con efecto overlay -->
    <div class="inicio-background">
        <div class="overlay"></div>
    </div>

    <!-- Contenido de bienvenida -->
    <div class="inicio-content">
        <h1 class="display-4 fw-bold text-center">Bienvenido a Red Ferratera</h1>
        <p class="lead text-center">
            Descubre, explora y comparte información sobre las vías ferratas en España. 
            Un deporte que combina aventura, adrenalina y naturaleza.
        </p>
        <div class="text-center">
            <a href="/RedFerratera/ferratas" class="btn btn-lg btn-primary"><i class="lucide lucide-map"></i> Explorar Ferratas</a>
        </div>
    </div>
</div>

<!-- Sección de información sobre las ferratas -->
<section class="container mt-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">¿Qué son las vías ferratas?</h2>
            <p>
                Las vías ferratas son recorridos de montaña equipados con elementos como cables, escalones metálicos y puentes colgantes. 
                Permiten a personas sin experiencia en escalada acceder a zonas de difícil acceso de manera controlada y segura.
            </p>
            <p>
                La práctica de vías ferratas es una forma emocionante de combinar senderismo y escalada, brindando vistas panorámicas espectaculares mientras 
                se atraviesan pasos verticales y horizontales con la ayuda de equipamiento especializado.
            </p>
            <h4 class="mt-4 text-success">🔍 Características principales:</h4>
            <ul class="list-unstyled">
                <li>✅ <strong>Aventura segura:</strong> Se requiere casco, arnés y disipador.</li>
                <li>✅ <strong>Dificultades variadas:</strong> Se clasifican en niveles K1 a K7 según su exigencia técnica.</li>
                <li>✅ <strong>Entorno natural:</strong> Se encuentran en montañas, desfiladeros y cañones de todo el mundo.</li>
                <li>✅ <strong>Accesibilidad:</strong> Diseñadas para distintos niveles de experiencia, desde principiantes hasta expertos.</li>
                <li>✅ <strong>Elementos adicionales:</strong> Algunas incluyen puentes tibetanos, pasos de mono y tirolinas.</li>
            </ul>
        </div>
        <div class="col-md-6 text-center">
            <img src="public/img/ferrata_info.jpg" alt="Vía Ferrata" class="img-fluid rounded shadow-lg">
        </div>
    </div>
</section>


<!-- Sección de Recomendaciones -->
<section class="container mt-5">
    <h2 class="fw-bold text-dark text-center mb-4"><i class="lucide lucide-lightbulb"></i> Recomendaciones para Vías Ferratas</h2>
	<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold text-dark">✅ Equipamiento básico</h2>
                    <ul>
                        <li>🔗 Disipador de energía con mosquetones homologados.</li>
                        <li>⛑ Casco de escalada.</li>
                        <li>🧤 Guantes para proteger las manos.</li>
                        <li>🎒 Arnés de escalada.</li>
                        <li>🥾 Calzado adecuado (preferiblemente botas de montaña o zapatillas de aproximación).</li>
                    </ul>
            	</div>
            </div>
        </div>
    	<div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold text-dark">⚠️ Consejos de seguridad</h2>
                    <ul>
                        <li>🔄 Revisa siempre tu equipo antes de iniciar.</li>
                        <li>🚷 No te desconectes nunca del cable de vida.</li>
                        <li>🕐 Evita realizar la vía ferrata en condiciones meteorológicas adversas.</li>
                        <li>🆘 Conoce el nivel de dificultad antes de intentarlo y no subestimes los riesgos.</li>
                        <li>👥 Realiza la actividad acompañado si eres principiante.</li>
                    </ul>
                </div>
            </div>
        </div>
		<div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold text-dark">🎯 Clasificación de las vías ferratas</h2>
                    <ul>
                        <li>🟢 K1-K2: Fácil, ideal para principiantes.</li>
                        <li>🔵 K3-K4: Moderado, requiere cierta experiencia.</li>
                        <li>🔴 K5-K6: Difícil, recomendado para expertos.</li>
                        <li>⚫ K7: Extremadamente difícil, solo para escaladores avanzados.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de contacto -->
<section class="container mt-5 text-center">
    <h2 class="fw-bold text-dark">📬 Contacto</h2>
    <p>Si tienes dudas o quieres colaborar, escríbenos:</p>
    <a href="mailto:megidorico@gmail.com" class="btn btn-outline-primary"><i class="lucide lucide-mail"></i> Enviar correo</a>
</section>

<style>
/* Estilos para la página de inicio */
.inicio-container {
    position: relative;
    width: 100%;
    height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.inicio-background {
    background: url('public/img/ferrata_background.jpg') no-repeat center center/cover;
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Oscurece la imagen de fondo */
}

.inicio-content {
    position: relative;
    z-index: 1;
    padding: 20px;
}

.bg-light {
    background-color: rgba(255, 255, 255, 0.9) !important;
}
</style>
