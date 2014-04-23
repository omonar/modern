<?php
  function addEventToZip($eid, $mid, $zip) {
    $query = "SELECT Id, MonitorId, StartTime, Frames FROM Events WHERE Id={$eid}";
    $results = dbFetchAll($query);

    $scale = max( reScale( SCALE_BASE, '100', ZM_WEB_DEFAULT_SCALE ), SCALE_BASE );

    foreach ($results as $result) {
      for($counter = 1; $counter <= $result['Frames']; $counter++) {
            $event['Id']=$result['Id'];
            $event['StartTime']=$result['StartTime'];
            $event['MonitorId']=$result['MonitorId'];
            $imageData = getImageSrc($event, $counter, $scale, (isset($_REQUEST['show']) && $_REQUEST['show']=="capt"));
            $imagePath = $imageData['thumbPath'];
            $eventPath = $imageData['eventPath'];
            $dImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-d.jpg", $eventPath, $counter);
            $rImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-r.jpg", $eventPath, $counter);
            $frames[] = viewImagePath($imagePath);
      }
      $zip->addDirectory("events/" . $event['MonitorId']);
      $zip->addDirectory("events/" . $event['MonitorId'] . "/" . $event['Id']);
    }
    $i = 0;
    foreach($frames as $frame) {
      $i++;
      if($i<10) {
        $filesString .= "\nframes.push(\"events/" . $mid . "/" . $eid . "/00" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/00" . $i . "-capture.jpg");
      }
      elseif ($i>=10 && $i <= 99) {
        $filesString .= "\nframes.push(\"events/" . $mid . "/" . $eid . "/0" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/0" . $i . "-capture.jpg");
      }
      else {
        $filesString .= "\nframes.push(\"events/" . $mid . "/" . $eid . "/" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/" . $i . "-capture.jpg");
      }
    }
    return $filesString;
  }

  function addEventsToZip($eids, $zip) {
    $query = "SELECT Id, MonitorId, StartTime, Frames FROM Events WHERE Id IN ('" . implode("','", $eids) . "')";
    $results = dbFetchAll($query);

    $scale = max( reScale( SCALE_BASE, '100', ZM_WEB_DEFAULT_SCALE ), SCALE_BASE );

    foreach ($results as $result) {
      for($counter = 1; $counter <= $result['Frames']; $counter++) {
            $event['Id']=$result['Id'];
            $event['StartTime']=$result['StartTime'];
            $event['MonitorId']=$result['MonitorId'];
            $imageData = getImageSrc($event, $counter, $scale, (isset($_REQUEST['show']) && $_REQUEST['show']=="capt"));
            $imagePath = $imageData['thumbPath'];
            $eventPath = $imageData['eventPath'];
            $dImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-d.jpg", $eventPath, $counter);
            $rImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-r.jpg", $eventPath, $counter);
            $frames[] = viewImagePath($imagePath);
      }
      $zip->addDirectory("events/" . $event['MonitorId']);
      $zip->addDirectory("events/" . $event['MonitorId'] . "/" . $event['Id']);
    }
    $i = 0;
    foreach($frames as $frame) {
      $i++;
      if($i === 1) {
        $filesString .= "\nevent{$eid} = [];";
      }
      if($i<10) {
        $filesString .= "\nevent{$eid}.push(\"events/" . $mid . "/" . $eid . "/00" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/00" . $i . "-capture.jpg");
      }
      elseif ($i>=10 && $i <= 99) {
        $filesString .= "\nevent{$eid}.push(\"events/" . $mid . "/" . $eid . "/0" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/0" . $i . "-capture.jpg");
      }
      else {
        $filesString .= "\nevent{$eid}.push(\"events/" . $mid . "/" . $eid . "/" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $mid . "/" . $eid . "/" . $i . "-capture.jpg");
      }
    }
    return $filesString;
  }


  if(isset($_REQUEST['exportevents'])) {
    require('skins/' . $skin . '/includes/ZipStream.php');
    if($_REQUEST['exportevents'] === "single") {
      if(isset($_REQUEST['eid']) && ctype_digit($_REQUEST['eid'])) {
        $zip = new ZipStream("event-" . $_REQUEST['eid'] . ".zip");
        $zip->addDirectory("events");
        $zip->addDirectory("assets");
        $filesString = addEventToZip($_REQUEST['eid'], $_REQUEST['mid'], $zip, $filesString);
        $zip->addFile(file_get_contents("skins/{$skin}/views/assets/images/onerror.png"), "assets/playback-placeholder.png");
        $playerFile = file_get_contents("skins/{$skin}/views/includes/standalone-event-player.html");
        $playerFile = str_replace("###files###", $filesString, $playerFile);
        $zip->addFile($playerFile, "player.html");
        return $zip->finalize();
      }
      else {
        die("ERROR: Invalid event ID!");
      }
    }
    elseif($_REQUEST['exportevents'] === "multiple") {
      $eventIDs = json_decode($_REQUEST['eids']); 

      reset($eventIDs);
      $filename = "events-" . key($eventIDs) . "-";
      end($eventIDs);
      $filename .= key($eventIDs) . ".zip";
      reset($eventIDs);

      $zip = new ZipStream($filename);
      $zip->addDirectory("events");
      $zip->addDirectory("assets");
      $variablesString .= "\nvar events = new Array();";
      foreach($eventIDs as $eventID => $monitorID) {
        $filesString .= addEventsToZip($eventID, $monitorID, $zip);
        $response = dbFetchOne("SELECT StartTime FROM Events WHERE Id='{$eventID}'");
        if(!$response) {
          die("ERROR: Failed to fetch event data");
        }
        $variablesString .= "\nevents['{$eventID}'] = new Array(\"{$monitorID}\", \"{$response[StartTime]}\");";
      }
      $zip->addLargeFile("skins/{$skin}/views/images/onerror.png", "assets/playback-placeholder.png");
      $playerFile = file_get_contents("skins/{$skin}/views/includes/standalone-events-player.html");
      $playerFile = str_replace("###variables###", $variablesString, $playerFile);
      $playerFile = str_replace("###placeholder###", $filesString, $playerFile);
      $zip->addFile($playerFile, "player.html");
      return $zip->finalize();
    }
    else {
      die("ERROR: Invalid export option!");
    }
    //  $response = dbQuery($query);
    //  if(!$response) {
    //    die("ERROR: Failed to delete event(s) from the database!");
    //  }
    //  else {
    //    echo "success";
    //  }
  }
?>