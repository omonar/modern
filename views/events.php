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

        <div class="row"> <!-- begin row -->
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

          <div class="col-md-2">
            <div class="well well-sm">
              <p>Order By</p>

              <select id="orderby-1" class="form-control pad-bottom">
                <option value="null" selected>Choose One</option>
                <option value="eventid">Event ID</option>
                <option value="camera">Camera</option>
                <option value="eventname">Event Name</option>
                <option value="starttime">Start Date/Time</option>
                <option value="endtime">End Date/Time</option>
                <option value="length">Event Length</option>
              </select>

              <select id="orderby-2" class="form-control pad-bottom">
                <option value="null" selected>Choose One</option>
                <option value="eventid">Event ID</option>
                <option value="camera">Camera</option>
                <option value="eventname">Event Name</option>
                <option value="starttime">Start Date/Time</option>
                <option value="endtime">End Date/Time</option>
                <option value="length">Event Length</option>
              </select>

              <select id="orderby-3" class="form-control pad-bottom">
                <option value="null" selected>Choose One</option>
                <option value="eventid">Event ID</option>
                <option value="camera">Camera</option>
                <option value="eventname">Event Name</option>
                <option value="starttime">Start Date/Time</option>
                <option value="endtime">End Date/Time</option>
                <option value="length">Event Length</option>
              </select>

              <select id="orderby-4" class="form-control pad-bottom">
                <option value="null" selected>Choose One</option>
                <option value="eventid">Event ID</option>
                <option value="camera">Camera</option>
                <option value="eventname">Event Name</option>
                <option value="starttime">Start Date/Time</option>
                <option value="endtime">End Date/Time</option>
                <option value="length">Event Length</option>
              </select>

              <select id="orderby-5" class="form-control pad-bottom">
                <option value="null" selected>Choose One</option>
                <option value="eventid">Event ID</option>
                <option value="camera">Camera</option>
                <option value="eventname">Event Name</option>
                <option value="starttime">Start Date/Time</option>
                <option value="endtime">End Date/Time</option>
                <option value="length">Event Length</option>
              </select>

            </div>
          </div>

          <div class="col-md-2">
            <div class="well well-sm">
              <p>Order Direction</p>

              <select id="orderdirection-1" class="form-control pad-bottom">
                <option value="asc" selected>Ascending</option>
                <option value="desc">Descending</option>
              </select>

              <select id="orderdirection-2" class="form-control pad-bottom">
                <option value="asc" selected>Ascending</option>
                <option value="desc">Descending</option>
              </select>

              <select id="orderdirection-3" class="form-control pad-bottom">
                <option value="asc" selected>Ascending</option>
                <option value="desc">Descending</option>
              </select>

              <select id="orderdirection-4" class="form-control pad-bottom">
                <option value="asc" selected>Ascending</option>
                <option value="desc">Descending</option>
              </select>

              <select id="orderdirection-5" class="form-control pad-bottom">
                <option value="asc" selected>Ascending</option>
                <option value="desc">Descending</option>
              </select>

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
        </div> <!-- end row -->

          <div class="row"> <!-- begin row -->
            <div class="col-md-12">
              <button id="show-events" class="btn btn-primary pull-right" style="margin-left: 10px;">Load Events</button>
              <button id="clear-filters" class="btn btn-default pull-right">Clear Filters</button>
            </div>
          </div> <!-- end row -->

      </div> <!-- end filter panel body -->
    </div> <!-- end filter panel -->

    <div id="events" class="events">
      
    </div>

  </div> <!-- end container -->

  <?php
    if(isset($_REQUEST['selectionset'])) {
  ?>
      <script type="text/javascript">
        var timeframe = "<?= $_REQUEST['timeframe']; ?>";
        var startdatetime = "<?= $_REQUEST['startdatetime']; ?>";
        var enddatetime = "<?= $_REQUEST['enddatetime']; ?>";
        var chosencameras = "<?= $_REQUEST['chosencameras']; ?>";
        var orderby = "<?= $_REQUEST['orderby']; ?>";

        $("#custom-timeframe").attr("checked", "");

        $(".monitor-checkbox").each(function() {
          $(this).removeAttr("checked");
          if($.inArray($(this).attr("value"), chosencameras) !== -1) {
            $(this).prop("checked", true);
          }
        });

        $("#custom-range-filters").show();

        $("#startdatetime").val(startdatetime);
        $("#enddatetime").val(enddatetime);     

        $("#orderby").val(orderby);

        getEvents(null, chosencameras.split(","), timeframe, startdatetime, enddatetime, orderby);
      </script>
  <?php
    }
  ?>

</body>
</html>