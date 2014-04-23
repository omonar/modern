<div class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <div class="navbar-collapse collapse">
    <ul class="nav navbar-nav">
      <li class="colour purple"><a href="?view=playback"><i class="fa fa-arrow-circle-left"></i> Playback</a></li>

      <li class="colour blue"><a href="?view=admin"><span class="fa fa-home"></span> Home</a></li>

      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-camera"></span> Cameras <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <!-- id="add-new-monitor" -->
          <li><a href="?skin=classic&amp;view=monitor" class="init-colorbox"><span class="fa fa-plus-circle"></span> Add camera</a></li>
          <li class="divider"></li>
          <li class="dropdown-header">Edit Existing</li>
          <?php
            $cameras = dbFetchAll("SELECT * FROM Monitors");
            foreach($cameras as $index => $camera) {
              $monitorClass = "monitor-unknown";
              if(!zmcStatus($camera)) {
                $monitorClass = "monitor-down";
              }
              elseif(!zmaStatus($camera)) {
                $monitorClass = "monitor-warning";
              }
              else {
                $monitorClass = "monitor-ok";
              }

              echo "<li><a href=\"?skin=classic&amp;view=monitor&amp;mid=" . $camera['Id'] . "\" class=\"init-colorbox\" data-monitorid=\"" . $camera['Id'] . "\"><span class=\"fa fa-edit\"></span> " . $camera['Name'];
              switch($monitorClass) {
                case "monitor-down":
                  echo " <span style=\"color: red;\" class=\"fa fa-exclamation-circle\"></span>";
                  break;
                case "monitor-warning":
                  echo " <span style=\"color: orange;\" class=\"fa fa-question-circle\"></span>";
                  break;
                case "monitor-ok":
                  echo " <span style=\"color: green;\" class=\"fa fa-check-circle-o\"></span>";
                  break;
              }
              echo "</a></li>";
            }
          ?>
        </ul>
      </li>

      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-crosshairs"></span> Zones <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <?php
            foreach($cameras as $camera) {
          ?>
            <li><a href="?skin=classic&amp;view=zones&amp;mid=<?=$camera['Id']?>" class="init-colorbox"><span class="fa fa-camera"></span> <?=$camera['Name']?></a></li>
          <?php
            }
          ?>
        </ul>
      </li>

      <li><a href="#" id="presetmanagement"><span class="fa fa-list"></span> Presets</a></li>

      <li><a href="?view=events"><span class="fa fa-picture-o"></span> Events</a></li>

      <li><a href="#" id="userlist"><span class="fa fa-users"></span> Users</a></li>

      <li><a href="?skin=classic&amp;view=log" class="init-colorbox"><span class="fa fa-terminal"></span> Log</a></li>

      <li><a href="?skin=classic&amp;view=options" class="init-colorbox"><span class="fa fa-cog"></span> Settings</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">

      <li class="colour red"><a href="#"><span class="fa fa-hdd-o"></span> <?=getDiskPercent();?>%</a></li>

      <li class="dropdown colour yellow">
        <a href="#" id="updates" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-arrow-circle-up"></span> Updates</a>
        <ul class="dropdown-menu">
          <li id="skinVersionMessage"><a href="#"><span class="fa fa-picture-o"></span> Checking for updates...</a></li>
        </ul>
      </li>

      <?php
        $running = daemonCheck();
        $status = $running?$SLANG['Running']:$SLANG['Stopped'];

        if($status === "Running") {
          $colour = "green";
          $icon = "fa-check-circle";
        }
        elseif($status === "Stopped") {
          $colour = "red";
          $icon = "fa-times-circle";
        }
        else {
          $colour = "yellow";
          $icon = "fa-exclamation-circle";
        }
      ?>

      <li class="colour <?=$colour?>"><a href="#" id="zm-change-state-opener"><span class="fa <?=$icon?>"></span> ZM <?=lcfirst($status)?></a></li>

      <li class="dropdown colour blue">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> <?=ucfirst($_SESSION['user']['Username'])?> <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a id="changepassword-opener" href="#"><span class="fa fa-edit"></span> Change Password</a></li>
          <li><a href="?view=logout"><span class="fa fa-sign-out"></span> Log Out</a></li>
        </ul>
      </li>

    </ul>
  </div>
  </div>