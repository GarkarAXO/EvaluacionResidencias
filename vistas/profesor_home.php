<?php
require '../layouts/headerS.php';
?>
<main>
    <div class="container mt-5">
        <h1 class="text-center">Bienvenido <?php
                                $nombreCompleto = trim($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellidoPaterno'] . ' ' . $_SESSION['usuario']['apellidoMaterno']);
                                echo (!empty($_SESSION['usuario']['nombre'])) ? $nombreCompleto : ($_SESSION['rol'] === 'alumno' ? $_SESSION['usuario']['numControl'] : $_SESSION['usuario']['cve']);
                                ?> </h1>
    </div>
</main>
<?php
require '../layouts/footer.php';
?>
