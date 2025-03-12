<h1>Reportes de ferratas pendientes de revisar</h1>
<?php foreach ($reportes as $reporte): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($reporte['ferrata']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($reporte['mensaje']); ?></p>
            <p class="card-text"><small class="text-muted">Reportado por <?php echo htmlspecialchars($reporte['usuario']); ?> el <?php echo htmlspecialchars($reporte['fecha_reporte']); ?></small></p>
        </div>
    </div>
<?php endforeach; ?>
