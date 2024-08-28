<?php

class Usuario_Menu_Edit_View {
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
            <title><?php echo $strings['Opciones de Edición']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1 class="h1"><?php echo $strings['Opciones de Edición']; ?></h1>

                <div class="options">
                    <a href="Usuarios_Controller.php?action=edit_user_view&DNI=<?php echo $this->user_data['DNI']; ?>" class="button">
                        <?php echo $strings['Editar datos de usuario']; ?>
                    </a><br>
                    <a href="Usuarios_Controller.php?action=change_password&DNI=<?php echo $this->user_data['DNI']; ?>" class="button">
                        <?php echo $strings['Cambiar contraseña']; ?>
                    </a>
                </div>

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
