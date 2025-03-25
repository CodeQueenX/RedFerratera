<?php
require_once __DIR__ . '/../models/Wikiloc.php';

class WikilocController {
    private $wikiloc;
    
    public function __construct() {
        // Crear instancia del modelo Wikiloc
        $this->wikiloc = new Wikiloc();
    }
    
    // Guardar un nuevo enlace de Wikiloc
    public function guardar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar permisos de administrador
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        // Obtener datos del formulario
        $ferrata_id = isset($_POST['ferrata_id']) ? intval($_POST['ferrata_id']) : 0;
        $wikiloc_embed = isset($_POST['wikiloc']) ? trim($_POST['wikiloc']) : '';
        
        // Validar datos
        if ($ferrata_id <= 0 || empty($wikiloc_embed)) {
            echo "Datos inválidos.";
            return;
        }
        
        // Crear nueva instancia de Wikiloc y guardar
        $wikiloc = new Wikiloc($ferrata_id, $wikiloc_embed);
        if ($wikiloc->save()) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al guardar el enlace de Wikiloc.";
        }
    }
    
    // Borrar un enlace de Wikiloc por ID
    public function borrar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar permisos de administrador
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo "No tienes permisos para realizar esta acción.";
            return;
        }
        
        // Obtener ID del enlace y de la ferrata
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ferrata_id = isset($_GET['ferrata_id']) ? intval($_GET['ferrata_id']) : 0;
        
        // Validar IDs
        if ($id <= 0 || $ferrata_id <= 0) {
            echo "Datos inválidos.";
            return;
        }
        
        // Eliminar el enlace
        if (Wikiloc::deleteById($id)) {
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=" . $ferrata_id);
            exit;
        } else {
            echo "Error al borrar el enlace de Wikiloc.";
        }
    }
}
