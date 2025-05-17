<?php
session_start();
require '../php/dbConexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = $_POST['identificador'];
    $contrasena = $_POST['contrasena'];
    $hashedPassword = hash('sha256', $contrasena); // Convertir la contraseña a SHA-256

    // Verificar si es administrador
    $stmt = $conn->prepare("SELECT idAdmin, nombre, apellidoPaterno, apellidoMaterno, contrasena FROM Administrador WHERE nombre = ?");
    $stmt->bind_param("s", $identificador);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($hashedPassword === $row['contrasena']) {  // Comparar hash de contraseña
            $_SESSION['usuario'] = $row;
            $_SESSION['rol'] = 'administrador';
            header("Location: dashboard_admin.php");
            exit();
        }
    }
    $stmt->close();

    // Verificar si es profesor
    $stmt = $conn->prepare("SELECT idDocente, nombre, apellidoPaterno, apellidoMaterno, contrasena FROM Profesor WHERE cve = ?");
    $stmt->bind_param("s", $identificador);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($contrasena, $row['contrasena'])) { // Comparar hash de contraseña
            $_SESSION['usuario'] = $row;
            $_SESSION['rol'] = 'profesor';
            header("Location: profesor_home.php");
            exit();
        }
    }
    $stmt->close();

    // Verificar si es alumno
    $stmt = $conn->prepare("SELECT idAlumno, nombre, apellidoPaterno, apellidoMaterno, contrasena FROM Alumno WHERE numControl = ?");
    $stmt->bind_param("s", $identificador);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($contrasena, $row['contrasena'])) { // Comparar hash de contraseña
            $_SESSION['usuario'] = $row;
            $_SESSION['rol'] = 'alumno';
            header("Location: alumno_home.php");
            exit();
        }
    }
    $stmt->close();
    $conn->close();

    // Si las credenciales son incorrectas, mostrar SweetAlert2
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Credenciales incorrectas. Inténtalo de nuevo.',
                confirmButtonColor: '#1b396a'
            });
        });
    </script>";
}
if (isset($_SESSION['usuario'])) {
    // Redirige según el rol del usuario
    if ($_SESSION['rol'] === 'administrador') {
        header("Location: vistas/dashboard_admin.php");
    } elseif ($_SESSION['rol'] === 'profesor') {
        header("Location: vistas/profesor_home.php");
    } elseif ($_SESSION['rol'] === 'alumno') {
        header("Location: vistas/alumno_home.php");
    }
    exit();
}
?>

<?php
require '../layouts/header2.php';
?>
<main class="pt-5">
<form method="POST" class="container small-form">
    <div class="mb-2">
        <label for="identificador" class="form-label small-label">Clave/Num. Control:</label>
        <input type="text" class="form-control form-control-sm" id="identificador" name="identificador" required>
    </div>
    <div class="mb-2">
        <label for="contrasena" class="form-label small-label">Contraseña:</label>
        <input type="password" class="form-control form-control-sm" id="contrasena" name="contrasena" required>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Iniciar Sesión</button>
    </div>
</form>
    <p class="auth-message">¿No tienes una cuenta? <a href="registro.php">Registrate aquí</a>.</p>
</main>
<?php
require '../layouts/footer.php';
?>

