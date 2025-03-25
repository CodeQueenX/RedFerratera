<!-- Título -->
<h1 class="text-center mt-4 mb-4">Listado de Vías Ferratas</h1>

<!-- Formulario de búsqueda -->
<form method="GET" action="index.php" class="mb-4 bg-white p-4 shadow-sm rounded">
    <input type="hidden" name="accion" value="buscar_ferratas">
    
    <div class="row g-2">
        <!-- Campo ubicación -->
        <div class="col-md-6">
            <label for="ubicacion" class="form-label">Ubicación:</label>
            <input type="text" id="ubicacion" name="ubicacion" placeholder="Ejemplo: Madrid, Valencia..." class="form-control">
        </div>

        <!-- Campo dificultad -->
        <div class="col-md-4">
            <label for="dificultad" class="form-label">Dificultad:</label>
            <select id="dificultad" name="dificultad" class="form-control">
                <option value="">Todas</option>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <option value="K<?= $i ?>">K<?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="row g-2">
        <!-- Campo comunidad -->
        <div class="col-md-4">
            <label for="comunidad" class="form-label">Comunidad Autónoma:</label>
            <select id="comunidad" name="comunidad" class="form-control">
                <option value="">Todas</option>
                <?php
                $comunidades = [
                    "Andalucía", "Aragón", "Asturias", "Baleares", "Canarias", "Cantabria",
                    "Castilla-La Mancha", "Castilla y León", "Cataluña", "Extremadura", "Galicia",
                    "Madrid", "Murcia", "Navarra", "País Vasco", "La Rioja", "Valencia"
                ];
                foreach ($comunidades as $comunidad):
                ?>
                    <option value="<?= $comunidad ?>"><?= $comunidad ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Campo provincia -->
        <div class="col-md-3">
            <label for="provincia" class="form-label">Provincia:</label>
            <input type="text" id="provincia" name="provincia" placeholder="Ejemplo: Alicante, Málaga..." class="form-control">
        </div>

        <!-- Campo estado -->
        <div class="col-md-3">
            <label for="estado" class="form-label">Estado:</label>
            <select id="estado" name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="Abierta">Abierta</option>
                <option value="Cerrada">Cerrada</option>
                <option value="No operativa">No operativa</option>
            </select>
        </div>

        <!-- Botón buscar -->
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </div>
</form>

<!-- Listado de ferratas organizadas por comunidades -->
<div class="accordion" id="accordionComunidades">
    <?php foreach ($ferratasOrganizadas as $index => $provincias): ?>
        <?php 
            $comunidad = htmlspecialchars($index);
            $comId = 'comunidad-' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower($comunidad)); 
        ?>
        
        <!-- Bloque de comunidad -->
        <div class="accordion-item mb-3 shadow-sm border-0">
            <h2 class="accordion-header" id="heading-<?= $comId ?>">
                <button class="accordion-button collapsed custom-accordion" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $comId ?>" aria-expanded="false" aria-controls="collapse-<?= $comId ?>">
                    <?= $comunidad ?>
                </button>
            </h2>

            <!-- Provincias dentro de la comunidad -->
            <div id="collapse-<?= $comId ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $comId ?>" data-bs-parent="#accordionComunidades">
                <div class="accordion-body">
                    <?php foreach ($provincias as $provincia => $listaFerratas): ?>
                        <div class="bloque-provincia mb-3">
                            <h4 class="m-0"><?= htmlspecialchars($provincia); ?></h4>
                        </div>

                        <!-- Tabla de ferratas por provincia -->
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mt-2">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Dificultad</th>
                                        <th>Estado</th>
                                        <th>Valoración</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listaFerratas as $ferrata): ?>
                                        <tr>
                                            <td class="ferrata-nombre" data-label="Nombre">
                                                <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>">
                                                    <?= htmlspecialchars($ferrata['nombre']); ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($ferrata['ubicacion']); ?></td>
                                            <td><?= htmlspecialchars($ferrata['dificultad']); ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= ($ferrata['estado'] === 'Abierta') ? 'bg-success' : 
                                                        (($ferrata['estado'] === 'Cerrada') ? 'bg-warning text-dark' : 
                                                            (($ferrata['estado'] === 'No operativa') ? 'bg-danger' : 
                                                                (($ferrata['estado'] === 'Precaución') ? 'bg-warning text-dark' : 'bg-secondary'))) ?>">
                                                    <?= htmlspecialchars($ferrata['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                    require_once 'app/models/Valoracion.php';
                                                    $valoracionModel = new Valoracion();
                                                    $ratingData = $valoracionModel->getAverageRating($ferrata['id']);
                                                    $promedio = ($ratingData && $ratingData['total'] > 0) ? round($ratingData['promedio'], 2) : 'Sin valoraciones';
                                                    echo $promedio . " / 5";
                                                ?>
                                            </td>
                                            <td>
                                                <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= rawurlencode($ferrata['nombre']); ?>" class="btn btn-outline-primary btn-sm">Ver detalles</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
