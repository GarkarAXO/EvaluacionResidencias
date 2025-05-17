<?php
session_start();
require '../php/dbConexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_datos'])) {
    $idAlumno = $_POST['idAlumno'];
    $nombreProyecto = $_POST['nombreProyecto'];
    $empresa = $_POST['empresa'];
    $asesorExterno = $_POST['asesorExterno'];
    $idAsesorInterno = $_POST['idAsesorInterno']; // 🔹 Se corrigió el nombre para que coincida con el fetch

    try {
        // Actualizar datos del alumno
        $stmt = $conn->prepare("UPDATE alumno SET nombreProyecto = ?, empresa = ?, asesorExterno = ?, idAsesorInterno = ? WHERE idAlumno = ?");
        $stmt->bind_param("ssssi", $nombreProyecto, $empresa, $asesorExterno, $idAsesorInterno, $idAlumno);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }

    $conn->close();
    exit();
}
?>
