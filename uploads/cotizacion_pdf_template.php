<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
        }
        
        /* Header */
        .header {
            background: #000;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .logo {
            width: 80px;
            height: auto;
            max-height: 60px;
        }
        .header-right {
            text-align: right;
        }
        .contrato-title {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .institucion {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .verificacion {
            font-size: 11px;
            opacity: 0.9;
        }
        
        /* Content */
        .content {
            padding: 20px;
        }
        
        /* Cliente Info */
        .cliente-info {
            margin-bottom: 25px;
        }
        .cliente-info h2 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }
        .cliente-details {
            display: flex;
            gap: 30px;
            font-size: 13px;
        }
        
        /* Products Table */
        .productos-section {
            margin-bottom: 30px;
        }
        .section-title {
            background: #ff8c42;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .productos-table th {
            background: #ff8c42;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #e67e22;
        }
        .productos-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }
        .productos-table .modelo-col {
            width: 15%;
            text-align: left;
            font-weight: bold;
        }
        .productos-table .diseno-col {
            width: 20%;
        }
        .productos-table .cantidad-col {
            width: 10%;
            font-weight: bold;
        }
        .productos-table .precio-col {
            width: 15%;
        }
        .productos-table .monto-col {
            width: 15%;
        }
        .productos-table .total-col {
            width: 12.5%;
        }
        .productos-table .adelanto-col {
            width: 12.5%;
        }
        
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Total row styling */
        .total-row {
            background: #fff2e6;
        }
        .total-row td {
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Services Section */
        .servicios-section {
            margin-top: 30px;
        }
        .servicios-title {
            background: #ff8c42;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .servicios-content {
            display: flex;
            gap: 30px;
        }
        .producto-info {
            flex: 1;
        }
        .producto-info h4 {
            color: #ff8c42;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .producto-info ul {
            list-style: none;
            padding-left: 0;
        }
        .producto-info li {
            margin-bottom: 3px;
            font-size: 11px;
            position: relative;
            padding-left: 12px;
        }
        .producto-info li:before {
            content: "-";
            position: absolute;
            left: 0;
        }
        
        .contrato-info h4 {
            color: #ff8c42;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .contrato-info ul {
            list-style: none;
            padding-left: 0;
        }
        .contrato-info li {
            margin-bottom: 3px;
            font-size: 11px;
            position: relative;
            padding-left: 12px;
        }
        .contrato-info li:before {
            content: "-";
            position: absolute;
            left: 0;
        }
        
        /* Product Gallery */
        .productos-gallery {
            flex: 2;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-left: 20px;
        }
        .gallery-item {
            text-align: center;
        }
        .gallery-item img {
            width: 100%;
            height: 80px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Responsive adjustments for PDF */
        .row-merge {
            border-left: none;
        }
        
        /* Dompdf compatibility fixes */
        table {
            border-spacing: 0;
        }
        
        .productos-table td[rowspan] {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <?php if (!empty($logo_base64)): ?>
                <img src="<?= $logo_base64 ?>" alt="Logo" class="logo">
            <?php else: ?>
                <div style="width: 80px; height: 60px; background: #333; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px;">LOGO</div>
            <?php endif; ?>
        </div>
        <div class="header-right">
            <div class="contrato-title">CONTRATO CUADRO</div>
            <div class="institucion">INSTITUCIÓN: <?= strtoupper(htmlspecialchars($info["colegio"])) ?></div>
            <div class="verificacion">VERIFICACIÓN DE MODELO Y CANTIDAD DE ALUMNOS</div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Cliente Info -->
        <div class="cliente-info">
            <h2>CLIENTE: <?= strtoupper(htmlspecialchars($info["cliente"])) ?></h2>
            <div class="cliente-details">
                <span><strong>Promoción:</strong> <?= htmlspecialchars($info["promocion"]) ?></span>
                <span><strong>Sección:</strong> <?= htmlspecialchars($info["seccion"]) ?></span>
                <span><strong>Fecha:</strong> <?= $info["fecha"] ?></span>
                <span><strong>Estado:</strong> <?= ucfirst(htmlspecialchars($info["estado"])) ?></span>
            </div>
        </div>

        <!-- Services Section -->
        <div class="servicios-section">
            <div class="servicios-title">TU CONTRATO INCLUYE</div>
            <div class="servicios-content">
                <div style="flex: 1;">
                    <div class="producto-info">
                        <h4>PRODUCTO ANUARIO</h4>
                        <ul>
                            <li>Material: VARIADO</li>
                            <li>Color: VARIADO</li>
                        </ul>
                    </div>
                    
                    <div class="contrato-info" style="margin-top: 20px;">
                        <h4>SERVICIOS INCLUIDOS</h4>
                        <ul>
                            <li>Sesión de fotos en dos locaciones</li>
                            <li>Un fotógrafo y asistente</li>
                            <li>Togas individuales y grupal</li>
                            <li>Gratis recordatorio para el docente</li>
                            <li>Recordatorio para la institución</li>
                            <li>Cuadro pequeño retrato con foto de toga 10*15)</li>
                            <li>Tarjeta de invitación digital</li>
                            <li>Video reels detrás de cámaras</li>
                            <li>Modelos a elección del cliente</li>
                            <li>Accesorios para la sesión fotográfica</li>
                            <li>Fotografías extras en la sesión para recuerdo de los jóvenes</li>
                            <li>Link de descarga de las fotos digitales</li>
                        </ul>
                    </div>
                </div>
                
                <div class="productos-gallery">
                    <?php 
                    // Mostrar imágenes de productos en galería
                    $gallery_images = [];
                    foreach ($detalles as $detalle) {
                        if (!empty($detalle["imagen_base64"])) {
                            $gallery_images[] = $detalle["imagen_base64"];
                        }
                    }
                    
                    // Mostrar hasta 12 imágenes
                    $sample_images = array_slice($gallery_images, 0, 12);
                    
                    foreach ($sample_images as $img_base64): 
                    ?>
                        <div class="gallery-item">
                            <img src="<?= $img_base64 ?>" alt="Producto">
                        </div>
                    <?php endforeach; ?>
                    
                    <?php
                    // Si hay menos de 12 imágenes, llenar con placeholders
                    $placeholders_needed = 12 - count($sample_images);
                    for ($i = 0; $i < $placeholders_needed; $i++):
                    ?>
                        <div class="gallery-item">
                            <div style="width: 100%; height: 80px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 10px;">SIN IMAGEN</div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="productos-section">
            <div class="section-title">DETALLES DE PRODUCTOS</div>
            <table class="productos-table">
                <thead>
                    <tr>
                        <th class="modelo-col">MODELO</th>
                        <th class="diseno-col">DISEÑO</th>
                        <th class="cantidad-col">CANTIDAD</th>
                        <th class="precio-col">PRECIO<br>UNIDAD</th>
                        <th class="monto-col">MONTO<br>TOTAL</th>
                        <th class="total-col">TOTAL</th>
                        <th class="adelanto-col">ADELANTO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rowspan_count = count($detalles);
                    $is_first_row = true;
                    foreach ($detalles as $index => $detalle): 
                    ?>
                    <tr>
                        <td class="modelo-col"><?= strtoupper(htmlspecialchars($detalle["nombre_producto"])) ?></td>
                        <td class="diseno-col">
                            <?php if (!empty($detalle["imagen_base64"])): ?>
                                <img src="<?= $detalle["imagen_base64"] ?>" class="producto-img" alt="Diseño">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 9px; margin: 0 auto;">SIN IMAGEN</div>
                            <?php endif; ?>
                        </td>
                        <td class="cantidad-col"><?= $detalle["cantidad"] ?></td>
                        <td class="precio-col">S/. <?= number_format($detalle["precio_unitario"], 0) ?></td>
                        <td class="monto-col">S/. <?= number_format($detalle["subtotal"], 0) ?></td>
                        <?php if ($is_first_row): ?>
                            <td class="total-col" rowspan="<?= $rowspan_count ?>">S/. <?= number_format($info["total"], 0) ?></td>
                            <td class="adelanto-col" rowspan="<?= $rowspan_count ?>">S/. <?= number_format($info["adelanto"], 0) ?></td>
                            <?php $is_first_row = false; ?>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Saldo -->
            <div style="text-align: right; margin-top: 10px; font-size: 14px;">
                <strong>SALDO PENDIENTE: S/. <?= number_format($info["total"] - $info["adelanto"], 0) ?></strong>
            </div>
        </div>
    </div>
</body>
</html>