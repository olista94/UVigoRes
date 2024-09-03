<?php
class Usuarios_Model {
    var $ID_Usuario;
    var $DNI;
    var $Nombre;
    var $Apellidos;
    var $NIU;
    var $Email;
    var $Rol;
    var $Contrasena;
    var $ID_Centro;

    function __construct($ID_Usuario, $DNI, $Nombre, $Apellidos, $NIU, $Email, $Rol, $Contrasena, $ID_Centro) {
        $this->ID_Usuario = $ID_Usuario;
        $this->DNI = $DNI;
        $this->Nombre = $Nombre;
        $this->Apellidos = $Apellidos;
        $this->NIU = $NIU;
        $this->Email = $Email;
        $this->Rol = $Rol;
        $this->Contrasena = $Contrasena;
        $this->ID_Centro = $ID_Centro;

        include_once 'Access_DB.php';
        $this->mysqli = ConnectDB();
    }

    function login() {
        // Sentencia SQL para buscar el usuario
        $sql = "SELECT * FROM Usuario WHERE DNI = '$this->DNI'";

        $resultado = $this->mysqli->query($sql); // Guarda el resultado
        if ($resultado->num_rows == 0) {
            return 'El DNI no existe'; // Devuelve mensaje de error
        } else {
            $tupla = $resultado->fetch_array();
            if ($tupla['Contrasena'] == $this->Contrasena) {
                return true; // Éxito
            } else {
                return 'La contraseña para este usuario no es correcta'; // Devuelve mensaje de error
            }
        }
    }

    function registrar() {
        if ($this->isAlreadyRegistered()) {
            return 'Error al insertar. Ya existe un usuario con ese DNI o NIU';
        }

        $sql = "INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena, ID_Centro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        // Bind parameters
        $stmt->bind_param('ssssssss', $this->DNI, $this->Nombre, $this->Apellidos, $this->NIU, $this->Email, $this->Rol, $this->Contrasena, $this->ID_Centro);
    
        // Execute statement
        if (!$stmt->execute()) {
            return 'Error al insertar';
        } 

        return 'Inserción correcta';
    }

    function edit() {
        $sql = "UPDATE Usuario SET 
                DNI = ?,
                NIU = ?, 
                Nombre = ?, 
                Apellidos = ?, 
                Email = ?, 
                Rol = ?, 
                Contrasena = ?,
                ID_Centro = ?
              WHERE ID_Usuario = ?";

        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('ssssssssi', $this->DNI, $this->NIU, $this->Nombre, $this->Apellidos, $this->Email, $this->Rol, $this->Contrasena, $this->ID_Centro, $this->ID_Usuario);
        if (!$stmt->execute()) {
            return 'Error en la modificación';
        } else {
            return 'Modificado correctamente';
        }
    }

    function search() {
        $sql = "SELECT * FROM Usuario 
                WHERE DNI LIKE ? AND
                      Nombre LIKE ? AND 
                      Apellidos LIKE ? AND 
                      NIU LIKE ? AND 
                      Email LIKE ? AND 
                      Rol LIKE ? AND
                      ID_Centro LIKE ?";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $search_dni = "%$this->DNI%";
        $search_nombre = "%$this->Nombre%";
        $search_apellidos = "%$this->Apellidos%";
        $search_niu = "%$this->NIU%";
        $search_correo = "%$this->Email%";
        $search_rol = "%$this->Rol%";
        $search_centro = "%$this->ID_Centro%";

        $stmt->bind_param('sssssss', $search_niu, $search_nombre, $search_apellidos, $search_dni, $search_correo, $search_rol, $search_centro);
        $stmt->execute();
        return $stmt->get_result();
    }

    function search_by_term($query) {
        $search_term = "%$query%";
        $sql = "SELECT * FROM Usuario
                WHERE DNI LIKE ?  
                OR Nombre LIKE ? 
                OR Apellidos LIKE ? 
                OR NIU LIKE ? 
                OR Email LIKE ? 
                OR Rol LIKE ?
                OR ID_Centro = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('sssssss', $search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
        $stmt->execute();
        return $stmt->get_result();
    }

    function delete() {
        // Verificar si el usuario a eliminar es un Admin
        $sql = "SELECT Rol FROM Usuario WHERE DNI = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('s', $this->DNI);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_to_delete = $result->fetch_assoc();
    
        if ($user_to_delete['Rol'] === 'Admin') {
            return 'No se puede eliminar un usuario con rol Admin';
        }
    
        // Proceder con la eliminación si no es Admin
        $sql = "DELETE FROM Usuario WHERE DNI = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('s', $this->DNI);
        if (!$stmt->execute()) {
            return 'Error al borrar';
        } else {
            return 'Borrado correctamente';
        }
    }    

    function rellenadatos() {
        $sql = "SELECT * FROM Usuario WHERE DNI = ?";
        
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('s', $this->DNI);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Verificar si se encontró algún resultado
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return false; // No se encontró ningún usuario
        }
    }
    function getRoles() {
        $roles = array();
        
        // Consulta para obtener la definición de la columna Rol
        $sql = "SHOW COLUMNS FROM usuario LIKE 'Rol'";
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

    function getCentros() {
        $centros = array();
        
        // Consulta para obtener los centros
        $sql = "SELECT ID_Centro, Nombre FROM Centro";
        $result = $this->mysqli->query($sql);

        if ($result === false) {
            return 'Error al realizar la consulta: ' . $this->mysqli->error;
        }

        while ($row = $result->fetch_assoc()) {
            $centros[$row['ID_Centro']] = $row['Nombre'];
        }

        return $centros;
    }

    function getCentroNameById($id_centro) {
        $sql = "SELECT Nombre FROM Centro WHERE ID_Centro = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('i', $id_centro);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['Nombre'];
        } else {
            return null; // No se encontró el centro
        }
    }

    function changePassword($new_password, $confirm_password) {
        if ($new_password !== $confirm_password) {
            return 'Las contraseñas no coinciden';
        }
    
        // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Asegúrate de hashear la contraseña
    
        $sql = "UPDATE Usuario SET Contrasena = ? WHERE DNI = ?";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('si', $new_password, $this->DNI);
    
        if (!$stmt->execute()) {
            return 'Error al cambiar la contraseña';
        } else {
            return 'Contraseña cambiada correctamente';
        }
    }

    private function isAlreadyRegistered() {
        $sql = "SELECT * FROM Usuario WHERE DNI = ? OR NIU = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('ss', $this->DNI, $this->NIU);
        $stmt->execute();
        $result = $stmt->get_result();

        return !empty($result->num_rows);
    }
}
?>
