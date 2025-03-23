<h1 class="text-center mt-4 mb-4">Listado de Vías Ferratas</h1>

<!-- Formulario de búsqueda -->
<form method="GET" action="index.php" class="mb-4 bg-white p-4 shadow-sm rounded">
    <input type="hidden" name="accion" value="buscar_ferratas">
    
    <div class="row g-2">
        <div class="col-md-6">
            <label for="ubicacion" class="form-label">Ubicación:</label>
            <input type="text" id="ubicacion" name="ubicacion" placeholder="Ejemplo: Madrid, Valencia..." class="form-control">
        </div>
        
        <div class="col-md-4">
            <label for="dificultad" class="form-label">Dificultad:</label>
            <select id="dificultad" name="dificultad" class="form-control">
                <option value="">Todas</option>
                <option value="K1">K1</option>
                <option value="K2">K2</option>
                <option value="K3">K3</option>
                <option value="K4">K4</option>
                <option value="K5">K5</option>
                <option value="K6">K6</option>
                <option value="K7">K7</option>
            </select>
        </div>
    </div>
    <div class="row g-2">
        <div class="col-md-4">
            <label for="comunidad" class="form-label">Comunidad Autónoma:</label>
            <select id="comunidad" name="comunidad" class="form-control">
                <option value="">Todas</option>
                <option value="Andalucía">Andalucía</option>
                <option value="Aragón">Aragón</option>
                <option value="Asturias">Asturias</option>
                <option value="Baleares">Islas Baleares</option>
                <option value="Canarias">Canarias</option>
                <option value="Cantabria">Cantabria</option>
                <option value="Castilla-La Mancha">Castilla-La Mancha</option>
                <option value="Castilla y León">Castilla y León</option>
                <option value="Cataluña">Cataluña</option>
                <option value="Extremadura">Extremadura</option>
                <option value="Galicia">Galicia</option>
                <option value="Madrid">Madrid</option>
                <option value="Murcia">Región de Murcia</option>
                <option value="Navarra">Navarra</option>
                <option value="País Vasco">País Vasco</option>
                <option value="La Rioja">La Rioja</option>
                <option value="Valencia">Comunidad Valenciana</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="provincia" class="form-label">Provincia:</label>
            <input type="text" id="provincia" name="provincia" placeholder="Ejemplo: Alicante, Málaga..." class="form-control">
        </div>

        <div class="col-md-3">
            <label for="estado" class="form-label">Estado:</label>
            <select id="estado" name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="Abierta">Abierta</option>
                <option value="Cerrada">Cerrada</option>
                <option value="No operativa">No operativa</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </div>
</form>

<!-- Lista de ferratas organizadas por comunidades y provincias -->
<div class="accordion" id="accordionComunidades">
    <?php foreach ($ferratasOrganizadas as $index => $provincias): ?>
        <?php 
            $comunidad = htmlspecialchars($index);
            $comId = 'comunidad-' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower($comunidad)); 
        ?>
        
        <div class="accordion-item mb-3 shadow-sm border-0">
            <h2 class="accordion-header" id="heading-<?= $comId ?>">
                <button class="accordion-button collapsed custom-accordion" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $comId ?>" aria-expanded="false" aria-controls="collapse-<?= $comId ?>">
                    <?= $comunidad ?>
                </button>
            </h2>
            <div id="collapse-<?= $comId ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $comId ?>" data-bs-parent="#accordionComunidades">
                <div class="accordion-body">
                    <?php foreach ($provincias as $provincia => $listaFerratas): ?>
                        <div class="bloque-provincia mb-3">
                            <h4 class="m-0"><?= htmlspecialchars($provincia); ?></h4>
                        </div>

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
                                                    $ratingData = Valoracion::getAverageRating($ferrata['id']);
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






