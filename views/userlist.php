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
  if(dbNumRows($query) > 1) {
    $response = dbFetchAll($query);
    if(!$response) {
      die("ERROR: Failed to fetch preset list");
    }
    foreach($response as $row) {
      $presets[$row['Id']] = $row['Name'];
    }
  }
  else {
    $presets = null;
  }
?>
  <div id="user-list-panel" class="panel panel-primary">
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
              if(!array_key_exists("defaultPreset", $response)) {
                $row['defaultPreset'] = "N/A";
              }
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
                    if($presets !== null) {
                      $value = $presets[$value];
                    }
                  }
                }

                echo "<td>$value</td>";
              }
              echo "<td><a href=\"?skin=classic&view=user&uid=$row[Id]\" data-userid=\"$row[Id]\" class=\"edit-user init-dynamic-colorbox\"><span class=\"glyphicon glyphicon-edit\"></span></a></td>";
              echo "<td><a href=\"#\" id=\"delete-user-$row[Id]\" class=\"delete-user\" data-userid=\"$row[Id]\"><span class=\"glyphicon glyphicon-remove-sign\"></span></a></td>";
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>
      <button id="add-new-user-opener" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Add New User</button>
    </div>
  </div>

  <div id="add-new-user-panel" class="panel panel-primary" style="display: none;">
    <div class="panel-heading">
      <h3 class="panel-title">Add New User</h3>
    </div>
    <div class="panel-body">
      <table id="preset-table" class="table table-striped">
        <tbody>
          <tr>
            <th>Username</th>
            <td><input type="text" id="Username" name="Username" class="form-control new-user-form-value" autofocus></td>
          </tr>
          <tr>
            <th>New Password</th>
            <td><input type="password" id="Password" name="Password" class="form-control new-user-form-value"></td>
          </tr>
          <tr>
            <th>Confirm Password</th>
            <td><input type="password" id="PasswordConfirmation" name="PasswordConfirmation" class="form-control new-user-form-value"></td>
          </tr>
          <tr>
            <th>Language</th>
            <td>
            <select name="Language" id="Language" class="form-control new-user-form-value">
              <option value="" selected="selected"></option>
              <option value="big5_big5">big5_big5</option>
              <option value="cn_zh">cn_zh</option>
              <option value="cs_cz">cs_cz</option>
              <option value="de_de">de_de</option>
              <option value="dk_dk">dk_dk</option>
              <option value="en_gb">en_gb</option>
              <option value="en_us">en_us</option>
              <option value="es_ar">es_ar</option>
              <option value="et_ee">et_ee</option>
              <option value="fr_fr">fr_fr</option>
              <option value="he_il">he_il</option>
              <option value="hu_hu">hu_hu</option>
              <option value="it_it">it_it</option>
              <option value="ja_jp">ja_jp</option>
              <option value="nl_nl">nl_nl</option>
              <option value="pl_pl">pl_pl</option>
              <option value="pt_br">pt_br</option>
              <option value="ro_ro">ro_ro</option>
              <option value="ru_ru">ru_ru</option>
              <option value="se_se">se_se</option>
            </select>
            </td>
          </tr>
          <tr>
            <th>Enabled</th>
            <td>
              <select name="Enabled" id="Enabled" class="form-control new-user-form-value">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Stream</th>
            <td>
              <select name="Stream" id="Stream" class="form-control new-user-form-value">
                <option value="None">None</option>
                <option value="View" selected>View</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Events</th>
            <td>
              <select name="Events" id="Events" class="form-control new-user-form-value">
                <option value="None">None</option>
                <option value="View">View</option>
                <option value="Edit" selected>Edit</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Control</th>
            <td>
              <select name="Control" id="Control" class="form-control new-user-form-value">
                <option value="None">None</option>
                <option value="View">View</option>
                <option value="Edit" selected>Edit</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Monitors</th>
            <td>
              <select name="Monitors" id="Monitors" class="form-control new-user-form-value">
                <option value="None">None</option>
                <option value="View">View</option>
                <option value="Edit" selected>Edit</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>System</th>
            <td>
              <select name="System" id="System" class="form-control new-user-form-value">
                <option value="None">None</option>
                <option value="View">View</option>
                <option value="Edit" selected>Edit</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Max Bandwidth</th>
            <td>
              <select name="MaxBandwidth" id="MaxBandwidth" class="form-control new-user-form-value">
                <option value="" selected="selected"></option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Restricted Monitors</th>
            <td>
              <select id="monitorIds" name="monitorIds" multiple="multiple" class="form-control new-user-form-value">
                <?php
                  $response = dbFetchAll("SELECT Id, Name FROM Monitors");
                  if(!$response) {
                    echo "<option value=\"null\">Error fetching list</option>";
                  }
                  else {
                    foreach($response as $row) {
                      echo "<option value=\"$row[Id]\">$row[Name]</option>";
                    }
                  }
                ?>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
      <a href="#" class="btn btn-primary back-to-preset-management" data-calledfrom="userlist"><span class="glyphicon glyphicon-chevron-left"></span> Back To Preset Mangement</a>
      <button id="add-new-user" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Save User</button>
    </div>
  </div>