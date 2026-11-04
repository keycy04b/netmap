<?php
header('Content-Type: application/json');
require_once "conexion.php";

function netmapTitleCaseFirstCompat(string $value): string {
    $value = trim($value);
    if ($value === '') return '';
    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($value, 1, null, 'UTF-8');
    }
    return strtoupper(substr($value, 0, 1)) . substr($value, 1);
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tipo_equipo = intval($_POST['id_tipo_equipo'] ?? 0);
    $marca         = netmapTitleCaseFirstCompat((string)($_POST['marca'] ?? ''));
    $modelo        = netmapTitleCaseFirstCompat((string)($_POST['modelo'] ?? ''));
    $serial        = strtoupper(trim($_POST['serial'] ?? '')); // El serial en mayúscula completa
    $direccion_ip   = trim($_POST['direccion_ip'] ?? '');
    $nombre_equipo  = netmapTitleCaseFirstCompat((string)($_POST['nombre_equipo'] ?? ''));
    $id_piso        = intval($_POST['id_piso'] ?? 0);
    $numero_puertos = intval($_POST['numero_puertos'] ?? 0);
    $posicion_x     = floatval($_POST['posicion_x'] ?? 0);
    $posicion_y     = floatval($_POST['posicion_y'] ?? 0);
    $observaciones = ucfirst(trim($_POST['observaciones'] ?? ''));
    
    
    
   
    

    // Gestión del Icono (Archivo local o Texto/URL)
    $icono = null;

    // 1. Si el usuario subió un archivo desde su PC
    if (isset($_FILES['archivo_icono']) && $_FILES['archivo_icono']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['archivo_icono']['tmp_name'];
        $fileName = $_FILES['archivo_icono']['name'];
        
        // Limpiar nombre y asegurar unicidad con time()
        $nuevoNombre = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $uploadFileDir = '../assets/icons/custom/';
        
        // Crear la carpeta si no existe
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        
        $dest_path = $uploadFileDir . $nuevoNombre;
        
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            // Guardamos la ruta relativa que se usará en el proyecto
            $icono = 'assets/icons/custom/' . $nuevoNombre;
        }
    } 
    // 2. Si no subió archivo pero escribió una URL o ruta en el input de texto
    elseif (!empty($_POST['icono_texto'])) {
        $icono = trim($_POST['icono_texto']);
    }

    // Validar campos obligatorios
    if (!$id_tipo_equipo || !$marca || !$modelo || !$serial || !$direccion_ip || !$id_piso) {
        echo json_encode(["estado" => "error", "mensaje" => "Por favor llena todos los campos obligatorios."]);
        exit;
    }

    // Insertar el equipo en la base de datos
    $sql = "INSERT INTO equipos (id_piso, id_tipo_equipo, icono, nombre_equipo, marca, modelo, serial, direccion_ip, numero_puertos, posicion_x, posicion_y, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iissssssidds", $id_piso, $id_tipo_equipo, $icono, $nombre_equipo, $marca, $modelo, $serial, $direccion_ip, $numero_puertos, $posicion_x, $posicion_y, $observaciones);

    if ($stmt->execute()) {
        $id_equipo_nuevo = $stmt->insert_id;
        $stmt->close();

        // Generar automáticamente los puertos físicos
        if ($numero_puertos > 0) {
            $sql_puerto = "INSERT INTO puertos (id_equipo, numero_puerto, tipo_puerto, estado) VALUES (?, ?, 'RED', 'DISPONIBLE')";
            $stmt_puerto = $conexion->prepare($sql_puerto);

            for ($i = 1; $i <= $numero_puertos; $i++) {
                $stmt_puerto->bind_param("ii", $id_equipo_nuevo, $i);
                $stmt_puerto->execute();
            }
            $stmt_puerto->close();
        }

        echo json_encode([
            "estado" => "ok", 
            "mensaje" => "¡Equipo registrado con éxito y $numero_puertos puertos generados automáticamente!"
        ]);
    } else {
        echo json_encode([
            "estado" => "error", 
            "mensaje" => "Error al guardar. Es posible que la dirección IP ya esté registrada. Detalle: " . $stmt->error
        ]);
    }

    $conexion->close();
}
