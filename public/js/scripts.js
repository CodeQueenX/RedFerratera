document.addEventListener("DOMContentLoaded", function () {
    // INICIALIZAR ICONOS LUCIDE
    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }

    // MODAL PARA AMPLIAR IMÁGENES
    let modal = document.getElementById("modalImagen");
    let modalImg = document.getElementById("imagenAmpliada");
    let cerrarModal = document.querySelector(".cerrar");

    function abrirModalImagen(event) {
        if (modal && modalImg) {
            modal.style.display = "flex";
            modalImg.src = event.target.src;
        }
    }

    function cerrarModalImagen() {
        if (modal) {
            modal.style.display = "none";
        }
    }

    // Aplicar eventos a las imágenes en "Detalles de Ferrata"
    document.querySelectorAll(".galeria-detalle img").forEach(img => {
        img.addEventListener("click", abrirModalImagen);
    });

    // Aplicar eventos a las imágenes en "Editar Ferrata"
    document.querySelectorAll(".editar-ferrata img").forEach(img => {
        img.addEventListener("click", abrirModalImagen);
    });

    // Cerrar el modal al hacer clic en la "X"
    if (cerrarModal) {
        cerrarModal.addEventListener("click", cerrarModalImagen);
    }

    // Cerrar el modal al hacer clic fuera de la imagen
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                cerrarModalImagen();
            }
        });
    }

    // CARGAR GOOGLE MAPS
    if (!window.mapaCargado && document.getElementById("map")) {
        let script = document.createElement("script");
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAQI4xaz6p1EWwRV5GxoDthHt8YxELrO88&callback=initMap";
        script.async = true;
        document.body.appendChild(script);
    }

    window.initMap = function () {
        let mapElement = document.getElementById("map");
        if (!mapElement) return;

        let coordenadas = mapElement.getAttribute("data-coordenadas").split(",");
        if (coordenadas.length < 2 || isNaN(coordenadas[0]) || isNaN(coordenadas[1])) {
            console.error("Coordenadas inválidas:", coordenadas);
            return;
        }

        let latLng = { lat: parseFloat(coordenadas[0]), lng: parseFloat(coordenadas[1]) };

        let map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: latLng,
        });

        new google.maps.Marker({
            position: latLng,
            map: map,
            title: "Ubicación de la ferrata"
        });
    };

    // CARGAR CLIMA DESDE API OPEN-METEO
    let weatherContainer = document.getElementById("weather-container");
    
    if (weatherContainer) {
        let coordenadas = weatherContainer.getAttribute("data-coordenadas");

        if (coordenadas) {
            let [lat, lon] = coordenadas.split(",").map(coord => coord.trim());

            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&temperature_unit=celsius&wind_speed_unit=kmh&winddirection=true&weathercode=true`)
                .then(response => response.json())
                .then(data => {
                    let weather = data.current_weather;
                    let weatherCode = weather.weathercode;
                    let weatherText = obtenerDescripcionTiempo(weatherCode);
                    let weatherIcon = obtenerIconoTiempo(weatherCode);

                    let weatherHtml = `
                        <p><strong>🌡 Temperatura:</strong> ${weather.temperature}°C</p>
                        <p><strong>💨 Viento:</strong> ${weather.windspeed} km/h</p>
                        <p><strong>📍 Dirección del viento:</strong> ${weather.winddirection}°</p>
                        <p><strong>${weatherIcon} Estado:</strong> ${weatherText}</p>
                    `;
                    weatherContainer.innerHTML = weatherHtml;
                })
                .catch(error => {
                    weatherContainer.innerHTML = "<p>⚠️ No se pudo obtener el clima.</p>";
                    console.error("Error obteniendo el clima:", error);
                });
        }
    }

    function obtenerDescripcionTiempo(code) {
        const weatherDescriptions = {
            0: "Despejado 🌞", 1: "Mayormente despejado 🌤", 2: "Parcialmente nublado ⛅",
            3: "Nublado ☁️", 45: "Niebla 🌫", 48: "Niebla con escarcha ❄️🌫",
            51: "Llovizna ligera 🌦", 53: "Llovizna moderada 🌧", 55: "Llovizna intensa 🌧💦",
            56: "Llovizna helada ligera ❄️🌦", 57: "Llovizna helada intensa ❄️🌧",
            61: "Lluvia ligera 🌦", 63: "Lluvia moderada 🌧", 65: "Lluvia intensa 🌧💦",
            66: "Lluvia helada ligera ❄️🌦", 67: "Lluvia helada intensa ❄️🌧",
            71: "Nieve ligera 🌨", 73: "Nieve moderada ❄️🌨", 75: "Nieve intensa ❄️❄️",
            77: "Granizo 🌩❄️", 80: "Chubascos ligeros 🌦", 81: "Chubascos moderados 🌧",
            82: "Chubascos intensos ⛈", 85: "Chubascos de nieve ligeros 🌨",
            86: "Chubascos de nieve intensos ❄️🌨", 95: "Tormenta eléctrica ⛈",
            96: "Tormenta con granizo 🌩❄️", 99: "Tormenta severa con granizo ⛈❄️"
        };
        return weatherDescriptions[code] || "Desconocido 🤷‍♂️";
    }

    function obtenerIconoTiempo(code) {
        if (code >= 0 && code <= 3) return "☀️";
        if (code >= 45 && code <= 48) return "🌫";
        if (code >= 51 && code <= 57) return "🌦";
        if (code >= 61 && code <= 67) return "🌧";
        if (code >= 71 && code <= 77) return "❄️";
        if (code >= 80 && code <= 82) return "🌧";
        if (code >= 85 && code <= 86) return "🌨";
        if (code >= 95 && code <= 99) return "⛈";
        return "❓";
    }
});

	// MODAL PARA EDITAR COMENTARIOS
document.addEventListener("DOMContentLoaded", function () {
    let modalEditarComentario = document.getElementById("modalEditarComentario");
    let comentarioIdInput = document.getElementById("comentario_id");
    let comentarioTextoInput = document.getElementById("comentario_texto");
    let cerrarModalBtn = modalEditarComentario ? modalEditarComentario.querySelector(".cerrar") : null;

    function abrirModalEdicion(id, texto) {
        if (modalEditarComentario && comentarioIdInput && comentarioTextoInput) {
            modalEditarComentario.style.display = "flex";
            comentarioIdInput.value = id;
            comentarioTextoInput.value = texto;
        }
    }

    function cerrarModalEdicion() {
        if (modalEditarComentario) {
            modalEditarComentario.style.display = "none";
        }
    }

    // Cerrar modal al hacer clic en la "X"
    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener("click", cerrarModalEdicion);
    }

    // Cerrar modal al hacer clic fuera del contenido
    if (modalEditarComentario) {
        modalEditarComentario.addEventListener("click", function (e) {
            if (e.target === modalEditarComentario) {
                cerrarModalEdicion();
            }
        });
    }

    // Exponer funciones al ámbito global (para llamadas desde HTML)
    window.abrirModalEdicion = abrirModalEdicion;
    window.cerrarModalEdicion = cerrarModalEdicion;
});

// USO DE COOKIES

document.addEventListener("DOMContentLoaded", function () {
    console.log("Script de cookies ejecutado"); // <-- Para ver si el script se carga

    let banner = document.getElementById("cookie-banner");
    let aceptarBoton = document.getElementById("accept-cookies");

    if (!document.cookie.includes("cookies_aceptadas=true")) {
        console.log("No se han aceptado las cookies, mostrando banner.");
        banner.style.display = "block";
    } else {
        console.log("Las cookies ya están aceptadas, ocultando banner.");
        banner.style.display = "none";
    }

    aceptarBoton.addEventListener("click", function () {
        console.log("Clic en aceptar cookies");
        document.cookie = "cookies_aceptadas=true; path=/; max-age=31536000"; // Guarda la cookie por 1 año
        banner.style.display = "none";  // Oculta el banner inmediatamente
    });
});


