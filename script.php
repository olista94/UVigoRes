<?php
// Incluye la conexión a la base de datos
include_once 'Access_DB.php';
$mysqli = ConnectDB();

// Iniciar una transacción para asegurar la consistencia
$mysqli->begin_transaction();

try {
    // Seleccionar los ID de recursos que tienen incidencias en estado 'Pendiente'
    $sql_incidencias = "SELECT DISTINCT ID_Recurso FROM Incidencia WHERE Estado = 'Pendiente'";
    $result_incidencias = $mysqli->query($sql_incidencias);

    if ($result_incidencias === false) {
        throw new Exception('Error al consultar incidencias: ' . $mysqli->error);
    }

    // Crear un array de recursos con incidencias pendientes
    $recursos_con_incidencias = [];
    while ($row = $result_incidencias->fetch_assoc()) {
        $recursos_con_incidencias[] = $row['ID_Recurso'];
    }

    // Construir la consulta SQL para actualizar los recursos
    $sql_update = "UPDATE Recurso SET Disponibilidad = 'Disponible' WHERE Disponibilidad = 'No disponible'";
    if (!empty($recursos_con_incidencias)) {
        // Si hay recursos con incidencias pendientes, exclúyelos de la actualización
        $placeholders = implode(',', array_fill(0, count($recursos_con_incidencias), '?'));
        $sql_update .= " AND ID_Recurso NOT IN ($placeholders)";
    }

    // Preparar la consulta
    $stmt_update = $mysqli->prepare($sql_update);
    if ($stmt_update === false) {
        throw new Exception('Error al preparar la consulta de actualización: ' . $mysqli->error);
    }

    // Bindear los parámetros si hay recursos con incidencias pendientes
    if (!empty($recursos_con_incidencias)) {
        $stmt_update->bind_param(str_repeat('i', count($recursos_con_incidencias)), ...$recursos_con_incidencias);
    }

    // Ejecutar la consulta de actualización
    if (!$stmt_update->execute()) {
        throw new Exception('Error al ejecutar la consulta de actualización: ' . $stmt_update->error);
    }

    // Si todo es exitoso, hacer commit
    $mysqli->commit();
    echo "Recursos actualizados exitosamente.";
} catch (Exception $e) {
    // Si hay algún error, hacer rollback
    $mysqli->rollback();
    echo "Error al actualizar recursos: " . $e->getMessage();
} finally {
    // Cerrar la conexión
    $mysqli->close();
}
?>
