<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Recursos_Model.php';
include_once '../Views/Resource_List_View.php';
include_once '../Views/Resource_Edit_View.php';
include_once '../Views/Resource_Add_View.php';
include_once '../Views/Resource_View_View.php';
include_once '../Views/MESSAGE.php';

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
    case 'list_recursos':
        if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Gestor') {
            $model = new Recursos_Model('', '', '', '');
            $result = $model->search(); // Obtén todos los recursos
            new Resource_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;
    
    case 'view_recursos':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Recursos_Model($_REQUEST['ID_Recurso'], '', '', '');
            $recurso_data = $model->rellenadatos()->fetch_array();
            
            if (!$recurso_data) {
                new MESSAGE('Recurso no encontrado', 'Recursos_Controller.php?action=list_recursos');
            } else {
                new Resource_View_View($recurso_data);
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'edit_recurso':
        if ($_SESSION['rol'] === 'Admin') {
            if (!isset($_POST['ID_Recurso'])) {
                $model = new Recursos_Model($_REQUEST['ID_Recurso'], '', '', '');
                $recurso_data = $model->rellenadatos()->fetch_array();

                if (!$recurso_data) {
                    new MESSAGE('Recurso no encontrado', 'Recursos_Controller.php?action=list_recursos');
                    exit();
                }

                new Resource_Edit_View($recurso_data);
            } else {
                $data = array(
                    'ID_Recurso' => $_POST['ID_Recurso'],
                    'Tipo' => $_POST['Tipo'],
                    'Descripcion' => $_POST['Descripcion'],
                    'Disponibilidad' => $_POST['Disponibilidad']
                );

                $model = new Recursos_Model(
                    $data['ID_Recurso'],
                    $data['Tipo'],
                    $data['Descripcion'],
                    $data['Disponibilidad']
                );
                $result = $model->edit();
                new MESSAGE($result, 'Recursos_Controller.php?action=list_recursos');
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'add_recurso':
        if ($_SESSION['rol'] === 'Admin') {
            if (!isset($_POST['Tipo'])) {
                new Resource_Add_View();
            } else {
                $data = array(
                    'Tipo' => $_POST['Tipo'],
                    'Descripcion' => $_POST['Descripcion'],
                    'Disponibilidad' => $_POST['Disponibilidad']
                );

                $model = new Recursos_Model(
                    '',
                    $data['Tipo'],
                    $data['Descripcion'],
                    $data['Disponibilidad']
                );
                $result = $model->add();
                new MESSAGE($result, 'Recursos_Controller.php?action=list_recursos');
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'delete_recurso':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Recursos_Model($_REQUEST['ID_Recurso'], '', '', '');
            $result = $model->delete();
            new MESSAGE($result, 'Recursos_Controller.php?action=list_recursos');
        } else {
            header('Location: ../index.php');
        }
        break;

    default:
        header('Location: ../index.php');
        break;
}

?>
