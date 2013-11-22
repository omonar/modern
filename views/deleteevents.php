<?php
  if(isset($_REQUEST['deleteevents'])) {
    if($_REQUEST['deleteevents'] === "single") {
      if(isset($_REQUEST['eid']) && ctype_digit($_REQUEST['eid'])) {
      $query = "DELETE FROM Events WHERE Id='$_REQUEST[eid]'";
      }
      else {
        die("ERROR: Invalid event ID!");
      }
    }
    else {
      $events = json_decode($_REQUEST['eids']);
      $query = "DELETE FROM Events WHERE Id IN ('" . implode("','", $events) . "')";
    }
    $response = dbQuery($query);
    if(!$response) {
      die("ERROR: Failed to delete event(s) from the database!");
    }
    else {
      echo "success";
    }
  }
?>