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

  xhtmlHeaders( __FILE__, $SLANG['Playback'] );
?>
  <body class="zm" onload="drawVisualization();"> <!-- begin body -->

    <?php require("header.php");  $monitors2 = $monitors?>

    <div class="view-wrapper">

        <div id="monitor-streams" class="monitor-streams grid"></div>

        <div class="controls-timeline controls"> <!-- begin controls-timeline -->
          <a href="#" class="playback-button" id="play"><img src="skins/emergent/views/images/playback/play.png" alt="play"></a>
          <label for="rangestart">Start</label>
          <input id="rangestart" type="text" class="hasDatePicker">
          <label for="rangeend">End</label>
          <input id="rangeend" type="text" class="hasDatePicker">
        </div> <!-- end controls-timeline -->

        <div id="timeline" class="timeline"></div>

        <div class="controls-misc controls">
          <button id="choose-cameras-opener" class="btn btn-default">Choose Cameras</button>
          <button id="preset-selection-opener" class="btn btn-default">Choose Preset</button>
          <p class="playback-date">0000-00-00</p><p class="playback-time">00:00:00</p>
        </div>

        <div id="choose-cameras" class="dialog-modal" title="Camera Selection">
          <ul>
            <?php
              $connkey = generateConnKey();
              $streamMode = "single";
              $sql = "SELECT * FROM Monitors";
              foreach(dbFetchAll( $sql ) as $row) {
                echo "<li>";
                outputImageStill( "monitor-stream-thumbnail-".$row['Id'], getStreamSrc(array( "mode=".$streamMode, "monitor=".$row['Id'] )), 160, 120,  null, "monitor-thumbnail" );
                echo "</li>";
              }
            ?>
          </ul>
        </div>

      </div> <!-- end view-wrapper -->
   
<?php require_once("footer.php"); ?>
