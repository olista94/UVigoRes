<?php

class Reserva_List_View {
    function __construct($result) {
        $this->result = $result;
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
            <title><?php echo $strings['Reservas del Día']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Reservas del Día']; ?></h1>
                <table class="table">
                    <thead>
                        <tr>
                            <!-- <th><?php echo $strings['ID Reserva']; ?></th> -->
                            <th><?php echo $strings['Usuario']; ?></th>
                            <th><?php echo $strings['Recurso']; ?></th>
                            <th><?php echo $strings['Centro']; ?></th>
                            <th><?php echo $strings['Hora Inicio']; ?></th>
                            <th><?php echo $strings['Hora Fin']; ?></th>
                            <th><?php echo $strings['Estado']; ?></th>
                            <th><?php echo $strings['Devuelto']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['NombreUsuario']}</td>";
                        echo "<td>{$row['TipoRecurso']}</td>";
                        echo "<td>{$row['NombreCentro']}</td>";
                        echo "<td>{$row['Hora_Inicio']}</td>";
                        echo "<td>{$row['Hora_Fin']}</td>";
                        echo "<td>{$row['Estado']}</td>";
                        echo "<td>" . ($row['Devuelto'] == 1 ? 'Sí' : 'No') . "</td>";
                        echo "<td>";
                        // Botón de confirmar reserva (solo si no está confirmada)
                        if ($row['Estado'] != 'Confirmada') {
                            echo "<a class='button button-confirm' href='Reservas_Controller.php?action=confirm_reserva&ID_Reserva={$row['ID_Reserva']}' title='Confirmar reserva'>
                                    <img src='../views/img/check.png' alt='Confirmar reserva' style='width: 20px; height: 20px;'>
                                  </a>";
                        }
                        if ($row['Devuelto'] != 1 && $row['Estado'] == 'Confirmada') {
                        echo "<a class='button button-return' href='Reservas_Controller.php?action=devuelta&ID_Reserva={$row['ID_Reserva']}' title='Confirmar devolucion'>
                                <img src='../views/img/return.png' alt='Recurso devuelto' style='width: 20px; height: 20px;'>
                              </a>";
                        }
                        echo "<a class='button button-delete' href='Reservas_Controller.php?action=delete_reserva&ID_Reserva={$row['ID_Reserva']}' title='Eliminar reserva' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta reserva?\")'>
                                <img src='../views/img/delete.png' alt='Eliminar reserva' style='width: 20px; height: 20px;'>
                              </a>";

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <a class="button" href="../index.php" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
