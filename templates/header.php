<?php require_once __DIR__ . '/../config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="es">
<!-- templates/header.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Hotel Admin' : 'Hotel Admin'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- NUEVAS FUENTES: Inter y Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="d-flex">
    <!-- ==================================================== -->
    <!--                      SIDEBAR                         -->
    <!-- ==================================================== -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h3>Hotel Admin</h3>
        </div>
        <ul class="list-unstyled components">
            <!-- Marcamos como 'active' la página actual -->
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php"><i class="fas fa-bed"></i> <span>Habitaciones</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>">
                <a href="clientes.php"><i class="fas fa-users"></i> <span>Clientes</span></a>
            </li>
            <li>
                <a href="reportes.php"><i class="fas fa-chart-line"></i> <span>Reportes</span></a>
            </li>
            <li>
                <a href="productos.php"><i class="fas fa-box-open"></i> <span>Productos</span></a>
            </li>
            <li class="mt-auto"> <!-- mt-auto lo empuja hacia abajo -->
                <a href="logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-toggler-wrapper">
             <button type="button" id="sidebarCollapse" class="btn btn-light">
                <i class="fas fa-align-left"></i>
                <span></span>
            </button>
        </div>
    </nav>