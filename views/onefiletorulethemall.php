<?php
	/* begin functions */
	function addPreset($name) {
		$query = "INSERT INTO Presets (prestID, presetName) VALUES(NULL, {$name})";
		$result = dbQuery($query);
		return $result;
	}

	function removePreset($presetID) {
		$query = "DELETE FROM Presets WHERE presetID = " . $presetID;
		$result = dbQuery($query);
		return $result;
	}

	function addCameraToPreset($cameraID, $presetID) {
		$query = "INSERT INTO PresetsLink (presetID, presetMonitorId) VALUES({$presetID}, {$cameraID})";
		$result = dbQuery($query);
		return $result;
	}

	function removeCameraFromPreset($cameraID, $presetID) {
		$query = "DELETE FROM PresetsLink WHERE presetMonitorId = " . $cameraID;
	}

	function userDefaultPresetCheck($username) {
		
	}

	/* end functions */

	if(isset($_REQUEST['userDefaultPresetCheck'])) {
		echo userDefaultPresetCheck($_REQUEST['username']);
	}

	if(isset($_REQUEST['addPreset'])) {
		echo addPreset($_REQUEST['presetName']);
	}

	if(isset($_REQUEST['removePreset'])) {
		echo removePreset($_REQUEST['removePreset']);
	}

	if(isset($_REQUEST['addCameraToPreset'])) {
		echo addCameraToPreset($_REQUEST['addCameraToPreset']);
	}

	if(isset($_REQUEST['removeCameraFromPreset'])) {
		echo removeCameraFromPreset($_REQUEST['removeCameraFromPreset']);
	}
	
	if(isset($_REQUEST['q'])) {
		$query = "SELECT Id, MonitorId, StartTime, Frames FROM Events WHERE ";
		$datetimes = explode(",", $_REQUEST['q']);
		$i=0;
		foreach($datetimes as $datetime) {
			if($i !== 0) {
				$query .= " OR ";
			}
			$query .= "StartTime='{$datetime}'";
			$i++;
		}

		$results = dbFetchAll($query);

    	$scale = max( reScale( SCALE_BASE, '100', ZM_WEB_DEFAULT_SCALE ), SCALE_BASE );

		foreach ($results as $result) {
			for($counter = 1; $counter <= $result['Frames']; $counter++) {
		        $event['Id']=$result['Id'];
		        $event['StartTime']=$result['StartTime'];
		        $event['MonitorId']=$result['MonitorId'];
				$imageData = getImageSrc($event, $counter, $scale, (isset($_REQUEST['show']) && $_REQUEST['show']=="capt"));
		        $imagePath = $imageData['thumbPath'];
		        $eventPath = $imageData['eventPath'];
		        $dImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-d.jpg", $eventPath, $counter);
		        $rImagePath = sprintf("%s/%0".ZM_EVENT_IMAGE_DIGITS."d-diag-r.jpg", $eventPath, $counter);
		        $frames[$result['MonitorId']][$result['Id']][] = "/zm/" . viewImagePath($imagePath);
			}
		}
    	echo json_encode($frames);
	}

	if (isset($_REQUEST['timeline'])) {
		$cameras = "'" . implode("','", explode(",", $_REQUEST['cameras'])) . "'";
		//echo json_encode(dbFetchAll("SELECT Events.Id, Events.MonitorId, Events.StartTime, Events.EndTime, Events.Frames, Events.MaxScore FROM Events, Monitors WHERE Events.MonitorId=Monitors.Id AND Events.MonitorId IN (" . $cameras . ") AND StartTime BETWEEN '" . $_REQUEST['start'] . "' AND '" . $_REQUEST['end'] . "'"));
		echo json_encode(dbFetchAll("SELECT Events.Id, Events.MonitorId, Monitors.Name, Events.StartTime, Events.EndTime AS Date, Events.StartTime, Events.EndTime, Events.Frames, Events.MaxScore FROM Events, Monitors WHERE Events.MonitorId=Monitors.Id AND Events.MonitorId IN (" . $cameras . ") AND StartTime BETWEEN '" . $_REQUEST['start'] . "' AND '" . $_REQUEST['end'] . "'"));
	}
?>