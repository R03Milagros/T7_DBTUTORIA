<?php
require('conexion.php');

# Contador del numero de matriculas
$idMatriculas = 0;

function existe($clave, $tabla, $atributoClave, $conexionBD){
  $consulta = "SELECT * FROM $tabla WHERE $atributoClave='$clave'";
  $resultado = mysqli_query($conexionBD, $consulta);
  return (mysqli_num_rows($resultado) > 0) ? true : false;
}

function InsertarAlumnos2022($archivoTmpCsv, $con, $idMatricula){
  # abrir el archivo
  $registros = file($archivoTmpCsv);

  for ($i = 0; $i < count($registros); $i++){
    # verificar que no exista en la base de datos
    $datos_alumno = explode(',', $registros[$i]);
    $codAlumno = $datos_alumno[1];
    $nombres = $datos_alumno[2];
    # aumentar el identificador de matricula
    $idMatricula++;
    if (!existe($codAlumno, 'alumno', 'codAlumno', $con)){
      # Agregar en la tabla 'alumno' y 'matricula'
      $insertarAlumno = "INSERT INTO alumno(
        codAlumno, nombreApellido
        ) VALUES ('$codAlumno', '$nombres')";
      $insertarMatricula = "INSERT INTO matricula(
        idMatricula, codAlumno, nombreApellido, tipo
        ) VALUES ('$idMatricula', '$codAlumno', '2022-1', 'Nuevo')";

      if (!mysqli_query($con, $insertarAlumno))
        echo "<h1>No se pudo agregar alumno, en InsertarAlumnos2022</h1>";
      if (!mysqli_query($con, $insertarMatricula))
        echo "<h1>No se pudo agregar matricula nueva, en InsertarAlumnos2022</h1>";
    }
    else{
      # el alumno ya existe, agregar solo a matricula
      $insertarMatricula = "INSERT INTO matricula(
        idMatricula, codAlumno, nombreApellido, tipo
        ) VALUES ('$idMatricula', '$codAlumno', '2022-1', 'Regular')";

      if (!mysqli_query($con, $insertarMatricula))
        echo "<h1>No se pudo agregar matricula regular, en InsertarAlumnos2022</h1>";
    }
  }
}

function InsertarDocentes2022($archivoTmpCsv, $con){
  # abrir el archivo
  $registros = file($archivoTmpCsv);

  for ($i = 1; $i < count($registros); $i++){
    $datos_docente = explode(',', $registros[$i]);
    $codDocente = $datos_docente[0];
    $nombres = $datos_docente[1];
    if (!existe($codDocente, 'docente', 'codDocente', $con)){
      $insertarDocente = "INSERT INTO docente(
        codDocente, nombres
        ) VALUES ('$codDocente', '$nombres')";
      if (!mysqli_query($con, $insertarDocente))
        echo "<h1>No se pudo insertar docente en InsertarDocentes2022</h1>";
    }
  }
}

function InsertarDistribucionDocentes2021($archivoTmpCsv, $con, $idMatricula){
  # abrir el archivo
  $registros = file($archivoTmpCsv);

  $i = 0;
  $longitud = count($registros);

  while ($i < $longitud){
    $datos = explode(',', $registros[$i]);
    $codigo = $datos[0];
    $nombre = $datos[1];
    if (str_contains($codigo, "Docente")){
      # TODO:Agregar docente
      # usar codigo
      # usar nombre
      $j = $i + 1;

      while (true && $j < $longitud){
        #if ($j >= $longitud){
        #  $i = $j;
        #  break;
        #}

        $codAlumno = $registros[$j];
        $nombreAlumno = $registros[$j];
        if (str_contains($codAlumno, "Docente")){
          $i = $j;
          break;
        }
        else{
          # insertar alumnos, matricula y tutoria
          if (!str_contains($codAlumno, "CODIGO")){
            # incrementar numero de matricula, inicia con 0
            $idMatricula++;
            $insertarAlumno = "INSERT INTO alumno(
              codAlumno, nombreApellido
              ) VALUES ('$codAlumno', '$nombreAlumno')";
            $insertarMatricula = "INSERT INTO matricula(
              idMatricula, codAlumno, semestre, tipo
              ) VALUES ('$idMatricula', '$codAlumno', '2021-2', 'Nuevo')";
            $insertarTutoria = "INSERT INTO tutoria(
              idMatricula, codDocente
              ) VALUES ('$idMatricula', '$codigo')";

            if (!mysqli_query($con, $insertarAlumno))
              echo "<h1>No se pudo insertar alumno 2021</h1>";
            if (!mysqli_query($con, $insertarMatricula))
              echo "<h1>No se pudo insertar matricula 2021</h1>";
            if (!mysqli_query($con, $insertarTutoria))
              echo "<h1>No se pudo insertar tutoria 2021</h1>";
          }
          $j++;
        }
      }
    }
    else
      $i++;
  }
}
?>