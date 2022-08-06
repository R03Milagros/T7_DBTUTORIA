USE bdtutoria;

DELIMITER $$
CREATE PROCEDURE NuevosMatriculados(semestre varchar(6))
BEGIN
DROP TEMPORARY TABLE IF EXISTS tablaNuevosMatriculados;
CREATE TEMPORARY TABLE nuevosMatriculados
AS
SELECT a.codAlumno, a.nombreApellido, nuevo.tipo FROM
(SELECT * FROM alumno) as a
INNER JOIN
(SELECT * FROM alumnomatriculado WHERE codigoSemestre = semestre and tipo = 'Nuevo') nuevo
ON a.codAlumno = nuevo.codAlumno;
END $$
DELIMITER ;

DELIMITER //
CREATE PROCEDURE NoAptosTutoria2022()
BEGIN
DROP TEMPORARY TABLE noAptosTutoria;
CREATE TEMPORARY TABLE tablaNoAptosTutoria
AS
SELECT a.codAlumno, a.nombreApellido FROM
(SELECT * FROM alumno) as a
INNER JOIN
(SELECT ma2021.codAlumno FROM
    (SELECT * FROM alumnomatriculado WHERE codigoSemestre='2021-2') as ma2021
	LEFT JOIN
	(SELECT * FROM alumnomatriculado WHERE codigoSemestre='2022-1') as ma2022
	ON ma2021.codAlumno = ma2022.codAlumno
	WHERE ma2022.codAlumno is null ) as noAptos
ON a.codAlumno = noAptos.codAlumno;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE DistribucionTutoria2021()
BEGIN
DROP TEMPORARY TABLE tablaDistribucionTutoria2021;
CREATE TEMPORARY TABLE distribucionTutoria2021
AS
SELECT codDocente, count(idTutoria) as NumeroTutorados
FROM tutoria
WHERE codigoSemestre = '2021-2'
GROUP BY codDocente;
END //
DELIMITER ;