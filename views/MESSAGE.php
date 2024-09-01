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
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="message-container">
                <p><?php echo $this->message; ?></p>
                <a href="<?php echo $this->redirect; ?>"><?php echo $strings['Volver']; ?></a>
            </div>
            </body>
        </html>

        <?php
    }
}

?>
