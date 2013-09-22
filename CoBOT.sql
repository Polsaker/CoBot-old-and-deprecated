CREATE TABLE defs (
  pal varchar(255) NOT NULL,
  def varchar(400) NOT NULL,
  PRIMARY KEY (pal)
);



CREATE TABLE games_banco (
  plata int(255) NOT NULL DEFAULT '100000',
  cobre int(255) NOT NULL,
  plat int(255) NOT NULL, 
  oro int(255) NOT NULL
);


INSERT INTO games_banco (plata,cobre,plat,oro) VALUES
(100000000,100,100,100);



CREATE TABLE games_channels (
  chan varchar(255) NOT NULL,
  PRIMARY KEY (chan)
);



INSERT INTO games_channels (chan) VALUES
('#games');


CREATE TABLE games_ppt (
  id int AUTO_INCREMENT,
  n1 varchar(50) NOT NULL,
  n2 varchar(50) NOT NULL,
  ts varchar(255) NOT NULL,
  dn int(5) NOT NULL,
  n1m int(4) NOT NULL DEFAULT '0',
  n2m int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);



CREATE TABLE games_users (
  id int AUTO_INCREMENT,
  nick varchar(255) NOT NULL,
  dinero varchar(255) NOT NULL,
  bono int(15) NOT NULL,
  nivel int(20) NOT NULL,
  imp int(5) NOT NULL DEFAULT '0',
  frozen int(5) NOT NULL DEFAULT '0',
  cobre int(255) NOT NULL,
  caja int(4) NOT NULL,
  plata int(255) NOT NULL,
  oro int(255) NOT NULL,
  dist int(3) NOT NULL,
  PRIMARY KEY (id)
);



CREATE TABLE ignore (
  host varchar(255) NOT NULL
);



CREATE TABLE linkchans (
  chan varchar(255) NOT NULL
);


INSERT INTO linkchans (chan) VALUES
('#cobot');


CREATE TABLE nickassoc (
  'ID' INTEGER AUTOINCREMENT,
  ircnick varchar(255) NOT NULL,
  wikinick varchar(255) NOT NULL,
  chn varchar(255) NOT NULL,
  PRIMARY KEY (ID)
) ;


CREATE TABLE proxys (
  ip varchar(255) NOT NULL,
  p int(255) NOT NULL
);


CREATE TABLE users (
  'id' INTEGER AUTOINCREMENT,
  'user' varchar(255) NOT NULL,
  pass varchar(255) NOT NULL,
  rng varchar(100) NOT NULL,
  PRIMARY KEY ('id')
);


CREATE TABLE wikichans (
  chan varchar(255) NOT NULL,
  wiki varchar(255) NOT NULL
);



INSERT INTO wikichans (chan, wiki) VALUES
('#cobot', 'es.wikipedia.org/w');
