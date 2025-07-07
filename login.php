<?php
// login.php (VERSIÓN SEGURA CON HASH)
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// --- CREDENCIALES SEGURAS ---
// Guardamos el nombre de usuario y el HASH de la contraseña
define('USUARIO_VALIDO', 'admin');

// PEGA AQUÍ EL HASH QUE GENERASTE EN EL PASO ANTERIOR
define('PASSWORD_HASH_VALIDO', '$2y$10$UYSQgGxA1XbiL5wzatfe2uYj6ia5P2zOYChJ1B3WikRkf/fIuK8by'); // Reemplaza esto con tu hash

$error = '';

// --- LÓGICA DE VERIFICACIÓN ACTUALIZADA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password_ingresada = $_POST['password'] ?? '';

    // Comparamos el usuario y luego verificamos la contraseña ingresada contra el hash guardado.
    if ($usuario === USUARIO_VALIDO && password_verify($password_ingresada, PASSWORD_HASH_VALIDO)) {
        
        // ¡Las credenciales son correctas!
        $_SESSION['user_id'] = 1; 
        $_SESSION['username'] = $usuario;
        
        header('Location: index.php');
        exit;

    } else {
        // Credenciales incorrectas
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f2f5;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="card login-card shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>
            <form method="POST" action="login.php">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>