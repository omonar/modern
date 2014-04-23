<?php
  xhtmlHeaders( __FILE__, "Admin" );
?>
<body class="zm"> <!-- begin body -->

  <?php
    include('includes/adminnavbar.php');

    $query = "SELECT Id, Name AS 'MonitorName', WebColour FROM Monitors";
    $response = dbFetchAll($query);
    if(!$response) {
      die("ERROR: Failed to fetch monitor data from database!");
    }
    foreach($response as $row) {
      $monitors[$row['Id']]['MonitorName'] = $row['MonitorName'];
      $monitors[$row['Id']]['WebColour'] = $row['WebColour'];
    }
  ?>

  <div class="container"> <!-- begin container -->
    <div class="row">
      <div class="col-md-12">
        <h4>System Information</h4>
        <table class="table table-striped">
          <tr>
            <th>Server Date &amp; Time</th>
            <td>
              <?php
                echo date("H:i:s jS F Y");
              ?>
            </td>
          </tr>
          <tr>
            <th>ZoneMinder Version</th>
            <td><?=ZM_VERSION?></td>
          </tr>
          <tr>
            <th>Skin Version</th>
            <td><?=file_get_contents('skins/modern/VERSION');?></td>
          </tr>
          <tr>
            <th>Load</th>
            <td><?= getLoad() ?></td>
          </tr>
          <tr>
            <th>Disk Usage</th>
            <td><?= getDiskPercent() ?>%</td>
          </tr>
          <tr>
            <th>Recorded Events</th>
            <td>
              <?php
                $response = dbFetchOne("SELECT COUNT(Id) AS event_count FROM Events");
                echo $response['event_count'];
              ?>
            </td>
          </tr>
          <tr>
            <th>Most Recent Event</th>
            <td>
              <?php
                $response = dbFetchOne("SELECT EndTime FROM Events ORDER BY EndTime DESC LIMIT 1");
                echo "recording ended at " . date("H:i:s \o\\n \\t\h\e jS F Y", strtotime($response['EndTime']));
              ?>
            </td>
          </tr>
          <tr>
            <th>Registered Users</th>
            <td>
              <?php
                $response = dbFetchOne("SELECT COUNT(Id) AS user_count FROM Users");
                echo $response['user_count'];
              ?>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <h4>Disk Usage</h4>
        <canvas id="disk-usage-chart" width="300" height="300"></canvas>
      </div>

      <div class="col-md-4">
        <h4>Event Overview For The Past 7 Days</h4>
        <canvas id="past-week-chart" width="300" height="300"></canvas>
      </div>

      <div class="col-md-4">
        <h4>Most Active Cameras Overview</h4>
        <canvas id="most-active-cameras-chart" width="300" height="300"></canvas>
      </div>

      <div class="col-md-2">
        <h4>Legend</h4>
        <?php
          foreach($monitors as $MonitorId => $row) {
            echo "<p><span class=\"fa fa-stop\" style=\"color: " . $row['WebColour'] . ";\"></span> " . $monitors[$MonitorId]['MonitorName'] . "</p>";
          }
        ?>
          <p><span class="fa fa-stop" style="color: #47A447;"></span> Available Disk Space</p>
          <p><span class="fa fa-stop" style="color: #D2322D;"></span> Used Disk Space</p>
      </div>
    </div>
  </div> <!-- end container -->

  <script>
    var data = [
      {
        value: <?=getDiskPercent()?>,
        color:"#D2322D"
      },
      {
        value : <?=(100-getDiskPercent())?>,
        color : "#47A447"
      }
    ];
    var ctx = document.getElementById("disk-usage-chart").getContext("2d");
    new Chart(ctx).Pie(data);

    <?php
      //$query = "SELECT Date(StartTime) AS Date, MonitorId, COUNT(Id) AS NumberOfEvents FROM Events WHERE DATE(StartTime)>=DATE(NOW() - INTERVAL 7 DAY) GROUP BY MonitorId, DAY(StartTime)";
      $query = "SELECT Date(StartTime) AS Date, Events.MonitorId, Monitors.WebColour, COUNT(Events.Id) AS NumberOfEvents FROM Events, Monitors WHERE Events.MonitorId=Monitors.Id AND DATE(StartTime)>=DATE(NOW() - INTERVAL 7 DAY) GROUP BY MonitorId, DAY(StartTime)";
      $response = dbFetchAll($query);
      if(!$response) {
        die("ERROR: Failed to fetch graph data!");
      }
      foreach($response as $row) {
        $graphData[$row['Date']][$row['MonitorId']]['NumberOfEvents'] = $row['NumberOfEvents'];
        $graphData[$row['Date']][$row['MonitorId']]['WebColour'] = $row['WebColour'];
      }
    ?>

    <?php
      foreach($monitors as $monitorId => $WebColour) {
        echo "var dataformonitor{$monitorId} = [";
        $dataString = "";
        foreach($graphData as $index => $value) {
          if(array_key_exists($monitorId, $value)) {
            $dataString .= $value[$monitorId]['NumberOfEvents'] . ", ";
          }
        }
        echo substr($dataString, 0, -2) . "];\n";
      }
    ?>

    <?php
      echo "var data = {\n";
      $i = 0;
      echo "labels : [";
      foreach($graphData as $index => $value) {
        $i++;
        $datetime = strtotime($index);
        echo "\"" . date("d/m/y", $datetime) . "\"";
        if($i < sizeof($graphData)) {
          echo ", ";
        }
        else {
          echo "],";
        }
      }
      echo "\n";
    ?>

      datasets : [


        <?
          foreach($monitors as $index => $row) {
            echo "{ fillColor: \"" . $row['WebColour'] . "\", strokeColor: \"" . $row['WebColour'] . "\", pointColor: \"" . $row['WebColour'] . "\", pointStrokeColor: \"#FFF\", data: dataformonitor{$index} },\n";
          }
        ?>
      ]
    }

    var ctx = document.getElementById("past-week-chart").getContext("2d");
    new Chart(ctx).Line(data);

    var data = [
      <?php
        $query = "SELECT MonitorId, COUNT(Events.Id) AS NumberOfEvents FROM Events GROUP BY MonitorId";
        $response = dbFetchAll($query);
        if(!$response) {
          die("ERROR: Failed to fetch data for chart!");
        }
        $i = 0;
        foreach($response as $row) {
          $i++;
          echo "{ value: " . $row['NumberOfEvents'] . ", color: \"" . $monitors[$row['MonitorId']]['WebColour'] . "\" }";
          if($i < sizeof($response)) {
            echo ",";
          }
        }
      ?>
    ];
    var ctx = document.getElementById("most-active-cameras-chart").getContext("2d");
    new Chart(ctx).Pie(data);
  </script>

</body>
</html>