<?php

class Reservas_Tipos_Recursos_View {
    function __construct($ID_Centro, $tipos, $franjas) {
        $this->render($ID_Centro, $tipos, $franjas);
    }

    function render($ID_Centro, $tipos, $franjas) {
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
                <h1 class="h1"><?php echo $strings['Seleccionar Tipo de Recurso:']; ?></h1>
                    <form action="Reservas_Controller.php?action=select_recurso" method="post">
                        <input type="hidden" name="ID_Centro" value="<?php echo $ID_Centro; ?>">
                        <div class="form-group">
                            <label for="tipo"><?php echo $strings['Tipo de recurso']; ?></label>
                            <select name="Tipo" id="tipo" class="form-group">
                                <?php
                                    foreach ($tipos as $tipo) {
                                        echo "<option value='{$tipo}'>{$tipo}</option>";
                                    }
                                ?>
                            </select>
                            <select name="ID_Franja" id="franja" class="form-group">
                                    <?php
                                    while ($franja = $franjas->fetch_assoc()) {
                                            echo "<option value='{$franja['ID_Franja']}'>{$franja['Hora_Inicio']} - {$franja['Hora_Fin']}</option>";
                                    }
                                    ?>
                            </select>
                            <!-- Aquí se establece la fecha mínima como el día actual -->
                            <input name="day" type="date" min="<?php echo date('Y-m-d'); ?>">
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
