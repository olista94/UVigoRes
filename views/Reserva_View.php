<?php

class Reserva_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../Models/Access_DB.php';
        $mysqli = ConnectDB();

        // Obtener recursos disponibles
        $sql = "SELECT * FROM Recurso WHERE Disponibilidad = 'Disponible'";
        $result = $mysqli->query($sql);

        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title><?php echo $strings['Reservar Recurso']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <h1><?php echo $strings['Reservar Recurso']; ?></h1>
            <form action="Reserva_Controller.php?action=reservar" method="post">
                <label for="ID_Recurso"><?php echo $strings['Recurso']; ?>:</label>
                <select name="ID_Recurso" id="ID_Recurso" required>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['ID_Recurso']}'>{$row['Descripcion']}</option>";
                    }
                    ?>
                </select><br>
                <label for="Fecha_Hora_Reserva"><?php echo $strings['Fecha y Hora de la Reserva']; ?>:</label>
                <input type="datetime-local" name="Fecha_Hora_Reserva" id="Fecha_Hora_Reserva" required><br>
                <input type="submit" value="<?php echo $strings['Reservar']; ?>">
            </form>
            <a href="../index.php"><?php echo $strings['Volver']; ?></a>
        </body>
        </html>

        <?php
    }
}

?>
