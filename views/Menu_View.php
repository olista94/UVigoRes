<?php

class Menu_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title><?php echo $strings['Menu']; ?></title>
        </head>
        <body>
            <h1><?php echo $strings['Menu']; ?></h1>
            <a href="../index.php"><?php echo $strings['Volver']; ?></a><br>
            <a href="Reserva_Controller.php?action=reservar"><?php echo $strings['Reservar Recurso']; ?></a>
            <a href="Usuarios_Controller.php?action=list_users"><?php echo $strings['Gestion de usuarios']; ?></a>
        </body>
        </html>

        <?php
    }
}

?>
