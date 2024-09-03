<?php

class Incidencias_Model {
    private $ID_Incidencia;
    private $ID_Usuario;
    private $ID_Recurso;
    private $Descripcion_Problema;
    private $Fecha_Reporte;
    private $Estado;

    public function __construct($ID_Incidencia, $ID_Usuario, $ID_Recurso, $Descripcion_Problema, $Fecha_Reporte, $Estado) {
        $this->ID_Incidencia = $ID_Incidencia;
        $this->ID_Usuario = $ID_Usuario;
        $this->ID_Recurso = $ID_Recurso;
        $this->Descripcion_Problema = $Descripcion_Problema;
        $this->Fecha_Reporte = $Fecha_Reporte;
        $this->Estado = $Estado;
        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    // Método para obtener todas las incidencias
    function getAllIncidencias() {
        $sql = "SELECT 
                    Incidencia.ID_Incidencia,
                    Usuario.Nombre AS Nombre_Usuario, 
                    Usuario.Apellidos AS Apellidos_Usuario, 
                    Recurso.Tipo AS Tipo_Recurso, 
                    Recurso.Descripcion AS Descripcion_Recurso, 
                    Incidencia.Descripcion_Problema, 
                    Incidencia.Fecha_Reporte, 
                    Incidencia.Estado,
                    Incidencia.Asignada
                FROM 
                    Incidencia
                JOIN 
                    Usuario ON Incidencia.ID_Usuario = Usuario.ID_Usuario
                JOIN 
                    Recurso ON Incidencia.ID_Recurso = Recurso.ID_Recurso";

        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return 'Error al realizar la consulta: ' . $this->mysqli->error;
        }

        return $result;
    }

    // Método para obtener los detalles de una incidencia y del usuario asignado
    function getIncidenciaById() {
        $sql = "SELECT 
                    Incidencia.ID_Incidencia,
                    Incidencia.Descripcion_Problema,
                    Incidencia.Fecha_Reporte,
                    Incidencia.Estado,
                    Usuario.Nombre AS Nombre_Usuario,
                    Usuario.Apellidos AS Apellidos_Usuario,
                    Recurso.Tipo AS Tipo_Recurso,
                    Recurso.Descripcion AS Descripcion_Recurso,
                    Centro.ID_Centro AS ID_Centro,  -- Aseguramos obtener el ID_Centro aquí
                    Centro.Nombre AS Nombre_Centro,
                    IncAsig.ID_Usuario AS ID_Usuario_Asignado,
                    UsuarioAsignado.Nombre AS Nombre_Usuario_Asignado,
                    UsuarioAsignado.Apellidos AS Apellidos_Usuario_Asignado,
                    IncAsig.Fecha_Asignacion
                FROM 
                    Incidencia
                JOIN 
                    Usuario ON Incidencia.ID_Usuario = Usuario.ID_Usuario
                JOIN 
                    Recurso ON Incidencia.ID_Recurso = Recurso.ID_Recurso
                JOIN 
                    Centro ON Recurso.ID_Centro = Centro.ID_Centro
                LEFT JOIN 
                    Incidencia_Asignacion IncAsig ON Incidencia.ID_Incidencia = IncAsig.ID_Incidencia
                LEFT JOIN 
                    Usuario UsuarioAsignado ON IncAsig.ID_Usuario = UsuarioAsignado.ID_Usuario
                WHERE 
                    Incidencia.ID_Incidencia = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $this->ID_Incidencia);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null; // Si no se encuentra la incidencia
        }
    }
    

    // Método para obtener recursos sin incidencias asignadas
    function getRecursosSinIncidencias() {
        $sql = "SELECT 
                    Recurso.ID_Centro,
                    Recurso.ID_Recurso, 
                    Recurso.Tipo, 
                    Recurso.Descripcion 
                FROM 
                    Recurso 
                LEFT JOIN 
                    Incidencia ON Recurso.ID_Recurso = Incidencia.ID_Recurso 
                WHERE 
                    Incidencia.ID_Incidencia IS NULL OR Incidencia.Estado = 'Resuelta'";
        
        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Método para obtener usuarios de tipo Becario o Personal de Conserjería
    function getBecariosYConserjes() {
        $sql = "SELECT ID_Usuario, Nombre, Apellidos, Rol FROM Usuario WHERE Rol IN ('Becario de infraestructura', 'Personal de conserjeria')";
        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Método para asignar una incidencia a un usuario en la tabla Incidencia_Asignacion
    public function assignIncidencia($ID_Usuario) {
        $stmt = $this->mysqli->prepare("INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario, Fecha_Asignacion) VALUES (?, ?, NOW())");
        $stmt->bind_param('ii', $this->ID_Incidencia, $ID_Usuario);

        if ($stmt->execute()) {
            // Marcar la incidencia como asignada
            $update_stmt = $this->mysqli->prepare("UPDATE Incidencia SET Asignada = TRUE WHERE ID_Incidencia = ?");
            $update_stmt->bind_param('i', $this->ID_Incidencia);
            $update_stmt->execute();

            return "Incidencia asignada correctamente.";
        } else {
            return "Error al asignar la incidencia: " . $this->mysqli->error;
        }
    }

    // Método para crear una nueva incidencia y asignarla a un usuario
    public function crearIncidencia($ID_Usuario_Asignado) {
        // Obtener el centro asociado al recurso
        $stmt = $this->mysqli->prepare("SELECT ID_Centro FROM Recurso WHERE ID_Recurso = ?");
        $stmt->bind_param('i', $this->ID_Recurso);
        $stmt->execute();
        $result = $stmt->get_result();
        $centro = $result->fetch_assoc();
        
        if (!$centro) {
            return "Error: No se encontró el centro asociado al recurso.";
        }

        // Crear incidencia
        $stmt = $this->mysqli->prepare("INSERT INTO Incidencia (ID_Usuario, ID_Recurso, Descripcion_Problema, Fecha_Reporte, Estado, Asignada) VALUES (?, ?, ?, NOW(), 'Pendiente', TRUE)");
        $stmt->bind_param('iis', $ID_Usuario_Asignado, $this->ID_Recurso, $this->Descripcion_Problema);
    
        if ($stmt->execute()) {
            $ID_Incidencia = $stmt->insert_id;
    
            // Asignar la incidencia creada al usuario especificado
            $assign_stmt = $this->mysqli->prepare("INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario, Fecha_Asignacion) VALUES (?, ?, NOW())");
            $assign_stmt->bind_param('ii', $ID_Incidencia, $ID_Usuario_Asignado);
    
            if ($assign_stmt->execute()) {
                return "Incidencia creada y asignada correctamente.";
            } else {
                return "Error al asignar la incidencia: " . $this->mysqli->error;
            }
        } else {
            return "Error al crear la incidencia: " . $this->mysqli->error;
        }
    }    

    public function add_incidencia() {
        $stmt = $this->mysqli->prepare("INSERT INTO Incidencia (ID_Usuario, ID_Recurso, Descripcion_Problema, Fecha_Reporte, Estado) VALUES (?, ?, ?, NOW(), 'Pendiente')");
        $stmt->bind_param('iis', $this->ID_Usuario, $this->ID_Recurso, $this->Descripcion_Problema);

        if ($stmt->execute()) {
            return "Incidencia creada correctamente.";
        } else {
            return "Error al crear la incidencia: " . $this->mysqli->error;
        }
    }

    public function getIncidenciasAsignadasPendientes($ID_Usuario) {
        $sql = "SELECT 
                    Incidencia.ID_Incidencia,
                    Recurso.Tipo AS Tipo_Recurso,
                    Recurso.Descripcion AS Descripcion_Recurso,
                    Incidencia.Descripcion_Problema
                FROM 
                    Incidencia
                JOIN 
                    Incidencia_Asignacion ON Incidencia.ID_Incidencia = Incidencia_Asignacion.ID_Incidencia
                JOIN 
                    Recurso ON Incidencia.ID_Recurso = Recurso.ID_Recurso
                WHERE 
                    Incidencia_Asignacion.ID_Usuario = ? AND Incidencia.Estado = 'Pendiente'";
    
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Usuario);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            return [];
        }
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function marcarIncidenciaResuelta($ID_Incidencia) {
        // Iniciar transacción
        $this->mysqli->begin_transaction();
    
        try {
            // Marcar la incidencia como resuelta
            $stmt1 = $this->mysqli->prepare("UPDATE Incidencia SET Estado = 'Resuelta' WHERE ID_Incidencia = ?");
            $stmt1->bind_param('i', $ID_Incidencia);
            $stmt1->execute();
    
            if ($stmt1->affected_rows == 0) {
                throw new Exception("No se pudo marcar la incidencia como resuelta.");
            }
    
            // Obtener el ID_Recurso asociado a la incidencia
            $stmt2 = $this->mysqli->prepare("SELECT ID_Recurso FROM Incidencia WHERE ID_Incidencia = ?");
            $stmt2->bind_param('i', $ID_Incidencia);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $recurso = $result->fetch_assoc();
    
            if (!$recurso) {
                throw new Exception("No se encontró el recurso asociado a la incidencia.");
            }
    
            $ID_Recurso = $recurso['ID_Recurso'];
    
            // Actualizar la disponibilidad del recurso a "Disponible"
            $stmt3 = $this->mysqli->prepare("UPDATE Recurso SET Disponibilidad = 'Disponible' WHERE ID_Recurso = ?");
            $stmt3->bind_param('i', $ID_Recurso);
            $stmt3->execute();
    
            if ($stmt3->affected_rows == 0) {
                throw new Exception("No se pudo actualizar la disponibilidad del recurso.");
            }
    
            // Confirmar transacción
            $this->mysqli->commit();
    
            return "Incidencia marcada como resuelta y recurso actualizado correctamente.";
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->mysqli->rollback();
            return "Error: " . $e->getMessage();
        }
    }

    // Método para obtener becarios y conserjes que pertenecen al mismo centro que el recurso de la incidencia
    public function getBecariosYConserjesDelCentro($ID_Centro) {
        $sql = "
                SELECT
                    ID_Centro, ID_Usuario, Nombre, Apellidos, Rol 
                FROM usuario 
                WHERE (Rol LIKE 'Personal de conserjeria' OR Rol LIKE 'Becario de infraestructura') AND ID_Centro = ?; 
            ";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $ID_Centro);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecursoByReserva($ID_Reserva) {
        $query = "SELECT Recurso.* FROM Reserva INNER JOIN recurso ON Reserva.ID_Recurso = recurso.ID_Recurso WHERE ID_Reserva = ? ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $ID_Reserva);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation_data = $result->fetch_assoc();
        $reservation_data['ID_Reserva'] = $ID_Reserva;
        
        return $reservation_data;
    }

    public function delete($ID_Incidencia) {
        $query = 'DELETE FROM Incidencia WHERE ID_Incidencia = ?';

        $stmt = $this->mysqli->prepare($query);
        
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $ID_Incidencia);
        $stmt->execute();

        if (!$stmt->execute()) {
            return 'Error al borrar';
        } 
            
        return 'Borrado correctamente';
    }
}
?>
