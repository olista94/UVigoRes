<?php

class Usuario_View_View {
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
            <title><?php echo $strings['Ver Usuario']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Ver Usuario']; ?></h1>
                <table class="table">
                    <tr>
                        <th><?php echo $strings['DNI']; ?></th>
                        <td><?php echo $this->user_data['DNI']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Nombre']; ?></th>
                        <td><?php echo $this->user_data['Nombre']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Apellidos']; ?></th>
                        <td><?php echo $this->user_data['Apellidos']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['NIU']; ?></th>
                        <td><?php echo $this->user_data['NIU']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Correo Electrónico']; ?></th>
                        <td><?php echo $this->user_data['Email']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Rol']; ?></th>
                        <td><?php echo $this->user_data['Rol']; ?></td>
                    </tr>
                </table>
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
