<?php
class Usuarios_Model {
    var $ID_Usuario;
    var $NIU;
    var $Nombre;
    var $Apellidos;
    var $DNI;
    var $Email;
    var $Rol;
    var $Contrasena;

    function __construct($ID_Usuario, $NIU, $Nombre, $Apellidos, $DNI, $Email, $Rol, $Contrasena) {
        $this->ID_Usuario = $ID_Usuario;
        $this->NIU = $NIU;
        $this->Nombre = $Nombre;
        $this->Apellidos = $Apellidos;
        $this->DNI = $DNI;
        $this->Email = $Email;
        $this->Rol = $Rol;
        $this->Contrasena = $Contrasena;

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
    

    function register() {
        $sql = "SELECT * FROM Usuario WHERE DNI = ?";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('s', $this->DNI);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return 'El usuario ya existe';
        } else {
            return true;
        }
    }

    // function registrar() {
    //     $sql = "INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) 
    //             VALUES (?, ?, ?, ?, ?, ?, ?)";
    //             var_dump($this->mysqli->query($sql));
    //     $stmt = $this->mysqli->prepare($sql);
    //     if ($stmt === false) {
    //         return 'Error al preparar la consulta: ' . $this->mysqli->error;
    //     }
    
    //     // $hashed_password = password_hash($this->Contrasena, PASSWORD_DEFAULT);
    //     $stmt->bind_param('sssssss', $this->DNI, $this->Nombre, $this->Apellidos, $this->NIU, $this->Email, $this->Rol, $this->Contrasena);
    
    //     if (!$stmt->execute()) {
    //         return 'Error al insertar. Ya existe un usuario con ese DNI';
    //     } else {
    //         return 'Inserción correcta';
    //     }
    // }
    function registrar() {
        $sql = "INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        // Bind parameters
        $stmt->bind_param('sssssss', $this->DNI, $this->Nombre, $this->Apellidos, $this->NIU, $this->Email, $this->Rol, $this->Contrasena);
    
        // Execute statement
        if (!$stmt->execute()) {
            return 'Error al insertar. Ya existe un usuario con ese DNI';
        } else {
            return 'Inserción correcta';
        }
    }

    function edit() {
        $sql = "UPDATE Usuario SET 
                NIU = ?, 
                Nombre = ?, 
                Apellidos = ?, 
                Email = ?, 
                Rol = ?, 
                Contrasena = ? 
              WHERE ID_Usuario = ?";

        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $stmt->bind_param('ssssssi', $this->NIU, $this->Nombre, $this->Apellidos, $this->Email, $this->Rol, $this->Contrasena, $this->ID_Usuario);
        if (!$stmt->execute()) {
            return 'Error en la modificación';
        } else {
            return 'Modificado correctamente';
        }
    }

    function search() {
        $sql = "SELECT * FROM Usuario 
                WHERE NIU LIKE ? AND 
                      Nombre LIKE ? AND 
                      Apellidos LIKE ? AND 
                      DNI LIKE ? AND 
                      Email LIKE ? AND 
                      Rol LIKE ?";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }

        $search_niu = "%$this->NIU%";
        $search_nombre = "%$this->Nombre%";
        $search_apellidos = "%$this->Apellidos%";
        $search_dni = "%$this->DNI%";
        $search_correo = "%$this->Email%";
        $search_rol = "%$this->Rol%";

        $stmt->bind_param('ssssss', $search_niu, $search_nombre, $search_apellidos, $search_dni, $search_correo, $search_rol);
        $stmt->execute();
        return $stmt->get_result();
    }

    function search_by_term($query) {
        $search_term = "%$query%";
        $sql = "SELECT * FROM Usuario 
                WHERE NIU LIKE ? 
                OR Nombre LIKE ? 
                OR Apellidos LIKE ? 
                OR DNI LIKE ? 
                OR Email LIKE ? 
                OR Rol LIKE ?";
        
        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Error al preparar la consulta: ' . $this->mysqli->error;
        }
    
        $stmt->bind_param('ssssss', $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
        $stmt->execute();
        return $stmt->get_result();
    }

    function delete() {
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
        return $stmt->get_result();
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
    
    
}
?>
