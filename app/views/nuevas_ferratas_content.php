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
            </tr>
        </thead>
        <tbody>
            <?php foreach($nuevasFerratas as $ferrata): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ferrata['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($ferrata['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($ferrata['dificultad']); ?></td>
                    <td><?php echo htmlspecialchars($ferrata['estado']); ?></td>
                    <td><?php echo htmlspecialchars($ferrata['fecha_creacion']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-center">No hay ferratas nuevas en este momento.</p>
<?php endif; ?>
