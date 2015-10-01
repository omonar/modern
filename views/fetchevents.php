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
    $orderFields = Array();
    if(isset($_REQUEST['orderby'])) {
    foreach($_REQUEST['orderby'] as $index => $value) {
      switch($value['orderField']) {
        case "null":
          $orderFields[] = "Events.Id";
          break;
        case "eventid":
          $orderFields[] = "Events.Id";
          break;
        case "camera":
          $orderFields[] = "Events.MonitorId";
          break;
        case "eventname":
          $orderFields[] = "Events.Name";
          break;
        case "starttime":
          $orderFields[] = "Events.StartTime";
          break;
        case "endtime":
          $orderFields[] = "Events.EndTime";
          break;
        case "length":
          $orderFields[] = "Events.Length";
          break;
        }
      }
    } else {
          $orderFields[] = "Events.Id";
    }
    if(sizeof($orderFields) > 0) {
      $orderString = " ORDER BY";
      $i = 0;
      foreach($orderFields as $index => $field) {
        if(isset($_REQUEST['orderby'])) {
        switch($_REQUEST['orderby'][$i]['orderDirection']) {
          case "desc":
            $orderDirections[] = "DESC";
            break;
          default:
            $orderDirections[] = "ASC";
            break;
        }
        } else {
            $orderDirections[] = "ASC";
        }
        $orderString .= " {$field} {$orderDirections[$i]}";
        if(($i + 1) < sizeof($orderFields)) {
          $orderString .= ",";
        }
        $i++;
      }
    }
    switch($_REQUEST['timeframe']) {
      case "all":
        $query = "SELECT * FROM Events ";
        if(isset($chosencameras)) {
          $query .= "WHERE MonitorId IN ('" . implode("','", $chosencameras) . "')";
        }
        $query .= "$orderString LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events";
        if(isset($chosencameras)) {
          $paginationQuery .= " WHERE MonitorId IN ('" . implode("','", $chosencameras) . "')";
        }
        break;
      case "today":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $query .= " DATE(StartTime) = CURDATE() $orderString LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE";
        if(isset($chosencameras)) {
          $paginationQuery .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $paginationQuery .= " DATE(StartTime) = CURDATE()";
        break;
      case "week":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " DATE(StartTime) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE())+6 DAY AND DATE(StartTime) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE())-1 DAY $orderString LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE";
        if(isset($chosencameras)) {
          $paginationQuery .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $paginationQuery .= " DATE(StartTime) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE())+6 DAY AND DATE(StartTime) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE())-1 DAY";
        break;
      case "month":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " DATE(StartTime) >= SUBDATE(CURDATE(), INTERVAL 1 MONTH) $orderString LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE";
        if(isset($chosencameras)) {
          $paginationQuery .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $paginationQuery .= " DATE(StartTime) >= SUBDATE(CURDATE(), INTERVAL 1 MONTH)";
        break;
      case "custom":
        $query = "SELECT * FROM Events WHERE";
        if(isset($chosencameras)) {
          $query .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND ";
        }
        $query .= " StartTime >= '$_REQUEST[startdatetime]' AND StartTime <= '$_REQUEST[enddatetime]' $orderString LIMIT $limit";
        $paginationQuery = "SELECT COUNT(Id) AS NUMROWS FROM Events WHERE";
        if(isset($chosencameras)) {
          $paginationQuery .= " MonitorId IN ('" . implode("','", $chosencameras) . "') AND";
        }
        $paginationQuery .= " StartTime >= '$_REQUEST[startdatetime]' AND StartTime <= '$_REQUEST[enddatetime]'";
        break;
    }
    if(dbNumRows($query) < 1) {
      die("<div class=\"alert alert-info\"><p>No events in selected range...</p></div>");
    }
    else {
      $response = dbFetchAll($query);
      if(!$response) {
        die("ERROR: Failed to fetch events!<br>" . $query);
      }
      else {
        $skipColumns = array(0 => "Archived", 1 => "Videoed", 2 => "Uploaded", 3 => "Emailed", 4 => "Messaged", 5 => "Executed", 6 => "Width", 7 => "Height", 8 => "Notes", 9 => "Frames", 10 => "AlarmFrames", 11 => "TotScore", 12 => "AvgScore", 13 => "MaxScore", 14 => "Cause");
        echo "<table class=\"table table-striped\">";
        echo "<thead><tr><td>Event Id</td><td>Camera</td><td>Name</td><td>Start Time</td><td>End Time</td><td>Length</td><td></td><td></td><td></td><td><input id=\"check-all\" type=\"checkbox\"></td></tr></thead>";
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
          echo "<td><a title=\"View event\" href=\"?view=playevent&eid=$event[Id]\" class=\"init-dynamic-colorbox\"><span class=\"fa fa-film\"></span></a></td>";
          echo "<td><a title=\"Export event\" href=\"#\" id=\"export-event-$event[Id]\" class=\"export-event\" data-eid=\"$event[Id]\" data-mid=\"$camera[Id]\"><span class=\"fa fa-save\"></span></a></td>";
          echo "<td><a title=\"Delete event\" href=\"#\" id=\"delete-event-$event[Id]\" class=\"delete-event\" data-eid=\"$event[Id]\" data-mid=\"$camera[Id]\"><span class=\"fa fa-times-circle\"></span></a></td>";
          echo "<td><a href=\"#\"><input type=\"checkbox\" data-eid=\"$event[Id]\" data-monitorid=\"$event[MonitorId]\" class=\"event-checkbox\"></td>";
          echo "</tr>";
        }
        echo "</table>";
        //echo "<button id=\"export-selected-events\" style=\"float:right; margin-left: 1rem;\" class=\"btn btn-success disabled\"><span class=\"fa fa-save\"></span> Export Selected</button>";
        echo "<button id=\"delete-selected-events\" style=\"float:right\" class=\"btn btn-danger disabled\"><span class=\"fa fa-trash-o\"></span> Delete Selected</button>";
        $response = dbFetchOne($paginationQuery);
        if(!$response) {
          die("ERROR: Failed to create pagination!");
        }
        else {
          echo "<p>$response[NUMROWS] events</p>";
          if($response['NUMROWS'] > 50) {
            echo "<ul class=\"pagination\">";
            echo "<li";
            if((!isset($_REQUEST['page']))||($_REQUEST['page']=="1")) {
              echo " class=\"disabled\"";
            }
            echo "><a id=\"events-previous-page\" href=\"#\">&laquo;</a></li>";
            $imax = floor(($response['NUMROWS'] + 49)/ 50);
            for($i=1; $i <= $imax; $i++) {
              echo "<li";
              if($i == $_REQUEST['page']) {
                echo " class=\"active\"";
              }
              echo "><a href=\"#\">$i</a></li>";
            }
            echo "<li";
            if((!isset($_REQUEST['page']))||($_REQUEST['page']==strval($imax))) {
              echo " class=\"disabled\"";
            }
            echo "><a id=\"events-next-page\" href=\"#\">&raquo;</a></li>";
            echo "</ul>";
          }
        }
      }
    }
  }
?>

<a href="#" class="btn btn-default"><span class="fa fa-arrow-circle-up"></span></a>