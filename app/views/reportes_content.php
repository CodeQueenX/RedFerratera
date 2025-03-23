<h1 class="text-center my-4 fw-bold">Reportes Pendientes de Ferratas</h1>

<?php if (!empty($reportes)): ?>
    <?php foreach ($reportes as $reporte): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title text-dark fw-semibold mb-2">
                    <?= htmlspecialchars($reporte['ferrata']); ?>
                </h5>
                <p class="card-text mb-3"><?= nl2br(htmlspecialchars($reporte['mensaje'])); ?></p>
                <p class="card-text text-muted small mb-0">
                    🧍 Reportado por <strong><?= htmlspecialchars($reporte['usuario']); ?></strong>
                    <br>
                    📅 Fecha: <?= date('d-m-Y H:i', strtotime($reporte['fecha_reporte'])); ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info text-center shadow-sm">
        No hay reportes pendientes en este momento.
    </div>
<?php endif; ?>
