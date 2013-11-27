<?php
  //
  // ZoneMinder web run state view file, $Date: 2008-10-07 10:02:17 +0100 (Tue, 07 Oct 2008) $, $Revision: 2651 $
  // Copyright (C) 2001-2008 Philip Coombes
  //
  // This program is free software; you can redistribute it and/or
  // modify it under the terms of the GNU General Public License
  // as published by the Free Software Foundation; either version 2
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

  if (!canEdit('System')) {
    $view = "error";
    return;
  }
  $running = daemonCheck();
  $status = $running?$SLANG['Running']:$SLANG['Stopped'];

  $states = dbFetchAll( "select * from States" );

  $allstates = array(0 => "stop", 1 => "start", 2 => "restart");

  if(!isset($_REQUEST['changestate'])) {
?>
    <div id="userlist-panel" class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Change ZoneMinder State</h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <select id="state" class="form-control">
            <option value="restart">Restart</option>
            <?php
              if($status !== "Running") {
            ?>
                <option value="start">Start</option>
            <?php
              }
              elseif ($status !== "Stopped") {
            ?>
                <option value="stop">Stop</option>
            <?php
              }
              foreach($states as $state) {
                $allstates[] = $state['Definition'];
                echo "<option value=\"$state[Definition]\">$state[Name]</option>";
              }
            ?>
          </select>
        </div>
        <button id="zm-change-state" class="btn btn-primary">Change State</button>
      </div>
    </div>
<?php
  }
  else {
    if(ctype_alnum($_REQUEST['newstate']) && in_array($_REQUEST['newstate'], $allstates)) {
      packageControl($_REQUEST['newstate']);
    }
  }
?>