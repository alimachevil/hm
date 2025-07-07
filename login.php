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

define('PASSWORD_HASH_VALIDO', '$2y$10$UYSQgGxA1XbiL5wzatfe2uYj6ia5P2zOYChJ1B3WikRkf/fIuK8by');

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
    <title>Login - Sistema Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            font-family: 'Poppins', sans-serif; 
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(-45deg, #4A55A2, #7895CB, #A0BFE0, #C5DFF8);
            background-size: 400% 400%;
            animation: gradient-animation 15s ease infinite;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            
            /* Efecto "Glassmorphism" (vidrio esmerilado) */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            
            /* Animación de entrada */
            opacity: 0;
            transform: scale(0.95) translateY(20px);
            animation: fade-in-up 0.6s ease-out forwards;
        }

        @keyframes fade-in-up {
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .card-title {
            font-weight: 600;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            color: black;
        }

        .form-label {
            font-weight: 500;
            color: black;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: black;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
            color: black;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            color: black;
        }
        
        /* Contenedor para el campo de contraseña y el botón de ver */
        .password-wrapper {
            position: relative;
        }

        #toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            background: none;
            border: none;
            padding: 0;
        }
        
        #toggle-password:hover {
            color: #fff;
        }

        .btn-primary {
            background-color: #fff;
            color: #4A55A2;
            border: none;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="card-title text-center mb-4">Bienvenido al Sistema</h3>
        <form method="POST" action="login.php">
            <?php if ($error): ?>
                <div class="alert alert-danger bg-white bg-opacity-25 text-white border-0"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <!-- Botón para mostrar/ocultar contraseña -->
                    <button type="button" id="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>

    <!-- JavaScript para la funcionalidad de ver contraseña -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            const eyeIcon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function() {
                // Cambia el tipo de input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Cambia el ícono
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>