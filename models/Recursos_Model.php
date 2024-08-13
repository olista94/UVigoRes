<?php
class Recursos_Model {
    var $ID_Recurso;
    var $Tipo;
    var $Descripcion;
    var $Disponibilidad;

    function __construct($ID_Recurso, $Tipo, $Descripcion, $Disponibilidad) {
        $this->ID_Recurso = $ID_Recurso;
        $this->Tipo = $Tipo;
        $this->Descripcion = $Descripcion;
        $this->Disponibilidad = $Disponibilidad;

        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    // Método para añadir un recurso
    function add() {
        $sql = "INSERT INTO Recursos (Tipo, Descripcion, Disponibilidad) 
                VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('sss', $this->Tipo, $this->Descripcion, $this->Disponibilidad);
    
        if (!$stmt->execute()) {
            return 'Error al insertar el recurso';
        } else {
            return 'Inserción correcta';
        }
    }
    

    // Método para editar un recurso
    function edit() {
        $sql = "UPDATE Recurso SET
                    Tipo = ?,
                    Descripcion = ?,
                    Disponibilidad = ?
                WHERE ID_Recurso = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('sssi', $this->Tipo, $this->Descripcion, $this->Disponibilidad, $this->ID_Recurso);

        if (!$stmt->execute()) {
            return 'Error en la modificación';
        } else {
            return 'Modificado correctamente';
        }
    }

    // Método para buscar recursos
    function search() {
        $sql = "SELECT * FROM Recurso 
                WHERE Tipo LIKE ? AND 
                      Descripcion LIKE ? AND 
                      Disponibilidad LIKE ?";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $search_tipo = "%$this->Tipo%";
        $search_descripcion = "%$this->Descripcion%";
        $search_disponibilidad = "%$this->Disponibilidad%";

        $stmt->bind_param('sss', $search_tipo, $search_descripcion, $search_disponibilidad);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Método para borrar un recurso
    function delete() {
        $sql = "DELETE FROM Recurso WHERE ID_Recurso = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $this->ID_Recurso);
        if (!$stmt->execute()) {
            return 'Error al borrar';
        } else {
            return 'Borrado correctamente';
        }
    }

    // Método para rellenar datos de un recurso por su ID
    function rellenadatos() {
        $sql = "SELECT * FROM Recurso WHERE ID_Recurso = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $this->ID_Recurso);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getDisponibilidad() {
        $roles = array();
        
        // Consulta para obtener la definición de la columna Disponibilidad
        $sql = "SHOW COLUMNS FROM recurso LIKE 'Disponibilidad'";
        $result = $this->mysqli->query($sql);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $type = $row['Type'];
    
            // Extraer los valores del ENUM de la cadena
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            if (isset($matches[1])) {
                $roles = explode("','", $matches[1]);
            }
        }
    
        return $roles;
    }
}

?>
