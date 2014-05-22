<?php
 require('skins/modern/views/framefetcher.php');
  $monitors = dbFetchAll("SELECT Monitors.Id, Monitors.Name, Monitors.Protocol, Monitors.Host, Monitors.Port, Monitors.Path, Monitors.Width, Monitors.Height FROM Monitors");
  foreach($monitors as $index => $monitor) {
    $monitors[$index]['LiveSrc'] = outputLiveStreamSrcModern($monitor, 640, 480);
  }
?>

cameras = JSON.parse('<?= json_encode($monitors); ?>
');