<?php
// uploads/cotizaciones_pdf.php
require_once("../config/conexion.php");
require_once("../vendor/autoload.php");

use Dompdf\Dompdf;
use Dompdf\Options;

// Detectar conexión PDO
$db = null;
if (isset($conexion) && $conexion instanceof PDO) {
    $db = $conexion;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    $db = $pdo;
} else {
    die("Error: no se encontró una conexión PDO en conexion.php");
}

// Obtener ID
$id_cotizacion = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id_cotizacion <= 0) {
    die("ID de cotización no válido.");
}

// Traer datos de la cotización
$sql = "SELECT c.*, d.id_detalle, d.nombre_producto, d.imagen, d.cantidad, d.precio_unitario, d.subtotal
        FROM cotizaciones c
        LEFT JOIN detalle_cotizacion d ON c.id_cotizacion = d.id_cotizacion
        WHERE c.id_cotizacion = ?
        ORDER BY d.id_detalle ASC";
$stmt = $db->prepare($sql);
$stmt->execute([$id_cotizacion]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    die("Cotización no encontrada.");
}

// Extraer información principal
$info = [
    "cliente"   => $rows[0]["cliente"],
    "colegio"   => $rows[0]["colegio"],
    "promocion" => $rows[0]["promocion"],
    "seccion"   => $rows[0]["seccion"],
    "fecha"     => $rows[0]["fecha"],
    "total"     => $rows[0]["total"],
    "adelanto"  => $rows[0]["adelanto"],
    "estado"    => $rows[0]["estado"],
];

// Extraer detalles de productos
$detalles = [];
foreach ($rows as $r) {
    if ($r["id_detalle"]) {
        $detalles[] = $r;
    }
}

// Capturar el HTML de la plantilla
ob_start();
include "cotizacion_pdf_template.php";
$html = ob_get_clean();

// Configurar Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// Descargar el PDF
$filename = "Cotizacion_" . $id_cotizacion . "_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
?>