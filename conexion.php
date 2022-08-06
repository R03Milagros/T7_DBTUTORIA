<?php
$usuario = "root";
$password = "";
$servidor = "localhost";
$baseDatos = "bdtutoria";
$con = mysqli_connect($servidor, $usuario, $password, $baseDatos);
mysqli_query($con, "SET SESSION collation_connection = 'utf8_unicode_ci'");
?>