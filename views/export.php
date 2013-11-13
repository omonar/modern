<?php
  if(isset($_REQUEST['events'])) {
    $events = $_REQUEST['events'];
    foreach($events as $eventArray) {
      echo $eventArray['monitorId'] . " " . $eventArray['eventId'] . " " . $eventArray['eventPath'] . "\n";
    }
  }
?>