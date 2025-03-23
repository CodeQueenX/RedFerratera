document.addEventListener("DOMContentLoaded", function () {
    // Inicialización de iconos (Lucide)
    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }

    // Modal para ampliar imágenes
    const modal = document.getElementById("modalImagen");
    const modalImg = document.getElementById("imagenAmpliada");
    const cerrarModal = document.querySelector(".cerrar");

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

    document.querySelectorAll(".galeria-detalle img").forEach(img => {
        img.addEventListener("click", abrirModalImagen);
    });
    document.querySelectorAll(".editar-ferrata img").forEach(img => {
        img.addEventListener("click", abrirModalImagen);
    });
    if (cerrarModal) {
        cerrarModal.addEventListener("click", cerrarModalImagen);
    }
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                cerrarModalImagen();
            }
        });
    }

    // Cargar Google Maps si existe el contenedor "map"
    if (!window.mapaCargado && document.getElementById("map")) {
        let scriptMaps = document.createElement("script");
        scriptMaps.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAQI4xaz6p1EWwRV5GxoDthHt8YxELrO88&callback=initMap";
        scriptMaps.async = true;
        document.body.appendChild(scriptMaps);
    }

    window.initMap = function () {
        let mapElement = document.getElementById("map");
        if (!mapElement) return;
        let coords = mapElement.getAttribute("data-coordenadas").split(",");
        if (coords.length < 2 || isNaN(coords[0]) || isNaN(coords[1])) {
            console.error("Coordenadas inválidas:", coords);
            return;
        }
        let lat = parseFloat(coords[0]);
        let lng = parseFloat(coords[1]);
        let latLng = { lat: lat, lng: lng };

        let map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: latLng,
        });

        let marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: "Ubicación de la ferrata"
        });

        // InfoWindow con enlace "Cómo llegar"
        let infoWindow = new google.maps.InfoWindow({
            content: `<a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank">Cómo llegar</a>`
        });

        marker.addListener('click', function () {
            infoWindow.open(map, marker);
        });
    };

    // Modal para editar comentarios
    (function () {
        const modalEditarComentario = document.getElementById("modalEditarComentario");
        const comentarioIdInput = document.getElementById("comentario_id");
        const comentarioTextoInput = document.getElementById("comentario_texto");
        const cerrarModalBtn = modalEditarComentario ? modalEditarComentario.querySelector(".cerrar") : null;

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

        if (cerrarModalBtn) {
            cerrarModalBtn.addEventListener("click", cerrarModalEdicion);
        }
        if (modalEditarComentario) {
            modalEditarComentario.addEventListener("click", function (e) {
                if (e.target === modalEditarComentario) {
                    cerrarModalEdicion();
                }
            });
        }
        window.abrirModalEdicion = abrirModalEdicion;
        window.cerrarModalEdicion = cerrarModalEdicion;
    })();

    // Manejo del banner de cookies usando localStorage
	const banner = document.getElementById("cookie-banner");
	const acceptButton = document.getElementById("accept-cookies");

	if (banner) {
	    if (!localStorage.getItem("cookies_aceptadas")) {
	        banner.classList.remove("oculto");
	    } else {
	        banner.classList.add("oculto");
	    }
	}

	if (acceptButton) {
	    acceptButton.addEventListener("click", function () {
	        localStorage.setItem("cookies_aceptadas", "true");
	        if (banner) banner.classList.add("oculto");
	        console.log("Cookie aceptada:", localStorage.getItem("cookies_aceptadas"));
	    });
	}

    // Formato de fecha para inputs
	const fechaInputs = document.querySelectorAll('input[type="date"]');
	    fechaInputs.forEach(fechaInput => {
	        flatpickr(fechaInput, {
	            altInput: true,
	            altFormat: "d-m-Y",      // Se muestra en formato dd-mm-aaaa
	            dateFormat: "Y-m-d",       // Se envía en formato aaaa-mm-dd
	            locale: "es"
	        });
	    });
		
	// Configurar Flatpickr para el campo de fecha de inicio de cierre
	    flatpickr("#fecha_inicio_cierre", {
	        altInput: true,
	        altFormat: "d-m-Y",        // Se muestra en formato dd-mm-aaaa
	        dateFormat: "Y-m-d",         // Se envía en formato aaaa-mm-dd
	        locale: "es",
	    });

	    // Configurar Flatpickr para el campo de fecha de fin de cierre
	    flatpickr("#fecha_fin_cierre", {
	        altInput: true,
	        altFormat: "d-m-Y",         // Se muestra en formato dd-mm-aaaa
	        dateFormat: "Y-m-d",        // Se envía en formato aaaa-mm-dd
	        locale: "es",
	    });

    // Manejo AJAX para valoraciones (estrellas)
    const starRatingDiv = document.getElementById("starRating");
    if (starRatingDiv) {
        const maxStars = 5;
        let selectedRating = 0;
        
        for (let i = 1; i <= maxStars; i++) {
            const star = document.createElement("span");
            star.classList.add("star");
            star.dataset.value = i;
            star.innerHTML = "&#9733;";
            star.style.cursor = "pointer";
            star.style.fontSize = "2rem";
            star.style.color = "#ccc";
            
            star.addEventListener("mouseover", function () {
                highlightStars(i);
            });
            star.addEventListener("mouseout", function () {
                highlightStars(selectedRating);
            });
            star.addEventListener("click", function () {
                selectedRating = i;
                highlightStars(selectedRating);
                submitRating(selectedRating);
            });
            starRatingDiv.appendChild(star);
        }
        
        function highlightStars(rating) {
            const stars = starRatingDiv.querySelectorAll(".star");
            stars.forEach(star => {
                star.style.color = (parseInt(star.dataset.value) <= rating) ? "#ff0" : "#ccc";
            });
        }
        
        function submitRating(rating) {
            const ferrataId = starRatingDiv.getAttribute("data-ferrata-id");
            const formData = new FormData();
            formData.append("ferrata_id", ferrataId);
            formData.append("valor", rating);
            
            fetch('/RedFerratera/index.php?accion=guardar_valoracion', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Valoración guardada. Nueva media: " + data.promedio);
                    const averageRatingSpan = document.getElementById("averageRating");
                    if (averageRatingSpan) {
                        averageRatingSpan.textContent = data.promedio;
                    }
                } else if (data.error) {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error en la petición:", error));
        }
    }
	
	// Búsqueda del menú
	const toggleSearch = document.getElementById("toggleSearch");
	const searchContainer = document.getElementById("searchContainer");

	    if (toggleSearch && searchContainer) {
	        toggleSearch.addEventListener("click", function () {
	            // Alterna la visibilidad de la barra de búsqueda
	            if (searchContainer.style.display === "none" || searchContainer.style.display === "") {
	                searchContainer.style.display = "block";
	                // Poner el foco en el input
	                const searchInput = searchContainer.querySelector('input[name="buscar"]');
	                if (searchInput) {
	                    searchInput.focus();
	                }
	            } else {
	                searchContainer.style.display = "none";
	            }
	        });
	    }
});
