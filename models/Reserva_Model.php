<?php

class Reserva_Model {
    var $ID_Reserva;
    var $ID_Usuario;
    var $ID_Recurso;
    var $Fecha_Hora_Reserva;
    var $Codigo_QR;
    var $Estado;

    function __construct($ID_Reserva, $ID_Usuario, $ID_Recurso, $Fecha_Hora_Reserva, $Codigo_QR, $Estado) {
        $this->ID_Reserva = $ID_Reserva;
        $this->ID_Usuario = $ID_Usuario;
        $this->ID_Recurso = $ID_Recurso;
        $this->Fecha_Hora_Reserva = $Fecha_Hora_Reserva;
        $this->Codigo_QR = $Codigo_QR;
        $this->Estado = $Estado;

        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    function reservar() {
        $sql = "INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, Codigo_QR, Estado) 
                VALUES ('$this->ID_Usuario', '$this->ID_Recurso', '$this->Fecha_Hora_Reserva', '$this->Codigo_QR', '$this->Estado')";
        
        if (!$this->mysqli->query($sql)) {
            return 'Error al reservar el recurso';
        } else {
            return 'Reserva realizada con éxito';
        }
    }
}

?>
