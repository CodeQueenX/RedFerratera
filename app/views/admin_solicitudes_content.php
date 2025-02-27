<h1 class="text-center">Solicitudes Pendientes</h1>

<h2>Ferratas Pendientes</h2>
<?php if (!empty($solicitudesFerratas)): ?>
    <ul class="list-group">
        <?php foreach ($solicitudesFerratas as $ferrata): ?>
            <li class="list-group-item">
                <h3><?= htmlspecialchars($ferrata['nombre']); ?></h3>
                <p><strong>Ubicación:</strong> <?= htmlspecialchars($ferrata['ubicacion']); ?></p>
                <p><strong>Dificultad:</strong> <?= htmlspecialchars($ferrata['dificultad']); ?></p>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($ferrata['descripcion']); ?></p>
                <p><strong>Coordenadas:</strong> <?= htmlspecialchars($ferrata['coordenadas'] ?? 'No especificadas'); ?></p>
                <p><strong>Fecha de Creación:</strong> <?= htmlspecialchars($ferrata['fecha_creacion']); ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($ferrata['estado']); ?></p>
                <a href="/RedFerratera/aprobar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-success">Aprobar</a>
                <a href="/RedFerratera/rechazar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-danger">Rechazar</a>
                <a href="/RedFerratera/editar-ferrata/<?= $ferrata['id']; ?>" class="btn btn-warning">Editar Ferrata</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="text-center">No hay ferratas pendientes.</p>
<?php endif; ?>


<h2>Reportes Pendientes</h2>
<?php if (!empty($solicitudesReportes)): ?>
    <ul class="list-group">
        <?php foreach ($solicitudesReportes as $reporte): ?>
            <li class="list-group-item">
                <h3>Reporte sobre: <?= htmlspecialchars($reporte['ferrata']); ?></h3>
                <p><strong>Usuario:</strong> <?= htmlspecialchars($reporte['usuario']); ?></p>
                <p><strong>Mensaje:</strong> <?= htmlspecialchars($reporte['mensaje']); ?></p>
                <p><strong>Fecha:</strong> <?= htmlspecialchars($reporte['fecha_reporte']); ?></p>
                <a href="/RedFerratera/aprobar-reporte/<?= $reporte['id']; ?>" class="btn btn-success">Aprobar</a>
				<a href="/RedFerratera/rechazar-reporte/<?= $reporte['id']; ?>" class="btn btn-danger">Rechazar</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="text-center">No hay reportes pendientes.</p>
<?php endif; ?>