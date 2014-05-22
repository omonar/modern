<?php
  if(isset($_REQUEST['formValues'])) {
    $values = $_REQUEST['formValues'];
    $response = dbFetchAll("SELECT Username FROM Users");
    if(!$response) {
      die("ERROR: Failed to fetch list of usernames!" . mysql_errno());
    }
    if(in_array($values['Username'])) {
      die("ERROR: Username already taken!");
    }
    else {
      $query = "INSERT INTO Users (Id, Username, Password, Language, Enabled, Stream, Events, Control, Monitors, System, MaxBandwidth, MonitorIds) VALUES (NULL, '$values[Username]', PASSWORD('$values[Password]'), '$values[Language]', '$values[Enabled]', '$values[Stream]', '$values[Events]', '$values[Control]', '$values[Monitors]', '$values[System]', '$values[MaxBandwidth]', '$values[monitorIds]');";
      $response = dbQuery($query);
      if(!$response) {
        die("ERROR: Failed to add new user!" . mysql_errno());
      }
      echo "success";
    }
  }
?>