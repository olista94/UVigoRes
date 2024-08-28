<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Reservas_Model.php';
include_once '../views/Reserva_List_View.php';
include_once '../views/Reserva_Historico_View.php';
include_once '../views/Reserva_List_User_View.php';
include_once '../views/Reserva_View.php';
include_once '../Views/Reservas_Centros_View.php';
include_once '../Views/Reservas_Tipos_Recursos_View.php';
include_once '../Views/Reservas_Recursos_View.php';
include_once '../Views/Reservas_Recursos_Disponibles_View.php';
include_once '../Views/MESSAGE.php';

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
    case 'select_centro':
        $model = new Reservas_Model('', '', '', '', '', '');
        $centros = $model->getCentros(); // Obtén todos los centros
        new Reservas_Centros_View($centros);
        break;

    case 'select_tipo_recurso':
        if (isset($_POST['ID_Centro'])) {
            $ID_Centro = $_POST['ID_Centro'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $tipos = $model->getTiposRecursosPorCentro($ID_Centro);
            new Reservas_Tipos_Recursos_View($ID_Centro, $tipos);
        } else {
            header('Location: Reservas_Controller.php?action=select_centro');
        }
        break;

    case 'select_recurso':
        if (isset($_POST['ID_Centro']) && isset($_POST['Tipo'])) {
            $ID_Centro = $_POST['ID_Centro'];
            $Tipo = $_POST['Tipo'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $recursos = $model->getRecursosPorTipo($ID_Centro, $Tipo);
            new Reservas_Recursos_View($ID_Centro, $Tipo, $recursos);
        } else {
            header('Location: Reservas_Controller.php?action=select_centro');
        }
        break;

    case 'select_franja':
        if (isset($_POST['ID_Recurso'])) {
            $ID_Recurso = $_POST['ID_Recurso'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $franjas = $model->getFranjasDisponibles($ID_Recurso);
            new Reservas_Recursos_Disponibles_View($ID_Recurso, $franjas);
        } else {
            header('Location: Reservas_Controller.php?action=select_centro');
        }
        break;

    case 'crear_reserva':  // Crea una reserva
        if (isset($_POST['ID_Recurso']) && isset($_POST['ID_Franja'])) {
            $ID_Recurso = $_POST['ID_Recurso'];
            $ID_Franja = $_POST['ID_Franja'];
            $DNI = $_SESSION['login']; // Usar el DNI almacenado en la sesión
    
            // Crear el objeto del modelo
            $model = new Reservas_Model('', '', $ID_Recurso, '', $ID_Franja, 'Pendiente');
            
            // Confirmar la reserva utilizando el DNI para obtener el ID_Usuario
            $resultado = $model->confirmarReserva($DNI);
    
            if ($resultado === "Reserva creada exitosamente") {
                new MESSAGE('Reserva realizada con éxito.', 'Reservas_Controller.php?action=select_centro');
            } else {
                new MESSAGE('Error al realizar la reserva: ' . $resultado, 'Reservas_Controller.php?action=select_centro');
            }
        } else {
            header('Location: Reservas_Controller.php?action=select_centro');
        }
        break;

    case 'ver_reservas':  // Nueva acción para ver reservas por fecha
        $model = new Reservas_Model('', '', '', '', '', '');
        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
        $reservas = $model->get_reservas_by_date($date);
        new Reserva_List_View($reservas, $date);
        break;

    case 'ver_reservas_usuario':
        if (!isset($_SESSION['login']) || $_SESSION['rol'] === 'Admin') {
            header('Location: ../index.php');
        } else {
            $model = new Reservas_Model('', '', '', '', '', '');
            $date = date('Y-m-d');
            $ID_Usuario = $_SESSION['ID_Usuario'];  // Asegúrate de que el ID del usuario esté guardado en la sesión
            $reservas = $model->get_reservas_by_date_user($date, $ID_Usuario);
            new Reserva_List_User_View($reservas);
        }
        break;
    
    case 'confirm_reserva': // Confirma una reserva
        if (isset($_REQUEST['ID_Reserva'])) {
            $ID_Reserva = $_REQUEST['ID_Reserva'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $resultado = $model->crear_reserva($ID_Reserva);
            if ($resultado === "Reserva confirmada exitosamente") {
                new MESSAGE('Reserva confirmada con éxito.', 'Reservas_Controller.php?action=ver_reservas');
            } else {
                new MESSAGE('Error al confirmar la reserva: ' . $resultado, 'Reservas_Controller.php?action=ver_reservas');
            }
        }
        break;

    case 'delete_reserva':
        if (isset($_REQUEST['ID_Reserva'])) {
            $ID_Reserva = $_REQUEST['ID_Reserva'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $resultado = $model->eliminar_reserva($ID_Reserva);
            if ($resultado === "Reserva eliminada exitosamente") {
                new MESSAGE('Reserva eliminada con éxito.', 'Reservas_Controller.php?action=ver_reservas');
            } else {
                new MESSAGE('Error al eliminar la reserva: ' . $resultado, 'Reservas_Controller.php?action=ver_reservas');
            }
        }
        break;

    case 'view_reserva':
        if (isset($_GET['ID_Reserva'])) {
            $ID_Reserva = $_GET['ID_Reserva'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $reserva_details = $model->get_reserva_details($ID_Reserva);
            if ($reserva_details) {
                new Reserva_View($reserva_details);
            } else {
                // Manejar error si no se encuentran los detalles de la reserva
                new MESSAGE('Error al obtener los detalles de la reserva.', 'Reservas_Controller.php?action=list_reservas');
            }
        } else {
            header('Location: Reservas_Controller.php?action=list_reservas');
        }
        break;

    case 'historico':
        $model = new Reservas_Model('', '', '', '', '', '');
        
        if ($_SESSION['rol'] === 'Admin') {
            // Si es admin, obtener todas las reservas
            $reservas = $model->getAllReservas();
            new Reserva_Historico_View($reservas, date('Y-m-d')); // Asumiendo que esta vista ya existe
        } else {
            // Si no es admin, obtener solo las reservas del usuario
            $ID_Usuario = $_SESSION['ID_Usuario'];
            $reservas = $model->getReservasByUser($ID_Usuario);
            new Reserva_Historico_View($reservas); // Vista para usuarios no admin
        }
        break;

    default:
        header('Location: Reservas_Controller.php?action=select_centro');
        break;
}

?>
