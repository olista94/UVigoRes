<?php

class Reservas_Recursos_Disponibles_View {
    function __construct($ID_Recurso, $franjas) {
        $this->render($ID_Recurso, $franjas);
    }

    function render($ID_Recurso, $franjas) {
        $user_role = $_SESSION['rol'];
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Usuarios_Model.php';

        // Obtener la hora actual
        $hora_actual = date('H:i');

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
                <h1 class="h1"><?php echo $strings['Seleccionar Franja Horaria:']; ?></h1>
                    <form action="Reservas_Controller.php?action=crear_reserva" method="post">
                        <div class="form-group">
                            <input type="hidden" name="ID_Recurso" value="<?php echo $ID_Recurso; ?>">
                                <select name="ID_Franja" id="franja" class="form-group">
                                    <?php
                                    while ($franja = $franjas->fetch_assoc()) {
                                        // Comprobar si la franja horaria es igual o posterior a la hora actual
                                        if ($franja['Hora_Inicio'] >= $hora_actual) {
                                            echo "<option value='{$franja['ID_Franja']}'>{$franja['Hora_Inicio']} - {$franja['Hora_Fin']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                        </div>
                        <button type="submit" class="button"><?php echo $strings['Confirmar Reserva']; ?></button>
                    </form>

                    <!-- Botón de volver atrás -->
                    <a href="Reservas_Controller.php?action=select_recurso&ID_Centro=<?php echo $_POST['ID_Centro']; ?>&Tipo=<?php echo $_POST['Tipo']; ?>" class="button">
                        <?php echo $strings['Volver Atrás']; ?>
                    </a>
            </div>
        </body>
        </html>
        <?php
    }
}

?>
