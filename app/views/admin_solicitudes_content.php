<!-- Solicitudes Pendientes -->
<h1 class="text-center mb-4">Solicitudes Pendientes</h1>

<!-- Ferratas Pendientes -->
<h2 class="text-primary">Ferratas Pendientes</h2>
<?php if (!empty($solicitudesFerratas)): ?>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($solicitudesFerratas as $ferrata): ?>
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title"><?= htmlspecialchars($ferrata['nombre']); ?></h4>
                        <p><strong>Ubicación:</strong> <?= htmlspecialchars($ferrata['ubicacion']); ?></p>
                        <p><strong>Dificultad:</strong> <?= htmlspecialchars($ferrata['dificultad']); ?></p>
                        <p><strong>Descripción:</strong> <?= htmlspecialchars($ferrata['descripcion']); ?></p>
                        <p><strong>Coordenadas:</strong> <?= htmlspecialchars($ferrata['coordenadas'] ?? 'No especificadas'); ?></p>
                        <p><strong>Fecha de Creación:</strong>
                            <?= (!empty($ferrata['fecha_creacion']) && $ferrata['fecha_creacion'] != '0000-00-00') ? date('d-m-Y', strtotime($ferrata['fecha_creacion'])) : 'Fecha no disponible'; ?>
                        </p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($ferrata['estado']); ?></p>

                        <!-- Acciones -->
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="/RedFerratera/index.php?accion=aprobar_ferrata&id=<?= $ferrata['id']; ?>" class="btn btn-success">Aprobar</a>
                            <a href="/RedFerratera/index.php?accion=rechazar_ferrata&id=<?= $ferrata['id']; ?>" class="btn btn-danger">Rechazar</a>
                            <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                                <a href="/RedFerratera/editar-ferrata/<?= $ferrata['id']; ?>?desde_gestion=1" class="btn btn-warning">Editar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-muted">No hay ferratas pendientes.</p>
<?php endif; ?>

<!-- Reportes Pendientes -->
<h2 class="text-primary mt-5">Reportes Pendientes</h2>
<?php if (!empty($solicitudesReportes)): ?>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($solicitudesReportes as $reporte): ?>
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Reporte sobre: <?= htmlspecialchars($reporte['ferrata']); ?></h5>
                        <p><strong>Usuario:</strong> <?= htmlspecialchars($reporte['usuario']); ?></p>
                        <p><strong>Mensaje:</strong> <?= htmlspecialchars($reporte['mensaje']); ?></p>
                        <p><small class="text-muted">Fecha: <?= htmlspecialchars($reporte['fecha_reporte']); ?></small></p>

                        <!-- Acciones -->
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="/RedFerratera/index.php?accion=aprobar_reporte&id=<?= htmlspecialchars($reporte['id']); ?>" class="btn btn-success">Aprobar</a>
                            <a href="/RedFerratera/index.php?accion=rechazar_reporte&id=<?= htmlspecialchars($reporte['id']); ?>" class="btn btn-danger">Rechazar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-muted">No hay reportes pendientes.</p>
<?php endif; ?>
