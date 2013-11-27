<?php
  xhtmlHeaders( __FILE__, "Events" );
?>
<body class="zm"> <!-- begin body -->

  <?php include('includes/adminnavbar.php'); ?>

  <div class="container"> <!-- begin container -->
    <div class="panel panel-primary events-filters"> <!-- begin filter panel -->
      <div class="panel-heading"> <!-- begin filter panel heading -->
        <h3 class="panel-title">Events Filter</h3>
      </div> <!-- end filter panel heading -->
      <div class="panel-body"> <!-- begin filter panel body -->

        <div class="row">
          <div class="col-md-2">
            <div class="well well-sm checkboxes">
              <p>Cameras</p>
              <?php
                foreach(dbFetchAll("SELECT * FROM Monitors") as $index => $camera) {
                  echo "<div class=\"checkbox\"><label><input type=\"checkbox\" value=\"" . $camera['Id'] . "\" class=\"monitor-checkbox\" checked>" . $camera['Name'] . "</label></div>";
                }
              ?>
            </div>
          </div>

          <div class="col-md-2">
            <div class="well well-sm">
              <p>Timeframe</p>

              <div class="radio">
                <label>
                  <input type="radio" name="timeframe" checked>All
                </label>
              </div>
              
              <div class="radio">
                <label>
                  <input type="radio" name="timeframe" value="today">Today
                </label>
              </div>

              <div class="radio">
                <label>
                  <input type="radio" name="timeframe" value="week">This Week
                </label>
              </div>

              <div class="radio">
                <label>
                  <input type="radio" name="timeframe" value="month">This Month
                </label>
              </div>

              <div class="radio">
                <label>
                  <input type="radio" id="custom-timeframe" name="timeframe" value="custom">Custom
                </label>
              </div>

            </div>
          </div>

          <div id="custom-range-filters" style="display: none;" class="col-md-3">
            <div class="well well-sm">
              <label for="startdatetime">Start Date & Time</label>
              <input id="startdatetime" name="startdatetime" type="text" class="form-control hasDatePicker">

              <label for="enddatetime">End Date & Time</label>
              <input id="enddatetime" name="enddatetime" type="text" class="form-control hasDatePicker">

            </div>
          </div>
        </div>

        <button id="show-events" style="float:right;" class="btn btn-primary">Show Events</button>

      </div> <!-- end filter panel body -->
    </div> <!-- end filter panel -->

    <div id="events" class="events">
      
    </div>

  </div> <!-- end container -->

</body>
</html>