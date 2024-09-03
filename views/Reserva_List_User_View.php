<?php

class Reserva_List_User_View {
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
            <title><?php echo $strings['Mis Reservas del Día']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Mis Reservas del Día']; ?></h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['Recurso']; ?></th>
                            <th><?php echo $strings['Hora Inicio']; ?></th>
                            <th><?php echo $strings['Hora Fin']; ?></th>
                            <th><?php echo $strings['Estado']; ?></th>
                            <th><?php echo $strings['Día']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['TipoRecurso']}</td>";
                        echo "<td>{$row['Hora_Inicio']}</td>";
                        echo "<td>{$row['Hora_Fin']}</td>";
                        echo "<td>{$row['Estado']}</td>";
                        echo "<td>{$row['Fecha_Disfrute_Reserva']}</td>";
                        echo "<td>
                                <a class='button button-view' href='Reservas_Controller.php?action=view_reserva&ID_Reserva={$row['ID_Reserva']}' title='Ver reserva'>
                                    <img src='../views/img/show.png' alt='Ver reserva' style='width: 20px; height: 20px;'>
                                </a>
                              </td>";
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
