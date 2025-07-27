-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-07-2025 a las 02:04:35
-- Versión del servidor: 8.0.17
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sweetdreams`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `editar_producto` (IN `p_id` INT, IN `p_nombre` VARCHAR(50), IN `p_precio` DECIMAL(10,2), IN `p_stock` INT(11))  BEGIN
    DECLARE existencia INT;
    
    SELECT COUNT(*) INTO existencia
    FROM productos
    WHERE id_producto = p_id;

    IF existencia = 0 THEN
        SELECT 'El producto no existe' AS error;
    ELSEIF p_nombre = '' THEN
        SELECT 'El nombre no puede estar vacío' AS error;
    ELSEIF p_precio <= 0 THEN
        SELECT 'El precio debe ser mayor que cero' AS error;
    ELSEIF p_stock < 0 THEN
        SELECT 'No puedes quitar stock al actualizar' AS error;
    ELSE
        UPDATE productos
        SET nombre = p_nombre,
            precio = p_precio,
            stock = stock + p_stock
        WHERE id_producto = p_id;

        SELECT 'Producto actualizado con éxito' AS mensaje;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `contraseña` varchar(20) NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT '0.00'
) ;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `correo`, `contraseña`, `saldo`) VALUES
(1, 'admin', 'admin@gmail.com', 'soyeladmin', '0.00'),
(2, 'David', 'deivo@gmail.com', 'maincra', '0.00'),
(3, 'Abrila', 'abi@gmail.com', 'a', '25.00'),
(4, 'jenis', 'Jenis@gmail.com', 'soylajenis', '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0'
) ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio`, `stock`) VALUES
(1, 'CHURRITOS NATURALES', '10.00', 25),
(3, 'CHURRITOS FUEGO', '10.00', 50),
(4, 'CHURRITOS HABANERO', '10.00', 50),
(5, 'PAPAS NATURALES', '12.00', 50),
(6, 'PAPAS DE QUESO', '12.00', 50),
(7, 'PAPAS ADOBADAS', '12.00', 50),
(8, 'PALOMITAS', '10.00', 50),
(9, 'PALOMITAS QUESO', '10.00', 50),
(10, 'DORITOS', '13.00', 50),
(11, 'TOSTITOS', '13.00', 50),
(12, 'CHEETO DE QUESO', '13.00', 50),
(13, 'HABAS', '8.00', 50),
(14, 'ANILLO', '8.00', 50),
(15, 'LAGRIMA', '8.00', 50),
(16, 'PANDITAS', '6.00', 50),
(17, 'LOMBRIZ', '6.00', 50),
(18, 'LOMBRIZ ACIDITA', '6.00', 50),
(19, 'FRUTITAS', '6.00', 50),
(20, 'MANGUITOS', '6.00', 50),
(21, 'ARITOS', '6.00', 50),
(22, 'PINGUINOS', '15.00', 50),
(23, 'DIENTES', '6.00', 50),
(24, 'CERECITAS', '6.00', 50),
(25, 'JELLY BEANS', '6.00', 50),
(26, 'LUNETAS', '6.00', 50),
(27, 'PASITAS', '6.00', 50),
(28, 'SUSPIROS', '6.00', 50),
(29, 'PEPINO', '10.00', 50),
(30, 'JÍCAMA', '10.00', 50),
(31, 'FRUTA DE TEMPORADA', '10.00', 50),
(32, 'ZANAHORIA', '10.00', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_venta_cliente` (`id_cliente`),
  ADD KEY `fk_venta_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `fk_venta_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
