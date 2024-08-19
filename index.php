<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UVigoRes</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
</head>
<body>
    <div class="container">
        <?php
        session_start();

        // Verifica si el usuario está autenticado
        if (!isset($_SESSION['login'])) {
            header('Location: Controllers/Login_Controller.php');
            exit();
        }

        // Obtener el rol del usuario desde la sesión
        $user_role = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

        // Mostrar la bienvenida al usuario
        echo "<header><h1>Bienvenido a UVigoRes, " . $_SESSION['nombre'] . " " . $_SESSION['apellidos'] . "</h1></header>";

        // Mostrar opciones del menú basado en el rol del usuario
        echo "<div class='menu'>";
        if ($user_role === 'Admin') {
            echo "<a href='Controllers/Usuarios_Controller.php?action=list_users'>Gestion de usuarios</a>";
            echo "<a href='Controllers/Centros_Controller.php?action=list_centros'>Gestion de centros</a>";
            echo "<a href='Controllers/Recursos_Controller.php?action=list_recursos'>Gestion de recursos</a>";
        } else {
            echo "<a href='Controllers/Usuarios_Controller.php?action=edit_user&DNI=" . urlencode($_SESSION['login']) . "'>Editar Usuario</a>";
        }
        echo "<a href='Controllers/Reserva_Controller.php?action=reservar'>Reservar recurso</a>";
        echo "<a href='Controllers/Login_Controller.php?action=Confirmar_DESCONECTAR'>Cerrar Sesión</a>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
