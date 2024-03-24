-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-09-2023 a las 01:06:03
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `referidos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `activarCliente` (IN `_id` INT)   UPDATE clientes SET status = '1' WHERE status = '0' and id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ClienteDeposito` (IN `id_m` INT, IN `descripcion_m` TEXT, IN `hash_m` TEXT, IN `red_m` TEXT, IN `monto` FLOAT, IN `fecha_1` DATE, IN `id_c` INT, IN `nombre_c` TEXT, IN `billetera` TEXT, IN `referencia` TEXT, IN `foto` TEXT, IN `fecha_2` DATE)  NO SQL INSERT INTO transacciones (transacciones.id_monedero, transacciones.descripcion_monedero, transacciones.hash, transacciones.red, transacciones.monto, transacciones.fecha, transacciones.id_cliente, transacciones.nombre_cliente, transacciones.billetera_cliente, transacciones.referencia_pago, transacciones.foto_pago, transacciones.tipo, transacciones.status, transacciones.fecha_transaccion) VALUES (id_m, descripcion_m , hash_m, red_m, monto, fecha_1, id_c, nombre_c , billetera, referencia, foto, "deposito", 0, fecha_2)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ClienteOrdenarPaquetes` (IN `paquete_id` INT, IN `cliente_id` INT, IN `_nombre_paquete` TEXT, IN `monedero_id` INT, IN `_descripcion` TEXT, IN `_monto` FLOAT, IN `_fecha` TEXT, IN `_meses` INT, IN `_porcentaje` FLOAT)   INSERT INTO orden (
    orden.id_cliente,
    orden.id_paquete,
    orden.nombre_paquete,
    orden.id_monedero,
    orden.descripcion_monedero,
    orden.monto,
    orden.fecha,
    orden.meses,
    orden.porcentaje
)
VALUES (cliente_id, paquete_id, _nombre_paquete, monedero_id, _descripcion, _monto, _fecha, _meses, _porcentaje)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ClientePaquetes` ()  NO SQL SELECT * FROM paquetes WHERE paquetes.status = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ClienteRetiro` (IN `id_m` INT, IN `descripcion_m` TEXT, IN `hash_m` TEXT, IN `red_m` TEXT, IN `monto` FLOAT, IN `fecha` DATE, IN `id_c` INT, IN `nombre_c` TEXT, IN `billetera` TEXT)   INSERT INTO transacciones
(transacciones.id_monedero, transacciones.descripcion_monedero, transacciones.hash, transacciones.red, transacciones.monto, transacciones.fecha, 
 transacciones.id_cliente, 
 transacciones.nombre_cliente, 
 transacciones.billetera_cliente, 
 transacciones.tipo, 
 transacciones.status, 
 transacciones.fecha_transaccion)
VALUES (id_m, descripcion_m, hash_m, red_m, monto, fecha, id_c, nombre_c, billetera, "retiro", 0, "0000-00-00")$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultaMonederoId` (IN `id` INT)  NO SQL SELECT * FROM monedero WHERE monedero.id = id AND monedero.status != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultaMonederos` ()  NO SQL SELECT * FROM monedero WHERE monedero.status = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultaMonederosAdmin` ()  NO SQL SELECT * from monedero where STATUS!=2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarAdminMensajes` (IN `id` INT)   SELECT * FROM mensajes WHERE mensajes.id_cliente = id AND mensajes.tipo = "admin" ORDER BY mensajes.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarAdminMontoDepositos` ()   SELECT SUM(monto) FROM transacciones WHERE tipo="deposito" AND status = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarAdminMontoRetiros` ()   SELECT SUM(monto) FROM transacciones WHERE tipo = "retiro" AND status = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarClienteId` (IN `id` INT)  NO SQL SELECT * FROM clientes WHERE clientes.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarClienteOrdenes` (IN `id` INT)   SELECT orden.id, orden.id_cliente, orden.id_paquete, orden.id_monedero, orden.nombre_paquete, orden.monto, orden.fecha, orden.meses, orden.porcentaje, orden.status, paquetes.img FROM orden INNER JOIN paquetes ON paquetes.id = orden.id_paquete
WHERE orden.id_cliente = id ORDER BY `id` DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarClientesAdmin` ()  NO SQL SELECT * FROM clientes where STATUS != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarClientesMensajes` (IN `admin_id` INT(1))   SELECT * FROM mensajes WHERE mensajes.tipo = "cliente" AND mensajes.id_admin = admin_id OR mensajes.id_admin = 0
ORDER by mensajes.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarDepositoId` (IN `_id` INT)   SELECT * FROM transacciones WHERE id = _id and tipo = "deposito"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarDepositosAdmin` ()  NO SQL SELECT t.*, c.dni, c.telefono, c.correo from transacciones t INNER JOIN clientes c ON t.id_cliente = c.id where t.STATUS != 3 and tipo = "deposito" ORDER BY id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarDepositosUser` (IN `id` INT)  NO SQL SELECT * FROM transacciones  where transacciones.status != 3 and transacciones.tipo = "deposito" and transacciones.id_cliente = id
ORDER BY transacciones.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarMensajesEnviadosAdmin` (IN `id` INT)   SELECT * FROM mensajes WHERE tipo = "admin" AND id_admin = id ORDER BY mensajes.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarMensajesEnviadosCliente` (IN `id` INT)   SELECT * FROM mensajes WHERE tipo = "cliente" AND mensajes.id_cliente = id ORDER BY mensajes.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarOrden` (IN `_id` INT)   SELECT * FROM orden WHERE orden.id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarPaqueteId` (IN `_id` INT)   SELECT * FROM paquetes WHERE id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarPaquetesAdmin` ()  NO SQL SELECT * from paquetes where STATUS!=2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarPermisologiaAdmin` ()  NO SQL SELECT * FROM permisologia where STATUS != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarPermisosEnRol` (IN `id` INT)  NO SQL SELECT * FROM permisos INNER JOIN roles ON permisos.id_rol = roles.id WHERE permisos.id_rol = id AND roles.status != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarReferidosD` (IN `ref` TEXT)  NO SQL SELECT * FROM clientes WHERE clientes.referido = ref AND clientes.status != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarRetiroId` (IN `_id` INT)   SELECT * FROM transacciones WHERE id = _id and tipo = "retiro"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarRetirosAdmin` ()  NO SQL SELECT t.*, c.dni, c.telefono, c.correo from transacciones t INNER JOIN clientes c ON t.id_cliente = c.id where t.STATUS != 3 and tipo = "retiro" ORDER BY `id` DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarRetirosUser` (IN `id` INT)  NO SQL SELECT * FROM transacciones where transacciones.status != 3 and transacciones.tipo = "retiro" and transacciones.id_cliente = id ORDER BY transacciones.id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultarRoles` ()  NO SQL select * from roles where id != 1 and status != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarSaldo` (IN `_id` INT)   SELECT round(saldo, 2) FROM clientes WHERE id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarUsuarioCorreo` (IN `_correo` VARCHAR(60))   SELECT correo FROM detalle_usuario WHERE correo = _correo$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarUsuarioDocumento` (IN `_documento` INT)   SELECT documento FROM detalle_usuario WHERE documento = _documento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarUsuarioNombre` (IN `_nombre` VARCHAR(40))   SELECT nombre FROM usuario WHERE nombre = _nombre$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultarUsuarios` ()  NO SQL select usuario.id, detalle_usuario.documento, detalle_usuario.nombre, detalle_usuario.apellido, detalle_usuario.telefono, detalle_usuario.correo, roles.descripcion, detalle_usuario.nacionalidad,  usuario.status,usuario.id_rol,usuario.nombre,detalle_usuario.fecha_nac,detalle_usuario.direccion,
usuario.clave from usuario INNER JOIN detalle_usuario on detalle_usuario.id_usuario= usuario.id INNER JOIN roles on roles.id=usuario.id_rol where usuario.status != 2 and usuario.id_rol != 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CrearMonederosAdmin` (IN `nombre` TEXT, IN `hash` TEXT, IN `red` TEXT)  NO SQL INSERT INTO monedero (nombre, HASH, red, status) VALUES ( nombre, hash, red, 1)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CrearPaquetesAdmin` (IN `nombre` TEXT, IN `precio` FLOAT, IN `porcentaje` FLOAT, IN `_img` TEXT, IN `_descripcion` VARCHAR(110), IN `_meses` INT)  NO SQL INSERT INTO paquetes (nombre, precio, porcentaje,img,descripcion, meses, status) VALUES ( nombre, precio, porcentaje, _img,_descripcion, _meses, 1)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CrearUsuario` (IN `_nombre` VARCHAR(40), IN `clave` VARCHAR(40), IN `_rol` INT, IN `_fecha` DATE)   INSERT INTO usuario VALUES(null,_nombre, clave, _fecha, _rol, 1)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DashboardMensaje1` (IN `mensaje` TEXT)   UPDATE dashboard_mensajes SET dashboard_mensajes.mensaje_1 = mensaje$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DashboardMensaje2` (IN `mensaje` TEXT)   UPDATE dashboard_mensajes SET dashboard_mensajes.mensaje_2 = mensaje$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EditarImagenPaqueteAdmin` (IN `_id` INT, IN `_img` TEXT)   UPDATE paquetes SET img = _img WHERE id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EditarMonederoAdmin` (IN `id` INT, IN `nombre` TEXT, IN `hash` TEXT, IN `red` TEXT)  NO SQL UPDATE monedero SET monedero.nombre = nombre, monedero.hash = hash, monedero.red = red WHERE monedero.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EditarPaqueteAdmin` (IN `id` INT, IN `nombre` TEXT, IN `precio` FLOAT, IN `porcentaje` FLOAT, IN `descripcion` VARCHAR(110), IN `meses` INT)  NO SQL UPDATE paquetes SET paquetes.nombre = nombre, paquetes.precio = precio, paquetes.porcentaje = porcentaje, paquetes.descripcion = descripcion, paquetes.meses = meses WHERE paquetes.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarMonederoAdmin` (IN `id` INT)  NO SQL UPDATE monedero SET status = 2 WHERE monedero.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarPaquetesAdmin` (IN `id` INT)  NO SQL UPDATE paquetes SET status = 2 WHERE paquetes.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarRolAdmin` (IN `id` INT)  NO SQL UPDATE roles SET status = 2 WHERE roles.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EnviarMensajeAdmin` (IN `id` INT, IN `id_admin` INT, IN `usuario` TEXT, IN `asunto` VARCHAR(30), IN `mensaje` TEXT)   INSERT INTO mensajes(
    mensajes.id_cliente,
    mensajes.id_admin,
    mensajes.usuario,
    mensajes.asunto,
    mensajes.mensaje,
    mensajes.tipo
)
VALUES (id,id_admin, usuario, asunto, mensaje, "admin")$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EnviarMensajeCliente` (IN `id` INT, IN `id_admin` INT, IN `usuario` TEXT, IN `asunto` VARCHAR(30), IN `mensaje` TEXT)   INSERT INTO mensajes(
    mensajes.id_cliente,
    mensajes.id_admin,
    mensajes.usuario,
    mensajes.asunto,
    mensajes.mensaje,
    mensajes.tipo
    )
VALUES (id, id_admin, usuario, asunto, mensaje, "cliente")$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `login` (IN `_correo` VARCHAR(60), IN `_contra` TEXT)  NO SQL SELECT * FROM clientes WHERE correo=_correo AND contrasena=_contra and status!= 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `loginAdmin` (IN `_email` VARCHAR(30), IN `_contra` VARCHAR(30))  NO SQL SELECT a.id,b.nombre,b.apellido,b.documento as dni,b.telefono,b.fecha_nac as fecha_n,b.correo,a.id_rol, c.descripcion, '' as img FROM usuario a inner join detalle_usuario b on a.id=b.id_usuario INNER JOIN roles c on a.id_rol=c.id WHERE a.nombre=_email AND a.clave=_contra and a.status=1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `OrdenDePaquetes` (IN `paquete_id` INT, IN `_cantidad` INT)   UPDATE paquetes SET cantidad = cantidad - _cantidad WHERE id = paquete_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RechazarDepositoAdmin` (IN `_id` INT)   UPDATE transacciones SET status = 2 WHERE id = _id and tipo = "deposito"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RechazarRetiroAdmin` (IN `_id` INT)   UPDATE transacciones SET status = 2 WHERE id = _id and tipo = "retiro"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registroCliente` (IN `_nombre` TEXT, IN `_apellido` TEXT, IN `_dni` VARCHAR(20), IN `_img` TEXT, IN `_telefono` VARCHAR(30), IN `_fecha_n` DATE, IN `_correo` TEXT, IN `_contrasena` TEXT, IN `_codigo_ref` TEXT, IN `_referido` TEXT)  NO SQL INSERT INTO clientes(nombre, apellido, dni, img, telefono, fecha_n, correo, contrasena, codigo_ref, referido, saldo, status) values(_nombre, _apellido, _dni, _img, _telefono, _fecha_n, _correo, _contrasena, _codigo_ref, _referido, 0, 0)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registroClienteAdmin` (IN `_nombre` TEXT, IN `_apellido` TEXT, IN `_dni` VARCHAR(20), IN `_img` TEXT, IN `_telefono` VARCHAR(30), IN `_fecha_n` DATE, IN `_correo` VARCHAR(50), IN `_contrasena` TEXT, IN `_codigo_ref` TEXT, IN `_referido` TEXT)  NO SQL INSERT INTO clientes(nombre, apellido, dni, img, telefono, fecha_n, correo, contrasena, codigo_ref, referido, saldo, status) values(_nombre, _apellido, _dni, _img, _telefono, _fecha_n, _correo, _contrasena, _codigo_ref, _referido, 0, 1)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RelConsultarTickets` ()   SELECT * from ticket ORDER BY id DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RelCreateTicket` (IN `_codigo_cliente` VARCHAR(100), IN `_telefono` VARCHAR(60), IN `_cliente` VARCHAR(60), IN `_fecha_apertura` DATE, IN `_sede` TEXT)  NO SQL INSERT INTO ticket (codigo_cliente, telefono, cliente, fecha_apertura, sede, status) VALUES ( _codigo_cliente, _telefono, _cliente, _fecha_apertura, _sede, 'RECEPCION')$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RetirarPaquete` (IN `id` INT)   UPDATE orden SET status = 1 WHERE orden.id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RetirarSaldo` (IN `_id` INT, IN `_saldo` FLOAT)   UPDATE clientes SET saldo = saldo - _saldo WHERE id = _id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SumarSaldo` (IN `_id` INT, IN `_saldo` FLOAT)   UPDATE clientes SET saldo = saldo + _saldo WHERE id = _id AND status != 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updatePassword` (IN `_id` INT, IN `_password` TEXT)  NO SQL update clientes set contrasena=_password where id=_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updatePasswordAdmin` (IN `_id` INT, IN `_password` VARCHAR(30))  NO SQL update usuario set clave=_password where id=_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProfile` (IN `_id` INT, IN `_dni` VARCHAR(20), IN `_nombre` TEXT, IN `_apellido` TEXT, IN `_telefono` VARCHAR(30), IN `_correo` TEXT)  NO SQL update clientes set dni=_dni,nombre=_nombre,apellido=_apellido,telefono=_telefono,correo=_correo where id=_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProfileAdmin` (IN `_id` INT, IN `_dni` VARCHAR(20), IN `_nombre` TEXT, IN `_apellido` TEXT, IN `_telefono` VARCHAR(20), IN `_correo` VARCHAR(50))  NO SQL update detalle_usuario set documento=_dni,nombre=_nombre,apellido=_apellido,telefono=_telefono,correo=_correo where id_usuario=_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarCliente` (IN `_dni` VARCHAR(20), IN `_correo` TEXT)  NO SQL SELECT * FROM clientes WHERE status!='2' and (dni=_dni or correo=_correo)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarCodigo` (IN `cod` TEXT)  NO SQL SELECT * FROM clientes WHERE status != '2' and codigo_ref = cod$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarDepositoAdmin` (IN `_id` INT)   UPDATE transacciones SET status = 1 WHERE id = _id and tipo = "deposito"$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarDNI` (IN `_id` INT, IN `_dni` VARCHAR(20))  NO SQL SELECT * FROM clientes WHERE status!='2' and id!=_id and dni=_dni$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarDNIAdmin` (IN `_id` INT, IN `_dni` VARCHAR(20))  NO SQL SELECT * FROM detalle_usuario WHERE id_usuario!=_id and documento=_dni$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarEmail` (IN `_id` INT, IN `_correo` TEXT)  NO SQL SELECT * FROM clientes WHERE status!='2' and id!=_id and correo=_correo$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarEmailAdmin` (IN `_id` INT, IN `_correo` VARCHAR(50))  NO SQL SELECT * FROM detalle_usuario WHERE id_usuario!=_id and correo=_correo$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarMonederos2Admin` (IN `id` INT, IN `nombre` TEXT)  NO SQL SELECT * FROM monedero WHERE monedero.status != 2 and monedero.nombre = nombre and monedero.id != id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarMonederosAdmin` (IN `nombre` TEXT)  NO SQL SELECT * FROM monedero WHERE status!= 2 and monedero.nombre = nombre$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarPaquete2Admin` (IN `id` INT, IN `nombre` TEXT)  NO SQL SELECT * FROM paquetes WHERE status!= 2 and paquetes.nombre = nombre and paquetes.id != id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarPaqueteAdmin` (IN `nombre` TEXT)  NO SQL SELECT * FROM paquetes WHERE status!= 2 and paquetes.nombre = nombre$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarPassword` (IN `_id` INT, IN `_password` TEXT)  NO SQL select * from clientes WHERE id=_id and contrasena=_password$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validarPasswordAdmin` (IN `_id` INT, IN `_password` VARCHAR(30))  NO SQL select * from usuario WHERE id=_id and clave=_password$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidarRetiroAdmin` (IN `referencia` TEXT, IN `img` TEXT, IN `_id` INT)   UPDATE transacciones SET referencia_pago = referencia, foto_pago = img, status = 1 WHERE id =_id and tipo = 'retiro'$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `status` varchar(11) NOT NULL,
  `fecha` varchar(12) NOT NULL,
  `posicion` varchar(10) NOT NULL,
  `codigo_cliente` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `status`, `fecha`, `posicion`, `codigo_cliente`) VALUES
(1, '', '+584243177318', '', '', '0', '262961032');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL,
  `id_ejecucion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `descripcion`, `fecha`, `id_ejecucion`) VALUES
(1, 'kjhyuih', '2023-08-23 16:57:35', 2),
(2, 'dfas', '2023-08-29 16:49:32', 5),
(3, 'sdfasdf', '2023-08-29 16:49:34', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_usuario`
--

CREATE TABLE `detalle_usuario` (
  `id` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `fecha_nac` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` text NOT NULL,
  `nacionalidad` varchar(2) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detalle_usuario`
--

INSERT INTO `detalle_usuario` (`id`, `documento`, `nombre`, `apellido`, `fecha_nac`, `telefono`, `correo`, `direccion`, `nacionalidad`, `id_usuario`) VALUES
(1, '27698404', 'Jorge', 'vargas', '2011-07-18', '04243579366', 'correo@gmail.com', 'dasdasdasdasdas', 'P', 1),
(2, '123123122', 'Jose', 'Mota', '0000-00-00', '123332212', 'jessblack19@gmail.com', '1234', 'P', 2),
(3, '20523813', 'jorge', 'vargas', '0000-00-00', '04243579367', 'jorgeavargasm@hotmail.es', 'direccion', 'V', 5),
(5, '1234565412', 'Manuel', 'Del Nogal', '2023-02-14', '13346567', 'manueldelnogal@gmail.com', 'El saman', 've', 8),
(8, '27839168', 'jorge', 'vargas', '1990-10-18', '04243579367', 'jorgillo@gmail.com', 'direccion', 'VE', 11),
(9, '12345', 'Manuel', 'Del Nogal', '2023-02-15', '13346567', 'madnasito@gmail.com', 'El saman', 've', 12),
(10, '123', 'Manuel', 'Del Nogal', '2023-02-15', '13346567', 'madnasito2@gmail.com', 'El saman', 've', 13),
(11, '1234', 'Manuel', 'Del Nogal', '2023-02-16', '13346567', 'manueldelnogalfe@gmail.com', 'El saman', 've', 14),
(12, '123456213', 'Manuel', 'Del Nogal', '2023-02-16', '13346567', 'manueldelnoga3l@gmail.com', 'El saman', 've', 15),
(15, '124125', 'Manuel', 'Del Nogal', '2020-02-04', '13346567', 'manueldelnogal_editado@gmail.com', '1234', 've', 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnostico`
--

CREATE TABLE `diagnostico` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci NOT NULL,
  `diagnostico` text COLLATE utf8_unicode_ci NOT NULL,
  `piezas` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `diagnostico`
--

INSERT INTO `diagnostico` (`id`, `id_ticket`, `descripcion`, `diagnostico`, `piezas`, `status`) VALUES
(1, 2, 'Corraaaaa', 'dfgd', 'dsfsd', 0),
(2, 3, 'Cristal roto', 'fgdsfg', 'sdfasd', 1),
(3, 2, 'Corraaaaa', 'Ho', 'sdfsd', 1),
(4, 1, 'Correa mala', 'Diagnostoco', 'Piezas', 1),
(5, 4, 'Correa malaaaaa', 'Diagnostivo', 'Piezas', 1),
(6, 5, 'grrgrgrg', 'ersdfasdf', 'fasdfasdfasdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejecucion`
--

CREATE TABLE `ejecucion` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `fecha` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci NOT NULL,
  `descripcion_2` text COLLATE utf8_unicode_ci NOT NULL,
  `sugerencia` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha_sugerencia` int(11) DEFAULT NULL,
  `costo_sugerencia` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ejecucion`
--

INSERT INTO `ejecucion` (`id`, `id_ticket`, `fecha`, `descripcion`, `descripcion_2`, `sugerencia`, `fecha_sugerencia`, `costo_sugerencia`, `status`) VALUES
(1, 2, '24', 'sdfsd', '', 'sdfsd', 34, 2500, 0),
(2, 3, '24', 'sdfsd', '2', 'fdsdf', 34, 2500, 1),
(3, 2, '24', 'new', '', 'dsfssdfs', 34, 52, 1),
(4, 1, '24', 'Mensaje al usuario', '', 'La sugerencias', 34, 2500, 1),
(5, 4, '24', 'Mensaje al ', '5', 'La sugerencia', 34, 2500, 1),
(6, 5, '24', 'Mensaje', '', 'sugerencia', 34, 2500, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` text COLLATE utf8_unicode_ci NOT NULL,
  `codigo_empleado` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(11) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `codigo_empleado`, `status`) VALUES
(1, 'Empleado tal', '000010', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id` int(11) NOT NULL,
  `experiencia` varchar(250) NOT NULL COMMENT '¿Cómo calificarías tu experiencia en nuestro servicio?',
  `calidad` varchar(250) NOT NULL COMMENT '¿Cómo calificarías la calidad de los servicios realizados?',
  `recomendacion` varchar(250) NOT NULL COMMENT '¿Qué tan probable es que recomiendes nuestro servicio?',
  `respuesta` varchar(250) NOT NULL COMMENT '¿El tiempo de respuesta fue el adecuado?',
  `comentario` varchar(800) NOT NULL COMMENT '¿Te gustaría dejar algún comentario adicional para mejorar nuestro servicio?',
  `id_ticket` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisologia`
--

CREATE TABLE `permisologia` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `permisologia`
--

INSERT INTO `permisologia` (`id`, `descripcion`, `status`) VALUES
(1, 'paquetes', 1),
(2, 'cliente', 1),
(3, 'monederos', 1),
(4, 'retiros', 1),
(5, 'depositos', 1),
(6, 'mensajeria', 1),
(7, 'configuracion', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_permisologia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `id_rol`, `id_permisologia`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(5, 3, 2),
(7, 4, 1),
(8, 5, 1),
(9, 5, 2),
(10, 5, 3),
(11, 1, 4),
(12, 1, 5),
(13, 1, 6),
(14, 1, 7),
(15, 1, 7),
(19, 6, 1),
(20, 7, 2),
(21, 7, 4),
(22, 7, 6),
(23, 8, 1),
(24, 8, 2),
(25, 8, 3),
(32, 9, 7),
(33, 9, 6),
(34, 9, 2),
(35, 9, 4),
(36, 9, 5),
(37, 9, 3),
(38, 9, 1),
(39, 2, 5),
(40, 2, 4),
(41, 2, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion`
--

CREATE TABLE `recepcion` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `recepcion`
--

INSERT INTO `recepcion` (`id`, `id_ticket`, `descripcion`, `status`) VALUES
(1, 1, 'Correa mala', 0),
(2, 2, 'Corraaaaa', 0),
(3, 3, 'Cristal roto', 0),
(4, 4, 'Correa malaaaaa', 0),
(5, 5, 'grrgrgrg', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `descripcion`, `status`) VALUES
(1, 'superusuario', 1),
(2, 'basico', 1),
(3, 'Nuevo Rol', 1),
(4, 'test', 0),
(5, 'Colegiatura', 1),
(6, 'Editar', 1),
(7, 'Prueba 1', 1),
(8, 'P1', 1),
(9, 'newone', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sede`
--

INSERT INTO `sede` (`id`, `descripcion`, `status`) VALUES
(1, 'Guadalajara', 1),
(2, 'Jalisco', 1),
(3, 'Puerto Vallarda', 1),
(4, 'Tequila', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `codigo_cliente` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cliente` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha_apertura` date NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `tiempo_estimado` int(11) DEFAULT NULL,
  `cotizacion` float DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `hora` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `sede` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `sugerencia` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ticket`
--

INSERT INTO `ticket` (`id`, `codigo_cliente`, `telefono`, `cliente`, `fecha_apertura`, `fecha_cierre`, `tiempo_estimado`, `cotizacion`, `fecha_entrega`, `hora`, `sede`, `status`, `sugerencia`) VALUES
(1, '262961032', '+584243177318', '', '2023-08-23', NULL, 24, 2000, NULL, '', '', 'EJECUCION', 'Con sugerecia'),
(2, '6532479460', '+584243177318', '', '2023-08-23', '2023-08-23', 24, 34, '0000-00-00', '', '', 'ENTREGADO', 'Con sugerecia'),
(3, '7633667122', '+584243177318', '', '2023-08-23', '2023-08-23', 24, 2000, '0000-00-00', '', '', 'ENTREGADO', 'Sin sugerecia'),
(4, '546156', '+584243177319', 'Jorge', '2023-08-28', '2023-08-29', 24, 200, '0000-00-00', '', '', 'ENTREGADO', NULL),
(5, '1116516', '+584243177318	', 'sdfas', '2023-08-29', '2023-08-29', 24, 2500, '0000-00-00', '', '', 'ENTREGADO', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tienda`
--

CREATE TABLE `tienda` (
  `id` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `clave` varchar(30) NOT NULL,
  `registro` date NOT NULL,
  `id_rol` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `clave`, `registro`, `id_rol`, `status`) VALUES
(1, 'yanfranblancosalas@gmail.com', '123456', '2021-01-28', 1, 1),
(2, 'test@gmail.com', '123456', '2021-01-28', 2, 1),
(3, 'jessb', 'MTIzNDU2', '2021-02-01', 3, 1),
(4, 'superusuario2', 'MTIzNDU2', '2021-03-31', 3, 1),
(5, 'jorgeavargasm', 'VdL7SDTOzL', '2022-11-22', 2, 1),
(6, 'test', '123456', '0000-00-00', 1, 1),
(7, 'MADNA', 'luL1Sr0Gk2', '2023-02-15', 2, 1),
(8, 'toor', 'XZtyG68fnE', '2023-02-15', 2, 1),
(9, 'rt', 'KdNwpB3GVX', '2023-02-15', 3, 1),
(10, 'jorgeavargasm', 'rznZzCjVHz', '2023-02-15', 2, 2),
(11, 'jorgeavargasm2', 'B6vT0owidl', '2023-02-15', 2, 1),
(12, 'madnasito', '6lpTdEJnW5', '2023-02-15', 2, 2),
(13, 'madnasito2', 'HiNipybLBJ', '2023-02-15', 2, 2),
(14, 'usuariocomun', 'JXQ1LKsGjQ', '2023-02-17', 2, 2),
(15, 'usuariocomun2', 'nzpAKl4QF7', '2023-02-17', 2, 2),
(18, 'usuariobasico', 'ueMKPNLt3n', '2023-02-17', 2, 2),
(19, 'germanrattia@gmail.com', '123456789', '0000-00-00', 1, 1),
(20, 'germanrattia@gmail.com', '123456', '2023-07-26', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ejecucion`
--
ALTER TABLE `ejecucion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisologia`
--
ALTER TABLE `permisologia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_permisologia` (`id_permisologia`);

--
-- Indices de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tienda`
--
ALTER TABLE `tienda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ejecucion`
--
ALTER TABLE `ejecucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisologia`
--
ALTER TABLE `permisologia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sede`
--
ALTER TABLE `sede`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tienda`
--
ALTER TABLE `tienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  ADD CONSTRAINT `detalle_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`id_permisologia`) REFERENCES `permisologia` (`id`);

--
-- Filtros para la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
