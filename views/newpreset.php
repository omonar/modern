<?php
  if(isset($_REQUEST['presetName']) && isset($_REQUEST['presetMonitorIDs'])) {
    if(ctype_alnum(trim(str_replace(' ', '', $_REQUEST['presetName'])))) {
      $matches = explode(",", $_REQUEST['presetMonitorIDs']);
      if(is_array($matches)) {
        $response = dbFetchAll("SELECT Id FROM Monitors");
        if(!$response) {
          die("ERROR: Failed to fetch list of monitors!");
        }
        foreach($response as $row) {
          $monitors[] = $row['Id'];
        }
        foreach($matches as $match) {
          if(ctype_digit($match) && in_array($match, $monitors)) {
            $presetMonitorIDs[] = $match;
          }
        }
        unset($response);
        $query = "INSERT INTO Groups (Id, Name, MonitorIds) VALUES (NULL, '" . $_REQUEST['presetName'] . "', '" . implode(",", $presetMonitorIDs) . "')";
          $response = dbQuery($query);
          if(!$response) {
            die("ERROR: Failed to create preset '" . $_REQUEST['presetName'] . "'!");
          }
          echo "success";
      }
      else {
        die("ERROR: Cameras chosen for the preset were invalid!");
      }
    }
    else {
      die("ERROR: Preset name can only contain alphanumeric characters!");
    }
  }
  else {
    die("ERROR: Preset name and cameras required!");
  }
?>