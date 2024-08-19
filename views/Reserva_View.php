

<?php

class Reserva_View {
    private $recursos;
    private $ID_Franja;

    function __construct($recursos, $ID_Franja) {
        $this->recursos = $recursos;
        $this->ID_Franja = $ID_Franja;
        $this->render();
    }

    function render() {
        $user_role = $_SESSION['rol']; // Recupera el rol del usuario
        include '../Locales/Strings_SPANISH.php';
        include_once '../Models/Access_DB.php';
        include_once '../Models/Reserva_Model.php';
        $mysqli = ConnectDB();

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
                <h1><?php echo $strings['Reservar Recurso']; ?></h1>
                <form action="Reserva_Controller.php?action=reservar" method="post" class="form">

                    <div class="form-group">
                        <label for="ID_Recurso"><?php echo $strings['Recurso']; ?>:</label>
                        <select name="ID_Recurso" id="ID_Recurso" required>
                            <?php
                            while ($row = $this->recursos->fetch_assoc()) {
                                echo "<option value='{$row['ID_Recurso']}'>{$row['Descripcion']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Fecha_Hora_Reserva"><?php echo $strings['Fecha y Hora de la Reserva']; ?>:</label>
                        <input type="datetime-local" name="Fecha_Hora_Reserva" id="Fecha_Hora_Reserva" required>
                    </div>

                    <div class="form-group">
                        <label for="ID_Franja"><?php echo $strings['Franja Horaria']; ?>:</label>
                        <input type="number" name="ID_Franja" id="ID_Franja" value="<?php echo $this->ID_Franja; ?>" readonly>
                    </div>

                    <button type="submit" class="button"><?php echo $strings['Reservar']; ?></button>
                </form>

                <a href="../index.php" class="button"><?php echo $strings['Volver']; ?></a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
