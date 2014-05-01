<div id="newzone-panel" class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Add New Zone</h3>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label for="newzone">Camera</label>
      <select id="newzone" class="form-control">
        <?php
          $cameras = dbFetchAll("SELECT * FROM Monitors");
          foreach($cameras as $camera) {
            echo "<option value=\"{$camera['Id']}\">{$camera['Name']}</option>";
          }
        ?>
      </select>
    </div>
    <button id="zm-new-zone" class="btn btn-primary">Add New Zone</button>
  </div>
</div>