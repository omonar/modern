<?php
 require('skins/modern/views/framefetcher.php');
  $monitors = dbFetchAll("SELECT Monitors.Id, Monitors.Name, Monitors.Protocol, Monitors.Host, Monitors.Port, Monitors.Path, COUNT(Events.Id) AS Events, Monitors.Width, Monitors.Height FROM Monitors INNER JOIN Events ON Events.MonitorId = Monitors.Id GROUP BY Monitors.Id");
  foreach($monitors as $index => $monitor) {
    $monitors[$index]['LiveSrc'] = outputLiveStreamSrcModern($monitor, 640, 480);
  }
?>

cameras = JSON.parse('<?= json_encode($monitors); ?>
');