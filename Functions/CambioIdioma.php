

<?php

//Creamos la sesion
session_start();
//Guarda en idioma,el idioma seleccionado
$idioma = $_POST['idioma'];
//Se guarda en una sessiom el idioma
$_SESSION['idioma'] = $idioma;
//Redirige a la página anterior al cambiar el idioma
header('Location:' . $_SERVER["HTTP_REFERER"]);
?>