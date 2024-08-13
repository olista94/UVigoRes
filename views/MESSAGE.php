<?php

class MESSAGE {
    function __construct($message, $redirect) {
        $this->message = $message;
        $this->redirect = $redirect;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php'; // Incluimos el archivo de idioma
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title><?php echo $strings['Mensaje']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
        </head>
        <body>
            <h1><?php echo $strings['Mensaje']; ?></h1>
            <p><?php echo $this->message; ?></p>
            <a href="<?php echo $this->redirect; ?>"><?php echo $strings['Volver']; ?></a>
        </body>
        </html>

        <?php
    }
}

?>
