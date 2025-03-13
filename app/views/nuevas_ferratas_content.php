<h1 class="text-center">Ferratas Nuevas (Último Mes)</h1>
<?php if(isset($nuevasFerratas) && count($nuevasFerratas) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Dificultad</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($nuevasFerratas as $ferrata): ?>
                <tr>
                    <td><a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>"><?= htmlspecialchars($ferrata['nombre']); ?></a></td>
                    <td><?php echo htmlspecialchars($ferrata['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($ferrata['dificultad']); ?></td>
                    <td>
                        <span class="badge 
                            <?= ($ferrata['estado'] === 'Abierta') ? 'bg-success' : 
                                (($ferrata['estado'] === 'Cerrada') ? 'bg-warning' : 
                                    (($ferrata['estado'] === 'No operativa') ? 'bg-danger' : 
                                        (($ferrata['estado'] === 'Precaución') ? 'bg-warning' : '')) ) ?>">
                            <?= htmlspecialchars($ferrata['estado']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d-m-Y', strtotime($ferrata['fecha_creacion'])); ?></td>
                    <td><a href="/RedFerratera/ferrata/<?= $ferrata['id']; ?>/<?= urlencode(strtolower(str_replace(' ', '-', $ferrata['nombre']))); ?>" class="btn btn-outline-primary btn-sm">Ver detalles</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-center">No hay ferratas nuevas en este momento.</p>
<?php endif; ?>
