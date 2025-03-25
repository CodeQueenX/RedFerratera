document.addEventListener("DOMContentLoaded", function () {
    // Inicializar iconos Lucide
    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }

    // Modal de imágenes
    const modal = document.getElementById("modalImagen");
    const modalImg = document.getElementById("imagenAmpliada");
    const cerrarModal = document.querySelector(".cerrar");

    function abrirModalImagen(e) {
        if (modal && modalImg) {
            modal.style.display = "flex";
            modalImg.src = e.target.src;
        }
    }

    function cerrarModalImagen() {
        if (modal) modal.style.display = "none";
    }

    document.querySelectorAll(".galeria-detalle img, .editar-ferrata img").forEach(img => {
        img.addEventListener("click", abrirModalImagen);
    });

    if (cerrarModal) cerrarModal.addEventListener("click", cerrarModalImagen);
    if (modal) {
        modal.addEventListener("click", e => {
            if (e.target === modal) cerrarModalImagen();
        });
    }

    // Google Maps
    if (!window.mapaCargado && document.getElementById("map")) {
        const scriptMaps = document.createElement("script");
        scriptMaps.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAQI4xaz6p1EWwRV5GxoDthHt8YxELrO88&callback=initMap";
        scriptMaps.async = true;
        document.body.appendChild(scriptMaps);
    }

    window.initMap = function () {
        const mapElement = document.getElementById("map");
        if (!mapElement) return;

        const [lat, lng] = mapElement.getAttribute("data-coordenadas").split(",").map(Number);
        if (isNaN(lat) || isNaN(lng)) {
            console.error("Coordenadas inválidas:", lat, lng);
            return;
        }

        const map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: { lat, lng }
        });

        const marker = new google.maps.Marker({
            position: { lat, lng },
            map,
            title: "Ubicación de la ferrata"
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `<a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank">Cómo llegar</a>`
        });

        marker.addListener("click", () => infoWindow.open(map, marker));
    };

    // Modal edición de comentarios
    const modalEditar = document.getElementById("modalEditarComentario");
    const inputComentarioId = document.getElementById("comentario_id");
    const inputComentarioTexto = document.getElementById("comentario_texto");
    const cerrarModalBtn = modalEditar?.querySelector(".cerrar");

    function abrirModalEdicion(id, texto) {
        if (modalEditar && inputComentarioId && inputComentarioTexto) {
            modalEditar.style.display = "flex";
            inputComentarioId.value = id;
            inputComentarioTexto.value = texto;
        }
    }

    function cerrarModalEdicion() {
        if (modalEditar) modalEditar.style.display = "none";
    }

    if (cerrarModalBtn) cerrarModalBtn.addEventListener("click", cerrarModalEdicion);
    if (modalEditar) {
        modalEditar.addEventListener("click", e => {
            if (e.target === modalEditar) cerrarModalEdicion();
        });
    }

    window.abrirModalEdicion = abrirModalEdicion;
    window.cerrarModalEdicion = cerrarModalEdicion;

    // Cookies
    const cookieBanner = document.getElementById("cookie-banner");
    const acceptCookies = document.getElementById("accept-cookies");

    if (cookieBanner) {
        if (!localStorage.getItem("cookies_aceptadas")) {
            cookieBanner.classList.remove("oculto");
        }
    }

    if (acceptCookies) {
        acceptCookies.addEventListener("click", () => {
            localStorage.setItem("cookies_aceptadas", "true");
            cookieBanner?.classList.add("oculto");
        });
    }

    // Flatpickr fechas
    flatpickr("input[type='date'], #fecha_inicio_cierre, #fecha_fin_cierre", {
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "Y-m-d",
        locale: "es"
    });

    // Valoraciones
    const ratingDiv = document.getElementById("starRating");
    if (ratingDiv) {
        let selected = 0;
        const max = 5;

        for (let i = 1; i <= max; i++) {
            const star = document.createElement("span");
            star.classList.add("star");
            star.dataset.value = i;
            star.innerHTML = "★";
            Object.assign(star.style, {
                cursor: "pointer",
                fontSize: "2rem",
                color: "#ccc"
            });

            star.addEventListener("mouseover", () => pintarEstrellas(i));
            star.addEventListener("mouseout", () => pintarEstrellas(selected));
            star.addEventListener("click", () => {
                selected = i;
                pintarEstrellas(selected);
                enviarValoracion(selected);
            });

            ratingDiv.appendChild(star);
        }

        function pintarEstrellas(valor) {
            ratingDiv.querySelectorAll(".star").forEach(star => {
                star.style.color = (parseInt(star.dataset.value) <= valor) ? "#ff0" : "#ccc";
            });
        }

        function enviarValoracion(valor) {
            const ferrataId = ratingDiv.getAttribute("data-ferrata-id");
            const formData = new FormData();
            formData.append("ferrata_id", ferrataId);
            formData.append("valor", valor);

            fetch("/RedFerratera/index.php?accion=guardar_valoracion", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Valoración guardada. Nueva media: " + data.promedio);
                    const promedioSpan = document.getElementById("averageRating");
                    if (promedioSpan) promedioSpan.textContent = data.promedio;
                } else {
                    alert("Error: " + (data.error || "Error desconocido"));
                }
            })
            .catch(error => console.error("Error al enviar valoración:", error));
        }
    }

    // Búsqueda menú
    const toggleBtn = document.getElementById("toggleSearch");
    const searchBox = document.getElementById("searchContainer");

    if (toggleBtn && searchBox) {
        toggleBtn.addEventListener("click", () => {
            const visible = searchBox.style.display === "block";
            searchBox.style.display = visible ? "none" : "block";
            if (!visible) {
                const input = searchBox.querySelector('input[name="buscar"]');
                input?.focus();
            }
        });
    }
});
