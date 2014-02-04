<?php

class connect_mongo {
    protected $server = 'mongo2';
    protected $port   = '27017';
    protected $connectionString ='mongo2.hq.cross';
    protected $dbName = 'CrossApplicantManagement';
//  $user = null;
//  $password = null;  
//  $dbname = null;
//  $options => array()
    
    public function connect() {
        $conn = new \MongoClient($this->connectionString);
        $db = $conn->selectDB($this->dbName);
        
        return $db;
    }
}