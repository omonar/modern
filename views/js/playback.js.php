cameras = JSON.parse('<?= json_encode(dbFetchAll("SELECT Monitors.Id, Monitors.Name, Monitors.Protocol, Monitors.Host, Monitors.Port, Monitors.Path, COUNT(Events.Id) AS Events, Monitors.Width, Monitors.Height FROM Monitors INNER JOIN Events ON Events.MonitorId = Monitors.Id GROUP BY Monitors.Id")); ?>
');