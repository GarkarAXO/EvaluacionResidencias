<?php
session_start();
require '../php/dbConexion.php'; // Conexión a la base de datos
require '../layouts/headerS.php';

// Obtener datos del alumno logueado
if (isset($_SESSION['usuario'])) {
    $idUsuario = $_SESSION['usuario']['idAlumno']; 
    $rolUsuario = $_SESSION['rol'];

    if ($rolUsuario === 'alumno') {
        $stmt = $conn->prepare("SELECT CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) AS nombreCompleto, carrera, nombreProyecto, empresa, asesorExterno, idAsesorInterno FROM alumno WHERE idAlumno = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $datosAlumno = $result->fetch_assoc();
        $stmt->close();

        // Obtener lista de asesores internos con nombre completo
        $stmtAsesores = $conn->prepare("SELECT idDocente, CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) AS nombreCompleto FROM profesor");
        $stmtAsesores->execute();
        $resultAsesores = $stmtAsesores->get_result();
        $asesores = $resultAsesores->fetch_all(MYSQLI_ASSOC);
        $stmtAsesores->close();

        // Obtener nombre completo del asesor interno (si existe)
        $nombreAsesorInterno = "";
        if ($datosAlumno && $datosAlumno['idAsesorInterno']) {
            $stmtAsesor = $conn->prepare("SELECT CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) AS nombreCompleto FROM profesor WHERE idDocente = ?");
            $stmtAsesor->bind_param("i", $datosAlumno['idAsesorInterno']);
            $stmtAsesor->execute();
            $resultAsesor = $stmtAsesor->get_result();
            if ($resultAsesor->num_rows > 0) {
                $rowAsesor = $resultAsesor->fetch_assoc();
                $nombreAsesorInterno = $rowAsesor['nombreCompleto'];
            }
            $stmtAsesor->close();
        }
    }
}
?>

<main class="container pt-5">
    <h2>Información del Proyecto</h2>
    <div class="row">
        <!-- Datos del alumno -->
        <div class="col-md-6">
            <div class="card bg-success text-white mb-3">
                <div class="card-header">Datos del Alumno</div>
                <div class="card-body">
                    <form>
                        <fieldset disabled>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" class="form-control" value="<?= $datosAlumno['nombreCompleto'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="carrera" class="form-label">Carrera</label>
                                <input type="text" id="carrera" class="form-control" value="<?= $datosAlumno['carrera'] ?? ''; ?>">
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>

        <!-- Datos de residencia -->
        <div class="col-md-6">
            <div class="card bg-primary text-white mb-3">
                <div class="card-header">Datos de Residencia</div>
                <div class="card-body">
                    <form>
                        <fieldset>
                            <div class="mb-3">
                                <label for="nombreProyecto" class="form-label">Nombre del Proyecto</label>
                                <input type="text" id="nombreProyecto" class="form-control" value="<?= $datosAlumno['nombreProyecto'] ?? ''; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="empresa" class="form-label">Empresa</label>
                                <input type="text" id="empresa" class="form-control" value="<?= $datosAlumno['empresa'] ?? ''; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="asesorExterno" class="form-label">Asesor Externo</label>
                                <input type="text" id="asesorExterno" class="form-control" value="<?= $datosAlumno['asesorExterno'] ?? ''; ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="asesorInterno" class="form-label">Asesor Interno</label>
                                <input type="text" id="asesorInterno" class="form-control" value="<?= $nombreAsesorInterno ?? ''; ?>" disabled>
                            </div>
                            <button type="button" class="btn principal text-light" data-bs-toggle="modal" data-bs-target="#modalDatosResidencia">Completar</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="modalDatosResidencia" tabindex="-1" aria-labelledby="modalDatosResidenciaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalDatosResidenciaLabel">Datos de Residencia</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formDatosResidenciaModal">
                        <div class="mb-3">
                            <label for="nombreProyectoModal" class="form-label">Nombre del Proyecto</label>
                            <input type="text" id="nombreProyectoModal" class="form-control" value="<?= $datosAlumno['nombreProyecto'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="empresaModal" class="form-label">Empresa</label>
                            <input type="text" id="empresaModal" class="form-control" value="<?= $datosAlumno['empresa'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="asesorExternoModal" class="form-label">Asesor Externo</label>
                            <input type="text" id="asesorExternoModal" class="form-control" value="<?= $datosAlumno['asesorExterno'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="idAsesorInternoModal" class="form-label">Asesor Interno</label>
                            <select id="idAsesorInternoModal" class="form-select">
                                <option value="">Selecciona un asesor</option>
                                <?php foreach ($asesores as $asesor): ?>
                                    <option value="<?= $asesor['idDocente']; ?>" <?= ($datosAlumno['idAsesorInterno'] == $asesor['idDocente']) ? 'selected' : ''; ?>>
                                    <?= $asesor['nombreCompleto']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="guardarCambios">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGuardarCambios = document.getElementById('guardarCambios');

    if (btnGuardarCambios) {
        btnGuardarCambios.addEventListener('click', () => {
            const nombreProyecto = document.getElementById('nombreProyectoModal').value;
            const empresa = document.getElementById('empresaModal').value;
            const asesorExterno = document.getElementById('asesorExternoModal').value;
            const idAsesorInterno = document.getElementById('idAsesorInternoModal').value;
            const idAlumno = <?= $_SESSION['usuario']['idAlumno'] ?? 'null'; ?>;

            // Validar que no haya campos vacíos
            if (!nombreProyecto || !empresa || !asesorExterno || !idAsesorInterno) {
                alert('Todos los campos deben estar completos.');
                return;
            }

            fetch('guardar_datos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `idAlumno=${idAlumno}&nombreProyecto=${encodeURIComponent(nombreProyecto)}&empresa=${encodeURIComponent(empresa)}&asesorExterno=${encodeURIComponent(asesorExterno)}&idAsesorInterno=${idAsesorInterno}&guardar_datos=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Datos guardados correctamente');

                    // Actualizar los valores en el formulario principal
                    document.getElementById('nombreProyecto').value = nombreProyecto;
                    document.getElementById('empresa').value = empresa;
                    document.getElementById('asesorExterno').value = asesorExterno;
                    document.getElementById('asesorInterno').value = document.getElementById('idAsesorInternoModal').options[document.getElementById('idAsesorInternoModal').selectedIndex].text;

                    // Cerrar el modal
                    let modal = bootstrap.Modal.getInstance(document.getElementById('modalDatosResidencia'));
                    modal.hide();
                } else {
                    alert('Error al guardar los datos: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                alert('Hubo un problema al guardar los datos.');
            });
        });
    }
});
</script>


<?php require '../layouts/footer.php'; ?>
