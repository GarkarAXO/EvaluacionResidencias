<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones - Residencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../tools/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php"> <!-- Redirige al index principal -->
                <img src="../img/itgam.png" alt="Logo" height="40" style="display: inline-block; vertical-align: middle;">
                <h5 style="display: inline-block; margin-left: 10px; vertical-align: middle;">Evaluaciones Residencias</h5>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li class="nav-item">
                            <span class="nav-link text-white">
                                <?php
                                $nombreCompleto = trim($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellidoPaterno'] . ' ' . $_SESSION['usuario']['apellidoMaterno']);
                                echo (!empty($_SESSION['usuario']['nombre'])) ? $nombreCompleto : ($_SESSION['rol'] === 'alumno' ? $_SESSION['usuario']['numControl'] : $_SESSION['usuario']['cve']);
                                ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../php/logout.php" id="cerrarSesion">Cerrar sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../index.php">Inicio</a> <!-- Cambié "Iniciar sesión" por "Inicio" -->
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

