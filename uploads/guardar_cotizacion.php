<?php
// uploads/guardar_cotizacion.php
require_once("../config/conexion.php");

// Detectar conexión PDO
$db = null;
if (isset($conexion) && $conexion instanceof PDO) {
    $db = $conexion;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    $db = $pdo;
} else {
    die("Error: no se encontró una conexión PDO en conexion.php");
}

try {
    // Validar que se recibieron datos POST
    if (!$_POST) {
        throw new Exception("No se recibieron datos del formulario");
    }

    // Extraer datos principales
    $cliente = trim($_POST['cliente'] ?? '');
    $colegio = trim($_POST['colegio'] ?? '');
    $promocion = trim($_POST['promocion'] ?? '');
    $seccion = trim($_POST['seccion'] ?? '');
    $adelanto = floatval($_POST['adelanto'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);

    // Validar campos requeridos
    if (empty($cliente)) {
        throw new Exception("El campo Cliente es requerido");
    }

    // Debug: Ver qué datos llegan
    error_log("POST recibido: " . print_r($_POST, true));
    error_log("FILES recibido: " . print_r($_FILES, true));

    // Insertar cotización principal
    $sql = "INSERT INTO cotizaciones (cliente, colegio, promocion, seccion, fecha, total, adelanto, estado) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?, 'pendiente')";
    $stmt = $db->prepare($sql);
    $stmt->execute([$cliente, $colegio, $promocion, $seccion, $total, $adelanto]);
    
    $id_cotizacion = $db->lastInsertId();

    // Procesar productos
    $productos = $_POST['productos'] ?? [];
    
    foreach ($productos as $index => $producto) {
        if (empty($producto['nombre'])) continue;

        $nombre_producto = trim($producto['nombre']);
        $cantidad = intval($producto['cantidad'] ?? 1);
        $precio_unitario = floatval($producto['precio'] ?? 0);
        $subtotal = $cantidad * $precio_unitario;
        
        // Procesar imagen si existe
        $imagen_nombre = null;
        
        // Verificar si hay archivo de imagen para este producto
        if (isset($_FILES['productos']['tmp_name'][$index]['imagen']) && 
            !empty($_FILES['productos']['tmp_name'][$index]['imagen'])) {
            
            $archivo = [
                'name' => $_FILES['productos']['name'][$index]['imagen'],
                'tmp_name' => $_FILES['productos']['tmp_name'][$index]['imagen'],
                'error' => $_FILES['productos']['error'][$index]['imagen'],
                'size' => $_FILES['productos']['size'][$index]['imagen']
            ];
            
            if ($archivo['error'] == 0) {
                $imagen_nombre = subirImagen($archivo);
            }
        }
        
        // Insertar detalle del producto
        $sql = "INSERT INTO detalle_cotizacion (id_cotizacion, nombre_producto, imagen, cantidad, precio_unitario, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_cotizacion, $nombre_producto, $imagen_nombre, $cantidad, $precio_unitario, $subtotal]);
    }
    
    // Redireccionar al listado con mensaje de éxito
    header("Location: cotizaciones.php?msg=success&id=$id_cotizacion");
    exit;
    
} catch (Exception $e) {
    // Redireccionar con error
    $error_msg = urlencode($e->getMessage());
    header("Location: cotizacion_form.php?error=$error_msg");
    exit;
}

function subirImagen($archivo) {
    $upload_dir = "../img_cotizaciones/";
    
    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("No se pudo crear el directorio de imágenes");
        }
    }
    
    // Verificar permisos de escritura
    if (!is_writable($upload_dir)) {
        throw new Exception("El directorio de imágenes no tiene permisos de escritura");
    }
    
    // Validar que el archivo fue subido correctamente
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo',
            UPLOAD_ERR_EXTENSION => 'Extensión no permitida'
        ];
        throw new Exception($errores[$archivo['error']] ?? 'Error desconocido al subir archivo');
    }
    
    // Validar tipo de archivo usando finfo
    $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_archivo = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($tipo_archivo, $tipos_permitidos)) {
        throw new Exception("Tipo de archivo no permitido: $tipo_archivo. Solo se aceptan: JPG, PNG, GIF, WEBP");
    }
    
    // Validar tamaño (máximo 5MB)
    if ($archivo['size'] > 5 * 1024 * 1024) {
        throw new Exception("El archivo es muy grande (" . round($archivo['size']/1024/1024, 2) . "MB). Máximo permitido: 5MB");
    }
    
    // Generar nombre único y seguro
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $nombre_archivo = time() . '_' . uniqid() . '.' . $extension;
    $ruta_destino = $upload_dir . $nombre_archivo;
    
    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        throw new Exception("Error al mover el archivo al directorio de destino");
    }
    
    // Verificar que el archivo se guardó correctamente
    if (!file_exists($ruta_destino)) {
        throw new Exception("El archivo no se guardó correctamente");
    }
    
    return $nombre_archivo;
}
?>