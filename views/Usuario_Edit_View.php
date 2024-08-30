<?php

class Usuario_Edit_View {
    function __construct($user_data) {
        $this->user_data = $user_data;
        $this->render();
    }

    function render() {
        $user_role = $_SESSION['rol'];
        include '../Locales/Strings_SPANISH.php';
        include_once '../models/Usuarios_Model.php';
        
        $usuariosModel = new Usuarios_Model(null, null, null, null, null, null, null, null);
        $roles = $usuariosModel->getRoles();
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $strings['Editar Usuario']; ?></title>
            <link href="../Views/img/icon.png" rel="shortcut icon" type="image/x-icon" />
            <link rel="stylesheet" href="../css/styles.css">
        </head>
        <body>
            <div class="container">
                <h1 class="h1"><?php echo $strings['Editar Usuario']; ?></h1>
                <form action="Usuarios_Controller.php?action=edit_user" method="post" class="form">
                    <input type="hidden" name="ID_Usuario" value="<?php echo htmlspecialchars($this->user_data['ID_Usuario']); ?>">

                    <!-- Campo DNI -->
                    <div class="form-group">
                        <label for="DNI"><?php echo $strings['DNI']; ?>:</label>
                        <input type="text" name="DNI" id="DNI" 
                               value="<?php echo htmlspecialchars($this->user_data['DNI']); ?>" 
                               <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                        <?php if ($user_role !== 'Admin'): ?>
                            <input type="hidden" name="DNI" value="<?php echo htmlspecialchars($this->user_data['DNI']); ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Campo Nombre -->
                    <div class="form-group">
                        <label for="Nombre"><?php echo $strings['Nombre']; ?>:</label>
                        <input type="text" name="Nombre" id="Nombre" 
                               value="<?php echo htmlspecialchars($this->user_data['Nombre']); ?>" 
                               <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                        <?php if ($user_role !== 'Admin'): ?>
                            <input type="hidden" name="Nombre" value="<?php echo htmlspecialchars($this->user_data['Nombre']); ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Campo Apellidos -->
                    <div class="form-group">
                        <label for="Apellidos"><?php echo $strings['Apellidos']; ?>:</label>
                        <input type="text" name="Apellidos" id="Apellidos" 
                               value="<?php echo htmlspecialchars($this->user_data['Apellidos']); ?>" 
                               <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                        <?php if ($user_role !== 'Admin'): ?>
                            <input type="hidden" name="Apellidos" value="<?php echo htmlspecialchars($this->user_data['Apellidos']); ?>">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Campo NIU -->
                    <div class="form-group">
                        <label for="NIU"><?php echo $strings['NIU']; ?>:</label>
                        <input type="text" name="NIU" id="NIU" 
                               value="<?php echo htmlspecialchars($this->user_data['NIU']); ?>" 
                               <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                        <?php if ($user_role !== 'Admin'): ?>
                            <input type="hidden" name="NIU" value="<?php echo htmlspecialchars($this->user_data['NIU']); ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Campo Correo Electrónico -->
                    <div class="form-group">
                        <label for="Correo_Electronico"><?php echo $strings['Correo Electrónico']; ?>:</label>
                        <input type="email" name="Email" id="Correo_Electronico" 
                               value="<?php echo htmlspecialchars($this->user_data['Email']); ?>" required>
                    </div>

                    <!-- Campo Rol -->
                    <div class="form-group">
                        <label for="Rol"><?php echo $strings['Rol']; ?>:</label>
                        <select name="Rol" id="Rol" <?php echo $user_role !== 'Admin' ? 'disabled' : ''; ?> required>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo htmlspecialchars($rol); ?>" 
                                        <?php if ($rol == $this->user_data['Rol']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($rol); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($user_role !== 'Admin'): ?>
                            <input type="hidden" name="Rol" value="<?php echo htmlspecialchars($this->user_data['Rol']); ?>">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="button"><?php echo $strings['Guardar Cambios']; ?></button>
                </form>

                <a class="button" href="Usuarios_Controller.php?action=list_users" title="<?php echo $strings['Volver']; ?>">
                    <img src="../views/img/turn-back.png" alt="<?php echo $strings['Volver']; ?>" style="width: 20px; height: 20px;">
                </a>
            </div>
        </body>
        </html>

        <?php
    }
}
?>
