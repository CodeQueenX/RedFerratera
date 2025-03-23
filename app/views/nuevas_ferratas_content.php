<h1 class="text-center my-4 fw-bold">Ferratas Nuevas (Último Mes)</h1>

<?php if(isset($nuevasFerratas) && count($nuevasFerratas) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle shadow-sm tabla-nuevas-ferratas">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Dificultad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($nuevasFerratas as $ferrata): ?>
                    <tr>
                        <td>
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>" class="ferrata-nueva-nombre">
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
                        <td><?= date('d-m-Y', strtotime($ferrata['fecha_creacion'])); ?></td>
                        <td class="text-center">
                            <a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>" class="btn btn-outline-primary btn-sm">
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
        No hay ferratas nuevas registradas en el último mes.
    </div>
<?php endif; ?>
