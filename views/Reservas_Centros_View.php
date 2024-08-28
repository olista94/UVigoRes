<?php

class Reservas_Centros_View {
    function __construct($centros) {
        $this->render($centros);
    }

    function render($centros) {
        $user_role = $_SESSION['rol'];
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Usuarios_Model.php';

        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Reservar Recurso']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1 class="h1"><?php echo $strings['Seleccionar Centro:']; ?></h1>
                    <form action="Reservas_Controller.php?action=select_tipo_recurso" method="post">
                        <div class="form-group">
                            <label for="tipo"><?php echo $strings['Centro']; ?></label>
                                <select name="ID_Centro" id="centro" class="form-group">
                                    <?php
                                        while ($centro = $centros->fetch_assoc()) {
                                            echo "<option value='{$centro['ID_Centro']}'>{$centro['Nombre']}</option>";
                                        }
                                    ?>
                                </select>
                        </div>
                        <button type="submit" class="button"><?php echo $strings['Seleccionar']; ?></button>
                    </form>

                    <!-- Botón de volver atrás -->
                    <a href="../index.php" class="button">
                        <?php echo $strings['Volver Atrás']; ?>
                    </a>
            </div>
        </body>       
        </html>
        <?php
    }
}

?>
