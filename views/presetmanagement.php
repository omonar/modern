<?php
  $response = dbFetchAll("SELECT Id, Name FROM Monitors");
  if(!$response) {
    die("ERROR: Failed to fetch data from database!");
  }
  foreach($response as $row) {
    $monitors[$row['Id']] = $row['Name'];
  }

  foreach(dbFetchAll("SELECT * FROM Groups") as $index => $value) {
    $presets[] = array('presetID' => $value['Id'], 'presetName' => $value['Name'], 'presetCameras' => explode(",", $value['MonitorIds']));
  }
?>
<div id="preset-management-panel" class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Preset Management</h3>
  </div>
  <div class="panel-body">
    <table id="preset-table" class="table table-striped">
      <thead>
        <tr>
          <th>Preset Name</th>
          <th>Cameras</th>
          <th>Edit Preset</th>
          <th>Delete Preset</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach($presets as $preset) {
            echo "<tr id=\"preset-" . $preset['presetID'] . "\" data-monitorids=\"" . implode(",", $preset['presetCameras']) . "\" data-presetname=\"" . $preset['presetName'] . "\"><td>" . $preset['presetName'] . "</td><td>";
            foreach($preset['presetCameras'] as $monitorId) {
              echo "<p>$monitors[$monitorId] ($monitorId)</p>";
            }
            echo "</td><td><a href=\"#\" class=\"btn btn-primary edit-preset\" data-presetid=\"$preset[presetID]\"><span class=\"fa fa-edit\"></span></a></td>";
            echo "<td><a href=\"#\" class=\"btn btn-danger delete-preset\" data-presetid=\"$preset[presetID]\"><span class=\"fa fa-times-circle\"></span></a></td></tr>";
          }
        ?>
      </tbody>
    </table>
    <a href="#" id="add-new-preset" class="btn btn-primary"><span class="fa fa-plus-circle"></span> Add New Preset</a>
  </div>
</div>

<div id="add-new-preset-panel" class="panel panel-primary" style="display: none;">
  <div class="panel-heading">
    <h3 class="panel-title">Add New Preset</h3>
  </div>
  <div class="panel-body">
    <table class="table">
      <tr>
        <th>Preset Name</th>
        <td><input type="text" id="new-preset-name" class="form-control"></td>
      </tr>
      <tr>
        <th>Preset Cameras</th>
        <td>
          <select id="new-preset-cameras" class="form-control" multiple="multiple">
            <?php
              foreach($monitors as $monitorID => $monitorName) {
                echo "<option value=\"$monitorID\">$monitorName</option>";
              }
            ?>
          </select>
        </td>
    </table>
    <a href="#" class="btn btn-primary back-to-preset-management" data-calledfrom="presetmanagement"><span class="fa fa-chevron-circle-left"></span> Back To Preset Mangement</a>
    <a href="#" id="save-new-preset" class="btn btn-success"><span class="fa fa-save"></span> Save New Preset</a>
  </div>
</div>

<div id="edit-preset-panel" class="panel panel-primary" style="display: none;">
  <div class="panel-heading">
    <h3 class="panel-title">Edit Preset</h3>
  </div>
  <div class="panel-body">
    <table class="table">
      <tr>
        <th>Preset Name</th>
        <td><input type="text" id="edit-preset-name" class="form-control"></td>
      </tr>
      <tr>
        <th>Preset Cameras</th>
        <td>
          <select id="edit-preset-cameras" class="form-control" multiple="multiple">
            <?php
              foreach($monitors as $monitorID => $monitorName) {
                echo "<option id=\"monitorid-$monitorID\" value=\"$monitorID\">$monitorName</option>";
              }
            ?>
          </select>
        </td>
    </table>
    <a href="#" class="btn btn-primary back-to-preset-management" data-calledfrom="presetmanagement"><span class="fa fa-chevron-circle-left"></span> Back To Preset Mangement</a>
    <a href="#" id="save-preset" class="btn btn-success"><span class="fa fa-save"></span> Save Preset</a>
  </div>
</div>