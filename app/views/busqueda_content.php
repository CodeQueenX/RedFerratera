<h1 class="text-center my-4 fw-bold">Resultados de Búsqueda</h1>

<?php if (!empty($ferratas)): ?>
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle shadow-sm">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Dificultad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ferratas as $ferrata): ?>
                    <tr>
                        <td class="ferrata-nombre">
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>" class="text-decoration-underline fw-semibold text-dark">
                                <?= htmlspecialchars($ferrata['nombre']); ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($ferrata['ubicacion']); ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($ferrata['dificultad']); ?></span></td>
                        <td>
                            <span class="badge 
                                <?= ($ferrata['estado'] === 'Abierta') ? 'bg-success' : 
                                    (($ferrata['estado'] === 'Cerrada') ? 'bg-warning text-dark' : 
                                        (($ferrata['estado'] === 'No operativa') ? 'bg-danger' : 
                                            (($ferrata['estado'] === 'Precaución') ? 'bg-warning text-dark' : 'bg-secondary'))) ?>">
                                <?= htmlspecialchars($ferrata['estado']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= rawurlencode($ferrata['nombre']); ?>" class="btn btn-outline-primary btn-sm">
                                Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center mt-4 shadow-sm" role="alert">
        No se encontraron resultados para tu búsqueda.
    </div>
<?php endif; ?>
