<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Recursos_Model.php';
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
        $ID_Centro = $_SESSION['ID_Centro'];
        $model   = new Reservas_Model('', '', '', '', '', '');
        $tipos   = $model->getTiposRecursosPorCentro($ID_Centro)->fetch_all();
        $tipos   = array_map(fn ($tipo) => array_pop($tipo), $tipos);
        $franjas = $model->getFranjasDisponibles();

        if ($_SESSION['rol'] === 'Estudiante') {
            $tipos = array_filter($tipos, fn (string $tipo) => in_array($tipo, ['Portátil', 'Seminario', 'Otros']));
        }

        new Reservas_Tipos_Recursos_View($ID_Centro, $tipos, $franjas);
        break;

    case 'select_recurso':
        $centerId = $_POST['ID_Centro'] ?? null;
        $type     = $_POST['Tipo'] ?? null;
        $day      = $_POST['day'] ?? null;
        $franja   = $_POST['ID_Franja'] ?? null;

        if (in_array(null, [$centerId, $type, $day, $franja])) {
            header('Location: Reservas_Controller.php?action=select_centro');
            break;
        }

        $model    = new Reservas_Model('', '', '', '', '', '');
        $recursos = $model->getAvailableResources($centerId, $type, $day, $franja);

        new Reservas_Recursos_View($centerId, $type, $recursos, $franja, $day);

        break;

    case 'crear_reserva':  // Crea una reserva
        $centerId   = $_POST['ID_Centro'] ?? null;
        $type       = $_POST['Tipo'] ?? null;
        $day        = $_POST['Day'] ?? null;
        $franja     = $_POST['Franja'] ?? null;
        $resourceId = $_POST['ID_Recurso'] ?? null;

        if (in_array(null, [$centerId, $type, $day, $franja, $resourceId])) {
            header('Location: Reservas_Controller.php?action=select_tipo_recurso');
        }

        $DNI = $_SESSION['login']; // Usar el DNI almacenado en la sesión

        // Crear el objeto del modelo
        $model = new Reservas_Model('', '', $resourceId, '', $franja, 'Pendiente', $day);
        
        // Confirmar la reserva utilizando el DNI para obtener el ID_Usuario
        $resultado = $model->confirmarReserva($DNI);

        if ($resultado === "Reserva creada exitosamente") {
            new MESSAGE('Reserva realizada con éxito.', 'Reservas_Controller.php?action=historico');
        } else {
            new MESSAGE('Error al realizar la reserva: ' . $resultado, 'Reservas_Controller.php?action=select_tipo_recurso');
        }
        break;

    case 'ver_reservas':  // Nueva acción para ver reservas por fecha
        $model = new Reservas_Model('', '', '', '', '', '', '', '');
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
            if (trim($resultado) === "Reserva confirmada exitosamente") {
                new MESSAGE('Reserva confirmada con éxito.', 'Reservas_Controller.php?action=ver_reservas');
            } else {
                new MESSAGE(' ' . $resultado, 'Reservas_Controller.php?action=ver_reservas');
            }
        }
        break;

    case 'devuelta': // Confirma reserva devuelta
        if (isset($_REQUEST['ID_Reserva'])) {
            $ID_Reserva = $_REQUEST['ID_Reserva'];
            $model = new Reservas_Model('', '', '', '', '', '');
            $resultado = $model->devolver_reserva($ID_Reserva);
            if (trim($resultado) === "Reserva devuelta") {
                new MESSAGE('Reserva devuelta con éxito.', 'Reservas_Controller.php?action=ver_reservas');
            } else {
                new MESSAGE(' ' . $resultado, 'Reservas_Controller.php?action=ver_reservas');
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
        
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Personal de conserjeria') {
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
