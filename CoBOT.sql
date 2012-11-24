CREATE TABLE IF NOT EXISTS `defs` (
  `pal` varchar(255) NOT NULL,
  `def` varchar(400) NOT NULL,
  PRIMARY KEY (`pal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `games_banco` (
  `plata` int(255) NOT NULL DEFAULT '100000'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `games_banco` (`plata`) VALUES
(100000000);



CREATE TABLE IF NOT EXISTS `games_channels` (
  `chan` varchar(255) NOT NULL,
  PRIMARY KEY (`chan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



INSERT INTO `games_channels` (`chan`) VALUES
('#games');# 1 fila(s) fueron afectadas.


CREATE TABLE IF NOT EXISTS `games_ppt` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `n1` varchar(50) NOT NULL,
  `n2` varchar(50) NOT NULL,
  `ts` varchar(255) NOT NULL,
  `dn` int(5) NOT NULL,
  `n1m` int(4) NOT NULL DEFAULT '0',
  `n2m` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;



CREATE TABLE IF NOT EXISTS `games_users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nick` varchar(255) NOT NULL,
  `dinero` varchar(255) NOT NULL,
  `bono` int(15) NOT NULL,
  `nivel` int(20) NOT NULL,
  `imp` int(5) NOT NULL DEFAULT '0',
  `frozen` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;



CREATE TABLE IF NOT EXISTS `ignore` (
  `host` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `linkchans` (
  `chan` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `linkchans` (`chan`) VALUES
('#cobot');# 1 fila(s) fueron afectadas.


CREATE TABLE IF NOT EXISTS `nickassoc` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `ircnick` varchar(255) NOT NULL,
  `wikinick` varchar(255) NOT NULL,
  `chn` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;


CREATE TABLE IF NOT EXISTS `proxys` (
  `ip` varchar(255) NOT NULL,
  `p` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `rng` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


INSERT INTO `users` (`id`, `user`, `pass`, `rng`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', '10,*');



CREATE TABLE IF NOT EXISTS `wikichans` (
  `chan` varchar(255) NOT NULL,
  `wiki` varchar(255) NOT NULL
);



INSERT INTO `wikichans` (`chan`, `wiki`) VALUES
('#cobot', 'es.wikipedia.org/w');

