<?php
  //
  // ZoneMinder web console file, $Date: 2009-02-19 10:05:31 +0000 (Thu, 19 Feb 2009) $, $Revision: 2780 $
  // Copyright (C) 2001-2008 Philip Coombes
  //
  // This program is free software; you can redistribute it and/or
  // modify it under the terms of the GNU General Public License
  // of the License, or (at your option) any later version.
  //
  // This program is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU General Public License for more details.
  //
  // You should have received a copy of the GNU General Public License
  // along with this program; if not, write to the Free Software
  // Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
  //

  noCacheHeaders();

  $maxWidth = 0;
  $maxHeight = 0;
  $cycleCount = 0;

  $cycleWidth = $maxWidth;
  $cycleHeight = $maxHeight;

  // workaround so we don't have to have modified /usr/share/zoneminder/lang/*.php files
  $SLANG['Playback'] = "Playback";

  xhtmlHeaders( __FILE__, $SLANG['Playback'] );
?>
  <body class="zm"> <!-- begin body -->

    <div class="view-wrapper">
        <div class="top-toolbar">
          <button id="scale-decrease" class="btn btn-default" data-rel="tooltip" data-placement="bottom" title="Reduce camera sizes"><span class="glyphicon glyphicon-minus"></span></button>
          <button id="scale-reset" class="btn btn-default" data-rel="tooltip" data-placement="bottom" title="Reset camera sizes"><span class="glyphicon glyphicon-th-large"></span></button>
          <button id="scale-increase" class="btn btn-default" data-rel="tooltip" data-placement="bottom" title="Increase camera sizes"><span class="glyphicon glyphicon-plus"></span></button>
        </div>

        <div id="monitor-streams" class="monitor-streams"></div>

        <div class="controls-toolbar">
          <div class="controls-timeline controls ui-corner-tr"> <!-- begin controls-timeline -->
            <ul class="controls-list">
              <li class="controls-timeline-playback-buttons">
                <button class="btn btn-default playback-button" id="playback" data-rel="tooltip" title="Enter Playback Mode"><span class="glyphicon glyphicon-film"></span></button>
                <button class="btn btn-default playback-button playpause-button" id="play" data-rel="tooltip" title="Play / Pause" disabled style="display: none"><span class="glyphicon glyphicon-play"></span></button>
                <select class="form-control" id="speed" data-rel="tooltip" title="Change playback speed" style="display: none;">
                  <option value="400">0.5x</option>
                  <option value="200" selected>1x</option>
                  <option value="100">2x</option>
                  <option value="50">4x</option>
                </select>
              </li>
              <li class="controls-timeline-playback-rangestart" data-rel="tooltip" title="Choose a new start date to examine">
                <label for="rangestart">Start</label>
                <input id="rangestart" type="text" class="hasDatePicker" disabled>
              </li>
              <li class="controls-timeline-playback-rangeend" data-rel="tooltip" title="Change the end date and time for when the listed events should end">
                <label for="rangeend">End</label>
                <input id="rangeend" type="text" class="hasDatePicker" disabled>
              </li>
              <li class="controls-timeline-playback-load" data-rel="tooltip" title="Load event data for the selected range">
                <button id="load-events" class="btn btn-default btn-sm load-events" disabled><span class="glyphicon glyphicon-screenshot"></span> Load Events</button>
              </li>
            </ul>
          </div> <!-- end controls-timeline -->

          <div id="timeline" class="timeline"></div>

          <div class="controls-misc controls ui-corner-tl">
            <ul class="controls-list">
              <li>
                <button id="choose-cameras-opener" class="btn btn-default" data-rel="tooltip" title="Add and remove individual cameras from view"><span class="glyphicon glyphicon-camera"></span></button>
                <button id="preset-selection-opener" class="btn btn-default" data-rel="tooltip" title="Quickly show and hide cameras in groups"><span class="glyphicon glyphicon-th"></span></button>
                <?php if(canEdit('System')===true) { ?>
                  <a href="?view=admin" class="btn btn-default playback-button" data-rel="tooltip" title="Edit System Settings"><span class="glyphicon glyphicon-cog"></span></a>
                <?php } ?>
                <a href="?view=logout" class="btn btn-default playback-button" data-rel="tooltip" title="Logout"><span class="glyphicon glyphicon-log-out"></span></a>
              </li>
              <li>
                <p style="visibility: hidden;" class="currently-playing">playback date & time</p>
                <p style="visibility: hidden;" class="playback-date" data-rel="tooltip" title="The exact date and time being monitored">0000-00-00</p>
                <p style="visibility: hidden;" class="playback-time">00:00:00</p>
              </li>
              <li>
                <button id="page-refresh" class="btn btn-default" data-rel="tooltip" title="Refresh this page. May fix sporadic behaviour."><span class="glyphicon glyphicon-refresh"></span></button>
                <button id="fullscreen" class="btn btn-default" data-rel="tooltip" title="Make this page fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></button>
              </li>
            </ul>
          </div>

          <div id="preset-selection" class="preset-selection dialog-modal" title="Preset Selection">
            <?php
              $defaultPresetId = getUserDefaultPresetId($_SESSION['user']['Id']);
              if($defaultPresetId===false) {
                $defaultPresetId = "-1";
              }
              echo "<ul class=\"preset-list\">";
              echo "<li class=\"preset-list-item\"><input type=\"radio\" class=\"preset-list-default-preset\" name=\"defaultpreset\" value=\"-1\"";
              if($defaultPresetId === "-1") {
                echo " checked";
              }
              echo "><a class=\"show-all-cameras preset-list-link\" href=\"#\">All Cameras</a></li>";
              foreach(dbFetchAll("SELECT * FROM Groups") as $index => $cameras) {
                echo "<li class=\"preset-list-item\"><input type=\"radio\" id=\"preset-$cameras[Id]\" class=\"preset-list-default-preset\" name=\"defaultpreset\" value=\"" . $cameras['Id'] . "\"";
                if($defaultPresetId == $cameras['Id']) {
                  echo " checked";
                }
                echo "><a class=\"preset-list-link\" href=\"#\" data-value=\"" . $cameras['MonitorIds'] . "\">" . $cameras['Name'] . "</a></li>";
              }
              echo "</ul>";
            ?>
          </div>

          <div id="choose-cameras" class="choose-cameras dialog-modal" title="Camera Selection"> <!-- begin choose-cameras -->
            <ul class="monitor-stream-thumbnails">
              <?php
                $i = 1;
                $connkey = generateConnKey();
                $streamMode = "single";
                $sql = "SELECT * FROM Monitors";
                foreach(dbFetchAll( $sql ) as $row) {
                  echo "<li class=\"monitor-stream-thumbnail-item\">";
                  outputImageStillModern( "monitor-stream-thumbnail-".$row['Id'], getStreamSrc(array( "mode=".$streamMode, "monitor=".$row['Id'] )), 160, 120,  $row['Name'] . "(" . $row['Id'] . ")", "monitor-thumbnail");
                  echo "</li>";
                  $i++;
                }
              ?>
            </ul>
          </div> <!-- end choose-cameras -->
        </div>

      </div> <!-- end view-wrapper -->
    </body>
  </html>