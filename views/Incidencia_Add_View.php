<?php

class Incidencia_Add_View {
    function __construct($reservation_data) {
        $this->reservation_data = $reservation_data;
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
            <title><?php echo $strings['Añadir Incidencia']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Añadir Incidencia']; ?></h1>
                <form action="../Controllers/Incidencias_Controller.php?action=add" method="post">
                    <!-- Comprobar si ID_Usuario está definido en la sesión -->
                    <input type="hidden" name="ID_Usuario" value="<?php echo isset($_SESSION['ID_Usuario']) ? htmlspecialchars($_SESSION['ID_Usuario']) : ''; ?>">
                    <!-- Comprobar si ID_Recurso está definido en los datos de la reserva -->
                    <input type="hidden" name="ID_Recurso" value="<?php echo isset($this->reservation_data['ID_Recurso']) ? htmlspecialchars($this->reservation_data['ID_Recurso']) : ''; ?>">

                    <label for="Descripcion_Problema"><?php echo $strings['Descripción del problema']; ?>:</label>
                    <textarea class="textarea-estilo" id="Descripcion_Problema" name="Descripcion_Problema" required></textarea>
                    
                    <button type="submit" class="button"><?php echo $strings['Crear Incidencia']; ?></button>
                </form>
                <a class="button" href="../Controllers/Reservas_Controller.php?action=view_reserva&ID_Reserva=<?php echo htmlspecialchars($this->reservation_data['ID_Reserva']); ?>" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
