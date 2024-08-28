<?php

class Usuario_Change_Password_View {
    function __construct($user_data) {
        $this->user_data = $user_data;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Cambiar Contraseña']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1 class="h1"><?php echo $strings['Cambiar Contraseña']; ?></h1>
                <form action="Usuarios_Controller.php?action=update_password" method="post" class="form">
                    <input type="hidden" name="ID_Usuario" value="<?php echo htmlspecialchars($this->user_data['ID_Usuario']); ?>">

                    <div class="form-group">
                        <input type="password" name="Nueva_Contrasena" id="Nueva_Contrasena" 
                               placeholder="<?php echo $strings['Nueva Contraseña']; ?>" required>
                    </div>

                    <div class="form-group">
                        <input type="password" name="Confirmar_Contrasena" id="Confirmar_Contrasena" 
                               placeholder="<?php echo $strings['Confirmar Contraseña']; ?>" required>
                    </div>

                    <button type="submit" class="button"><?php echo $strings['Guardar Cambios']; ?></button>
                </form>

                <a class="button" href="Usuarios_Controller.php?action=list_users" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
