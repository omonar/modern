<?php
  foreach(dbFetchAll("SELECT * FROM Groups") as $index => $cameras) {
    $groups[] = array('value' => $cameras['Id'], 'text' => $cameras['Name']);
  }
  echo json_encode($groups);
?>