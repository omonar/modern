<?php
  function addEventToZip($eid, $zip) {
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
    }
    $zip->addDirectory("events/" . $_REQUEST['eid']);
    $i = 0;
    foreach($frames as $frame) {
      $i++;
      if($i<10) {
        $filesString .= "\nframes.push(\"events/" . $eid . "/00" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $eid . "/00" . $i . "-capture.jpg");
      }
      elseif ($i>=10 && $i <= 99) {
        $filesString .= "\nframes.push(\"events/" . $eid . "/0" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $eid . "/0" . $i . "-capture.jpg");
      }
      else {
        $filesString .= "\nframes.push(\"events/" . $eid . "/" . $i . "-capture.jpg\");";
        $zip->addLargeFile($frame, "events/" . $eid . "/" . $i . "-capture.jpg");
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
        $filesString = addEventToZip($_REQUEST['eid'], $zip, $filesString);
        $zip->addLargeFile("skins/modern/views/images/onerror.png", "assets/playback-placeholder.png");
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
      $zip = new ZipStream("events-" . $eventIDs[0] . "-" . $eventIDs[sizeof($eventIDs)-1] . ".zip");
      $zip->addDirectory("events");
      $zip->addDirectory("assets");
      foreach($eventIDs as $eventID) {
        $filesString .= addEventToZip($eventID, $zip);
      }
      $zip->addLargeFile("skins/modern/views/images/onerror.png", "assets/playback-placeholder.png");
      $playerFile = file_get_contents("skins/{$skin}/views/includes/standalone-event-player.html");
      $playerFile = str_replace("###files###", $filesString, $playerFile);
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