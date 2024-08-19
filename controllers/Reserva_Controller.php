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

class Reserva_Controller {

    function __construct() {
        // if (isset($_GET['action']) && $_GET['action'] == 'reservar') {
        //     $this->reservar();
        // } else {
        //     $this->mostrarFormularioReserva();
        // }
        $this->mostrarFormularioReserva();
    }

    // Muestra el formulario de reserva con los recursos disponibles
    function mostrarFormularioReserva() {
        $model = new Reserva_Model(null, null, null, null);
        $ID_Franja = isset($_GET['ID_Franja']) ? $_GET['ID_Franja'] : 1; // Asumimos una franja predeterminada
        $recursosDisponibles = $model->getRecursosDisponibles($ID_Franja);
        new Reserva_View($recursosDisponibles, $ID_Franja);
    }

    // Maneja la acción de reservar un recurso
    function reservar() {
        if (isset($_POST['ID_Recurso']) && isset($_POST['Fecha_Hora_Reserva']) && isset($_POST['ID_Franja'])) {
            $ID_Usuario = $_SESSION['ID_Usuario']; // ID del usuario autenticado
            $ID_Recurso = $_POST['ID_Recurso'];
            $Fecha_Hora_Reserva = $_POST['Fecha_Hora_Reserva'];
            $ID_Franja = $_POST['ID_Franja'];

            $model = new Reserva_Model($ID_Usuario, $ID_Recurso, $Fecha_Hora_Reserva, $ID_Franja);

            if ($model->reservarRecurso()) {
                echo "Reserva realizada con éxito";
            } else {
                echo "El recurso no está disponible en la franja horaria seleccionada.";
            }
        }
    }
}
?>
