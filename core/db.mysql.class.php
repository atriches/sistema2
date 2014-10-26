<?php

class DB extends Base {
    
    private $Host, $User, $Pass, $Database, $DBLink, $LogArray;
    
    public function __construct() {
        
        try {
            $this->Host = constant('mysql_host');
            $this->User = constant('mysql_user');
            $this->Pass = constant('mysql_pass');
            $this->Database = constant('mysql_db');
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }
        
        return $this->connect();
    }
    
    public function connect() {
        $link = mysql_connect($this->Host, $this->User, $this->Pass);
        if (! $link) {
            $this->outputAlert($this->getLog());
            return false;
        }   
            
        mysql_select_db($this->Database);
        
        $this->DBLink = $link;
        
        return $link;
    }
    
    public function close() {
        $link = $this->link();
        mysql_close($link);
    }
    
    public function query($sql) {
        if (! $this->link())
            return false;
        
        $link = $this->link();
        
        $query = mysql_query($sql, $link);
        
        if (! $query) {
            $this->log(array(mysql_errno($link), mysql_error($link), $sql));
            return false;
        }
        
        return $query;
    }
    
    public function fetch($sql) {
        if (! $this->link())
            return false;
        
        $link = $this->link();
        
        $query = mysql_query($sql, $link);
        
        if (mysql_error($link)) {
            $this->log(array(mysql_errno($link), mysql_error($link), $sql));
            return false;
        }
        
        if (mysql_num_rows($query) < 1)
            return null;
        
        $res = array();
        while ($r=mysql_fetch_assoc($query))
            $res[] = $r;
        
        return $res;
    }
    
    public function lastid() {
        if (! $this->link())
            return false;
        
        $link = $this->link();
        return mysql_insert_id();
    }
    
    //
    public function link() {
        return $this->DBLink;
    }
    
    protected function log($a) {
        $this->LogArray[] = $a;
    }
    public function getLog($last=true) {
        $log = $this->LogArray;
        if (empty($log))
            return null;
        return ($last) ? array_pop($log) : $log;
    }
    
}

?>