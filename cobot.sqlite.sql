CREATE TABLE caca (
  id int PRIMARY_KEY AUTO_INCREMENT,
  username varchar NOT NULL,
  pass varchar NOT NULL
);


CREATE TABLE users (
  id INTEGER AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  pass varchar(255) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE userpriv (
  'id' INTEGER AUTO_INCREMENT,
  'uid' INTEGER,
  rng varchar(100) NOT NULL,
  sec varchar(100) NOT NULL,
  PRIMARY KEY ('id')
);
