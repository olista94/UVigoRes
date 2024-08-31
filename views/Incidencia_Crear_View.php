<?php

class Incidencia_Crear_View {
    function __construct($recursos, $usuarios, $selectedResource) {
        $this->recursos         = $recursos;
        $this->usuarios         = $usuarios;
        $this->selectedResource = $selectedResource;
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
            <title><?php echo $strings['Crear Incidencia']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Crear Incidencia']; ?></h1>
                <form action="Incidencias_Controller.php?action=crear_incidencia" method="POST">
                    
                    <!-- Seleccionar Recurso -->
                    <label for="ID_Recurso"><?php echo $strings['Seleccionar Recurso']; ?>:</label>
                    <select name="ID_Recurso" id="Recurso" required>
                        <option value="">Selecciona un recurso</option>
                        <?php foreach ($this->recursos as $recurso) : ?>
                            <option 
                                data-center="<?php echo $recurso['ID_Centro']; ?>" 
                                value="<?php echo $recurso['ID_Recurso']; ?>" 
                                <?php if($this->selectedResource === $recurso['ID_Recurso']) echo 'selected'; ?>
                            >
                                <?php echo $recurso['Tipo'] . ' - ' . $recurso['Descripcion']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <!-- Motivo de la Incidencia -->
                    <label for="Descripcion_Problema"><?php echo $strings['Descripción del problema']; ?>:</label><br>
                    <textarea class="textarea-estilo" id="Descripcion_Problema" name="Descripcion_Problema" required></textarea><br>

                    <!-- Asignar a Usuario -->
                    <label for="ID_Usuario"><?php echo $strings['Asignar a']; ?>:</label>
                    <select name="ID_Usuario" id="usuario" required>
                        <?php foreach ($this->usuarios as $usuario) : ?>
                            <option value="<?php echo $usuario['ID_Usuario']; ?>">
                                <?php echo $usuario['Nombre'] . ' ' . $usuario['Apellidos'] . ' (' . $usuario['Rol'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="submit" class="button" value="<?php echo $strings['Crear Incidencia']; ?>">
                </form>
                <a class="button" href="Incidencias_Controller.php?action=list_all_incidencias" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
            <script src="../views/js/incidencias.js"></script>
        </body>
        </html>

        <?php
    }
}
?>
