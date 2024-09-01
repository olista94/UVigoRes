<?php

class Centro_Add_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Centros_Model.php'; // Incluir el modelo de recursos
        
        // $recursosModel = new Recursos_Model(); // Crear una instancia del modelo de recursos
        $centrosModel = new Centros_Model(null, null, null, null, null); // Crear una instancia del modelo de centros
        $centros = $centrosModel->getCentros();
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Añadir Centro']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <div id="validation-message" class="error-message hidden"></div>
                <h1><?php echo $strings['Añadir Centro']; ?></h1>
                <form action="Centros_Controller.php?action=add_centro" method="post" class="form">

                    <div class="form-group">
                        <label for="Nombre"><?php echo $strings['Nombre']; ?>:</label>
                        <input type="text" name="Nombre" id="Nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="Direccion"><?php echo $strings['Dirección']; ?>:</label>
                        <input type="text" name="Direccion" id="Direccion" required>
                    </div>

                    <div class="form-group">
                        <label for="Telefono"><?php echo $strings['Teléfono']; ?>:</label>
                        <input type="text" name="Telefono" id="Telefono" required>
                    </div>

                    <div class="form-group">
                        <label for="Email"><?php echo $strings['Correo Electrónico']; ?>:</label>
                        <input type="text" name="Email" id="Email" required>
                    </div>

                    <button id="submit-btn" type="submit" class="button"><?php echo $strings['Añadir']; ?></button>
                </form>
                <a class="button" href="Centros_Controller.php?action=list_centros" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
            <script src="../views/js/validationCenter.js"></script> 
        </body>
        </html>

        <?php
    }
}
?>
