CREATE TABLE users (
  id INTEGER AUTO_INCREMENT,
  'user' varchar(255) NOT NULL,
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
