<?php

class Recurso_Add_View {
    function __construct() {
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Recursos_Model.php'; // Incluir el modelo de recursos
        
        // $recursosModel = new Recursos_Model(); // Crear una instancia del modelo de recursos
        $recursosModel = new Recursos_Model(null, null, null, null, null); // Crear una instancia del modelo de recursos
        $disponibles = $recursosModel->getDisponibilidad(); // Obtener la disponibilidad desde el modelo
        $centros = $recursosModel->getCentros(); // Obtener los desde el modelo
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Añadir Recurso']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Añadir Recurso']; ?></h1>
                <form action="Recursos_Controller.php?action=add_resource" method="post" class="form">

                    <div class="form-group">
                        <label for="Tipo"><?php echo $strings['Tipo']; ?>:</label>
                        <input type="text" name="Tipo" id="Tipo" required>
                    </div>

                    <div class="form-group">
                        <label for="Descripción"><?php echo $strings['Descripción']; ?>:</label>
                        <input type="text" name="Descripción" id="Descripción" required>
                    </div>

                    <div class="form-group">
                        <label for="Disponibilidad"><?php echo $strings['Disponibilidad']; ?>:</label>
                            <select name="Disponibilidad" id="Disponibilidad" required>
                                <?php foreach ($disponibles as $disponibilidad): ?>
                                    <option value="<?php echo $rol; ?>"><?php echo $disponibilidad; ?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="Centro"><?php echo $strings['Centro']; ?>:</label>
                            <select name="Centro" id="Centro" required>
                                <?php foreach ($centros as $centro): ?>
                                    <option value="<?php echo $centro; ?>"><?php echo $centro; ?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                    

                    <button type="submit" class="button"><?php echo $strings['Añadir']; ?></button>
                </form>
                <a class="button" href="Recursos_Controller.php?action=list_recursos" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
