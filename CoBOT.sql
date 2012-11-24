-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 02-10-2012 a las 20:11:42
-- Versión del servidor: 5.1.63
-- Versión de PHP: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `CoBOT`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `defs`
--

CREATE TABLE IF NOT EXISTS `defs` (
  `pal` varchar(255) NOT NULL,
  `def` varchar(400) NOT NULL,
  PRIMARY KEY (`pal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `games_banco`
--

CREATE TABLE IF NOT EXISTS `games_banco` (
  `plata` bigint(255) NOT NULL DEFAULT '100000'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `games_banco`
--

INSERT INTO `games_banco` (`plata`) VALUES
(100000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `games_channels`
--

CREATE TABLE IF NOT EXISTS `games_channels` (
  `chan` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `games_channels`
--

INSERT INTO `games_channels` (`chan`) VALUES
('#games'),

--
-- Estructura de tabla para la tabla `games_users`
--

CREATE TABLE IF NOT EXISTS `games_users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nick` varchar(255) NOT NULL,
  `dinero` varchar(255) NOT NULL,
  `bono` int(15) NOT NULL,
  `nivel` int(20) NOT NULL,
  `imp` int(5) NOT NULL DEFAULT '0',
  `frozen` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=174 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nickassoc`
--

CREATE TABLE IF NOT EXISTS `nickassoc` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `ircnick` varchar(255) NOT NULL,
  `wikinick` varchar(255) NOT NULL,
  `chn` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proxys`
--

CREATE TABLE IF NOT EXISTS `proxys` (
  `ip` varchar(255) NOT NULL,
  `p` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `rng` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user`, `pass`, `rng`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wikichans`
--

CREATE TABLE IF NOT EXISTS `wikichans` (
  `chan` varchar(255) NOT NULL,
  `wiki` varchar(255) NOT NULL,
  PRIMARY KEY (`chan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE  `CoBOT`.`games_ppt` (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`n1` VARCHAR( 50 ) NOT NULL ,
`n2` VARCHAR( 50 ) NOT NULL ,
`ts` VARCHAR( 255 ) NOT NULL ,
`n1m` INT( 4 ) NOT NULL DEFAULT  '0',
`n2m` INT( 4 ) NOT NULL DEFAULT  '0',
`dn` INT( 5 ) NOT NULL
) ENGINE = MYISAM ;
