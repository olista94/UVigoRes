<?php
class Incidencia_List_View {
    function __construct($result) {
        $this->result = $result;
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
            <title><?php echo $strings['Lista de Incidencias']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Lista de Incidencias']; ?></h1>
                
                <!-- Botón para crear una nueva incidencia, visible solo para Admin y Personal de conserjería -->
                <?php if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'Personal de conserjeria'): ?>
                    <a class="button" href="Incidencias_Controller.php?action=crear_incidencia_form" title="<?php echo $strings['Crear Incidencia']; ?>">
                        <img src="../views/img/add-resource.png" alt="<?php echo $strings['Crear Incidencia']; ?>" style="width: 20px; height: 20px;">
                    </a><br>
                <?php endif; ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['Nombre']; ?></th>
                            <th><?php echo $strings['Tipo de recurso']; ?></th>
                            <th><?php echo $strings['Descripción del recurso']; ?></th>
                            <th><?php echo $strings['Descripción del problema']; ?></th>
                            <th><?php echo $strings['Fecha del reporte']; ?></th>
                            <th><?php echo $strings['Estado']; ?></th>
                            <th><?php echo $strings['Asignada']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        // Combinar el nombre y apellidos en una sola columna
                        $nombre_completo = $row['Nombre_Usuario'] . ' ' . $row['Apellidos_Usuario'];
                        $asignada_text = $row['Asignada'] ? $strings['Sí'] : $strings['No']; // Determinar texto de asignación
                        
                        echo "<tr>";
                        echo "<td>{$nombre_completo}</td>";
                        echo "<td>{$row['Tipo_Recurso']}</td>";
                        echo "<td>{$row['Descripcion_Recurso']}</td>";
                        echo "<td>{$row['Descripcion_Problema']}</td>";
                        echo "<td>{$row['Fecha_Reporte']}</td>";
                        echo "<td>{$row['Estado']}</td>";
                        echo "<td>{$asignada_text}</td>"; // Mostrar si está asignada
                        echo "<td>
                                <a class='button button-view' href='Incidencias_Controller.php?action=view_incidencia&ID_Incidencia={$row['ID_Incidencia']}' title='Ver incidencia'>
                                    <img src='../views/img/show.png' alt='Ver incidencia' style='width: 20px; height: 20px;'>
                                </a>
                                <a class='button button-delete' href='Incidencias_Controller.php?action=delete_incidencia&ID_Incidencia={$row['ID_Incidencia']}' title='Eliminar incidencia' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta incidencia?\")'>
                                    <img src='../views/img/delete-resource.png' alt='Eliminar incidencia' style='width: 20px; height: 20px;'>
                                </a>";

                        // Verificar si la incidencia no está asignada antes de mostrar el botón de asignar
                        if (!$row['Asignada']) {  // Verificar si 'Asignada' es falso (no está asignada)
                            echo "<a class='button button-assign' href='Incidencias_Controller.php?action=assign_incidencia&ID_Incidencia={$row['ID_Incidencia']}' title='Asignar incidencia'>
                                    <img src='../views/img/assign.png' alt='Asignar incidencia' style='width: 20px; height: 20px;'>
                                  </a>";
                        }

                        echo "</td>";
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
