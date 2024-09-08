<?php

class Reservas_Model {
    var $ID_Reserva;
    var $ID_Usuario;
    var $ID_Recurso;
    var $Fecha_Hora_Reserva;
    var $ID_Franja;
    var $Estado;

    var $Fecha_Disfrute_Reserva;

    function __construct($ID_Reserva, $ID_Usuario, $ID_Recurso, $Fecha_Hora_Reserva, $ID_Franja, $Estado, $Fecha_Disfrute_Reserva = null) {
        $this->ID_Reserva = $ID_Reserva;
        $this->ID_Usuario = $ID_Usuario;
        $this->ID_Recurso = $ID_Recurso;
        $this->Fecha_Hora_Reserva = $Fecha_Hora_Reserva;
        $this->ID_Franja = $ID_Franja;
        $this->Estado = $Estado;
        $this->Fecha_Disfrute_Reserva = $Fecha_Disfrute_Reserva;

        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    public function get_reservas_by_date($date) {
        $sql = "SELECT 
                    r.ID_Reserva,
                    u.Nombre AS NombreUsuario,
                    u.Apellidos AS ApellidosUsuario,
                    rec.Tipo AS TipoRecurso,
                    c.Nombre AS NombreCentro,
                    f.Hora_Inicio,
                    f.Hora_Fin,
                    r.Estado,
                    r.Devuelto,
                    r.Fecha_Disfrute_Reserva AS Fecha_Disfrute_Reserva
                FROM Reserva r
                JOIN Usuario u ON r.ID_Usuario = u.ID_Usuario
                JOIN Recurso rec ON r.ID_Recurso = rec.ID_Recurso
                JOIN Centro c ON rec.ID_Centro = c.ID_Centro
                JOIN Franja f ON r.ID_Franja = f.ID_Franja
                WHERE DATE(r.Fecha_Disfrute_Reserva) = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            return null;  // Retornar null o manejar el error adecuadamente
        }
        
        return $result;  // Retorna el objeto mysqli_result
    }
    
    
    public function get_reservas_by_date_user($date, $ID_Usuario) {
        $sql = "SELECT 
                    r.ID_Reserva,
                    rec.Tipo AS TipoRecurso,
                    f.Hora_Inicio,
                    f.Hora_Fin,
                    r.Estado,
                    r.Fecha_Disfrute_Reserva AS Fecha_Disfrute_Reserva
                FROM Reserva r
                JOIN Recurso rec ON r.ID_Recurso = rec.ID_Recurso
                JOIN Franja f ON r.ID_Franja = f.ID_Franja
                WHERE DATE(r.Fecha_Hora_Reserva) = ? AND r.ID_Usuario = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $date, $ID_Usuario);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            return null;  // Retornar null o manejar el error adecuadamente
        }
        
        return $result;  // Retorna el objeto mysqli_result
    }
    
    public function get_reserva_details($ID_Reserva) {
        $sql = "SELECT 
                    r.ID_Reserva,
                    u.Nombre AS NombreUsuario,
                    u.Apellidos AS ApellidosUsuario,
                    rec.Tipo AS TipoRecurso,
                    c.Nombre AS Nombre_Centro,
                    r.Fecha_Hora_Reserva,
                    f.Hora_Inicio,
                    f.Hora_Fin,
                    r.Estado,
                    r.Fecha_Disfrute_Reserva AS Fecha_Disfrute_Reserva
                FROM Reserva r
                JOIN Usuario u ON r.ID_Usuario = u.ID_Usuario
                JOIN Recurso rec ON r.ID_Recurso = rec.ID_Recurso
                JOIN Centro c ON rec.ID_Centro = c.ID_Centro
                JOIN Franja f ON r.ID_Franja = f.ID_Franja
                WHERE r.ID_Reserva = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Reserva);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            return null;  // Retornar null o manejar el error adecuadamente
        }
        
        return $result->fetch_assoc();  // Retorna el array asociativo con los detalles de la reserva
    }

    // Método para obtener los centros
    function getCentros() {
        $sql = "SELECT * FROM Centro";
        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return 'Error al realizar la consulta: ' . $this->mysqli->error;
        }

        return $result;
    }

    function getAvailableResources($ID_Centro, $type, $day, $franja) {
        $sql = 'SELECT * FROM Recurso ' .
            'WHERE Recurso.Disponibilidad = \'Disponible\' AND Recurso.ID_Centro = ? ' .
            'AND Recurso.Tipo = ? ' . 
            'AND Recurso.ID_Recurso NOT IN (SELECT Reserva.ID_Recurso FROM Reserva WHERE Reserva.Fecha_Disfrute_Reserva = ? AND Reserva.ID_Franja = ?)';

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('issi', $ID_Centro, $type, $day, $franja);
        $stmt->execute();

        return $stmt->get_result();
    }

    // Método para obtener los tipos de recursos disponibles en un centro específico
    function getTiposRecursosPorCentro($ID_Centro) {
        $sql = "SELECT DISTINCT Tipo FROM Recurso WHERE ID_Centro = ? AND Disponibilidad = 'Disponible'";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Centro);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Método para obtener los recursos disponibles de un tipo específico en un centro
    function getRecursosPorTipo($ID_Centro, $Tipo) {
        $sql = "SELECT * FROM Recurso WHERE ID_Centro = ? AND Tipo = ? AND Disponibilidad = 'Disponible'";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('is', $ID_Centro, $Tipo);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Método para obtener las franjas horarias disponibles para un recurso específico
    function getFranjasDisponibles() {
        $sql  = "SELECT Franja.* FROM Franja ";
        $stmt = $this->mysqli->prepare($sql);

        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear_reserva($ID_Reserva) {
        $sql = "UPDATE Reserva SET Estado = 'Confirmada' WHERE ID_Reserva = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Reserva);
        if ($stmt->execute()) {
            return "Reserva confirmada exitosamente.";
        } else {
            return "Error al confirmar la reserva.";
        }
    }

    public function devolver_reserva($ID_Reserva) {
        $sql = "UPDATE Reserva SET Devuelto = 1 WHERE ID_Reserva = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Reserva);
        if ($stmt->execute()) {
            return "Reserva devuelta exitosamente.";
        } else {
            return "Error al devolver la reserva.";
        }
    }
    
    public function eliminar_reserva($ID_Reserva) {
        // Iniciar una transacción para asegurar la consistencia
        $this->mysqli->begin_transaction();
    
        try {
            // Obtener el ID del recurso asociado a la reserva
            $sql_recurso = "SELECT ID_Recurso FROM Reserva WHERE ID_Reserva = ?";
            $stmt_recurso = $this->mysqli->prepare($sql_recurso);
            $stmt_recurso->bind_param('i', $ID_Reserva);
            $stmt_recurso->execute();
            $result_recurso = $stmt_recurso->get_result();
            $recurso = $result_recurso->fetch_assoc();
    
            if ($recurso) {
                $ID_Recurso = $recurso['ID_Recurso'];
    
                // Eliminar la reserva
                $sql_delete = "DELETE FROM Reserva WHERE ID_Reserva = ?";
                $stmt_delete = $this->mysqli->prepare($sql_delete);
                $stmt_delete->bind_param('i', $ID_Reserva);
                $stmt_delete->execute();
    
                // Actualizar el estado del recurso a 'Disponible'
                $sql_update_recurso = "UPDATE Recurso SET Disponibilidad = 'Disponible' WHERE ID_Recurso = ?";
                $stmt_update_recurso = $this->mysqli->prepare($sql_update_recurso);
                $stmt_update_recurso->bind_param('i', $ID_Recurso);
                $stmt_update_recurso->execute();
    
                // Si todo es exitoso, hacer commit
                $this->mysqli->commit();
                return "Reserva eliminada exitosamente.";
            } else {
                throw new Exception("Error al obtener el recurso de la reserva.");
            }
        } catch (Exception $e) {
            // Si hay algún error, hacer rollback
            $this->mysqli->rollback();
            return "Error al eliminar la reserva: " . $e->getMessage();
        }
    }
    

    function confirmarReserva($DNI) {
        // Obtener el ID_Usuario utilizando el DNI
        $ID_Usuario = $this->obtenerIDUsuario($DNI);
    
        if (!$ID_Usuario) {
            return "Error: No se encontró un usuario con el DNI proporcionado.";
        }
    
        // Asignar el ID obtenido a la variable del modelo
        $this->ID_Usuario = $ID_Usuario;
    
        // Insertar la reserva en la base de datos con la fecha y hora actual y estado "No Confirmado"
        $sql = "INSERT INTO reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Estado, Fecha_Disfrute_Reserva) VALUES (?, ?, NOW(), ?, 'No Confirmada', ?)";
        $stmt = $this->mysqli->prepare($sql);
        

        // Llamar a la función para actualizar el estado del recurso a 'No Disponible'
        $actualizacion = $this->actualizarEstadoRecurso($this->ID_Recurso);

        if (!$stmt) {
            return "Error al preparar la consulta: " . $this->mysqli->error;
        }
    
        $stmt->bind_param('iiss', $this->ID_Usuario, $this->ID_Recurso, $this->ID_Franja, $this->Fecha_Disfrute_Reserva);
    
        if (!$stmt->execute()) {
            return "Error al ejecutar la consulta: " . $stmt->error;
        }

        return "Reserva creada exitosamente";
    }
    
    function actualizarEstadoRecurso($ID_Recurso) {
        $sql = "UPDATE Recurso SET Disponibilidad = 'No Disponible' WHERE ID_Recurso = ?";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt === false) {
            return "Error al preparar la consulta de actualización: " . $this->mysqli->error;
        }
    
        $stmt->bind_param('i', $ID_Recurso);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return "Error al actualizar el estado del recurso: " . $stmt->error;
        }
    }

    // Método para obtener todas las reservas
    public function getAllReservas() {
        $sql = "SELECT 
                    r.ID_Reserva,
                    u.Nombre AS NombreUsuario,
                    u.Apellidos AS ApellidosUsuario,
                    rec.Descripcion AS DescripcionRecurso,
                    c.Nombre AS NombreCentro,
                    r.Fecha_Hora_Reserva AS FechaReserva,
                    f.Hora_Inicio,
                    f.Hora_Fin,
                    r.Estado,
                    r.Fecha_Disfrute_Reserva AS Fecha_Disfrute_Reserva
                FROM Reserva r
                JOIN Usuario u ON r.ID_Usuario = u.ID_Usuario
                JOIN Recurso rec ON r.ID_Recurso = rec.ID_Recurso
                JOIN Centro c ON rec.ID_Centro = c.ID_Centro
                JOIN Franja f ON r.ID_Franja = f.ID_Franja
                ORDER BY r.Fecha_Hora_Reserva DESC";
        
        $result = $this->mysqli->query($sql);
    
        if ($result === false) {
            return null;  // Retornar null o manejar el error adecuadamente
        }
    
        return $result;  // Retorna el objeto mysqli_result
    }
    
    // Método para obtener reservas específicas de un usuario
    public function getReservasByUser($ID_Usuario) {
        $sql = "SELECT 
                    r.ID_Reserva,
                    u.Nombre AS NombreUsuario,
                    u.Apellidos AS ApellidosUsuario,
                    rec.Descripcion AS DescripcionRecurso,
                    c.Nombre AS NombreCentro,
                    r.Fecha_Hora_Reserva AS FechaReserva,  -- Extraer solo la fecha
                    f.Hora_Inicio,
                    f.Hora_Fin,
                    r.Estado,
                    r.Fecha_Disfrute_Reserva AS Fecha_Disfrute_Reserva
                FROM Reserva r
                JOIN Usuario u ON r.ID_Usuario = u.ID_Usuario
                JOIN Recurso rec ON r.ID_Recurso = rec.ID_Recurso
                JOIN Centro c ON rec.ID_Centro = c.ID_Centro
                JOIN Franja f ON r.ID_Franja = f.ID_Franja
                WHERE r.ID_Usuario = ?
                ORDER BY r.Fecha_Hora_Reserva DESC";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Usuario);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            return null;  // Retornar null o manejar el error adecuadamente
        }
        
        return $result;  // Retorna el objeto mysqli_result
    }

    // Método para obtener el ID_Usuario a partir del DNI
    function obtenerIDUsuario($DNI) {
        $sql = "SELECT ID_Usuario FROM usuario WHERE DNI = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $DNI);
        $stmt->execute();
        $stmt->bind_result($ID_Usuario);
        $stmt->fetch();

        if ($ID_Usuario) {
            return $ID_Usuario;
        } else {
            return null;
        }
    }

    public function getIDCentros() {
        $query = "SELECT ID_Centro, Nombre FROM Centro";
        return $this->mysqli->query($query);
    }

    public function getUsuarios() {
        $query = "SELECT ID_Usuario, Nombre, Apellidos FROM Usuario";
        return $this->mysqli->query($query);
    }

}

?>
