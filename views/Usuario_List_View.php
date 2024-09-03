<?php

class Usuario_List_View {
    function __construct($users_with_centros) {
        $this->users_with_centros = $users_with_centros;
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
            <title><?php echo $strings['Lista de Usuarios']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Lista de Usuarios']; ?></h1>
                <a class="button" href="Usuarios_Controller.php?action=add_user" title="<?php echo $strings['Añadir Usuario']; ?>">
                    <img src="../views/img/add-user.png" alt="<?php echo $strings['Añadir Usuario']; ?>" style="width: 20px; height: 20px;">
                </a>
                <a class="button" href="../index.php" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a><br>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['DNI']; ?></th>
                            <th><?php echo $strings['Nombre']; ?></th>
                            <th><?php echo $strings['Apellidos']; ?></th>
                            <th><?php echo $strings['NIU']; ?></th>
                            <th><?php echo $strings['Correo Electrónico']; ?></th>
                            <th><?php echo $strings['Rol']; ?></th>
                            <th><?php echo $strings['Centro']; ?></th> <!-- Añadir la columna de Centro -->
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($this->users_with_centros as $row) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['DNI']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Apellidos']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['NIU']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Rol']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Nombre_Centro']) . "</td>"; // Mostrar el nombre del centro
                            echo "<td>
                                    <a class='button button-view' href='Usuarios_Controller.php?action=view_user&DNI=" . urlencode($row['DNI']) . "' title='Ver usuario'>
                                        <img src='../views/img/show.png' alt='Ver usuario' style='width: 20px; height: 20px;'>
                                    </a>
                                    <a class='button button-edit' href='Usuarios_Controller.php?action=edit_user&DNI=" . urlencode($row['DNI']) . "' title='Editar usuario'>
                                        <img src='../views/img/edit-user.png' alt='Editar usuario' style='width: 20px; height: 20px;'>
                                    </a>";
                        
                            // Verifica si el usuario NO es Admin para mostrar el botón de eliminar
                            if ($row['Rol'] !== 'Admin') {
                                echo "<a class='button button-delete' href='Usuarios_Controller.php?action=delete_user&DNI=" . urlencode($row['DNI']) . "' title='Eliminar usuario' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este usuario?\")'>
                                        <img src='../views/img/delete-user.png' alt='Eliminar usuario' style='width: 20px; height: 20px;'>
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
