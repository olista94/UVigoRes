<?php

class Resource_View_View {
    function __construct($resource_data) {
        $this->resource_data = $resource_data;
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
            <!-- Enlace al archivo CSS -->
            <link rel="stylesheet" href="css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Ver Recurso']; ?></h1>
                <table class="table">
                    <tr>
                        <th><?php echo $strings['Tipo']; ?></th>
                        <td><?php echo $this->resource_data['Tipo']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Descripcion']; ?></th>
                        <td><?php echo $this->resource_data['Descripcion']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Disponibilidad']; ?></th>
                        <td><?php echo $this->resource_data['Disponibilidad']; ?></td>
                    </tr>
                </table>
                <a class="button" href="Recursos_Controller.php?action=list_recursos" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
