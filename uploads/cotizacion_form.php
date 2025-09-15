<?php
// cotizacion_form.php
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';
$success_msg = isset($_GET['msg']) && $_GET['msg'] == 'success' ? 'Cotización guardada exitosamente' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cotización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Nueva Cotización</h2>

    <!-- Mensajes de error/éxito -->
    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Éxito:</strong> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="guardar_cotizacion.php" method="POST" enctype="multipart/form-data" id="formCotizacion">
        <!-- Datos Generales -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Datos Generales</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Cliente *</label>
                        <input type="text" name="cliente" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Colegio</label>
                        <input type="text" name="colegio" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Promoción</label>
                        <input type="text" name="promocion" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sección</label>
                        <input type="text" name="seccion" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adelanto (S/.)</label>
                        <input type="number" step="0.01" name="adelanto" class="form-control" value="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Productos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaProductos">
                        <thead class="table-dark">
                            <tr>
                                <th width="25%">Producto</th>
                                <th width="20%">Imagen</th>
                                <th width="10%">Cantidad</th>
                                <th width="15%">Precio Unit. (S/.)</th>
                                <th width="15%">Subtotal (S/.)</th>
                                <th width="15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-success mb-3" id="agregarProducto">+ Agregar Producto</button>

                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Total (S/.)</strong></label>
                            <input type="text" name="total" id="totalGeneral" class="form-control form-control-lg" readonly style="font-weight: bold; font-size: 1.2em;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Cotización
            </button>
            <a href="cotizaciones.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    let index = 0;

    // Agregar primera fila automáticamente
    agregarFilaProducto();

    // Agregar fila
    $("#agregarProducto").click(function(){
        agregarFilaProducto();
    });

    function agregarFilaProducto() {
        index++;
        let fila = `
            <tr>
                <td>
                    <input type="text" name="productos[${index}][nombre]" class="form-control" 
                           placeholder="Nombre del producto" required>
                </td>
                <td>
                    <input type="file" name="productos[${index}][imagen]" class="form-control" 
                           accept="image/*" onchange="previsualizarImagen(this)">
                    <small class="text-muted">JPG, PNG, GIF (máx. 5MB)</small>
                </td>
                <td>
                    <input type="number" name="productos[${index}][cantidad]" 
                           class="form-control cantidad" min="1" value="1">
                </td>
                <td>
                    <input type="number" step="0.01" name="productos[${index}][precio]" 
                           class="form-control precio" min="0" value="0">
                </td>
                <td>
                    <input type="text" name="productos[${index}][subtotal]" 
                           class="form-control subtotal" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar" 
                            onclick="eliminarFila(this)" title="Eliminar producto">
                        <i class="fas fa-trash"></i> X
                    </button>
                </td>
            </tr>`;
        $("#tablaProductos tbody").append(fila);
    }

    // Eliminar fila
    window.eliminarFila = function(btn) {
        if ($("#tablaProductos tbody tr").length > 1) {
            $(btn).closest("tr").remove();
            calcularTotal();
        } else {
            alert("Debe mantener al menos un producto");
        }
    };

    // Calcular subtotal y total
    $(document).on("input change", ".cantidad, .precio", function(){
        let fila = $(this).closest("tr");
        let cantidad = parseFloat(fila.find(".cantidad").val()) || 0;
        let precio = parseFloat(fila.find(".precio").val()) || 0;
        let subtotal = cantidad * precio;
        fila.find(".subtotal").val(subtotal.toFixed(2));
        calcularTotal();
    });

    function calcularTotal(){
        let total = 0;
        $(".subtotal").each(function(){
            total += parseFloat($(this).val()) || 0;
        });
        $("#totalGeneral").val(total.toFixed(2));
    }

    // Previsualizar imagen
    window.previsualizarImagen = function(input) {
        if (input.files && input.files[0]) {
            let file = input.files[0];
            
            // Validar tamaño
            if (file.size > 5 * 1024 * 1024) {
                alert("El archivo es muy grande. Máximo 5MB.");
                input.value = '';
                return;
            }
            
            // Validar tipo
            if (!file.type.match('image.*')) {
                alert("Por favor seleccione una imagen válida.");
                input.value = '';
                return;
            }
        }
    };

    // Validar formulario antes de enviar
    $("#formCotizacion").on("submit", function(e) {
        let cliente = $("input[name='cliente']").val().trim();
        if (!cliente) {
            alert("El campo Cliente es requerido");
            e.preventDefault();
            return false;
        }

        let hayProductos = false;
        $("input[name*='[nombre]']").each(function() {
            if ($(this).val().trim()) {
                hayProductos = true;
                return false;
            }
        });

        if (!hayProductos) {
            alert("Debe agregar al menos un producto");
            e.preventDefault();
            return false;
        }

        // Mostrar loading
        $(this).find("button[type='submit']").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    });
});
</script>

</body>
</html>