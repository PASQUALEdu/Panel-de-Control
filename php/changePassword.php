<?php
session_start();
include('../db/db.php'); // Ajusta la ruta según tu estructura

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, devolvemos un error (podrías redirigir o enviar mensaje)
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autenticado.']);
    exit();
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nueva_contraseña = mysqli_real_escape_string($conexion, $_POST['nueva_contraseña']);
    $confirmar_contraseña = mysqli_real_escape_string($conexion, $_POST['confirmar_contraseña']);

    // Verificar que los campos no estén vacíos y que las contraseñas coincidan
    if (empty($nueva_contraseña) || empty($confirmar_contraseña)) {
        $response['error'] = "Todos los campos son requeridos.";
    } elseif ($nueva_contraseña != $confirmar_contraseña) {
        $response['error'] = "Las contraseñas no coinciden.";
    } else {
        $usuario = $_SESSION['usuario'];
        $consulta = "UPDATE personal SET password = '$nueva_contraseña' WHERE usuario = '$usuario'";
        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado) {
            $response['mensaje'] = "Contraseña cambiada exitosamente.";
        } else {
            $response['error'] = "Hubo un problema al actualizar la contraseña. Intenta de nuevo.";
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
