<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="estilo1.css">
	<script> 
		function confirmarRegistro()
		{
			return confirm("\u00BFEst\u00E1 seguro que desea registrar datos?");
		}
	</script>
</head>
<body>
	<header>
		<h1><center>TUTORÍAS</center></h1>
	</header>
	<div class="contenido">
		<div class="sub1">
			<form action="subirArchivos.php" method="POST" enctype="multipart/form-data"/>
				<div class="file-input text-center">
					<p class="file-input__p" for="file-input">
						<span> ALUMNOS</span>
		            </p>
					<input  class="file" type="file" name="Alumnos" id="file-input" class="file-input__input" accept=".csv"/>
				</div>
				<div>
					<p class="file-input__p" for="file-input">
						<span> DOCENTES</span>
		            </p>
					<input  class="file" type="file" name="Docentes" id="file-input" class="file-input__input" accept=".csv"/>
				</div>
				<div>
					<p class="file-input__p" for="file-input">
						<span> TUTORÍAS ANTERIORES</span>
		            </p>
					<input  class="file" type="file" name="Tutorias" id="file-input" class="file-input__input" accept=".csv"/>
				</div>
				<div class="button">
					<input type="submit" name="subir" class="btn" value="SUBIR" onclick="return confirmarRegistro()" />
				</div>
			</form>
			<img class="img" src="imagen.jpg"><br>
		</div>
		<div class="sub2">
		<form action="mostrarDatos.php" method="POST" enctype="multipart/form-data"/>
				<p>SELECCIONE OPCIÓN:</p>
				<select class="list" name="opciones" id="opcion" title="Seleccione opción">
					<option>LISTA DE ALUMNOS QUE YA NO SON CONSIDERADOS EN LA TUTORIA </option>
					<option>DISTRIBUCION BALANCEADA DE TUTORIAS PARA EL PRESENTE SEMESTRE</option>
				</select>
				<input class ="btn" type="submit" name="subir" class="btn-mostrar" value="MOSTRAR"/>
			</form>
		</div>
	</div>
</body>
</html>