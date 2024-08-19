<?php

class Centro_View_View {
    function __construct($center_data) {
        $this->center_data = $center_data;
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
            <title><?php echo $strings['Ver Recurso']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Ver Centro']; ?></h1>
                <table class="table">
                    <tr>
                        <th><?php echo $strings['Nombre']; ?></th>
                        <td><?php echo $this->center_data['Nombre']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Dirección']; ?></th>
                        <td><?php echo $this->center_data['Direccion']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Teléfono']; ?></th>
                        <td><?php echo $this->center_data['Telefono']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Correo Electrónico']; ?></th>
                        <td><?php echo $this->center_data['Email']; ?></td>
                    </tr>
                </table>
                <a class="button" href="Centros_Controller.php?action=list_centros" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
