<?php

class Reservas_Recursos_View {
    function __construct($ID_Centro, $Tipo, $recursos, $franja, $day) {
        $this->render($ID_Centro, $Tipo, $recursos, $franja, $day);
    }

    function render($ID_Centro, $Tipo, $recursos, $franja, $day) {
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
                <h1 class="h1"><?php echo $strings['Seleccionar Recurso']; ?></h1>
                    <form action="Reservas_Controller.php?action=crear_reserva" method="post">
                        <input type="hidden" name="ID_Centro" value="<?php echo $ID_Centro; ?>">
                        <input type="hidden" name="Tipo" value="<?php echo $Tipo; ?>">
                        <input type="hidden" name="Franja" value="<?php echo $franja; ?>">
                        <input type="hidden" name="Day" value="<?php echo $day; ?>">
                        <div class="form-group">
                            <label for="tipo"><?php echo $strings['Recurso']; ?></label>
                                <select name="ID_Recurso" id="recurso" class="form-group">
                                    <?php
                                        while ($recurso = $recursos->fetch_assoc()) {
                                            echo "<option value='{$recurso['ID_Recurso']}'>{$recurso['Descripcion']}</option>";
                                        }
                                    ?>
                                </select>
                        </div>
                    <button type="submit" class="button"><?php echo $strings['Seleccionar']; ?></button>
                </form>

                <!-- Botón de volver atrás -->
                <a href="Reservas_Controller.php?action=select_tipo_recurso&ID_Centro=<?php echo $ID_Centro; ?>" class="button">
                    <?php echo $strings['Volver Atrás']; ?>
                </a>
            </div>
        </body>
        </html>
        <?php
    }
}
?>
