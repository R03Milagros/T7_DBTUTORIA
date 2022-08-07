<?php
require('funcionesInsercionDatos.php');

$valorRecibido = $_GET['opciones'];
$valorProcesar = 0;
if ($valorRecibido == 'LISTA DE ALUMNOS QUE YA NO SON CONSIDERADOS EN LA TUTORÍA')
  $valorProcesar = 1;
else
  $valorProcesar = 2;


function Balancear($distribucion2021, $copiaDistribucion, $nuevos){
  $nuevosActualizados = array();
  while (list($codAlumno, $nombre) = mysqli_fetch_array($nuevos, PDO::FETCH_NUM)){
    array_push($nuevosActualizados, array($codAlumno, $nombre));
  }

  # nuevos
  # --- 0 : [codAlumno, nombre]
  # --- 1 : [codAlumno, nombre]

  # distribucion2021
  # --- 0 : [codDocente, numero]
  # --- 1 : [codDocente, numero]

  # distribucion2022
  # --- 0 : [codDocente, codAlumno, nombre]

  # 0: coddocente
  # 1: codalumno
  # 2: nombre del alumno
  $distribucion2022 = array();
  /*

  */
  $maximo = 0;
  # sacar el maximo de tutorados por cada docente
  while (list($codDocente, $numero) = mysqli_fetch_array($distribucion2021, PDO::FETCH_NUM)){
    if ($numero > $maximo)
      $maximo = $numero;
  }
  $contador = 0;
  $cantidadNuevos = count($nuevosActualizados);
  # Completar los tutorados de cada docente hasta llegar al maximo
  while (list($codDocente, $numero) = mysqli_fetch_array($copiaDistribucion, PDO::FETCH_NUM)){
    if ($numero < $maximo){
      # le faltan alumnos,
      $diferencia = $maximo - $numero;
      #echo "Diferencia " . $diferencia . "<br>";
      #echo "Numero " . $numero . "<br>";
      while ($diferencia > 0 && $contador < $cantidadNuevos){
        array_push($distribucion2022, array($codDocente, $nuevosActualizados[$contador][0], $nuevosActualizados[$contador][1]));
        $diferencia--;
        $contador++;
      }
    }
  }
  # Verificar si aun faltan alumnos por asignar
  #$contador = 0;
  if ($contador < $cantidadNuevos){
    # asignar cada alumno restante a cada docente
    for ($i = 0; $i < count($nuevosActualizados); $i++){
      array_push($distribucion2022, array($distribucion2022[$contador][0], $nuevosActualizados[$i][0], $nuevosActualizados[$i][1]));
      $contador++;
    }
  }
  return $distribucion2022;
}

# ???
function AgregarTutoria($nuevaDistribucion){
  global $con;
  # Recuperar la tabla distribucionparcial2022
  $proc = "CALL distribucionparcial2022();";
  $consulta = "SELECT * FROM tablaDistribucionParcial2022";
  mysqli_query($con, $proc);
  $distribucionParcial = mysqli_query($con, $consulta);
  while (list($codigo, $nombre) = mysqli_fetch_array($distribucionParcial, PDO::FETCH_NUM)){
    //echo "Codigo  " . "Nombres<br>";
    echo $codigo . "   " . $nombre . "<br>";
  }
}
# ??

function MostrarDistribucion2022(){
  global $con;

  $proc1 = "CALL conteotutoradosxdocente();";
  $proc2 = "CALL nuevosmatriculados('2022-1');";
  $consulta1 = "SELECT * FROM tutoradoxdocente2022;";
  $consulta2 = "SELECT * FROM tablanuevosmatriculados;";
  if (!mysqli_query($con, $proc1))
    echo "alert('ocurrio un error');";
  $docentesxtutorados = mysqli_query($con, $consulta1);
  $docentesxtutorados2 = mysqli_query($con, $consulta1);
  if (!mysqli_query($con, $proc2))
    echo "alert('Ocurrio un error');";
  $nuevos = mysqli_query($con, $consulta2);
  $nuevaDistribucion = Balancear($docentesxtutorados, $docentesxtutorados2, $nuevos);

  echo '<div id="main-container">';
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th> CODIGO DOCENTE </th><th> CODIGO ALUMNOS </th><th> APELLIDOS Y NOMBRES</th>';
	echo '</tr>';
	echo '</thead>';
  
  for ($j = 0; $j < count($nuevaDistribucion); $j++){
    echo '<tr>';
		echo '<td>'.$nuevaDistribucion[$j][0]  .'</td><td>' . $nuevaDistribucion[$j][1]. '</td><td>' . $nuevaDistribucion[$j][2]. '</td>';
		echo '</tr>';

  }
  echo 	'</table>';
	echo '</div>';

  # agregar a la base de datos las tutorias 2022
  AgregarTutorias2022($nuevaDistribucion);
}

function MostrarAlumnosNoTutorados(){
  global $con;

  $procedimiento = "CALL NoTutorados2022();";
  if (!mysqli_query($con, $procedimiento))
    echo "alert('Ocurrio un error');";
  $consulta = "SELECT * FROM tablaNoTutorados;";
  $registros = mysqli_query($con, $consulta);
  if (!$registros)
    echo "alert('Ocurrio errores');";

  echo '<div id="main-container">';
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th> CODIGO </th><th> APELLIDOS Y NOMBRES</th>';
	echo '</tr>';
	echo '</thead>';
  while (list($codigo, $nombre) = mysqli_fetch_array($registros, PDO::FETCH_NUM)){
    //echo "Codigo  " . "Nombres<br>";
    echo '<tr>';
		echo '<td>'.$codigo .'</td><td>' . $nombre . '</td>';
		echo '</tr>';
  }
  echo 	'</table>';
	echo '</div>';
}

if ($valorProcesar == 1){
  MostrarAlumnosNoTutorados();
}
else
  MostrarDistribucion2022();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <link rel="stylesheet" href="estilos-tabla.css">
</head>
</html>
