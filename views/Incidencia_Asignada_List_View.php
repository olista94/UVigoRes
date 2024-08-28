<?php

class Incidencia_Asignada_List_View {
    private $result;

    function __construct($result) {
        $this->result = $result;
        $this->render();
    }

    function render() {
        include '../Locales/Strings_SPANISH.php';

        // Verificar si el usuario tiene el rol adecuado
        if ($_SESSION['rol'] !== 'Becario de infraestrucura' && $_SESSION['rol'] !== 'Personal de conserjeria') {
            header('Location: ../index.php');
            exit();
        }
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Incidencias Asignadas']; ?></title>
            
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1><?php echo $strings['Incidencias Asignadas']; ?></h1>

                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $strings['Tipo de recurso']; ?></th>
                            <th><?php echo $strings['Descripción del recurso']; ?></th>
                            <th><?php echo $strings['Descripción del problema']; ?></th>
                            <th><?php echo $strings['Acciones']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($this->result as $row) {
                        echo "<tr>";
                        echo "<td>{$row['Tipo_Recurso']}</td>";
                        echo "<td>{$row['Descripcion_Recurso']}</td>";
                        echo "<td>{$row['Descripcion_Problema']}</td>";
                        echo "<td>
                                <a class='button button-confirm' href='Incidencias_Controller.php?action=marcar_resuelta&ID_Incidencia={$row['ID_Incidencia']}' title='Marcar como Resuelta'>
                                    <img src='../views/img/check.png' alt='Marcar como Resuelta' style='width: 20px; height: 20px;'>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <a class="button" href="../index.php" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style='width: 20px; height: 20px;'>
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
