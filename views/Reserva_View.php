<?php

class Reserva_View {
    function __construct($reservation_data) {
        $this->reservation_data = $reservation_data;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';

        // Convertir la fecha y hora de reserva al formato dd-mm-aaaa hh:mm:ss
        $fecha_hora_reserva = date("d-m-Y H:i:s", strtotime($this->reservation_data['Fecha_Hora_Reserva']));
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Ver Reserva']; ?></title>

            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Ver Reserva']; ?></h1>
                <table class="table">
                    <tr>
                        <th><?php echo $strings['Usuario']; ?></th>
                        <td><?php echo $this->reservation_data['NombreUsuario'] . ' ' . $this->reservation_data['ApellidosUsuario']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Recurso']; ?></th>
                        <td><?php echo $this->reservation_data['TipoRecurso']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Centro']; ?></th>
                        <td><?php echo $this->reservation_data['Nombre_Centro']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Fecha y Hora de la Reserva']; ?></th>
                        <td><?php echo $fecha_hora_reserva; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Hora Inicio']; ?></th>
                        <td><?php echo $this->reservation_data['Hora_Inicio']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Hora Fin']; ?></th>
                        <td><?php echo $this->reservation_data['Hora_Fin']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $strings['Estado']; ?></th>
                        <td><?php echo $this->reservation_data['Estado']; ?></td>
                    </tr>
                </table>
                <a class="button" href="Reservas_Controller.php?action=ver_reservas_usuario" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
                <!-- Botón para crear incidencia -->
                <a class="button" href="Incidencias_Controller.php?action=add_incidencia&ID_Reserva=<?php echo $this->reservation_data['ID_Reserva']; ?>" title="<?php echo $strings['Crear Incidencia']; ?>">
                    <img src="../views/img/incidencia.png" alt="<?php echo $strings['Crear Incidencia']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
