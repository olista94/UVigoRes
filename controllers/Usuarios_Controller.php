<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Usuarios_Model.php';
include_once '../Views/Usuario_List_View.php';
include_once '../Views/Usuario_Edit_View.php';
include_once '../Views/Usuario_Add_View.php';
include_once '../Views/Usuario_View_View.php';
include_once '../Views/MESSAGE.php';

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
    case 'list_users':
        if ($_SESSION['rol'] === 'Admin') {
            include_once '../Models/Usuarios_Model.php';
    
            $search = '';
            if (isset($_POST['search'])) {
                $search = $_POST['search'];
            }
    
            // Crear una instancia del modelo
            $user_model = new Usuarios_Model('', '', '', '', '', '', '', '');
    
            // Obtener los resultados de la búsqueda, o todos los usuarios si no hay término de búsqueda
            if ($search !== '') {
                $result = $user_model->search_by_term($search);
            } else {
                $result = $user_model->search(); // Llamada a la función search para obtener todos los usuarios
            }
    
            // Mostrar la vista con los resultados
            include_once '../Views/Usuario_List_View.php';
            new Usuario_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;    

    case 'search_users':
        if ($_SESSION['rol'] === 'Admin') {
            $search_query = $_POST['search_query'];
            $model = new Usuarios_Model('', '', '', '', '', '', '', '');
            $result = $model->search($search_query); // Modifica tu método search() para aceptar un parámetro de búsqueda
            new Usuario_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'view_user':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Usuarios_Model('', '', '', '', $_REQUEST['DNI'], '', '', '');
            $user_data = $model->rellenadatos()->fetch_array();
            
            if (!$user_data) {
                new MESSAGE('Usuario no encontrado', 'Usuarios_Controller.php?action=list_users');
            } else {
                new Usuario_View_View($user_data);
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'edit_user':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['login'] === $_REQUEST['DNI']) {
            if (!isset($_POST['DNI'])) {
                $dni_to_edit = $_SESSION['rol'] === 'Admin' ? $_REQUEST['DNI'] : $_SESSION['login'];
                $model = new Usuarios_Model('', '', '', '', $dni_to_edit, '', '', '');
                $user_data = $model->rellenadatos()->fetch_array();
    
                if (!$user_data) {
                    new MESSAGE('Usuario no encontrado', 'Usuarios_Controller.php?action=list_users');
                    exit();
                }
    
                new Usuario_Edit_View($user_data);
            } else {
    
                $data = array(
                    'ID_Usuario' => $_POST['ID_Usuario'],
                    'NIU' => $_POST['NIU'],
                    'Nombre' => $_POST['Nombre'],
                    'Apellidos' => $_POST['Apellidos'],
                    'DNI' => $_POST['DNI'],
                    'Email' => $_POST['Email'],
                    'Rol' => $_POST['Rol'],
                    'Contrasena' => $_POST['Contrasena']
                );
    
                $model = new Usuarios_Model(
                    $data['ID_Usuario'],
                    $data['NIU'],
                    $data['Nombre'],
                    $data['Apellidos'],
                    $data['DNI'],
                    $data['Email'],
                    $data['Rol'],
                    $data['Contrasena']
                );
                $result = $model->edit();
                new MESSAGE($result, 'Usuarios_Controller.php?action=list_users');
            }
        } else {
            header('Location: ../index.php');
        }
        break; 
        
    case 'add_user':
        if ($_SESSION['rol'] === 'Admin') {
            if (!isset($_POST['DNI'])) {
                new Usuario_Add_View();
            } else {
                $data = array(
                    'NIU' => $_POST['NIU'],
                    'Nombre' => $_POST['Nombre'],
                    'Apellidos' => $_POST['Apellidos'],
                    'DNI' => $_POST['DNI'],
                    'Email' => $_POST['Email'],
                    'Rol' => $_POST['Rol'],
                    'Contrasena' => $_POST['Contrasena']
                );

                $model = new Usuarios_Model(
                    '',
                    $data['NIU'],
                    $data['Nombre'],
                    $data['Apellidos'],
                    $data['DNI'],
                    $data['Email'],
                    $data['Rol'],
                    $data['Contrasena']
                );
                $result = $model->registrar();
                new MESSAGE($result, 'Usuarios_Controller.php?action=list_users');
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'delete_user':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Usuarios_Model('', '', '', '', $_REQUEST['DNI'], '', '', '');
            $result = $model->delete();
            new MESSAGE($result, 'Usuarios_Controller.php?action=list_users');
        } else {
            header('Location: ../index.php');
        }
        break;

    default:
        header('Location: ../index.php');
        break;
}

function validarDNI($dni) {
    $letras = "TRWAGMYFPDXBNJZSQVHLCKET";
    if (preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
        $numero = substr($dni, 0, -1);
        $letra = strtoupper(substr($dni, -1));
        return ($letra == $letras[$numero % 23]);
    }
    return false;
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

?>
