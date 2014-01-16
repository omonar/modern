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
      <li class="colour purple"><a href="?view=playback"><i class="glyphicon glyphicon-chevron-left"></i> Playback</a></li>

      <li class="colour blue"><a href="?view=admin"><span class="glyphicon glyphicon-home"></span> Home</a></li>

      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-camera"></span> Cameras <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="#" id="add-new-monitor"><span class="glyphicon glyphicon-plus-sign"></span> Add camera</a></li>
          <li class="divider"></li>
          <li class="dropdown-header">Edit Existing</li>
          <?php
            foreach(dbFetchAll("SELECT * FROM Monitors") as $index => $camera) {
              echo "<li><a href=\"?skin=classic&view=monitor&mid=" . $camera['Id'] . "\" class=\"init-colorbox\" data-monitorid=\"" . $camera['Id'] . "\"><span class=\"glyphicon glyphicon-edit\"></span> " . $camera['Name'] . "</a></li>";
            }
          ?>
        </ul>
      </li>

      <li><a href="#" id="presetmanagement"><span class="glyphicon glyphicon-list"></span> Presets</a></li>

      <li><a href="?view=events"><span class="glyphicon glyphicon-picture"></span> Events</a></li>

      <li><a href="#" id="userlist"><span class="glyphicon glyphicon-user"></span> Users</a></li>

      <li><a href="?skin=classic&view=options" class="init-colorbox"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>

    </ul>
    <ul class="nav navbar-nav navbar-right">

      <li class="dropdown colour yellow">
        <a href="#" id="updates" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-circle-arrow-up"></span> Updates</a>
        <ul class="dropdown-menu">
          <li id="skinVersionMessage"><a href="#"><span class="glyphicon glyphicon-picture"></span> Checking for updates...</a></li>
        </ul>
      </li>

      <?php
        $running = daemonCheck();
        $status = $running?$SLANG['Running']:$SLANG['Stopped'];

        if($status === "Running") {
          $colour = "green";
          $icon = "glyphicon-ok-sign";
        }
        elseif($status === "Stopped") {
          $colour = "red";
          $icon = "glyphicon-remove-sign";
        }
        else {
          $colour = "yellow";
          $icon = "glyphicon-exclamation-sign";
        }
      ?>

      <li class="colour <?=$colour?>"><a href="#" id="zm-change-state-opener"><span class="glyphicon <?=$icon?>"></span> ZoneMinder is <?=lcfirst($status)?></a></li>

      <li class="dropdown colour blue">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=ucfirst($_SESSION['user']['Username'])?> <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a id="changepassword-opener" href="#"><span class="glyphicon glyphicon-edit"></span> Change Password</a></li>
          <li><a href="?view=logout"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
        </ul>
      </li>

    </ul>
  </div>
  </div>