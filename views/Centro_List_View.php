<?php

class Centro_List_View {
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
            <title><?php echo $strings['Lista de Centros']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Lista de Centros']; ?></h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['Nombre']; ?></th>
                            <th><?php echo $strings['Dirección']; ?></th>
                            <th><?php echo $strings['Teléfono']; ?></th>
                            <th><?php echo $strings['Correo Electrónico']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $this->result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['Nombre']}</td>";
                        echo "<td>{$row['Direccion']}</td>";
                        echo "<td>{$row['Telefono']}</td>";
                        echo "<td>{$row['Email']}</td>";
                        echo "<td>
                                <a class='button button-view' href='Centros_Controller.php?action=view_centros&ID_Centro={$row['ID_Centro']}' title='Ver centro'>
                                    <img src='../views/img/show.png' alt='Ver usuario' style='width: 20px; height: 20px;'>
                                </a>
                                <a class='button button-edit' href='Centros_Controller.php?action=edit_centro&ID_Centro={$row['ID_Centro']}' title='Editar centro'>
                                    <img src='../views/img/edit-resource.png' alt='Editar centro' style='width: 20px; height: 20px;'></a>
                                <a class='button button-delete' href='Centros_Controller.php?action=delete_centro&ID_Centro={$row['ID_Centro']}' title='Eliminar centro' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este recurso?\")'>
                                    <img src='../views/img/delete-resource.png' alt='Eliminar centro' style='width: 20px; height: 20px;'>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <a class="button" href="Centros_Controller.php?action=add_centro"title="<?php echo $strings['Añadir Centro']; ?>">
                    <img src="../views/img/add-resource.png" alt="<?php echo $strings['Añadir Recurso']; ?>" style="width: 20px; height: 20px;">
                </a><br>
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
