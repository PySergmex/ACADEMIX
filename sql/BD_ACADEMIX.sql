-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-12-2025 a las 04:49:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `academix`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id_calificacion` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL,
  `id_usuario_estudiante` int(11) NOT NULL,
  `calificacion_valor` decimal(6,2) NOT NULL,
  `calificacion_comentario` varchar(255) DEFAULT NULL,
  `calificacion_fecha_registro` datetime DEFAULT current_timestamp(),
  `id_usuario_registro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_estatus_inscripcion`
--

CREATE TABLE `cat_estatus_inscripcion` (
  `id_estatus_inscripcion` tinyint(3) UNSIGNED NOT NULL,
  `estatus_inscripcion_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cat_estatus_inscripcion`
--

INSERT INTO `cat_estatus_inscripcion` (`id_estatus_inscripcion`, `estatus_inscripcion_descripcion`) VALUES
(1, 'Pendiente'),
(2, 'Aprobado'),
(3, 'Rechazado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_estatus_usuario`
--

CREATE TABLE `cat_estatus_usuario` (
  `id_estatus_usuario` tinyint(3) UNSIGNED NOT NULL,
  `estatus_usuario_descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cat_estatus_usuario`
--

INSERT INTO `cat_estatus_usuario` (`id_estatus_usuario`, `estatus_usuario_descripcion`) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Suspendido'),
(4, 'Pendiente'),
(5, 'Eliminado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_roles`
--

CREATE TABLE `cat_roles` (
  `id_rol` tinyint(3) UNSIGNED NOT NULL,
  `rol_nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cat_roles`
--

INSERT INTO `cat_roles` (`id_rol`, `rol_nombre`) VALUES
(1, 'Administrador'),
(2, 'Profesor'),
(3, 'Estudiante'),
(4, 'Invitado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL,
  `id_usuario_estudiante` int(11) NOT NULL,
  `entrega_fecha` datetime DEFAULT current_timestamp(),
  `entrega_ruta_archivo` varchar(255) DEFAULT NULL,
  `entrega_observaciones` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_usuario_estudiante` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_estatus_inscripcion` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `inscripcion_fecha_solicitud` datetime DEFAULT current_timestamp(),
  `inscripcion_fecha_resolucion` datetime DEFAULT NULL,
  `id_usuario_resolvio` int(11) DEFAULT NULL,
  `inscripcion_comentario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `materia_nombre` varchar(100) NOT NULL,
  `materia_descripcion` text DEFAULT NULL,
  `materia_activa` tinyint(1) NOT NULL DEFAULT 1,
  `materia_horario` varchar(100) DEFAULT NULL,
  `id_usuario_maestro` int(11) NOT NULL,
  `materia_fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `tarea_titulo` varchar(200) NOT NULL,
  `tarea_descripcion` text DEFAULT NULL,
  `tarea_fecha_creacion` datetime DEFAULT current_timestamp(),
  `tarea_fecha_limite` datetime NOT NULL,
  `tarea_ponderacion` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tarea_archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario_nombres` varchar(100) NOT NULL,
  `usuario_apellido_paterno` varchar(50) NOT NULL,
  `usuario_apellido_materno` varchar(50) DEFAULT NULL,
  `usuario_correo` varchar(100) NOT NULL,
  `usuario_password` varchar(255) NOT NULL,
  `id_rol` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `id_estatus_usuario` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `usuario_fecha_creacion` datetime DEFAULT current_timestamp(),
  `usuario_fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario_nombres`, `usuario_apellido_paterno`, `usuario_apellido_materno`, `usuario_correo`, `usuario_password`, `id_rol`, `id_estatus_usuario`, `usuario_fecha_creacion`, `usuario_fecha_actualizacion`) VALUES
(1, 'Sergio Eduardo', 'Cervantes', 'Mata', 'admin@academix.com', '$2y$10$FeUb9MIhAM3wIBX1uKrf3.jYVZtt0ikfYadW4Hmfk8MCWN1EYXUqa', 1, 1, '2025-11-21 17:30:00', '2025-11-30 21:39:50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD KEY `id_usuario_estudiante` (`id_usuario_estudiante`),
  ADD KEY `id_usuario_registro` (`id_usuario_registro`),
  ADD KEY `idx_calificaciones_tarea` (`id_tarea`);

--
-- Indices de la tabla `cat_estatus_inscripcion`
--
ALTER TABLE `cat_estatus_inscripcion`
  ADD PRIMARY KEY (`id_estatus_inscripcion`);

--
-- Indices de la tabla `cat_estatus_usuario`
--
ALTER TABLE `cat_estatus_usuario`
  ADD PRIMARY KEY (`id_estatus_usuario`);

--
-- Indices de la tabla `cat_roles`
--
ALTER TABLE `cat_roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id_entrega`),
  ADD UNIQUE KEY `id_tarea` (`id_tarea`,`id_usuario_estudiante`),
  ADD KEY `id_usuario_estudiante` (`id_usuario_estudiante`),
  ADD KEY `idx_entregas_tarea` (`id_tarea`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `id_usuario_estudiante` (`id_usuario_estudiante`,`id_materia`),
  ADD KEY `id_estatus_inscripcion` (`id_estatus_inscripcion`),
  ADD KEY `id_usuario_resolvio` (`id_usuario_resolvio`),
  ADD KEY `idx_inscripciones_estudiante` (`id_usuario_estudiante`),
  ADD KEY `idx_inscripciones_materia` (`id_materia`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`),
  ADD KEY `idx_materias_maestro` (`id_usuario_maestro`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_usuario_creador` (`id_usuario_creador`),
  ADD KEY `idx_tareas_materia` (`id_materia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario_correo` (`usuario_correo`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_estatus_usuario` (`id_estatus_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id_calificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`),
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`id_usuario_estudiante`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `calificaciones_ibfk_3` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `entregas_ibfk_1` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`),
  ADD CONSTRAINT `entregas_ibfk_2` FOREIGN KEY (`id_usuario_estudiante`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_usuario_estudiante`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`id_estatus_inscripcion`) REFERENCES `cat_estatus_inscripcion` (`id_estatus_inscripcion`),
  ADD CONSTRAINT `inscripciones_ibfk_4` FOREIGN KEY (`id_usuario_resolvio`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`id_usuario_maestro`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `cat_roles` (`id_rol`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_estatus_usuario`) REFERENCES `cat_estatus_usuario` (`id_estatus_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
