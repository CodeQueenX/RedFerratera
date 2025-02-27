<h1 class="text-center mt-4 mb-4">Listado de Vías Ferratas</h1>

<!-- Formulario de búsqueda -->
<form method="GET" action="index.php" class="mb-4">
    <input type="hidden" name="accion" value="buscar_ferratas">
    
    <div class="row g-2">
        <div class="col-md-6">
            <label class="form-label">Ubicación:</label>
            <input type="text" name="ubicacion" placeholder="Ejemplo: Madrid, Valencia..." class="form-control">
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Dificultad:</label>
            <select name="dificultad" class="form-control">
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
    <div class="row g-2">
    
        <div class="col-md-4">
            <label class="form-label">Comunidad Autónoma:</label>
            <select name="comunidad" class="form-control">
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
            <label class="form-label">Provincia:</label>
            <input type="text" name="provincia" placeholder="Ejemplo: Alicante, Málaga..." class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado:</label>
            <select name="estado" class="form-control">
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

<?php if (!empty($ferratasOrganizadas)): ?>
    <?php foreach ($ferratasOrganizadas as $comunidad => $provincias): ?>
        <div class="mt-4 p-2 rounded text-white text-center" style="background-color: rgba(46, 125, 50, 0.75);">
            <h3 class="m-0"><?= htmlspecialchars($comunidad); ?></h3>
        </div>

        <?php foreach ($provincias as $provincia => $listaFerratas): ?>
            <div class="mt-3 p-2 rounded text-white text-center" style="background-color: rgba(100, 181, 246, 0.75);">
                <h4 class="m-0"><?= htmlspecialchars($provincia); ?></h4>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover mt-2">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Dificultad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaFerratas as $ferrata): ?>
                            <tr>
                                <td><?= htmlspecialchars($ferrata['nombre']); ?></td>
                                <td><?= htmlspecialchars($ferrata['ubicacion']); ?></td>
                                <td><?= htmlspecialchars($ferrata['dificultad']); ?></td>
                                <td>
                                    <span class="badge 
                                        <?= ($ferrata['estado'] === 'Abierta') ? 'bg-success' : 
                                            (($ferrata['estado'] === 'Cerrada') ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= htmlspecialchars($ferrata['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>" class="btn btn-outline-primary btn-sm">Ver detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-center text-muted">No hay ferratas disponibles.</p>
<?php endif; ?>





