<?php

class Incidencia_View_View {
    function __construct($incidencia) {
        $this->incidencia = $incidencia;
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
            <title><?php echo $strings['Detalle de Incidencia']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Detalle de Incidencia']; ?></h1>
                <div class="detail">
                    <p><strong><?php echo $strings['Nombre']; ?>:</strong> <?php echo $this->incidencia['Nombre_Usuario'] . ' ' . $this->incidencia['Apellidos_Usuario']; ?></p>
                    <p><strong><?php echo $strings['Tipo de recurso']; ?>:</strong> <?php echo $this->incidencia['Tipo_Recurso']; ?></p>
                    <p><strong><?php echo $strings['Descripción del recurso']; ?>:</strong> <?php echo $this->incidencia['Descripcion_Recurso']; ?></p>
                    <p><strong><?php echo $strings['Centro']; ?>:</strong> <?php echo $this->incidencia['Nombre_Centro']; ?></p>
                    <p><strong><?php echo $strings['Descripción del problema']; ?>:</strong> <?php echo $this->incidencia['Descripcion_Problema']; ?></p>
                    <p><strong><?php echo $strings['Fecha del reporte']; ?>:</strong> <?php echo $this->incidencia['Fecha_Reporte']; ?></p>
                    <p><strong><?php echo $strings['Estado']; ?>:</strong> <?php echo $this->incidencia['Estado']; ?></p>

                    <!-- Información del usuario asignado -->
                    <?php if ($this->incidencia['ID_Usuario_Asignado']) { ?>
                        <p><strong><?php echo $strings['Usuario Asignado']; ?>:</strong> <?php echo $this->incidencia['Nombre_Usuario_Asignado'] . ' ' . $this->incidencia['Apellidos_Usuario_Asignado']; ?></p>
                        <p><strong><?php echo $strings['Fecha de Asignación']; ?>:</strong> <?php echo $this->incidencia['Fecha_Asignacion']; ?></p>
                    <?php } else { ?>
                        <p><strong><?php echo $strings['Usuario Asignado']; ?>:</strong> <?php echo $strings['No Asignado']; ?></p>
                    <?php } ?>
                </div>
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
