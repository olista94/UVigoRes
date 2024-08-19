<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Centros_Model.php';
include_once '../Views/Centro_List_View.php';
include_once '../Views/Centro_Edit_View.php';
include_once '../Views/Centro_Add_View.php';
include_once '../Views/Centro_View_View.php';
include_once '../Views/MESSAGE.php';

// Comprobamos que acción está definida
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
    case 'list_centros':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Centros_Model('', '', '', '', '');
            $result = $model->search(); // Obtén todos los centros
            new Centro_List_View($result);
        } else {
            header('Location: ../index.php');
        }
        break;
    
    case 'view_centros':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Centros_Model($_REQUEST['ID_Centro'], '', '', '', '');
            $centro_data = $model->rellenadatos()->fetch_array();
            
            if (!$centro_data) {
                new MESSAGE('Centro no encontrado', 'Centros_Controller.php?action=list_centros');
            } else {
                new Centro_View_View($centro_data);
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'edit_centro':
        if ($_SESSION['rol'] === 'Admin') {
            if (!isset($_POST['ID_Centro'])) {
                $model = new Centros_Model($_REQUEST['ID_Centro'], '', '', '','');
                $centro_data = $model->rellenadatos()->fetch_array();

                if (!$centro_data) {
                    new MESSAGE('Centro no encontrado', 'Centros_Controller.php?action=list_centros');
                    exit();
                }

                new Centro_Edit_View($centro_data);
            } else {
                $data = array(
                    'ID_Centro' => $_POST['ID_Centro'],
                    'Nombre' => $_POST['Nombre'],
                    'Direccion' => $_POST['Direccion'],
                    'Telefono' => $_POST['Telefono'],
                    'Email' => $_POST['Email']
                );

                $model = new Centros_Model(
                    $data['ID_Centro'],
                    $data['Nombre'],
                    $data['Direccion'],
                    $data['Telefono'],
                    $data['Email']
                );
                $result = $model->edit();
                new MESSAGE($result, 'Centros_Controller.php?action=list_centros');
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'add_centro':
        if ($_SESSION['rol'] === 'Admin') {
            if (!isset($_POST['Nombre'])) {
                new Centro_Add_View();
            } else {
                $data = array(
                    'Nombre' => $_POST['Nombre'],
                    'Direccion' => $_POST['Direccion'],
                    'Telefono' => $_POST['Telefono'],
                    'Email' => $_POST['Email']
                );

                $model = new Centros_Model(
                    '',
                    $data['Nombre'],
                    $data['Direccion'],
                    $data['Telefono'],
                    $data['Email']
                );
                $result = $model->add();
                new MESSAGE($result, 'Centros_Controller.php?action=list_centros');
            }
        } else {
            header('Location: ../index.php');
        }
        break;

    case 'delete_centro':
        if ($_SESSION['rol'] === 'Admin') {
            $model = new Centros_Model($_REQUEST['ID_Centro'], '', '', '', '');
            $result = $model->delete();
            new MESSAGE($result, 'Centros_Controller.php?action=list_centros');
        } else {
            header('Location: ../index.php');
        }
        break;

    default:
        header('Location: ../index.php');
        break;
}

?>
