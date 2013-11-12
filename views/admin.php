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

$maxWidth = 0;
$maxHeight = 0;
$cycleCount = 0;

$cycleWidth = $maxWidth;
$cycleHeight = $maxHeight;

xhtmlHeaders( __FILE__, "Admin" );
?>
<body class="zm"> <!-- begin body -->
  <?php require_once("header.php"); ?>
  <div class="view-wrapper"> <!-- begin view-wrapper -->
    <div id="tabs" class="tabs"> <!-- begin tabs -->
      <ul> <!-- begin tabs ul -->
        <li><a href="#status">Status</a></li>
        <li><a href="?view=cameras">Cameras</a></li>
        <li><a href="?view=events">Events</a></li>
        <li><a href="?view=users">Users</a></li>
        <li><a href="?view=options">System</a></li>
        <li><a href="?view=log">Log</a></li>
      </ul> <!-- end tabs ul -->

      <div id="status">
        <p>Zoneminder is:</p>
        <?php
          $running = daemonCheck();
          $status = $running?$SLANG['Running']:$SLANG['Stopped'];
          if($running === 1) {
            echo "<p class=\"zm-status running\">{$status}</p>";
          }
          else {
           echo "<p class=\"zm-status notrunning\">{$status}</p>"; 
          }
        ?>
      </div>

    </div> <!-- end tabs -->
  </div> <!-- end view-wrapper -->
<?php require_once("footer.php"); ?>