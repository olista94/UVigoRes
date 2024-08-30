<?php

class Recurso_List_View {
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
            <title><?php echo $strings['Lista de Recursos']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Lista de Recursos']; ?></h1>
                <a class="button" href="Recursos_Controller.php?action=add_recurso"title="<?php echo $strings['Añadir Recurso']; ?>">
                    <img src="../views/img/add-resource.png" alt="<?php echo $strings['Añadir Recurso']; ?>" style="width: 20px; height: 20px;">
                </a><br>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['Tipo']; ?></th>
                            <th><?php echo $strings['Descripción']; ?></th>
                            <th><?php echo $strings['Disponibilidad']; ?></th>
                            <th><?php echo $strings['Centro']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['Tipo']}</td>";
                        echo "<td>{$row['Descripcion']}</td>";
                        echo "<td>{$row['Disponibilidad']}</td>";
                        echo "<td>{$row['Nombre_Centro']}</td>";
                        echo "<td>
                                <a class='button button-view' href='Recursos_Controller.php?action=view_recursos&ID_Recurso={$row['ID_Recurso']}' title='Ver usuario'>
                                    <img src='../views/img/show.png' alt='Ver usuario' style='width: 20px; height: 20px;'>
                                </a>
                                <a class='button button-edit' href='Recursos_Controller.php?action=edit_recurso&ID_Recurso={$row['ID_Recurso']}' title='Editar recurso'>
                                    <img src='../views/img/edit-resource.png' alt='Editar recurso' style='width: 20px; height: 20px;'></a>
                                <a class='button button-delete' href='Recursos_Controller.php?action=delete_recurso&ID_Recurso={$row['ID_Recurso']}' title='Eliminar recurso' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este recurso?\")'>
                                    <img src='../views/img/delete-resource.png' alt='Eliminar recurso' style='width: 20px; height: 20px;'>
                                </a>
                              </td>";
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
