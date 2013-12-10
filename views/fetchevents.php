<?php
  $timeframes = array(0 => "all", 1 => "today", 2 => "week", 3 => "month", 4 => "custom");
  if(isset($_REQUEST['timeframe']) && in_array($_REQUEST['timeframe'], $timeframes)) {
    $cameras = dbFetchAll("SELECT * FROM Monitors");
    if(isset($_REQUEST['page'])) {
      if(strlen($_REQUEST['page']<1)) {
        $_REQUEST['page'] = 1;
      }
      $limit = (($_REQUEST['page']-1) * 50) . ", 50";
    }
    else {
      $limit = "0, 50";
    }
    if(isset($_REQUEST['chosencameras']) && $_REQUEST['chosencameras'] != "null") {
      $chosencameras = json_decode($_REQUEST['chosencameras']);
    }
    switch($_REQUEST['timeframe']) {
      case "all":
        $query = "SELECT * FROM Events ";
        if(isset($chosencameras)) {
          $query .= "WHERE MonitorId IN ('" . implode("','", $chosencameras) . "')";
        }
        $query .= " LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events";
        break;
      case "today":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $query .= " DATE(StartTime) = CURDATE() LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE DATE(StartTime) = CURDATE()";
        break;
      case "week":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " DATE(StartTime) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE())+6 DAY AND DATE(StartTime) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE())-1 DAY LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE DATE(StartTime) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE())+6 DAY AND DATE(StartTime) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE())-1 DAY";
        break;
      case "month":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " DATE(StartTime) >= SUBDATE(CURDATE(), INTERVAL 1 MONTH) LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE DATE(StartTime) >= SUBDATE(CURDATE(), INTERVAL 1 MONTH)";
        break;
      case "custom":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " StartTime >= '$_REQUEST[startdatetime]' AND StartTime <= '$_REQUEST[enddatetime]' LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE StartTime >= '$_REQUEST[startdatetime]' AND StartTime <= '$_REQUEST[enddatetime]'";
        break;
    }
    $response = dbFetchAll($query);
    if(!$response) {
      die("ERROR: Failed to fetch events!<br>" . $query);
    }
    else {
      $skipColumns = array(0 => "Archived", 1 => "Videoed", 2 => "Uploaded", 3 => "Emailed", 4 => "Messaged", 5 => "Executed", 6 => "Width", 7 => "Height", 8 => "Notes", 9 => "Frames", 10 => "AlarmFrames", 11 => "TotScore", 12 => "AvgScore", 13 => "MaxScore");
      echo "<table class=\"table table-striped\">";
      echo "<thead><tr><td>Event Id</td><td>Camera</td><td>Name</td><td>Cause</td><td>Start Time</td><td>End Time</td><td>Length</td></tr></thead>";
      foreach($response as $event) {
        echo "<tr>";
        foreach($event as $key => $value) {
          if(in_array($key, $skipColumns)) {
            continue;
          }
          if($key === "MonitorId") {
            foreach($cameras as $camera) {
              if($camera['Id'] == $value) {
                echo "<td>($value) $camera[Name]</td>";
              }
            }
          }
          else {
            echo "<td>$value</td>";
          }
        }
        echo "<td><a href=\"?view=playevent&eid=$event[Id]\" class=\"init-dynamic-colorbox\"><span class=\"glyphicon glyphicon-film\"></span></a></td>";
        echo "<td><a href=\"#\" id=\"export-event-$event[Id]\" class=\"export-event\" data-eid=\"$event[Id]\"><span class=\"glyphicon glyphicon-export\"></span></a></td>";
        echo "<td><a href=\"#\" id=\"delete-event-$event[Id]\" class=\"delete-event\" data-eid=\"$event[Id]\"><span class=\"glyphicon glyphicon-remove-sign\"></span></a></td>";
        echo "<td><a href=\"#\"><input type=\"checkbox\" data-eid=\"$event[Id]\" data-monitorid=\"$event[MonitorId]\" class=\"event-checkbox\"></td>";
        echo "</tr>";
      }
      echo "</table>";
      echo "<button id=\"export-selected-events\" style=\"float:right; margin-left: 1rem;\" class=\"btn btn-success disabled\"><span class=\"glyphicon glyphicon-export\"></span> Export Selected</button>";
      echo "<button id=\"delete-selected-events\" style=\"float:right\" class=\"btn btn-danger disabled\"><span class=\"glyphicon glyphicon-trash\"></span> Delete Selected</button>";
      $response = dbFetchOne($paginationQuery);
      if(!$response) {
        die("ERROR: Failed to create pagination!");
      }
      else {
        echo "<p>$response[NUMROWS] events</p>";
        if($response[NUMROWS] > 50) {
          echo "<ul class=\"pagination\">";
          echo "<li";
          if((!isset($_REQUEST['page']))||($_REQUEST['page']=="1")) {
            echo " class=\"disabled\"";
          }
          echo "><a id=\"events-previous-page\" href=\"#\">&laquo;</a></li>";
          for($i=1; ($i <= $response['NUMROWS'] / 50); $i++) {
            echo "<li";
            if($i == $_REQUEST['page']) {
              echo " class=\"active\"";
            }
            echo "><a href=\"#\">$i</a></li>";
          }
          echo "<li><a id=\"events-next-page\" href=\"#\">&raquo;</a></li>";
          echo "</ul>";
        }
      }
    }
  }
?>

<a href="#" class="btn btn-default"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>