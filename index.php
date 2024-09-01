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
        echo "<header><h1>Bienvenido a UVigoRes, " . htmlspecialchars($_SESSION['nombre']) . " " . htmlspecialchars($_SESSION['apellidos']) . "</h1></header>";

        // Mostrar opciones del menú basado en el rol del usuario
        echo "<div class='menu'>";

        if ($user_role === 'Admin') {
            // Opciones para el rol Admin
            echo "<a href='Controllers/Usuarios_Controller.php?action=list_users'>Gestión de usuarios</a>";
            echo "<a href='Controllers/Centros_Controller.php?action=list_centros'>Gestión de centros</a>";
            echo "<a href='Controllers/Recursos_Controller.php?action=list_recursos'>Gestión de recursos</a>";
            echo "<a href='Controllers/Reservas_Controller.php?action=ver_reservas'>Ver reservas del día</a>";
            echo '<a href="Controllers/Reservas_Controller.php?action=historico">Histórico de reservas</a>';
            echo "<a href='Controllers/Incidencias_Controller.php?action=list_all_incidencias'>Ver todas las incidencias</a>";
        }

        if ($user_role === 'Personal de conserjeria') {
            // Opciones para el rol Personal de conserjeria y Becario de infraestructura
            echo "<a href='Controllers/Reservas_Controller.php?action=ver_reservas'>Ver reservas del día</a>";
            echo '<a href="Controllers/Reservas_Controller.php?action=historico">Histórico de reservas</a>';
            echo "<a href='Controllers/Incidencias_Controller.php?action=list_all_incidencias'>Ver todas las incidencias</a>";
            echo "<a href='Controllers/Incidencias_Controller.php?action=list_incidencias_asignadas'>Ver mis incidencias asignadas</a>";
        }

        if ($user_role === 'Becario de infraestructura') {
            // Opciones para el rol Personal de conserjeria y Becario de infraestructura
            echo "<a href='Controllers/Incidencias_Controller.php?action=list_all_incidencias'>Ver todas las incidencias</a>";
            echo "<a href='Controllers/Incidencias_Controller.php?action=list_incidencias_asignadas'>Ver mis incidencias asignadas</a>";
        }

        if ($user_role === 'Docente' ||  $user_role === 'Estudiante') {
            // Opciones para usuarios no administradores
            echo "<a href='Controllers/Usuarios_Controller.php?action=edit_user&DNI=" . urlencode($_SESSION['login']) . "'>Editar usuario</a>";
            echo "<a href='Controllers/Reservas_Controller.php?action=ver_reservas_usuario'>Ver mis reservas del día</a>";
            echo '<a href="Controllers/Reservas_Controller.php?action=historico">Histórico de Reservas</a>';
        }

        // Opción común para todos los usuarios
        echo "<a href='Controllers/Reservas_Controller.php?action=select_centro'>Reservar recurso</a>";
        echo "<a href='Controllers/Login_Controller.php?action=Confirmar_DESCONECTAR'>Cerrar Sesión</a>";

        echo "</div>";
        ?>
    </div>
</body>
</html>
