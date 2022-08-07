<?php
require('conexion.php');

# Contador del numero de matriculas, de manera general
$numeroTutorias = 0;
# Contador del numero de docentes
$numeroDocentes = 0;

# Definir los atributos de cada tabla
$at_alumno = ['codAlumno', 'nombreApellido'];
$at_docente = ['codDocente', 'nombreApellido'];
$at_tutoria = ['idTutoria', 'codAlumno', 'codigoSemestre', 'codDocente'];
$at_docenteContratado = ['codDocente', 'codigoSemestre'];
$at_alumnoMatriculado = ['codAlumno', 'codigoSemestre', 'tipo'];
$at_semestre = ['codigoSemestre'];

function existe($clave, $tabla, $atributoClave, $conexionBD){
  $consulta = "SELECT * FROM $tabla WHERE $atributoClave='$clave'";
  $resultado = mysqli_query($conexionBD, $consulta);
  return (mysqli_num_rows($resultado) > 0) ? true : false;
}

function InsertarAlumnos2022($archivoTmpCsv){
  global $con;
  global $at_alumno, $at_alumnoMatriculado;

  # abrir el archivo
  $registros = file($archivoTmpCsv);

  for ($i = 0; $i < count($registros); $i++){
    # verificar que no exista en la base de datos
    $datos_alumno = explode(',', $registros[$i]);
    $codAlumno = $datos_alumno[1];
    $nombres = $datos_alumno[2];
    if (!existe($codAlumno, 'alumno', 'codAlumno', $con)){
      # Agregar en la tabla 'alumno' y 'matricula'
      Insertar($at_alumno, [$codAlumno, $nombres], 'alumno', $con);
      Insertar($at_alumnoMatriculado, [$codAlumno, '2022-1', 'Nuevo'], 'alumnoMatriculado', $con);
    }
    else{
      # el alumno ya existe, agregar solo a matricula
      Insertar($at_alumnoMatriculado, [$codAlumno, '2022-1', 'Regular'], 'alumnoMatriculado', $con);
    }
  }
}

function InsertarDocentes2022($archivoTmpCsv){
  global $con;
  global $numeroDocentes;
  global $at_docente, $at_docenteContratado;

  # abrir el archivo
  $registros = file($archivoTmpCsv);

  for ($i = 1; $i < count($registros); $i++){
    $datos_docente = explode(',', $registros[$i]);
    # $codDocente = $datos_docente[0];
    $nombres = $datos_docente[1];
    # -- Busqueda por nombre
    $codDocente = existeNombreDocente($nombres);
    if ($codDocente == -1){
      $numeroDocentes++;
      Insertar($at_docente, [$numeroDocentes, $nombres], 'docente', $con);
      Insertar($at_docenteContratado, [$numeroDocentes, '2022-1'], 'docenteContratado', $con);
    }
    else{
      Insertar($at_docenteContratado, [$codDocente, '2022-1'], 'docenteContratado', $con);
    }
  }
}

function AgregarTutorias2022($registrosBalanceados){
  global $con;
  global $at_tutoria;
  # Recuperar la tabla distribucionparcial2022
  $proc = "CALL distribucionparcial2022();";
  $consulta = "SELECT * FROM tablaDistribucionParcial2022";
  mysqli_query($con, $proc);
  $distribucionParcial = mysqli_query($con, $consulta);
  #$consulta2 = "SELECT MAX(idTutoria) AS id FROM tutoria";
  #$tutorias = mysqli_query($con, $consulta2);
  #$filasTutoria += 2;
  $filasTutoria = 701;
  while (list($codAlumno, $codDocente) = mysqli_fetch_array($distribucionParcial, PDO::FETCH_NUM)){
    //echo "Codigo  " . "Nombres<br>";
    #echo "Una vez" . "<br>";
    Insertar($at_tutoria, [$filasTutoria, $codAlumno, '2022-1', $codDocente], 'tutoria', $con);
    $filasTutoria++;
  }
  # agregar los nuevos alumnos
  # 0 : coddocente
  # 1 : codalumno
  # 2 : nombrealumno ??
  for ($i = 0; $i < count($registrosBalanceados); $i++){
    Insertar($at_tutoria, [$filasTutoria, $registrosBalanceados[$i][1], '2022-1', $registrosBalanceados[$i][0]], 'tutoria', $con);
    $filasTutoria++;
  }
}

function existeNombreDocente($nombre){
  global $con;
  $consulta = "SELECT * FROM docente";
  $resultado = mysqli_query($con, $consulta);
  #echo "Nombre a buscar " . $nombre . strlen($nombre) . "<br>";
  while (($registro = mysqli_fetch_assoc($resultado)) != null){
    #echo print_r($registro) . "<br>";
    $codDocente = $registro['codDocente'];
    $nombreApellido = $registro['nombreApellido'];
    if ($nombre == $nombreApellido){
      # encontrado
      return $codDocente;
    }
  }
  return -1;
}

# Primer funcion a ejecutar (invocar)
function InsertarDistribucionDocentes2021($archivoTmpCsv){
  global $con;
  global $numeroTutorias, $numeroDocentes;
  global $at_alumno, $at_docente, $at_alumnoMatriculado, $at_docenteContratado, $at_tutoria;

  # abrir el archivo
  $registros = file($archivoTmpCsv);

  $i = 0;
  $longitud = count($registros);

  while ($i < $longitud){
    $datos = explode(',', $registros[$i]);
    if (count($datos) > 1){
      $codigo = $datos[0];
      $nombre = $datos[1];
      if (str_contains($codigo, "Docente") && (strlen($nombre) > 2)){
        $numeroDocentes++;
        # -- Insertar en tabla docente
        Insertar($at_docente, [$numeroDocentes, trim($nombre)], 'docente', $con);
        # -- Insertar en la tabla docenteContratado
        Insertar($at_docenteContratado, [$numeroDocentes, '2021-2'], 'docenteContratado', $con);

        $j = $i + 1;

        while ($j < $longitud){
          $datosAlumno = explode(',', $registros[$j]);
          $codAlumno = $datosAlumno[0];
          $nombreAlumno = $datosAlumno[1];
          if (str_contains($codAlumno, "Docente")){
            $i = $j;
            break;
          }
          else{
            # insertar alumnos, matricula y tutoria
            if (!str_contains($codAlumno, "CODIGO")){
              # -- incrementar numero de alumnos matriculados
              $numeroTutorias++;
              # -- Insertar en la tabla alumno
              if (!existe($codAlumno, 'alumno', 'codAlumno', $con)){
                # -- Insertar en la tabla alumno
                Insertar($at_alumno, [$codAlumno, trim($nombreAlumno)], 'alumno', $con);
                # -- Insertar en la tabla alumnoMatricuado
                Insertar($at_alumnoMatriculado, [$codAlumno, '2021-2', 'Nuevo'], 'alumnoMatriculado', $con);
                # -- Insertar en la tabla tutoria
                Insertar($at_tutoria, [$numeroTutorias, $codAlumno, '2021-2', $numeroDocentes], 'tutoria', $con);
              }
            }
            $j++;
          }
        }
        $i = $j;
      }
      else
        $i++;
    }
    else
      $i++;
  }
}

$ponerComillas = function ($dato){
  return "'" . $dato . "'";
};

function Insertar($atributos, $valores, $tabla, $conexionBD){
  global $ponerComillas;
  $valoresMapeados = array_map($ponerComillas, $valores);
  $insertar = "INSERT INTO " . $tabla . "(" . implode(", ", $atributos)
    . ") VALUES (" . implode(", ", $valoresMapeados) . ")";
  if (!mysqli_query($conexionBD, $insertar))
    echo "<h1>Ocurrio un error al insertar</h1>";
}
?>