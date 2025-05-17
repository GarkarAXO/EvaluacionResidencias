<?php
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Usuario de la BD
$password = ""; // Contraseña de la BD
$database = "evaluacionres"; // Nombre de la BD

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
