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

CREATE TABLE semestre(
  -- Atributos
  codigoSemestre varchar(6),
  -- Definir clave
  primary key (codigoSemestre)
);

CREATE TABLE alumnoMatriculado(
  -- Atributos
  codAlumno varchar(6),
  codigoSemestre varchar(6),
  tipo varchar(8) check (tipo in ('Regular', 'Nuevo')),
  -- Definir variables
  primary key (codAlumno, codigoSemestre),
  foreign key (codAlumno) references alumno(codAlumno),
  foreign key (codigoSemestre) references semestre(codigoSemestre)
);

CREATE TABLE docente(
  -- Atributos
  codDocente smallint,
  nombreApellido varchar(100) not null,
  -- Definir claves
  primary key (codDocente)
);

CREATE TABLE docenteContratado(
  -- Atributos
  codDocente smallint,
  codigoSemestre varchar(6),
  -- Definir claves
  primary key (codDocente, codigoSemestre),
  foreign key (codDocente) references docente(codDocente),
  foreign key (codigoSemestre) references semestre(codigoSemestre)
);

CREATE TABLE tutoria(
  -- Atributos
  idTutoria smallint,
  codAlumno varchar(6),
  codigoSemestre varchar(6),
  codDocente smallint,
  -- Definir claves
  primary key (idTutoria),
  foreign key (codAlumno) references alumno(codAlumno),
  foreign key (codDocente) references docente(codDocente),
  foreign key (codigoSemestre) references semestre(codigoSemestre)
);

-- Creacion de triggers

-- Creacion de funciones

-- Creacion de procedimientos almacenados