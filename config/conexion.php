<?php

try {
    $conexion = new PDO("mysql:host=localhost;dbname=ishumecotizacion;charset=utf8", "root", "");
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo ("Conexio exitosa 👌");
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
