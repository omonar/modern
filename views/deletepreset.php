<?php
  if(isset($_REQUEST['presetID']) && ctype_digit($_REQUEST['presetID'])) {
    $query = "DELETE FROM Groups WHERE Id=" . $_REQUEST['presetID'];
    $response = dbQuery($query);
    if(!$response) {
      die("ERROR: Failed to delete preset!");
    }
    echo "success";
  }
  else {
    die("ERROR: Invalid preset ID!");
  }
?>