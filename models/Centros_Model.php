<?php
class Centros_Model {
    var $ID_Centro;
    var $Nombre;
    var $Direccion;
    var $Telefono;
    var $Email;

    function __construct($ID_Centro, $Nombre, $Direccion, $Telefono, $Email) {
        $this->ID_Centro = $ID_Centro;
        $this->Nombre = $Nombre;
        $this->Direccion = $Direccion;
        $this->Telefono = $Telefono;
        $this->Email = $Email;

        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    // Método para añadir un centro
    function add() {
        $sql = "INSERT INTO Centro (Nombre, Direccion, Telefono, Email) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('ssss', $this->Nombre, $this->Direccion, $this->Telefono, $this->Email);
    
        if (!$stmt->execute()) {
            return 'Error al insertar el centro';
        } else {
            return 'Inserción correcta';
        }
    }
    

    // Método para editar un centro
    function edit() {
        $sql = "UPDATE Centro SET
                    Nombre = ?,
                    Direccion = ?,
                    Telefono = ?,
                    Email = ?
                WHERE ID_Centro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('ssssi', $this->Nombre, $this->Direccion, $this->Telefono, $this->Email, $this->ID_Centro);

        if (!$stmt->execute()) {
            return 'Error en la modificación';
        } else {
            return 'Modificado correctamente';
        }
    }

    // Método para buscar centros
    function search() {
        $sql = "SELECT * FROM Centro 
                WHERE Nombre LIKE ? AND 
                      Direccion LIKE ? AND 
                      Telefono LIKE ? AND
                      Email LIKE ?";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $search_nombre = "%$this->Nombre%";
        $search_direccion = "%$this->Direccion%";
        $search_telefono = "%$this->Telefono%";
        $search_email = "%$this->Email%";

        $stmt->bind_param('ssss', $search_nombre, $search_direccion, $search_telefono, $search_email);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Método para borrar un centro
    function delete() {
        $sql = "DELETE FROM Centro WHERE ID_Centro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $this->ID_Centro);
        if (!$stmt->execute()) {
            return 'Error al borrar';
        } else {
            return 'Borrado correctamente';
        }
    }

    // Método para rellenar datos de un centro por su ID
    function rellenadatos() {
        $sql = "SELECT * FROM Centro WHERE ID_Centro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $this->ID_Centro);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Método para obtener el nombre de todos los centros
    function getCentros() {
        $sql = "SELECT Nombre FROM Centro";
        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return 'Error al realizar la consulta: ' . $this->mysqli->error;
        }

        $centros = array();
        while ($row = $result->fetch_assoc()) {
            $centros[] = $row['Nombre'];
        }

        return $centros;
    }
}

?>
