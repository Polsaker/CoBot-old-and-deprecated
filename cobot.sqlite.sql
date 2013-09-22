CREATE TABLE users (
  'id' INTEGER AUTOINCREMENT,
  'user' varchar(255) NOT NULL,
  pass varchar(255) NOT NULL,
  PRIMARY KEY ('id')
);

CREATE TABLE userpriv (
  'id' INTEGER AUTOINCREMENT,
  'uid' INTEGER,
  rng varchar(100) NOT NULL,
  sec varchar(100) NOT NULL,
  PRIMARY KEY ('id')
);
