<?php
  //
  // Copyright (C) 2013 Kevin Varley at Emergent Ltd
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
  if(!isset($_SESSION['user']['Username']) || !canEdit('System')) {
    die("Access denied. <a href=\"?view=login\">Login</a>");
  }
  $query = "SELECT * FROM Groups";
  $response = dbFetchAll($query);
  if(!$response) {
    die("ERROR: Failed to fetch preset list");
  }
  foreach($response as $row) {
    $presets[$row['Id']] = $row['Name'];
  }
?>
  <div id="userlist-panel" class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">User List</h3>
    </div>
    <div class="panel-body">
      <table id="user-table" class="table table-striped">
        <thead>
          <tr>
            <th>Username</th>
            <th>Language</th>
            <th>Enabled?</th>
            <th>Stream</th>
            <th>Events</th>
            <th>Control</th>
            <th>Monitors</th>
            <th>Devices</th>
            <th>System</th>
            <th>Max Bandwidth</th>
            <th>Default Preset</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $query = "SELECT * FROM Users";
            $response = dbFetchAll($query);
            if(!$response) {
              die("<tr><td>ERROR: Failed to fetch user list!</td></tr>");
            }
            foreach($response as $row) {
              echo "<tr>";
              foreach($row as $key => $value) {
                if($key === "Id" || $key === "Password" || $key === "MonitorIds") {
                  continue;
                }
                if($key === "Enabled") {
                  if($value === "1") {
                    $value = "Yes";
                  }
                  else {
                    $value = "No";
                  }
                }

                if($key === "defaultPreset") {
                  if($value === "-1") {
                    $value = "All Cameras";
                  }
                  else {
                    $value = $presets[$value];
                  }
                }

                echo "<td>$value</td>";
              }
              echo "<td><a href=\"?view=user&uid=$row[Id]\" class=\"init-dynamic-colorbox\"><span class=\"glyphicon glyphicon-edit\"></span></a></td>";
              echo "<td><a href=\"#\" id=\"delete-user-$row[Id]\" class=\"delete-user\" data-userid=\"$row[Id]\"><span class=\"glyphicon glyphicon-remove-sign\"></span></a></td>";
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>
      <a href="?view=user" class="init-dynamic-colorbox btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Add New User</a>
    </div>
  </div>