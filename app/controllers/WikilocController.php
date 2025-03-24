<?php
require_once __DIR__ . '/../models/Wikiloc.php';

class WikilocController {
    private $wikiloc;
    
    public function __construct() {
        $this->wikiloc = new Wikiloc();
    }
    
    public function guardar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $wikiloc_embed = isset($_POST['wikiloc']) ? trim($_POST['wikiloc']) : '';
        
        if ($ferrata_id <= 0 || empty($wikiloc_embed)) {
            echo "Datos inválidos.";
            return;
        }
        
        $wikiloc = new Wikiloc($ferrata_id, $wikiloc_embed);
        
        if ($wikiloc->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el enlace de Wikiloc.";
        }
    }
    
    public function borrar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;
        
        if ($id <= 0 || $ferrata_id <= 0) {
            echo "Datos inválidos.";
            return;
        }
        
        if (Wikiloc::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el enlace de Wikiloc.";
        }
    }
}
