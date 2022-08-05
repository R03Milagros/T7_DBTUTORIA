DROP DATABASE IF EXISTS bdtutoria;

CREATE DATABASE bdtutoria;

USE bdtutoria;

CREATE TABLE alumno(
  -- Atributos
  codAlumno varchar(6),
  nombreApellido varchar(100) not null,
  -- Definir claves
  primary key (codAlumno)
);

CREATE TABLE matricula(
  -- Atributos
  idMatricula smallint,
  codAlumno varchar(6),
  semestre varchar(6),
  tipo varchar(8) check (tipo in ('Regular', 'Nuevo')),
  -- Definir variables
  primary key (idMatricula),
  foreign key (codAlumno) references alumno(codAlumno)
);

CREATE TABLE docente(
  -- Atributos
  codDocente varchar(4),
  nombres varchar(100) not null,
  -- Definir claves
  primary key (codDocente)
);

CREATE TABLE tutoria(
  -- Atributos
  idMatricula smallint,
  codDocente varchar(4),
  -- Definir claves
  primary key (idMatricula, codDocente),
  foreign key (idMatricula) references matricula(idMatricula),
  foreign key (codDocente) references docente(codDocente)
);

-- Creacion de triggers

-- Creacion de funciones

-- Creacion de procedimientos almacenados
DELIMITER //
CREATE PROCEDURE procMatriculadosRegulares()
BEGIN
CREATE TEMPORARY TABLE matriculadosRegular
AS
SELECT *
FROM
  SELECT a.codAlumno, a.nombreApellido, resultado.semestre
  FROM
    (SELECT * FROM alumno) as a
    INNER JOIN
    (SELECT *
    FROM
      ((SELECT * FROM matricula WHERE semestre='2021-2') as ma2021
      INNER JOIN
      (SELECT * FROM matricula WHERE semestre='2022-1') as ma2022
      ON ma2021.codAlumno = ma2022.codAlumno)) as resultado
    ON a.codAlumno = resultado.codAlumno;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE procMatriculadosNuevos()
BEGIN
CREATE TEMPORARY TABLE matriculadosNuevos
AS
SELECT *
FROM
  SELECT a.codAlumno, a.nombreApellido, resultado.semestre
  FROM
    (SELECT * FROM alumno) as a
    RIGHT JOIN
    (SELECT *
    FROM
      ((SELECT * FROM matricula WHERE semestre='2021-2') as ma2021
      INNER JOIN
      (SELECT * FROM matricula WHERE semestre='2022-1') as ma2022
      ON ma2021.codAlumno = ma2022.codAlumno
      WHERE ma2021.codAlumno is null)) as resultado
    ON a.codAlumno = resultado.codAlumno;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE procAlumnosRetirados()
BEGIN
CREATE TEMPORARY TABLE alumnosRetirados
AS
SELECT *
FROM
  SELECT a.codAlumno, a.nombreApellido, resultado.semestre
  FROM
    (SELECT * FROM alumno) as a
    LEFT JOIN
    (SELECT *
    FROM
      ((SELECT * FROM matricula WHERE semestre='2021-2') as ma2021
      INNER JOIN
      (SELECT * FROM matricula WHERE semestre='2022-1') as ma2022
      ON ma2021.codAlumno = ma2022.codAlumno
      WHERE ma2022.codAlumno is null)) as resultado
    ON a.codAlumno = resultado.codAlumno;
END //
DELIMITER ;