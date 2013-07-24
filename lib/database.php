<?php
  require_once('config.php');
  
  class Database {
    var $db = null;
    var $error = null;
    
    function Database() {
      global $CONFIG;
      if($this->db == null) {
        $this->db = new mysqli($CONFIG['db_host'],$CONFIG['db_user'],$CONFIG['db_pass'],$CONFIG['db']);
        if(mysqli_connect_errno()) {
          $this->error = mysqli_connect_error();
          return false;
        }
      }
    }
    
    function getRow($sql) {
      $result = $this->query($sql);
      if(!$result)
        return $result;
      if(isset($result[0]))
        return $result[0];
    }
    
    function query($sql) {
      $result = $this->db->query($sql);
      $rows = Array();
      if(!$result) {
        $this->error = $this->db->error;
        return false;        
      } else {
        if(!is_object($result))
          return mysqli_error($this->db);
        while($row = $result->fetch_assoc()) {
          $rows[] = $row;
        }
        $result->close();
        return $rows;
      }
    }
    
    function escape($string) {
      if($this->db != null) {
        $string = mysqli_real_escape_string($this->db,$string);
      } else {
        $string = add_slashes($string);
      }
      return $string;
    }
    
    function lastInsertID() {
      return $this->db->insert_id;
    }
  }
?>
