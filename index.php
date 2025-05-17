<?php
session_start();

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
require 'layouts/header.php';
?>

<main class="pb-3">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1 class="text-center">Bienvenido a la Plataforma de Evaluaciones</h1>
                <p class="text-center">Por favor, inicia sesión o regístrate para continuar.</p>
            </div>
            <div class="col-md-6">
                <img src="img/2.png" alt="Evaluaciones" class="img-fluid">
            </div>
        </div>

        <section id="descripcion-plataforma">
    <h2>¿Por qué usar nuestra plataforma de evaluación?</h2>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr class="table-dark">
                <th>Beneficio</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Evaluar de forma objetiva</td>
                <td>Facilita la evaluación basada en criterios claros y definidos.</td>
            </tr>
            <tr>
                <td>Recibir feedback valioso</td>
                <td>Permite a asesores y residentes recibir retroalimentación constructiva sobre su desempeño.</td>
            </tr>
            <tr>
                <td>Identificar áreas de mejora</td>
                <td>Ayuda a reconocer las fortalezas y debilidades para enfocar el desarrollo profesional.</td>
            </tr>
            <tr>
                <td>Mejorar la comunicación</td>
                <td>Fomenta el diálogo y la colaboración entre asesores y residentes.</td>
            </tr>
        </tbody>
    </table>
    <p>
        ¡Únete a nuestra plataforma y contribuye a tu crecimiento y al de tus compañeros!
    </p>
</section>

    <section id="preguntas-frecuentes">
    <h2>Preguntas frecuentes</h2>
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    ¿Quién puede evaluar a quién?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Los residentes pueden evaluar a sus asesores internos, y los asesores internos pueden evaluar a los residentes.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    ¿Qué tipo de criterios se utilizan en la evaluación?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    La evaluación se basa en criterios definidos que abarcan diferentes aspectos del desempeño, como conocimientos técnicos, habilidades de comunicación, compromiso, etc.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    ¿Es confidencial la información de las evaluaciones?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Sí, la información de las evaluaciones es confidencial y solo puede ser vista por el evaluador y el evaluado.
                </div>
            </div>
        </div>
    </div>
</section>
    </div>

</main>

<?php
require 'layouts/footer.php';
?>