<?php
require_once __DIR__ . '/app/models/Ferrata.php';

$ferrata = new Ferrata();

// Obtener todas las ferratas
$ferratas = $ferrata->obtenerFerratas();

echo "<h2>Lista de Vías Ferratas</h2>";
foreach ($ferratas as $fila) {
    echo "<p><strong>{$fila['nombre']}</strong> - {$fila['ubicacion']} ({$fila['dificultad']})</p>";
}
?>
