<?php
include('conexion.php');
include('funcionesInsercionDatos.php');

$val = existeNombreDocente('BORIS CHULLO LLAVE');

echo "Valor retornado " . $val;
?>