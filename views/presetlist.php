<?php
  ini_set('display_errors',1); 
  error_reporting(E_ALL);
  foreach(dbFetchAll("SELECT * FROM Groups") as $index => $cameras) {
    $groups[] = array('value' => $cameras['Id'], 'text' => $cameras['Name']);
  }
  echo json_encode($groups);
?>