<?php
  $_view = $_REQUEST['view'];
  if(empty($_view)) $_view = 'live';
?>

<button type="button" id="mainnav-btn"class="btn btn-default dropdown-toggle btn-fixedpos mainnav-btn">
  Menu
  <span class="glyphicon glyphicon-chevron-right">
</button>

<nav class="main-nav"> <!-- begin main nav -->
  <ul class="main-nav-list">
    <li class="main-nav-item">
      <a href="?view=live" class="main-nav-link <?php if($_view=='live') echo 'active';?>">Live View</a>
    </li>
    <li class="main-nav-item">
      <a href="?view=playback" class="main-nav-link <?php if($_view=='playback') echo 'active';?>">Playback</a>
    </li>
    <li class="main-nav-item">
      <a href="?view=admin" class="main-nav-link <?php if($_view=='admin') echo 'active';?>">Admin</a>
    </li>
  </ul>
</nav> <!-- end main nav -->

<button type="button" id="preset-btn" class="btn btn-default dropdown-toggle btn-fixedpos preset-btn">
  Presets
  <span class="glyphicon glyphicon-chevron-right"></span>
</button>