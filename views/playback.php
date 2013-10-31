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
  <body class="zm" onload="drawVisualization();"> <!-- begin body -->

    <?php require("header.php");  $monitors2 = $monitors?>

    <div class="view-wrapper">

        <div id="monitor-streams" class="monitor-streams grid"></div>

        <div class="controls-timeline controls ui-corner-tr"> <!-- begin controls-timeline -->
          <ul class="controls-list">
            <li class="controls-timeline-playback-buttons">
              <button class="playback-button" id="rewind"><span class="glyphicon glyphicon-backward"></span></button>
              <button class="playback-button" id="play"><span class="glyphicon glyphicon-play"></span></button>
              <button class="playback-button" id="fastforward"><span class="glyphicon glyphicon-forward"></span></button>
            </li>
            <li class="controls-timeline-playback-rangestart">
              <label for="rangestart">Start</label>
              <input id="rangestart" type="text" class="hasDatePicker">
            </li>
            <li class="controls-timeline-playback-rangeend">
              <label for="rangeend">End</label>
              <input id="rangeend" type="text" class="hasDatePicker">
          </li>
          </ul>
        </div> <!-- end controls-timeline -->

        <div id="timeline" class="timeline"></div>

        <div class="controls-misc controls ui-corner-tl">
          <ul class="controls-list">
            <li>
              <button id="choose-cameras-opener" class="btn btn-default">Choose Cameras</button>
            </li>
            <li>
              <button id="preset-selection-opener" class="btn btn-default">Choose Preset</button>
            </li>
            <li>
               <p class="currently-playing">currently playing</p>
              <p class="playback-date">0000-00-00</p>
              <p class="playback-time">00:00:00</p>
            </li>
          </ul>
        </div>

        <div id="preset-selection" class="preset-selection dialog-modal" title="Preset Selection">
          <?php
            echo "<ul class=\"preset-list\">";
            foreach(dbFetchAll("SELECT * FROM Groups") as $index => $cameras) {
              echo "<li class=\"preset-list-item\"><a class=\"preset-list-link\" href=\"#\" data-value=\"" . $cameras['MonitorIds'] . "\">" . $cameras['Name'] . "</a></li>";
            }
            echo "</ul>";
          ?>
        </div>

        <div id="choose-cameras" class="choose-cameras dialog-modal" title="Camera Selection">
          <ul>
            <?php
              $connkey = generateConnKey();
              $streamMode = "single";
              $sql = "SELECT * FROM Monitors";
              foreach(dbFetchAll( $sql ) as $row) {
                echo "<li class=\"floatleft\">";
                outputImageStillWithClass( "monitor-stream-thumbnail-".$row['Id'], getStreamSrc(array( "mode=".$streamMode, "monitor=".$row['Id'] )), 160, 120,  null, "monitor-thumbnail" );
                echo "</li>";
              }
            ?>
          </ul>

        </div>

      </div> <!-- end view-wrapper -->
   
<?php require_once("footer.php"); ?>
