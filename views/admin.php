<?php
  xhtmlHeaders( __FILE__, "Admin" );

?>
<body class="zm"> <!-- begin body -->

  <?php include('includes/adminnavbar.php'); ?>

  <div class="container"> <!-- begin container -->
    <div class="row">
      <div class="col-md-3">
        <h4>Disk Usage</h4>
        <canvas id="disk-usage-chart" width="200" height="200"></canvas>
      </div>
    </div>
  </div> <!-- end container -->

  <script>
    var data = [
      {
        value: <?=getDiskPercent()?>,
        color:"#990033"
      },
      {
        value : <?=(100-getDiskPercent())?>,
        color : "#29AB87"
      }
    ];
    var ctx = document.getElementById("disk-usage-chart").getContext("2d");
    new Chart(ctx).Pie(data);
  </script>

</body>
</html>