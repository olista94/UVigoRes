<?php

class Incidencia_Asignar_View {
    function __construct($incidencia, $usuarios) {
        $this->incidencia = $incidencia;
        $this->usuarios = $usuarios;
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
            <title><?php echo $strings['Asignar Incidencia']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Asignar Incidencia']; ?></h1>
                <form action="Incidencias_Controller.php?action=assign" method="POST">
                    <input type="hidden" name="ID_Incidencia" value="<?php echo $this->incidencia['ID_Incidencia']; ?>">
                    <input type="hidden" name="ID_Centro" value="<?php echo isset($this->reservation_data['ID_Recurso']) ? htmlspecialchars($this->reservation_data['ID_Recurso']) : ''; ?>">
                    
                    <!-- Información de la incidencia -->
                    <p><strong><?php echo $strings['Nombre']; ?>:</strong> <?php echo $this->incidencia['Nombre_Usuario'] . ' ' . $this->incidencia['Apellidos_Usuario']; ?></p>
                    <p><strong><?php echo $strings['Tipo de recurso']; ?>:</strong> <?php echo $this->incidencia['Tipo_Recurso']; ?></p>
                    <p><strong><?php echo $strings['Descripción del recurso']; ?>:</strong> <?php echo $this->incidencia['Descripcion_Recurso']; ?></p>
                    <p><strong><?php echo $strings['Centro']; ?>:</strong> <?php echo $this->incidencia['Nombre_Centro']; ?></p>
                    <p><strong><?php echo $strings['Descripción del problema']; ?>:</strong> <?php echo $this->incidencia['Descripcion_Problema']; ?></p>
                    <p><strong><?php echo $strings['Fecha del reporte']; ?>:</strong> <?php echo $this->incidencia['Fecha_Reporte']; ?></p>
                    <p><strong><?php echo $strings['Estado']; ?>:</strong> <?php echo $this->incidencia['Estado']; ?></p>

                    <!-- Selector de usuario para asignación -->
                    <label for="ID_Usuario"><?php echo $strings['Asignar a']; ?>:</label>
                    <select name="ID_Usuario" id="Rol" required>
                        <?php foreach ($this->usuarios as $usuario) : ?>
                            <option value="<?php echo $usuario['ID_Usuario']; ?>">
                                <?php echo $usuario['Nombre'] . ' ' . $usuario['Apellidos'] . ' (' . $usuario['Rol'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="submit" class="button" value="<?php echo $strings['Asignar']; ?>">
                </form>
                <a class="button" href="Incidencias_Controller.php?action=list_all_incidencias" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
