<?php
  $running = daemonCheck();
  $status = $running?$SLANG['Running']:$SLANG['Stopped'];
  echo $status;
?>