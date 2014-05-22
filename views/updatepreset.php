<?php
  if(isset($_REQUEST['presetID']) && isset($_REQUEST['presetName']) && isset($_REQUEST['presetMonitorIDs'])) {
    if(!ctype_digit($_REQUEST['presetID'])) {
      die("ERROR: Invalid preset ID!");
    }
    if(!ctype_alnum(trim(str_replace(' ', '', $_REQUEST['presetName'])))) {
      die("ERROR: Preset names can only contain alphanumeric characters!");
    }
    $response = dbFetchAll("SELECT Id FROM Monitors");
    if(!$response) {
      die("ERROR: Failed to fetch monitors from database!");
    }
    foreach($response as $row) {
      $monitorIDs[] = $row['Id'];
    }
    $unprocessedPresetMonitorIDs = explode(",", $_REQUEST['presetMonitorIDs']);
    foreach($unprocessedPresetMonitorIDs as $monitorID) {
      if(ctype_digit($monitorID) && in_array($monitorID, $monitorIDs)) {
        $presetMonitorIDs[] = $monitorID;
      }
    }
    if(sizeof($presetMonitorIDs)<1) {
      die("ERROR: Invalid monitors chosen or no monitors chosen!");
    }
    unset($unprocessedPresetMonitorIDs);
    $query = "UPDATE Groups SET Name='" . $_REQUEST['presetName'] . "', MonitorIds='" . implode(",", $presetMonitorIDs) . "' WHERE Id=" . $_REQUEST['presetID'];
    $response = dbQuery($query);
    if(!$response) {
      die("ERROR: Failed to update preset!");
    }
    echo "success";
  }
?>