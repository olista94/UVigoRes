<?php
class Reserva_Model {
    private $ID_Reserva;
    private $ID_Usuario;
    private $ID_Recurso;
    private $Fecha_Hora_Reserva;
    private $ID_Franja;
    private $Estado;
    private $mysqli;

    public function __construct($ID_Usuario, $ID_Recurso, $Fecha_Hora_Reserva, $ID_Franja) {
        $this->ID_Usuario = $ID_Usuario;
        $this->ID_Recurso = $ID_Recurso;
        $this->Fecha_Hora_Reserva = $Fecha_Hora_Reserva;
        $this->ID_Franja = $ID_Franja;
        $this->mysqli = ConnectDB();
    }

    // Método para verificar la disponibilidad del recurso en una franja horaria
    public function esRecursoDisponible() {
        $sql = "SELECT * FROM Reserva WHERE ID_Recurso = ? AND ID_Franja = ? AND Fecha_Hora_Reserva = ? AND Estado = 'Confirmada'";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->ID_Recurso, $this->ID_Franja, $this->Fecha_Hora_Reserva);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows === 0; // Devuelve true si no hay reservas confirmadas
    }

    // Método para reservar un recurso
    public function reservarRecurso() {
        if ($this->esRecursoDisponible()) {
            $sql = "INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Estado) 
                    VALUES (?, ?, ?, ?, 'No Confirmada')";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("iisi", $this->ID_Usuario, $this->ID_Recurso, $this->Fecha_Hora_Reserva, $this->ID_Franja);
            return $stmt->execute();
        } else {
            return false; // El recurso no está disponible
        }
    }

    // Obtener recursos disponibles en una franja horaria específica
    public function getRecursosDisponibles($ID_Franja) {
        $sql = "SELECT r.* FROM Recurso r 
                WHERE r.Disponibilidad = 'Disponible' 
                AND NOT EXISTS (
                    SELECT 1 FROM Reserva res 
                    WHERE res.ID_Recurso = r.ID_Recurso 
                    AND res.ID_Franja = ? 
                    AND res.Estado = 'Confirmada'
                )";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $ID_Franja);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
