<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Incidencias_Model.php';
include_once '../Views/Incidencia_Add_View.php';
include_once '../Views/Incidencia_Asignada_List_View.php';
include_once '../Views/Incidencia_Asignar_View.php';
include_once '../Views/Incidencia_Crear_View.php';
include_once '../Views/Incidencia_List_View.php';
include_once '../Views/Incidencia_View_View.php';
include_once '../Views/MESSAGE.php';

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {

    case 'list_all_incidencias':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Becario de infraestructura' || $_SESSION['rol'] === 'Personal de conserjeria') {
            $model = new Incidencias_Model('','','','','','');
            $result = $model->getAllIncidencias(); // Obtén todas las incidencias
            new Incidencia_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'view_incidencia':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Becario de infraestructura' || $_SESSION['rol'] === 'Personal de conserjeria') {
            $ID_Incidencia = $_GET['ID_Incidencia'];
            $model = new Incidencias_Model($ID_Incidencia, '', '', '', '', '');
            $incidencia = $model->getIncidenciaById();
            new Incidencia_View_View($incidencia);
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'assign_incidencia':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Personal de conserjeria') {
            $ID_Incidencia = $_GET['ID_Incidencia'];
            $model = new Incidencias_Model($ID_Incidencia, '', '', '', '', '');
            $incidencia = $model->getIncidenciaById();
    
            if ($incidencia) {  // Aseguramos que la incidencia existe
                // Obtener el ID del centro del recurso relacionado con la incidencia
                $ID_Centro = $incidencia['ID_Centro'];
    
                // Obtener usuarios becarios y conserjes que pertenecen al centro
                $usuarios = $model->getBecariosYConserjesDelCentro($ID_Centro);
                new Incidencia_Asignar_View($incidencia, $usuarios);
            } else {
                new MESSAGE('Incidencia no encontrada.', '../index.php');
            }
        } else {
            header('Location: ../index.php');
        }
        break;
        
    case 'assign':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ID_Incidencia = $_POST['ID_Incidencia'];
            $ID_Usuario = $_POST['ID_Usuario'];
            
            $model = new Incidencias_Model($ID_Incidencia, '', '', '', '', '');
            $resultado = $model->assignIncidencia($ID_Usuario);
            
            new MESSAGE($resultado, 'Incidencias_Controller.php?action=list_all_incidencias');
        } else {
            new MESSAGE('Método no permitido.', '../index.php');
        }
        break;

    case 'add_incidencia':
        if (isset($_GET['ID_Reserva'])) {
            $ID_Reserva = $_GET['ID_Reserva'];
            
            // Crear instancia del modelo y obtener ID_Recurso basado en ID_Reserva
            $model = new Incidencias_Model('', '', '', '', '', '');
            $reservation_data = $model->getRecursoByReserva($ID_Reserva);
    
            if ($reservation_data) {
                // Pasar ID_Reserva y ID_Recurso a la vista
                new Incidencia_Add_View($reservation_data);
            } else {
                new MESSAGE('Error al cargar los datos de la reserva.', '../index.php');
            }
        } else {
            new MESSAGE('Error al cargar los datos de la reserva.', '../index.php');
        }
        break;

    case 'delete_incidencia':
        if (isset($_GET['ID_Incidencia'])) {
            $ID_Incidencia = $_GET['ID_Incidencia'];

            $model   = new Incidencias_Model($ID_Incidencia, '', '', '', '', '');
            $message = $model->delete($ID_Incidencia);

            new MESSAGE($message, 'Incidencias_Controller.php?action=list_all_incidencias');
        } else {
            new MESSAGE('Error al cargar los datos de la incidencia.', '../index.php');
        }
        break;
        
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ID_Usuario = $_POST['ID_Usuario'];
            $ID_Recurso = $_POST['ID_Recurso'];
            $Descripcion_Problema = $_POST['Descripcion_Problema'];
            
            $model = new Incidencias_Model('', $ID_Usuario, $ID_Recurso, $Descripcion_Problema, '', '');
            $resultado = $model->add_incidencia();
            
            new MESSAGE($resultado, '../index.php');
        } else {
            new MESSAGE('Método no permitido.', '../index.php');
        }
        break;

    // Nueva acción para mostrar el formulario de crear incidencia
    case 'crear_incidencia_form':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Personal de conserjeria') {
            $model    = new Incidencias_Model('', '', '', '', '', '');
            $centroID = $_GET['ID_Centro'] ?? null;
            $usuarios = !empty($centroID) 
                ? $model->getBecariosYConserjesDelCentro($centroID) 
                : $model->getBecariosYConserjes();
            $selectedResource = $_GET['ID_Recurso'] ?? null;

            $recursos = $model->getRecursosSinIncidencias(); // Obtener recursos sin incidencias
            new Incidencia_Crear_View($recursos, $usuarios, $selectedResource);
        } else {
            header('Location: ../index.php');
        }
        break;

    // Nueva acción para procesar la creación de la incidencia
    case 'crear_incidencia':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Personal de conserjeria')) {
            $ID_Recurso = $_POST['ID_Recurso'];
            $Descripcion_Problema = $_POST['Descripcion_Problema'];
            $ID_Usuario = $_POST['ID_Usuario'];
            
            $model = new Incidencias_Model('', '', $ID_Recurso, $Descripcion_Problema, '', '');
            $resultado = $model->crearIncidencia($ID_Usuario);
            
            new MESSAGE($resultado, 'Incidencias_Controller.php?action=list_all_incidencias');
        } else {
            new MESSAGE('Método no permitido.', '../index.php');
        }
        break;

    case 'list_incidencias_asignadas':
        if ($_SESSION['rol'] === 'Becario de infraestructura' || $_SESSION['rol'] === 'Personal de conserjeria') {
            $ID_Usuario = $_SESSION['ID_Usuario']; // Asumiendo que el ID del usuario está almacenado en la sesión
            $model = new Incidencias_Model('', '', '', '', '', '');
            $result = $model->getIncidenciasAsignadasPendientes($ID_Usuario);
            new Incidencia_Asignada_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'marcar_resuelta':
        if ($_SESSION['rol'] === 'Becario de infraestructura' || $_SESSION['rol'] === 'Personal de conserjeria') {
            if (isset($_GET['ID_Incidencia'])) {
                $ID_Incidencia = $_GET['ID_Incidencia'];
                $model = new Incidencias_Model('', '', '', '', '', '');
                $resultado = $model->marcarIncidenciaResuelta($ID_Incidencia);
                
                new MESSAGE($resultado, 'Incidencias_Controller.php?action=list_incidencias_asignadas');
            } else {
                new MESSAGE('ID de incidencia no especificado.', 'Incidencias_Controller.php?action=list_incidencias_asignadas');
            }
        } else {
            header('Location: ../index.php');
        }
        break;
        
    default:
        header('Location: ../index.php');
        break;
}

?>
