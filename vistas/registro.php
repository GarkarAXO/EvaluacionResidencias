<?php
session_start();
require '../php/dbConexion.php'; // Archivo de conexión a la base de datos
require '../layouts/header2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoUsuario = $_POST['tipoUsuario'];
    $identificador = $_POST['identificador'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);

    if ($tipoUsuario === 'asesor') {
        $stmt = $conn->prepare("SELECT idDocente, nombre, apellidoPaterno, apellidoMaterno FROM Profesor WHERE cve = ?");
    } else {
        $stmt = $conn->prepare("SELECT idAlumno, nombre, apellidoPaterno, apellidoMaterno FROM Alumno WHERE numControl = ?");
    }

    $stmt->bind_param("s", $identificador);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($tipoUsuario === 'asesor') {
            $query = $conn->prepare("UPDATE Profesor SET contrasena = ? WHERE idDocente = ?");
            $query->bind_param("si", $contrasena, $row['idDocente']);
        } else {
            $query = $conn->prepare("UPDATE Alumno SET contrasena = ? WHERE idAlumno = ?");
            $query->bind_param("si", $contrasena, $row['idAlumno']);
        }
        $query->execute();
        
        // Mensaje de éxito con SweetAlert2
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Registro exitoso',
                text: 'Ahora puedes iniciar sesión.',
                confirmButtonColor: '#1b396a'
            }).then(() => {
                setTimeout(() => { // Retraso de 1 segundo
                    window.location.href = 'login.php';
                }, 1000); // 1000 milisegundos = 1 segundo
            });
        </script>";
    } else {
        // Mensaje de error con SweetAlert2
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Identificador no encontrado. Contacta al administrador.',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
    $stmt->close();
    $conn->close();
}


?>
<body>
    <main class="pt-4">
    <form method="POST" class="container small-form">
    <div class="mb-2">  <label for="tipoUsuario" class="form-label small-label">Tipo de usuario:</label>
        <select class="form-select form-select-sm" id="tipoUsuario" name="tipoUsuario" required>
            <option value="asesor">Asesor</option>
            <option value="residente">Residente</option>
        </select>
    </div>
    <div class="mb-2">
        <label for="identificador" class="form-label small-label">Clave/Num. Control:</label>
        <input type="text" class="form-control form-control-sm" id="identificador" name="identificador" required>
    </div>
    <div class="mb-2">
        <label for="contrasena" class="form-label small-label">Contraseña:</label>
        <input type="password" class="form-control form-control-sm" id="contrasena" name="contrasena" required>
    </div>
    <div class="d-grid gap-2"> <button type="submit" class="btn btn-primary btn-sm">Registrarse</button>
    </div>
</form>

        <p class="auth-message">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
    </main>
<?php
require '../layouts/footer.php';
?>
