<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>
    <main class="playback">
      <img src="/zm/skins/modern/views/assets/images/onerror.png">
    </main>
    <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery.min.js"></script>
    <script>
      var eid = <?=$_REQUEST['eid']?>;

      <?php
        $query = "SELECT Id, MonitorId, StartTime, Frames FROM Events WHERE Id=$_REQUEST[eid]";
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
                $frames[] = "/zm/" . viewImagePath($imagePath);
          }
        }
        echo "var unprocessed = '" . implode(',', $frames) . "';";
        echo "var frames = unprocessed.split(',');";
      ?>

      function displayFrame(src) {
        jQuery("img").attr('src', src);
      }

      function playbackFrames(imgarray) {
        var x = 0;
        setInterval(function() {
          if(x < frames.length) {
            displayFrame(imgarray[x]);
          }
          else {
            displayFrame('/zm/skins/modern/views/assets/images/onerror.png');
          }
          x++;
        }, 200);
      }
      $(document).ready(function() {
        playbackFrames(frames);
      });
    </script>
  </body>
</html>