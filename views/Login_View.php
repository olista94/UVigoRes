<?php

class Login_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php'; // Incluimos el archivo de idioma
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Login']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css"> <!-- Enlace al archivo CSS -->
        </head>
        <body>
            <div class="container">
                <h1 class="h1"><?php echo $strings['Login']; ?></h1>
                <form action="../Controllers/Login_Controller.php?action=Confirmar_LOGIN" method="post">
                    <div class="form-group">
                        <label for="login"><?php echo $strings['DNI']; ?>:</label>
                        <input type="text" name="login" id="login" placeholder="<?php echo $strings['DNI']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo $strings['Contraseña']; ?>:</label>
                        <input type="password" name="password" id="password" placeholder="<?php echo $strings['Contraseña']; ?>" required>
                    </div>
                    <button type="submit"><?php echo $strings['Login']; ?></button>
                </form>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
