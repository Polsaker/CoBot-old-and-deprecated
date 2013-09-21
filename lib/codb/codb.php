<?php
define("DB_MYSQL", 1);
define("DB_SQLITE", 2);
class codb{
    public $db_type;
    public $db_string;
    private $dbobj;
    
    public function connect($dbtype, $db, $hostname=null, $user=null, $pass=null){
        switch($dbtype){
            case DB_MYSQL:
                $dbh = new PDO("mysql:host=$hostname;dbname=$db", $user, $password);
                break;
            case DB_SQLITE:
                $dbh = new PDO("sqlite:$db.sdb");
                break;
        }
        $this->dbobj=$dbh;
    }
    
    public function exec($query){
        return $this->dbobj->exec($query);
    }
    
    public function query($query){
        return $this->dbobj->query($query);
    }
    
    public function disconnect(){
        $this->dbobj=null;
    }
}
