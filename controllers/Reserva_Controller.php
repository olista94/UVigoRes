<?php

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: Login_Controller.php');
    exit();
}

include_once '../Models/Reserva_Model.php';
include_once '../Views/Menu_View.php';
include_once '../Views/Reserva_View.php';
include_once '../Views/MESSAGE.php';

function getDataForm() {
    $ID_Recurso = $_REQUEST['ID_Recurso'];
    $Fecha_Hora_Reserva = $_REQUEST['Fecha_Hora_Reserva'];
    $Codigo_QR = 'QR' . uniqid();
    $Estado = 'No Confirmada';

    return array(
        'ID_Usuario' => $_SESSION['ID_Usuario'],
        'ID_Recurso' => $ID_Recurso,
        'Fecha_Hora_Reserva' => $Fecha_Hora_Reserva,
        'Codigo_QR' => $Codigo_QR,
        'Estado' => $Estado
    );
}

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
    case 'menu':
        new Menu_View();
        break;

    case 'reservar':
        if (!isset($_POST['ID_Recurso'])) {
            new Reserva_View();
        } else {
            $data = getDataForm();
            $reserva = new Reserva_Model(
                '',
                $data['ID_Usuario'],
                $data['ID_Recurso'],
                $data['Fecha_Hora_Reserva'],
                $data['Codigo_QR'],
                $data['Estado']
            );
            $respuesta = $reserva->reservar();
            new MESSAGE($respuesta, '../index.php');
        }
        break;

    default:
        new Menu_View();
}

?>
