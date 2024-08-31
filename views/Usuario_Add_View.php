<?php

class Usuario_Add_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Usuarios_Model.php'; // Incluir el modelo de usuarios
        
        // $usuariosModel = new Usuarios_Model(); // Crear una instancia del modelo de usuarios
        $usuariosModel = new Usuarios_Model(null, null, null, null, null, null, null, null); // Crear una instancia del modelo de usuarios
        $roles = $usuariosModel->getRoles(); // Obtener los roles desde el modelo

        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Añadir Usuario']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <div id="validation-message" class="error-message hidden"></div>
                <h1><?php echo $strings['Añadir Usuario']; ?></h1>
                <form id="user-form" action="Usuarios_Controller.php?action=add_user" method="post" class="form">
                    <div class="form-group">
                        <label for="DNI"><?php echo $strings['DNI']; ?>:</label>
                        <input type="text" name="DNI" id="dni" required>
                    </div>

                    <div class="form-group">   
                        <label for="Nombre"><?php echo $strings['Nombre']; ?>:</label>
                        <input type="text" name="Nombre" id="Nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="Apellidos"><?php echo $strings['Apellidos']; ?>:</label>
                        <input type="text" name="Apellidos" id="Apellidos" required>

                    <div class="form-group">
                        <label for="NIU"><?php echo $strings['NIU']; ?>:</label>
                        <input type="text" name="NIU" id="niu" required>
                    </div>

                    <div class="form-group">
                        <label for="Email"><?php echo $strings['Correo Electrónico']; ?>:</label>
                        <input name="Email" id="email" required autocomplete="off">
                    </div>

                    <div class="form-group"></div>
                        <label for="Rol"><?php echo $strings['Rol']; ?>:</label>
                            <select name="Rol" id="Rol" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol; ?>"><?php echo $rol; ?></option>
                                 <?php endforeach; ?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="Contrasena"><?php echo $strings['Contrasena']; ?>:</label>
                        <input type="password" name="Contrasena" id="contrasena" required autocomplete="off"><br>
                    </div>

                    <button id="submit-btn" type="submit" class="button"><?php echo $strings['Añadir']; ?></button>
                </form>
                <a class="button" href="Usuarios_Controller.php?action=list_users" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
            <script src="../views/js/validaciones.js"></script>    
        </body>
        </html>

        <?php
    }

}
?>

