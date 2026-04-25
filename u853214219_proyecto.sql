-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-04-2026 a las 18:05:20
-- Versión del servidor: 11.8.6-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u853214219_proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `puntuacion` int(11) DEFAULT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `usuario_id`, `curso_id`, `puntuacion`, `comentario`, `created_at`) VALUES
(1, 2, 1, 4, 'Es muy facil de entender', '2026-04-25 12:05:13'),
(2, 3, 2, 4, 'Muy buen contenido, aunque algunos temas se quedaron cortos. Recomendado.', '2026-04-25 12:05:13'),
(3, 4, 3, 5, 'Me encantó la parte de Pandas. Muy útil para mi trabajo.', '2026-04-25 12:05:13'),
(4, 6, 4, 4, 'Buen curso, pero se necesita más práctica. Los conceptos base son sólidos.', '2026-04-25 12:05:13'),
(5, 2, 2, 2, 'Estuvo chido', '2026-04-25 12:06:16'),
(6, 2, 7, 4, 'Esta facil de entender solo falta mejorar las lecciones', '2026-04-25 17:55:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `profesor_id` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre`, `descripcion`, `profesor_id`, `imagen`, `created_at`) VALUES
(1, 'Desarrollo Web Moderno con React', 'Aprende React desde cero hasta nivel avanzado, incluyendo hooks, context API y Next.js', 1, 'react.png', '2026-04-25 12:05:13'),
(2, 'Marketing Digital para Negocios', 'Estrategias de marketing en redes sociales, SEO, email marketing y analítica web', 2, 'mk.jpg', '2026-04-25 12:05:13'),
(3, 'Python para Análisis de Datos', 'Manipulación de datos con Pandas, NumPy y visualización con Matplotlib', 3, 'pyh.jpg', '2026-04-25 12:05:13'),
(4, 'Inteligencia Artificial práctica', 'Machine Learning, redes neuronales y TensorFlow para proyectos reales', 4, 'ia.png', '2026-04-25 12:05:13'),
(5, 'UX/UI Design Profesional', 'Diseño de interfaces centrado en el usuario, prototipado con Figma y pruebas de usabilidad', 5, 'ux.jpg', '2026-04-25 12:05:13'),
(6, 'Desarrollo de Apps Móviles con Flutter', 'Crea apps para iOS y Android con un solo código usando Flutter y Dart', 1, 'flut.jpg', '2026-04-25 12:05:13'),
(7, 'Curso para principiantes de lenguaje C', 'Este es un curso para gente que tiene nulo conociemiento en lenguaje c', 6, 'curso_1777139662_69ecffcea3d7b.png', '2026-04-25 17:54:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `nombre`, `correo`, `especialidad`, `created_at`) VALUES
(1, 'María González López', 'maria.gonzalez1131@gmail.com', 'Desarrollo Web y JavaScript', '2026-04-25 12:05:13'),
(2, 'Carlos Rodríguez Fernández', 'carlos.rodriguez1131@gmail.com', 'Marketing Digital y SEO', '2026-04-25 12:05:13'),
(3, 'Ana Martínez Sánchez', 'ana.martinez1131@gmail.com', 'Ciencia de Datos y Python', '2026-04-25 12:05:13'),
(4, 'Javier López Torres', 'javier.lopez1131@gmail.com', 'Inteligencia Artificial', '2026-04-25 12:05:13'),
(5, 'Laura García Ruiz', 'laura.garcia1131@gmail.com', 'Diseño UX/UI', '2026-04-25 12:05:13'),
(6, 'Angel Huerta Cadena ', 'Angel12@gmail.com', 'Programador en lengauje c', '2026-04-25 17:50:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_videos`
--

CREATE TABLE `progreso_videos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `visto` tinyint(1) DEFAULT 0,
  `fecha_visto` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `progreso_videos`
--

INSERT INTO `progreso_videos` (`id`, `usuario_id`, `video_id`, `visto`, `fecha_visto`) VALUES
(1, 2, 1, 1, '2025-04-20 10:00:00'),
(2, 2, 2, 1, '2025-04-21 11:00:00'),
(3, 2, 3, 1, '2025-04-22 09:30:00'),
(4, 3, 5, 1, '2026-04-25 12:05:13'),
(5, 3, 6, 1, '2026-04-25 12:05:13'),
(6, 3, 7, 1, '2026-04-25 12:05:13'),
(7, 4, 8, 1, '2025-04-23 14:00:00'),
(8, 4, 9, 1, '2025-04-24 15:00:00'),
(9, 5, 14, 1, '2025-04-25 16:00:00'),
(10, 2, 5, 1, '2026-04-25 12:05:19'),
(11, 2, 6, 1, '2026-04-25 12:05:57'),
(12, 2, 7, 1, '2026-04-25 12:06:01'),
(13, 2, 8, 1, '2026-04-25 12:06:36'),
(14, 2, 4, 1, '2026-04-25 12:09:16'),
(15, 2, 9, 1, '2026-04-25 17:23:45'),
(16, 2, 10, 1, '2026-04-25 17:27:21'),
(17, 2, 11, 1, '2026-04-25 17:29:04'),
(18, 2, 12, 1, '2026-04-25 17:29:45'),
(19, 2, 14, 1, '2026-04-25 17:31:01'),
(20, 2, 15, 1, '2026-04-25 17:37:55'),
(21, 2, 17, 1, '2026-04-25 17:38:01'),
(22, 2, 18, 1, '2026-04-25 17:41:40'),
(23, 2, 19, 1, '2026-04-25 17:41:48'),
(24, 2, 20, 1, '2026-04-25 17:55:02'),
(25, 2, 21, 1, '2026-04-25 17:55:08'),
(26, 2, 22, 1, '2026-04-25 17:55:11'),
(27, 7, 20, 1, '2026-04-25 17:58:07'),
(28, 7, 21, 1, '2026-04-25 17:58:10'),
(29, 7, 22, 1, '2026-04-25 17:58:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('admin','cliente') DEFAULT 'cliente',
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `tema` enum('claro','oscuro') DEFAULT 'claro',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contraseña`, `rol`, `imagen_perfil`, `tema`, `created_at`) VALUES
(1, 'David Gamboa Melendez', 'davidgamboa317@gmail.com', 'Gamboa10', 'admin', NULL, 'claro', '2026-04-25 12:05:13'),
(2, 'Jesús Ramírez Pérez', 'jesus.ramirez1131@gmail.com', 'cliente123', 'cliente', 'avatar_2_1777120008.png', 'oscuro', '2026-04-25 12:05:13'),
(3, 'Fernanda Castro López', 'fernanda.castro1131@gmail.com', 'cliente123', 'cliente', NULL, 'oscuro', '2026-04-25 12:05:13'),
(4, 'Alejandro Mendoza Ríos', 'alejandro.mendoza1131@gmail.com', 'cliente123', 'cliente', NULL, 'claro', '2026-04-25 12:05:13'),
(5, 'Valeria Herrera Soto', 'valeria.herrera1131@gmail.com', 'cliente123', 'cliente', NULL, 'claro', '2026-04-25 12:05:13'),
(6, 'Ricardo Fuentes Gil', 'ricardo.fuentes1131@gmail.com', 'cliente123', 'cliente', NULL, 'oscuro', '2026-04-25 12:05:13'),
(7, 'Brenda Karen Gamboa Melendez', 'Brendagam12@gmail.com', 'bren12', 'cliente', NULL, 'claro', '2026-04-25 17:56:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `visualizaciones` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `curso_id`, `titulo`, `descripcion`, `orden`, `url`, `visualizaciones`, `created_at`) VALUES
(1, 1, 'Introducción a React', 'Historia, instalación y primer componente', 1, 'https://www.youtube.com/embed/MPLN1ahXgcs', 250, '2026-04-25 12:05:13'),
(2, 1, 'Componentes y Props', 'Creación de componentes funcionales y paso de propiedades', 2, 'https://www.youtube.com/embed/SxK_pgoticY', 250, '2026-04-25 12:05:13'),
(3, 1, 'Estado y Eventos', 'useState y manejo de eventos en React', 3, 'https://www.youtube.com/embed/KWeQ-FqyMcw', 250, '2026-04-25 12:05:13'),
(4, 1, 'Efectos secundarios y APIs', 'useEffect y llamadas a APIs', 4, 'https://www.youtube.com/embed/y0ELkQ3D9sU', 251, '2026-04-25 12:05:13'),
(5, 2, 'Introducción al Marketing Digital', 'Conceptos clave y evolución', 1, 'https://www.youtube.com/embed/Ctd6BTuZmjA', 181, '2026-04-25 12:05:13'),
(6, 2, 'SEO básico', 'Optimización en buscadores', 2, 'https://www.youtube.com/embed/_ewwO0Y1zb4', 181, '2026-04-25 12:05:13'),
(7, 2, 'Publicidad en redes sociales', 'Facebook Ads y Google Ads', 3, 'https://www.youtube.com/embed/hgqRvnyjQak', 181, '2026-04-25 12:05:13'),
(8, 3, 'Introducción a Jupyter Notebook', 'Configuración y primeros pasos', 1, 'https://www.youtube.com/embed/mENHDQ8SLsI', 321, '2026-04-25 12:05:13'),
(9, 3, 'NumPy para cálculo numérico', 'Arrays y operaciones', 2, 'https://www.youtube.com/embed/EaWsOcc7R2M', 321, '2026-04-25 12:05:13'),
(10, 3, 'Pandas para manipulación de datos', 'DataFrames y series', 3, 'https://www.youtube.com/embed/35kWoAtgzic', 321, '2026-04-25 12:05:13'),
(11, 3, 'Visualización con Matplotlib', 'Gráficos personalizados', 4, 'https://www.youtube.com/embed/q5txLirbIgM', 321, '2026-04-25 12:05:13'),
(12, 4, 'Fundamentos de ML', 'Regresión lineal y clasificación', 1, 'https://www.youtube.com/embed/8nSGUb9zCco', 96, '2026-04-25 12:05:13'),
(13, 4, 'Redes neuronales con TensorFlow', 'Construcción de una red simple', 2, 'https://www.youtube.com/embed/sQqiW7TGoUU', 95, '2026-04-25 12:05:13'),
(14, 5, 'Principios de UX', 'Investigación y arquitectura de la información', 1, 'https://www.youtube.com/embed/qAG9OHma300', 211, '2026-04-25 12:05:13'),
(15, 5, 'Diseño en Figma', 'Componentes y prototipado', 2, 'https://www.youtube.com/embed/GoNzQHc7-qo', 211, '2026-04-25 12:05:13'),
(16, 5, 'Pruebas de usabilidad', 'Métodos y análisis', 3, 'https://www.youtube.com/embed/IoNv03Q5pok', 210, '2026-04-25 12:05:13'),
(17, 6, 'Introducción a Flutter', 'Instalación y primer proyecto', 1, 'https://www.youtube.com/embed/zNmDOXbTugE', 131, '2026-04-25 12:05:13'),
(18, 6, 'Widgets básicos', 'Stateless vs Stateful widgets', 2, 'https://www.youtube.com/embed/9ACoJV3xa38', 131, '2026-04-25 12:05:13'),
(19, 6, 'Navegación y rutas', 'Movimiento entre pantallas', 3, ' https://www.youtube.com/embed/vOtw69YwofI', 131, '2026-04-25 12:05:13'),
(20, 7, 'Inicio al lenguaje C', 'Pequeña introducción al lenguaje C', 1, 'https://www.youtube.com/embed/V63VmbZcXHM', 2, '2026-04-25 17:54:22'),
(21, 7, 'Operadores lenguaje C', 'Todos los operadores del lenaguaje C', 2, 'https://www.youtube.com/embed/62_mg-8TbBE', 2, '2026-04-25 17:54:22'),
(22, 7, 'Funciones del lenguaje C', 'Tipos de funciones del lenguaje C', 3, 'https://www.youtube.com/embed/nmC_C-2WyUE', 2, '2026-04-25 17:54:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_curso` (`usuario_id`,`curso_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `progreso_videos`
--
ALTER TABLE `progreso_videos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progreso` (`usuario_id`,`video_id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `progreso_videos`
--
ALTER TABLE `progreso_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `progreso_videos`
--
ALTER TABLE `progreso_videos`
  ADD CONSTRAINT `progreso_videos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progreso_videos_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
