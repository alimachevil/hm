<?php
// config/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si la "llave" de sesión 'user_id' no existe, significa que no ha iniciado sesión.
if (!isset($_SESSION['user_id'])) {
    // Lo redirigimos a la página de login.
    header('Location: login.php');
    exit;
}
?>