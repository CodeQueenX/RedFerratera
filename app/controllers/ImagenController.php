<?php
require_once __DIR__ . '/../models/Imagen.php';

class ImagenController {
    
    public function eliminarImagen($imagen_id, $ferrata_id) {
        $imagenModel = new Imagen();
        $imagen = $imagenModel->obtenerImagenPorId($imagen_id);
        
        if ($imagen) {
            // Elimina la imagen del servidor
            $ruta = __DIR__ . '/../../public/img/ferratas/' . $imagen['ruta'];
            if (file_exists($ruta)) {
                unlink($ruta); // Elimina el archivo físico
            }
            
            // Elimina el registro de la base de datos
            $imagenModel->eliminarImagen($imagen_id);
            
            // Redirige de vuelta a la página de editar ferrata
            header("Location: /RedFerratera/index.php?accion=editar_ferrata&id=$ferrata_id");
            exit();
        } else {
            echo "Imagen no encontrada.";
        }
    }
}
?>