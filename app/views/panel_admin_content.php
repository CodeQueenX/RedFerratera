<h1 class="text-center">Panel de Administración</h1>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ferratas_pendientes as $ferrata): ?>
            <tr>
                <td><?php echo htmlspecialchars($ferrata['nombre']); ?></td>
                <td><?php echo htmlspecialchars($ferrata['ubicacion']); ?></td>
                <td>
                    <a href="/RedFerratera/aprobar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-success">Aprobar</a>
					<a href="/RedFerratera/rechazar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-danger">Rechazar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
