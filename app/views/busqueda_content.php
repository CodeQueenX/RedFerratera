<h1 class="text-center mt-4">Resultados de búsqueda</h1>

<?php if (!empty($ferratas)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
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
                <?php foreach ($ferratas as $ferrata): ?>
                    <tr>
                        <td>
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>">
                                <?= htmlspecialchars($ferrata['nombre']); ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($ferrata['ubicacion']); ?></td>
                        <td><?= htmlspecialchars($ferrata['dificultad']); ?></td>
                        <td>
                            <span class="badge 
                                <?= ($ferrata['estado'] === 'Abierta') ? 'bg-success' : 
                                    (($ferrata['estado'] === 'Cerrada') ? 'bg-warning' : 
                                        (($ferrata['estado'] === 'No operativa') ? 'bg-danger' : 
                                            (($ferrata['estado'] === 'Precaución') ? 'bg-warning' : '')) ) ?>">
                                <?= htmlspecialchars($ferrata['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= rawurlencode($ferrata['nombre']); ?>" class="btn btn-outline-primary btn-sm">Ver detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-center text-muted">No se encontraron resultados para tu búsqueda.</p>
<?php endif; ?>
