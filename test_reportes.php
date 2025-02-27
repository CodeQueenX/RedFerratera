<?php
require_once __DIR__ . '/app/models/Reporte.php';

$reporte = new Reporte();

// Probar obtener todos los reportes
$reportes = $reporte->obtenerReportes();
echo "<h2>Lista de Reportes</h2>";
foreach ($reportes as $r) {
    echo "<p><strong>{$r['ferrata']}</strong> - Reportado por <strong>{$r['usuario']}</strong> el {$r['fecha_reporte']} <br> Mensaje: {$r['mensaje']}</p>";
}
?>
