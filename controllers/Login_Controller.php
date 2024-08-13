<?php

// Creamos la sesión
session_start();

// Variable del idioma
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'SPANISH'; // Por defecto el idioma en español
    $idioma = 'SPANISH'; // Español
} else {
    $idioma = $_SESSION['idioma']; // Idioma en la variable
}

// Incluimos los archivos necesarios
include_once '../Locales/Strings_'.$idioma.'.php';
include_once "../Models/Usuarios_Model.php";
include_once "../Views/Login_View.php";
include_once "../Views/MESSAGE.php";

// Función para recoger los datos del formulario
function getDataForm() {
    $DNI = $_REQUEST['login']; // DNI como login
    $Contrasena = $_REQUEST['password']; // Contraseña

    return array('DNI' => $DNI, 'Contrasena' => $Contrasena);
}

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

// Según la acción elegida
switch ($_REQUEST['action']) {

    // Queremos hacer login
    case 'Confirmar_LOGIN':
        // Recogemos los datos del formulario
        $data = getDataForm();
        $DNI = $data['DNI'];
        $Contrasena = $data['Contrasena'];

        // Creamos el objeto usuario con el DNI y contraseña
        $usuario = new Usuarios_Model('', '', '', '', $DNI, '', '', $Contrasena);
        $respuesta = $usuario->login(); // Hacemos login y guardamos respuesta

        // Si la respuesta es afirmativa
        if ($respuesta === true) {
            $_SESSION['login'] = $DNI; // Guardamos datos de sesión
            // Recogemos más datos del usuario, si es necesario
            $usuario->rellenadatos(); // Esto debería devolver los datos del usuario

            // Almacena más detalles del usuario si es necesario
            $usuarioData = $usuario->rellenadatos()->fetch_array();
            $_SESSION['ID_Usuario'] = $usuarioData['ID_Usuario'];
            $_SESSION['nombre'] = $usuarioData['Nombre'];
            $_SESSION['apellidos'] = $usuarioData['Apellidos'];
            $_SESSION['correo'] = $usuarioData['Correo_Electronico'];
            $_SESSION['rol'] = $usuarioData['Rol'];

            header('Location: ../index.php'); // Redirige al índice
        } else {
            new MESSAGE($respuesta, './Login_Controller.php'); // Muestra el mensaje de error
        }
        break;

    // Queremos hacer logout
    // case 'Confirmar_DESCONECTAR':
    //     // Destruir la sesión
    //     session_unset();
    //     session_destroy();
    //     header('Location: ../index.php'); // Redirige al índice después de logout
    //     break;
    //Si queremos hacer logout
	case 'Confirmar_DESCONECTAR':
		include '../Functions/Desconectar.php'; //Desconectamos
	break;

    // Por defecto
    default:
        new Login_View(); // Muestra la vista de login
}

?>
