<?php

class Centro_Edit_View {
    function __construct($center_data) {
        $this->center_data = $center_data;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Centros_Model.php'; // Incluir el modelo de centros
        
        $centrosModel = new Centros_Model(null, null, null, null, null); // Crear una instancia del modelo de centros
        $centros = $centrosModel->getCentros();
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Editar Centro']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Editar Centro']; ?></h1>
                <form action="Centros_Controller.php?action=edit_centro" method="post" class="form">
                    <input type="hidden" name="ID_Centro" value="<?php echo htmlspecialchars($this->center_data['ID_Centro']); ?>">

                    <div class="form-group">
                        <label for="Nombre"><?php echo $strings['Nombre']; ?>:</label>
                        <input type="text" name="Nombre" id="Nombre" 
                               value="<?php echo htmlspecialchars($this->center_data['Nombre']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Direccion"><?php echo $strings['Dirección']; ?>:</label>
                        <input type="text" name="Direccion" id="Direccion" 
                                value="<?php echo htmlspecialchars($this->center_data['Direccion']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Telefono"><?php echo $strings['Teléfono']; ?>:</label>
                        <input type="text" name="Telefono" id="Telefono" 
                                value="<?php echo htmlspecialchars($this->center_data['Telefono']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Email"><?php echo $strings['Correo Electrónico']; ?>:</label>
                        <input type="text" name="Email" id="Email" 
                                value="<?php echo htmlspecialchars($this->center_data['Email']); ?>" required>
                    </div>

                    <button type="submit" class="button"><?php echo $strings['Guardar Cambios']; ?></button>
                </form>
                <a class="button" href="Centros_Controller.php?action=list_centros"><?php echo $strings['Volver']; ?></a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
