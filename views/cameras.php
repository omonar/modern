<table class="table">
  <th>Camera Name</th>
  <?php
    $query = "SELECT * FROM Monitors WHERE 1";
    foreach(dbFetchAll($query) as $camera) {
      echo "<tr>";
      echo "<td>" . $camera['Name'] . "</td>";
      echo "<td><a class=\"dialog\" href=\"?view=monitor&mid=" . $camera['Id'] . "\" data-monitorid=\"" . $camera['Id'] . "\"><span class=\"glyphicon glyphicon-edit\"></span></td>";
      echo "</tr>";
    }
  ?>
</table>