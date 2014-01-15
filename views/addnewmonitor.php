<?php
  if(!isset($_REQUEST['editedValues'])) {
    die("ERROR: Permission denied!");
  }
  echo "<pre>";
  print_r($_REQUEST['editedValues']);
  echo "</pre>";
?>