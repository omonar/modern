cameras = JSON.parse('<?= json_encode(dbFetchAll("SELECT Monitors.Id, Monitors.Name, Monitors.Protocol, Monitors.Host, Monitors.Port, Monitors.Path, COUNT(Events.Id) AS Events FROM Monitors INNER JOIN Events ON Events.MonitorId = Monitors.Id GROUP BY Monitors.Id")); ?>
');
activity = JSON.parse('<?= json_encode(dbFetchAll("SELECT Events.Id, Events.MonitorId, Monitors.Name, Events.StartTime, Events.EndTime AS Date, Events.StartTime, Events.EndTime, Events.Frames, Events.MaxScore FROM Events, Monitors WHERE Events.MonitorId=Monitors.Id AND DATE(EndTime) = DATE(NOW()) ORDER BY EndTime")); ?>
');