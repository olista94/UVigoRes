<?php

function ConnectDB() {
    $mysqli = new mysqli("localhost", "root", "", "uvigores2");

    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: " . $mysqli->connect_error;
    }

    return $mysqli;
}

?>
