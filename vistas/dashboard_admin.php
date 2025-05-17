<?php
session_start();
require '../php/dbConexion.php'; // Conexión a la base de datos
require '../layouts/headerS.php'; // Header con sesión activa
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

function mostrarSweetAlert($icon, $title, $text = null) {
    echo "<script>
        Swal.fire({
            icon: '$icon',
            title: '$title',
            text: '$text',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location = 'dashboard_admin.php'; // Redirige a la página deseada
        });
    </script>";
}

// Obtener estadísticas
$totalAlumnos = $conn->query("SELECT COUNT(*) AS total FROM Alumno")->fetch_assoc()['total'];
$totalProfesores = $conn->query("SELECT COUNT(*) AS total FROM Profesor")->fetch_assoc()['total'];


function obtenerAlumnos($conn, $busqueda = null) {
    $sql = "SELECT * FROM alumno";
    if ($busqueda) {
        $sql .= " WHERE numControl LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR apellidoPaterno LIKE '%$busqueda%' OR apellidoMaterno LIKE '%$busqueda%'";
    }
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error al obtener alumnos: " . $conn->error;
        return [];
    }
}


function obtenerProfesores($conn, $busqueda_profesores = null) {
    $sql = "SELECT * FROM profesor";
    if ($busqueda_profesores) {
        $sql .= " WHERE cve LIKE '%$busqueda_profesores%' OR nombre LIKE '%$busqueda_profesores%' OR apellidoPaterno LIKE '%$busqueda_profesores%' OR apellidoMaterno LIKE '%$busqueda_profesores%'";
    }
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error al obtener profesores: " . $conn->error;
        return [];
    }
}

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;
$alumnos = obtenerAlumnos($conn, $busqueda);

$busqueda_profesores = isset($_GET['busqueda_profesores']) ? $_GET['busqueda_profesores'] : null;
$profesores = obtenerProfesores($conn, $busqueda_profesores);
// Procesamiento de formularios (Agregar, Editar, Eliminar)



// Alumno
if (isset($_POST['agregarAlumno'])) {
    $numControl = $_POST['numControl'];
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $carrera = $_POST['carrera'];

    $sql = "INSERT INTO alumno (numControl, nombre, apellidoPaterno, apellidoMaterno, carrera) VALUES ('$numControl', '$nombre', '$apellidoPaterno', '$apellidoMaterno', '$carrera')";

    if ($conn->query($sql) === TRUE) {
        // Alerta de éxito con SweetAlert2
        mostrarSweetAlert('success', 'Alumno agregado correctamente');
    } else {
        // Alerta de error con SweetAlert2
        mostrarSweetAlert('error', 'Error al agregar alumno', $conn->error);
    }
}

if (isset($_POST['editarAlumno'])) {
    $idAlumno = $_POST['idAlumno'];
    $numControl = $_POST['numControl'];
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $carrera = $_POST['carrera'];
    $nombreProyecto = $_POST['nombreProyecto'];
    $empresa = $_POST['empresa'];
    $asesorExterno = $_POST['asesorExterno'];
    $idAsesorInterno = $_POST['idAsesorInterno'];

    // Obtener la contraseña del formulario
    $contrasena = $_POST['contrasena'];

    // Verificar si se proporcionó una nueva contraseña
    if (!empty($contrasena)) {
        // Hashear la nueva contraseña
        $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);

        // Actualizar todos los campos, incluyendo la contraseña
        $sql = "UPDATE alumno SET numControl = '$numControl', nombre = '$nombre', apellidoPaterno = '$apellidoPaterno', apellidoMaterno = '$apellidoMaterno', carrera = '$carrera', contrasena = '$contrasena_hasheada', nombreProyecto = '$nombreProyecto', empresa = '$empresa', asesorExterno = '$asesorExterno', idAsesorInterno = '$idAsesorInterno' WHERE idAlumno = $idAlumno";
    } else {
        // Actualizar todos los campos, excepto la contraseña
        $sql = "UPDATE alumno SET numControl = '$numControl', nombre = '$nombre', apellidoPaterno = '$apellidoPaterno', apellidoMaterno = '$apellidoMaterno', carrera = '$carrera', nombreProyecto = '$nombreProyecto', empresa = '$empresa', asesorExterno = '$asesorExterno', idAsesorInterno = '$idAsesorInterno' WHERE idAlumno = $idAlumno";
    }

    if ($conn->query($sql) === TRUE) {
        mostrarSweetAlert('success', 'Alumno actualizado correctamente');
    } else {
        echo "Error al actualizar alumno: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['eliminarAlumno'])) {
    $idAlumno = $_POST['idAlumno'];

    $sql = "DELETE FROM alumno WHERE idAlumno = $idAlumno";

    if ($conn->query($sql) === TRUE) {
        mostrarSweetAlert('success', 'Alumno eliminado correctamente');
    } else {
        mostrarSweetAlert('error', 'Error al eliminar alumno', $conn->error);
    }
}

// Profesor
if (isset($_POST['agregarProfesor'])) {
    $cve = $_POST['cve'];
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hashea la contraseña

    $sql = "INSERT INTO profesor (cve, nombre, apellidoPaterno, apellidoMaterno, contrasena) VALUES ('$cve', '$nombre', '$apellidoPaterno', '$apellidoMaterno', '$contrasena')";

    if ($conn->query($sql) === TRUE) {
        // Alerta de éxito con SweetAlert2
        mostrarSweetAlert('success', 'Profesor agregado correctamente');
    } else {
        // Alerta de error con SweetAlert2
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error al agregar profesor',
                text: '" . $conn->error . "' // Muestra el mensaje de error de la base de datos
            });
        </script>";
    }
}

// Profesor
if (isset($_POST['editarProfesor'])) {
    $idDocente = $_POST['idDocente'];
    $cve = $_POST['cve'];
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno']; // Nuevos campos
    $apellidoMaterno = $_POST['apellidoMaterno']; // Nuevos campos
    $contrasena = $_POST['contrasena'];

    if (!empty($contrasena)) {
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE profesor SET cve = '$cve', nombre = '$nombre', apellidoPaterno = '$apellidoPaterno', apellidoMaterno = '$apellidoMaterno', contrasena = '$contrasena' WHERE idDocente = $idDocente";
    } else {
        $sql = "UPDATE profesor SET cve = '$cve', nombre = '$nombre', apellidoPaterno = '$apellidoPaterno', apellidoMaterno = '$apellidoMaterno' WHERE idDocente = $idDocente";
    }

    if ($conn->query($sql) === TRUE) {
        mostrarSweetAlert('success', 'Profesor actualizado correctamente');
    } else {
        mostrarSweetAlert('error', 'Error al actualizar profesor', $conn->error);
    }
}

if (isset($_POST['eliminarProfesor'])) {
    $idDocente = $_POST['idDocente'];

    $sql = "DELETE FROM profesor WHERE idDocente = $idDocente";

    if ($conn->query($sql) === TRUE) {
        mostrarSweetAlert('success', 'Profesor eliminado correctamente');
    } else {
        mostrarSweetAlert('error', 'Error al eliminar profesor', $conn->error);
    }
}

?>

<main>
<div class="container pt-5">
            <section class="col-md-9">
            <h2>Bienvenido, Administrador</h2>
            <p>Gestiona los datos del sistema.</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-primary text-white mb-3">
                        <div class="card-header">Total de Alumnos</div>
                        <div class="card-body">
                            <h5 class="card-title" id="totalAlumnos"><?php echo $totalAlumnos; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white mb-3">
                        <div class="card-header">Total de Profesores</div>
                        <div class="card-body">
                            <h5 class="card-title" id="totalProfesores"><?php echo $totalProfesores; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
        <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Descarga de Archivos</h2>
                <p>Descarga los registros y los formularios en PDF y Excel:</p>
                <div class="d-flex justify-content-center">
                    <a href="ruta/al/archivo.pdf" download class="btn btn-primary me-3">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="ruta/al/archivo.xlsx" download class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
        </section>

            <h2>Alumnos</h2>
            <form method="GET">
                <input type="text" name="busqueda" placeholder="Buscar alumnos...">
                <button type="submit">Buscar</button>
            </form>
            <div type="button" class="btn btn-sm principal" data-bs-toggle="modal"
                data-bs-target="#agregarAlumnoModal">Agregar Alumno</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Num. Control</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td>
                                <?php echo $alumno['numControl']; ?>
                            </td>
                            <td>
                                <?php echo $alumno['nombre']; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editarAlumnoModal<?php echo $alumno['idAlumno']; ?>">Editar</button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="eliminarAlumno(<?php echo $alumno['idAlumno']; ?>)">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade" id="editarAlumnoModal<?php echo $alumno['idAlumno']; ?>" tabindex="-1" aria-labelledby="editarAlumnoModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="editarAlumnoModalLabel">Editar Alumno</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <input type="hidden" name="idAlumno" value="<?php echo $alumno['idAlumno']; ?>">
                                        <label for="numControl">Num. Control:</label>
                                        <input type="text" name="numControl" value="<?php echo $alumno['numControl']; ?>" required>
                                        <label for="nombre">Nombre:</label>
                                        <input type="text" name="nombre" value="<?php echo $alumno['nombre']; ?>" required>
                                        <label for="apellidoPaterno">Apellido Paterno:</label>
                                        <input type="text" name="apellidoPaterno" value="<?php echo $alumno['apellidoPaterno']; ?>" required>
                                        <label for="apellidoMaterno">Apellido Materno:</label>
                                        <input type="text" name="apellidoMaterno" value="<?php echo $alumno['apellidoMaterno']; ?>" required>
                                        <label for="carrera">Carrera:</label>
                                        <select name="carrera" required>
                                            <option value="ITICS" <?php if ($alumno['carrera'] == 'ITICS') echo 'selected'; ?>>ITICS</option>
                                            <option value="IGEM" <?php if ($alumno['carrera'] == 'IGEM') echo 'selected'; ?>>IGEM</option>
                                            <option value="ILOG" <?php if ($alumno['carrera'] == 'ILOG') echo 'selected'; ?>>ILOG</option>
                                            <option value="IAMB" <?php if ($alumno['carrera'] == 'IAMB') echo 'selected'; ?>>IAMB</option>
                                            <option value="IIND" <?php if ($alumno['carrera'] == 'IIND') echo 'selected'; ?>>IIND</option>
                                            <option value="IFER" <?php if ($alumno['carrera'] == 'IFER') echo 'selected'; ?>>IFER</option>
                                        </select>
                                        <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                                        <input type="password" name="contrasena">
                                        <label for="nombreProyecto">Nombre del Proyecto:</label>
                                        <input type="text" name="nombreProyecto" value="<?php echo $alumno['nombreProyecto']; ?>" required>
                                        <label for="empresa">Empresa:</label>
                                        <input type="text" name="empresa" value="<?php echo $alumno['empresa']; ?>" required>
                                        <label for="asesorExterno">Asesor Externo:</label>
                                        <input type="text" name="asesorExterno" value="<?php echo $alumno['asesorExterno']; ?>" required>
                                        <label for="idAsesorInterno">ID Asesor Interno:</label>
                                        <input type="text" name="idAsesorInterno" value="<?php echo $alumno['idAsesorInterno']; ?>" required>
                                        <button type="submit" name="editarAlumno" class="btn btn-primary btn-sm">Guardar Cambios</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="modal fade" id="agregarAlumnoModal" tabindex="-1" aria-labelledby="agregarAlumnoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="agregarAlumnoModalLabel">Agregar Alumno</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <form method="post">
                        <label for="numControl">Num. Control:</label>
                        <input type="text" name="numControl" required>

                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" required>

                        <label for="apellidoPaterno">Apellido Paterno:</label>
                        <input type="text" name="apellidoPaterno" required>

                        <label for="apellidoMaterno">Apellido Materno:</label>
                        <input type="text" name="apellidoMaterno" required>

                        <label for="carrera">Carrera:</label>
                        <select name="carrera" required>
                            <option value="ITICS">ITICS</option>
                            <option value="IGEM">IGEM</option>
                            <option value="ILOG">ILOG</option>
                            <option value="IAMB">IAMB</option>
                            <option value="IIND">IIND</option>
                            <option value="IFER">IFER</option>
                        </select>
                        <button type="submit" name="agregarAlumno" class="btn btn-primary btn-sm">Agregar</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
            <h2>Profesores</h2>
            <form method="GET">
                <input type="text" name="busqueda_profesores" placeholder="Buscar profesores...">
                <button type="submit">Buscar</button>
            </form>
            <div type="button" class="btn principal btn-sm" data-bs-toggle="modal"
                data-bs-target="#agregarProfesorModal">Agregar Profesor</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($profesores as $profesor): ?>
                        <tr>
                            <td>
                                <?php echo $profesor['cve']; ?>
                            </td>
                            <td>
                                <?php echo $profesor['nombre']; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editarProfesorModal<?php echo $profesor['idDocente']; ?>">Editar</button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="eliminarProfesor(<?php echo $profesor['idDocente']; ?>)">Eliminar</button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editarProfesorModal<?php echo $profesor['idDocente']; ?>"
                            tabindex="-1" aria-labelledby="editarProfesorModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="editarProfesorModalLabel">Editar Profesor</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <form method="post">
                                        <input type="hidden" name="idDocente" value="<?php echo $profesor['idDocente']; ?>">
                                        <label for="cve">Clave:</label>
                                        <input type="text" name="cve" value="<?php echo $profesor['cve']; ?>" required>
                                        <label for="nombre">Nombre:</label>
                                        <input type="text" name="nombre" value="<?php echo $profesor['nombre']; ?>" required>
                                        <label for="apellidoPaterno">Apellido Paterno:</label>
                                        <input type="text" name="apellidoPaterno" value="<?php echo $profesor['apellidoPaterno']; ?>" required>
                                        <label for="apellidoMaterno">Apellido Materno:</label>
                                        <input type="text" name="apellidoMaterno" value="<?php echo $profesor['apellidoMaterno']; ?>" required>
                                        <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                                        <input type="password" name="contrasena">
                                        <button type="submit" name="editarProfesor" class="btn btn-primary btn-sm">Guardar Cambios</button>
                                    </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="modal fade" id="agregarProfesorModal" tabindex="-1"
                aria-labelledby="agregarProfesorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="agregarProfesorModalLabel">Agregar Profesor</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
    <form method="post">
        <label for="cve">Clave:</label>
        <input type="text" name="cve" required>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required>
        <label for="apellidoPaterno">Apellido Paterno:</label>
        <input type="text" name="apellidoPaterno" required>
        <label for="apellidoMaterno">Apellido Materno:</label>
        <input type="text" name="apellidoMaterno" required>
        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena">
        <button type="submit" name="agregarProfesor" class="btn btn-primary btn-sm">Agregar</button>
    </form>
</div>
</div>
</div>
</div>
</div>
</main>

<script>
    const mostrarCamposBtn = document.getElementById('mostrarCampos');
    const camposOpcionales = document.getElementById('camposOpcionales');
    const agregarAlumnoForm = document.getElementById('agregarAlumnoForm');

    mostrarCamposBtn.addEventListener('click', () => {
        camposOpcionales.style.display = 'block';
        mostrarCamposBtn.style.display = 'none';
    });

    agregarAlumnoForm.addEventListener('submit', (event) => {
        if (camposOpcionales.style.display === 'none') {
            // Enviar solo los campos obligatorios
            const formData = new FormData(agregarAlumnoForm);
            const datosObligatorios = {};
            formData.forEach((value, key) => {
                datosObligatorios[key] = value;
            });

            // Puedes enviar 'datosObligatorios' al servidor mediante AJAX si lo deseas
            console.log("Datos a enviar:", datosObligatorios);

            // Evitar que el formulario se envíe de forma tradicional
            event.preventDefault();
        }
    });
</script>
<script>
    function eliminarAlumno(idAlumno) {
        Swal.fire({
            title: "¿Estás seguro de eliminar este alumno?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "No, cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = ''; // La misma página
                form.innerHTML = `<input type="hidden" name="idAlumno" value="${idAlumno}">
                                  <input type="hidden" name="eliminarAlumno" value="true">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function eliminarProfesor(idDocente) {
        Swal.fire({
            title: "¿Estás seguro de eliminar este profesor?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "No, cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                form.innerHTML = `<input type="hidden" name="idDocente" value="${idDocente}">
                                  <input type="hidden" name="eliminarProfesor" value="true">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?php require '../layouts/footer.php'; ?>