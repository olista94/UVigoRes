<?php

class Resource_Edit_View {
    function __construct($resource_data) {
        $this->resource_data = $resource_data;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Recursos_Model.php'; // Incluir el modelo de recursos
        
        // $recursosModel = new Recursos_Model(); // Crear una instancia del modelo de recursos
        $recursosModel = new Recursos_Model(null, null, null, null); // Crear una instancia del modelo de recursos
        $disponibles = $recursosModel->getDisponibilidad(); // Obtener los roles desde el modelo
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Editar Recurso']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Editar Recurso']; ?></h1>
                <form action="Recursos_Controller.php?action=edit_recurso" method="post" class="form">
                    <input type="hidden" name="ID_Recurso" value="<?php echo htmlspecialchars($this->resource_data['ID_Recurso']); ?>">

                    <div class="form-group">
                        <label for="Tipo"><?php echo $strings['Tipo']; ?>:</label>
                        <input type="text" name="Tipo" id="Tipo" 
                               value="<?php echo htmlspecialchars($this->resource_data['Tipo']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Descripción"><?php echo $strings['Descripción']; ?>:</label>
                        <textarea name="Descripcion" id="Descripcion" rows="4" required><?php echo htmlspecialchars($this->resource_data['Descripcion']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="Disponibilidad"><?php echo $strings['Disponibilidad']; ?>:</label>
                        <select name="Disponibilidad" id="Disponibilidad" <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                            <?php foreach ($disponibles as $disponibilidad): ?>
                                <option value="<?php echo htmlspecialchars($disponibilidad); ?>" 
                                        <?php if ($disponibilidad == $this->resource_data['Disponibilidad']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($disponibilidad); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="button"><?php echo $strings['Guardar Cambios']; ?></button>
                </form>
                <a class="button" href="Recursos_Controller.php?action=list_recursos"><?php echo $strings['Volver']; ?></a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
