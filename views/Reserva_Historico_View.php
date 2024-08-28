<?php

class Reserva_Historico_View {
    private $result;

    function __construct($result) {
        $this->result = $result;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        // session_start(); // Asegúrate de tener la sesión iniciada

        $isAdmin = ($_SESSION['rol'] === 'Admin'); // Comprobar si el usuario es Admin

        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Histórico de Reservas']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Histórico de Reservas']; ?></h1>
                <table class="table">
                    <thead>
                        <tr>
                            <?php if ($isAdmin): ?>
                                <th><?php echo $strings['Usuario']; ?></th>
                            <?php endif; ?>
                            <th><?php echo $strings['Recurso']; ?></th>
                            <th><?php echo $strings['Centro']; ?></th>
                            <th><?php echo $strings['Fecha y Hora de la Reserva']; ?></th>
                            <th><?php echo $strings['Hora Inicio']; ?></th>
                            <th><?php echo $strings['Hora Fin']; ?></th>
                            <th><?php echo $strings['Estado']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        echo "<tr>";
                        if ($isAdmin) {
                            echo "<td>{$row['NombreUsuario']} {$row['ApellidosUsuario']}</td>";
                        }
                        echo "<td>{$row['TipoRecurso']}</td>";
                        echo "<td>{$row['NombreCentro']}</td>";
                        echo "<td>{$row['FechaReserva']}</td>";
                        echo "<td>{$row['Hora_Inicio']}</td>";
                        echo "<td>{$row['Hora_Fin']}</td>";
                        echo "<td>{$row['Estado']}</td>";
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
