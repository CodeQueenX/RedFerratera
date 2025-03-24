<?php
require_once __DIR__ . '/../models/Imagen.php';

class ImagenController {
    private $imagen;
    
    public function subirImagen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagenes'])) {
            $imagenModel = new Imagen();
            $ferrata_id = $_POST['ferrata_id'] ?? null;
            
            if ($ferrata_id && !empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $nombreArchivo = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                    $rutaDestino = "public/img/ferratas/" . $nombreArchivo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenModel->guardarImagen($ferrata_id, $nombreArchivo);
                    }
                }
            }
        }
        
        header("Location: index.php?accion=editar_ferrata&id=$ferrata_id");
        exit();
    }
    
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