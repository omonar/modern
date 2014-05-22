<?php
  if (!canView('Monitors')) {
      die("ERROR: Permission denied!");
  }

  if(isset($_REQUEST['mid']) && ctype_digit($_REQUEST['mid'])) {
    $query = "SELECT * FROM Monitors WHERE Monitors.Id='" . $_REQUEST['mid'] . "'";
    $response = dbFetchOne($query);
    if(!$response) {
      die("ERROR: Failed to fetch camera infromation from database!");
    }
?>
    <ul class="nav nav-tabs">
      <li class="active"><a href="#general" data-toggle="tab">General</a></li>
      <li><a href="#source" data-toggle="tab">Source</a></li>
      <li><a href="#timestamp" data-toggle="tab">Timestamp</a></li>
      <li><a href="#buffers" data-toggle="tab">Buffers</a></li>
      <li><a href="#misc" data-toggle="tab">Misc</a></li>
    </ul>

    <div class="tab-content">
        <!-- begin general tab -->
        <div class="tab-pane active" id="general">
          <form action="?view=edimtebonitor" method="post">
          <table class="table">
            <tr>
              <th>Name</th>
              <td><input type="text" id="Name" name="Name" class="form-control monitor-form-value" value="<?=$response['Name']?>"></td>
            </tr>
            <tr>
              <th>Function</th>
              <td>
                  <select id="Function" name="Function" class="form-control monitor-form-value">
                    <option value="None"<?php if($response['Function']==="None") { echo " selected"; } ?>>None</option>
                    <option value="Monitor"<?php if($response['Function']==="Monitor") { echo " selected"; } ?>>Monitor</option>
                    <option value="Modect"<?php if($response['Function']==="Modect") { echo " selected"; } ?>>Motion Detect</option>
                    <option value="Record"<?php if($response['Function']==="Record") { echo " selected"; } ?>>Record</option>
                    <option value="Mocord"<?php if($response['Function']==="Nocord") { echo " selected"; } ?>>Motion Record</option>
                    <option value="Nodect"<?php if($response['Function']==="Nodect") { echo " selected"; } ?>>No Detection</option>
                  </select>
              </td>
            </tr>
            <tr>
              <th>Enabled</th>
              <td><input type="checkbox" id="Enabled" name="Enabled" class="monitor-form-value"<?php if($response['Enabled'] === "1") { echo " checked"; } ?>></td>
            </tr>
            <tr>
              <th>Linked Monitors</th>
              <td>
                <select id="LinkedMonitors" name="LinkedMonitors" multiple="multiple" class="form-control monitor-form-value">
                  <?php
                    $query = "SELECT Id, Name FROM Monitors";
                    $response2 = dbFetchAll($query);
                    if(!$response2) {
                      echo "<option>Failed to fetch monitor list</option>";
                    }
                    if(strlen($response['LinkedMonitors']) > 0) {
                      $linkedMonitors = explode(",", $response['LinkedMonitors']);
                    }
                    foreach($response2 as $monitor) {
                      echo "<option value=\"$monitor[Id]\"";
                      if(isset($linkedMonitors) && in_array($monitor['Id'], $linkedMonitors)) {
                        echo " selected";
                      }
                      echo ">$monitor[Name]</option>";
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <th>Maximum FPS</th>
              <td><input type="number" id="MaxFPS" name="MaxFPS" class="form-control monitor-form-value" value="<?=$response['MaxFPS']?>"></td>
            </tr>
            <tr>
              <th>Alarm Maximum FPS</th>
              <td><input type="number" id="AlarmMaxFPS" name="AlarmMaxFPS" class="form-control monitor-form-value" value="<?=$response['AlarmMaxFPS']?>"></td>
            </tr>
            <tr>
              <th>Reference Image Blend Percentage</th>
              <td><input type="number" id="RefBlendPerc" name="RefBlendPerc" class="form-control monitor-form-value" value="<?=$response['RefBlendPerc']?>"></td>
            </tr>
            <tr>
              <th>Triggers</th>
              <td>None Available</td>
            </tr>
          </table>
          <button type="submit" class="saveall btn btn-primary">Save All</button>
        </div>
        <!-- end general tab -->
        
        <!-- begin source tab -->
        <div class="tab-pane" id="source">
          <table class="table">
            <tr>
              <th>Source Type</th>
              <td>
                  <select id="Type" name="Type" class="form-control monitor-form-value">
                    <option value="Local"<?php if($response['Type'] === "Local") { echo " selected"; } ?>>Local</option>
                    <option value="Remote"<?php if($response['Type'] === "Remote") { echo " selected"; } ?>>Remote</option>
                    <option value="File"<?php if($response['Type'] === "File") { echo " selected"; } ?>>File</option>
                    <option value="Ffmpeg"<?php if($response['Type'] === "Ffmpeg") { echo " selected"; } ?>>FFMPEG</option>
                  </select>
              </td>
            </tr>
              <tr class="localSource">
                <th>Device Path</th>
                <td><input type="text" id="monitorDevicePath" name="monitorDevicePath" class="form-control monitor-form-value"></td>
              </tr>
              <tr class="localSource">
                <th>Capture Method</th>
                <td>
                    <select id="monitorCaptureMethod" name="monitorCaptureMethod" class="form-control monitor-form-value">
                      <option value="v4l2">Video For Linux 2</option>
                    </select>
                </td>
              </tr>
              <tr class="localSource">
                <th>Device Channel</th>
                <td>
                  <select id="Channel" class="form-control monitor-form-value">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>
                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                  </select>
                </td>
              </tr>
              <tr class="localSource">
                <th>Capture Palette</th>
                <td>
                    <select name="WebColourPalette" class="form-control monitor-form-value">
                      <option value="">Undefined</option>
                      <option value="875836498">RGB444</option>
                      <option value="1329743698">RGB555</option>
                      <option value="1346520914">RGB565</option>
                      <option value="861030210">BGR24</option>
                      <option value="859981650">RGB24</option>
                      <option value="877807426">BGR32</option>
                      <option value="876758866">RGB32</option>
                      <option value="1497715271">GREY</option>
                      <option value="1448695129">YUYV</option>
                      <option value="1345466932">YUV422P</option>
                      <option value="1345401140">YUV411P</option>
                      <option value="875836505">YUV444</option>
                      <option value="961959257">YUV410</option>
                      <option value="842093913">YUV420</option>
                      <option value="1195724874">JPEG</option>
                    </select>
                </td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Protocol</th>
                <td>
                  <select id="Protocol" name="Protocol" class="form-control monitor-form-value">
                    <option value="http"<?php if($response['Protocol'] === "http") { echo " selected"; } ?>>HTTP</option>
                    <option value="rtsp"<?php if($response['Protocol'] === "rtsp") { echo " selected"; } ?>>RTSP</option>
                  </select>
                </td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Method</th>
                <td>
                  <select id="Method" name="Method" class="form-control monitor-form-value">
                    <option value="simple"<?php if($response['Method'] === "simple") { echo " selected"; } ?>>Simple</option>
                    <option value="regexp"<?php if($response['Method'] === "regexp") { echo " selected"; } ?>>Regexp</option>
                  </select>
                </td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Source Name</th>
                <td><input type="text" id="Host" name="Host" class="form-control monitor-form-value" placeholder="Enter IP address" value="<?=$response['Host']?>"></td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Host Port</th>
                <td><input type="number" id="Port" name="Port" class="form-control monitor-form-value" placeholder="Port Number" value="<?=$response['Port']?>"></td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Host Path</th>
                <td><input type="text" id="Path" name="Path" class="form-control monitor-form-value" placeholder="Enter path" value="<?=$response['Path']?>"></td>
              </tr>
              <tr class="remoteSource">
                <th>Remote Image Colours</th>
                <td>
                  <select id="monitorRemoteImageColours" name="monitorRemoteImageColours" class="form-control monitor-form-value">
                    <option value="3"<?php if($response['Method'] === "3") { echo " selected"; } ?>>24-Bit Colour</option>
                    <option value="1"<?php if($response['Method'] === "1") { echo " selected"; } ?>>8-Bit Colour</option>
                  </select>
                </td>
              </tr>
              <tr class="miscSource">
                <th>Source Path</th>
                <td><input type="text" id="monitorSourcePath" name="monitorSourcePath" class="form-control monitor-form-value" value="<?=$reponse['Path']?>"></td>
              </tr>
              <tr class="miscSource">
                <th>Source Colours</th>
                <td>
                  <select id="monitorSourceColours" name="monitorSourceColours" class="form-control monitor-form-value">
                    <option value="3"<?php if($response['Method'] === "3") { echo " selected"; } ?>>24-Bit Colour</option>
                    <option value="1"<?php if($response['Method'] === "1") { echo " selected"; } ?>>8-Bit Colour</option>
                  </select>
                </td>
              </tr>
            <tr>
              <th>Capture Width (pixels)</th>
              <td><input type="text" id="Width" name="Width" class="form-control monitor-form-value" value="<?=$response['Width']?>"></td>
            </tr>
            <tr>
              <th>Capture Height (pixels)</th>
              <td><input type="text" id="Height" name="Height" class="form-control monitor-form-value" value="<?=$response['Height']?>"></td>
            </tr>
            <tr>
              <th>Preserve Aspect Ratio</th>
              <td>Not yet implemented <input type="checkbox" id="monitorPreserveAspectRatio" name="monitorPreserveAspectRatio" class="monitor-form-value"></td>
            </tr>
            <tr>
              <th>Orientation</th>
              <td>
                <select id="monitorOrientation" name="monitorOrientation" class="form-control monitor-form-value">
                  <option value="0"<?php if($response['Orientation'] === "0") { echo " selected"; } ?>>Normal</option>
                  <option value="90"<?php if($response['Orientation'] === "90") { echo " selected"; } ?>>Rotate Right</option>
                  <option value="180"<?php if($response['Orientation'] === "180") { echo " selected"; } ?>>Inverted</option>
                  <option value="270"<?php if($response['Orientation'] === "270") { echo " selected"; } ?>>Rotate Left</option>
                  <option value="hori"<?php if($response['Orientation'] === "hori") { echo " selected"; } ?>>Flipped Horizontally</option>
                  <option value="vert"<?php if($response['Orientation'] === "vert") { echo " selected"; } ?>>Flipped Vertically</option>
                </select>
              </td>
            </tr>
          </table>
          <button type="submit" class="saveall btn btn-primary">Save All</button>
        </div>
        <!-- end source tab -->
        
        <!-- begin timestamp tab -->
        <div class="tab-pane" id="timestamp">
          <table class="table">
            <tr>
              <th>Timestamp Label Format</th>
              <td><input type="text" id="LabelFormat" name="LabelFormat" class="form-control monitor-form-value" value="<?=$response['LabelFormat']?>"></td>
            </tr>
            <tr>
              <th>Timestamp Label Position X</th>
              <td><input type="number" id="LabelX" name="LabelX" class="form-control monitor-form-value" value="<?=$response['LabelX']?>"></td>
            </tr>
            <tr>
              <th>Timestamp Label Position Y</th>
              <td><input type="number" id="LabelY" name="LabelY" class="form-control monitor-form-value" value="<?=$response['LabelY']?>"></td>
            </tr>
          </table>
          <button type="submit" class="saveall btn btn-primary">Save All</button>
        </div>
        <!-- end timestamp tab -->
        
        <!-- begin buffers tab -->
        <div class="tab-pane" id="buffers">
          <table class="table">
            <tr>
              <th>Image Buffer Size (frames)</th>
              <td><input type="number" id="ImageBufferCount" name="ImageBufferCount" class="form-control monitor-form-value" value="<?=$response['ImageBufferCount']?>"></td>
            </tr>
            <tr>
              <th>Warmup Frames</th>
              <td><input type="number" id="WarmupCount" name="WarmupCount" class="form-control monitor-form-value" value="<?=$response['WarmupCount']?>"></td>
            </tr>
            <tr>
              <th>Pre Event Image Count</th>
              <td><input type="number" id="PreEventCount" name="PreEventCount" class="form-control monitor-form-value" value="<?=$response['PreEventCount']?>"></td>
            </tr>
            <tr>
              <th>Post Event Image Count</th>
              <td><input type="number" id="PostEventCount" name="PostEventCount" class="form-control monitor-form-value" value="<?=$response['PostEventCount']?>"></td>
            </tr>
            <tr>
              <th>Stream Replay Image Buffer</th>
              <td><input type="number" id="StreamReplayBuffer" name="StreamReplayBuffer" class="form-control monitor-form-value" value="<?=$response['StreamReplayBuffer']?>"></td>
            </tr>
            <tr>
              <th>Alarm Frame Count</th>
              <td><input type="number" id="AlarmFrameCount" name="AlarmFrameCount" class="form-control monitor-form-value" value="<?=$response['AlarmFrameCount']?>"></td>
            </tr>
          </table>
          <button type="submit" class="saveall btn btn-primary">Save All</button>
        </div>
        <!-- end buffers tab -->
        
        <!-- begin misc tab -->
        <div class="tab-pane" id="misc">
          <table class="table">
            <tr>
              <th>Event Prefix</th>
              <td><input type="text" id="EventPrefix" name="EventPrefix" class="form-control monitor-form-value" value="<?=$response['EventPrefix']?>"></td>
            </tr>
            <tr>
              <th>Section Length</th>
              <td><input type="number" id="SectionLength" name="SectionLength" class="form-control monitor-form-value" value="<?=$response['SectionLength']?>"></td>
            </tr>
            <tr>
              <th>Frame Skip</th>
              <td><input type="number" id="FrameSkip" name="FrameSkip" class="form-control monitor-form-value" value="<?=$response['FrameSkip']?>"></td>
            </tr>
            <tr>
              <th>FPS Report Interval</th>
              <td><input type="number" id="FPSReportInterval" name="FPSReportInterval" class="form-control monitor-form-value" value="<?=$response['FPSReportInterval']?>"></td>
            </tr>
            <tr>
              <th>Default View</th>
              <td>
                  <select id="monitorDefaultView" name="monitorDefaultView" class="form-control monitor-form-value">
                    <option value="events">Events</option>
                  </select>
              </td>
            </tr>
            <tr>
              <th>Default Rate</th>
              <td>
                <select name="DefaultRate" id="DefaultRate" class="form-control monitor-form-value">
                  <option value="10000"<?php if($response['DefaultRate'] === "10000") { echo " selected"; } ?>>100x</option>
                  <option value="5000"<?php if($response['DefaultRate'] === "5000") { echo " selected"; } ?>>50x</option>
                  <option value="2500"<?php if($response['DefaultRate'] === "2500") { echo " selected"; } ?>>25x</option>
                  <option value="1000"<?php if($response['DefaultRate'] === "1000") { echo " selected"; } ?>>10x</option>
                  <option value="400"<?php if($response['DefaultRate'] === "400") { echo " selected"; } ?>>4x</option>
                  <option value="200"<?php if($response['DefaultRate'] === "200") { echo " selected"; } ?>>2x</option>
                  <option value="100"<?php if($response['DefaultRate'] === "100") { echo " selected"; } ?>>Real</option>
                  <option value="50"<?php if($response['DefaultRate'] === "50") { echo " selected"; } ?>>1/2x</option>
                  <option value="25"<?php if($response['DefaultRate'] === "25") { echo " selected"; } ?>>1/4x</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>Default Scale</th>
              <td>
                <select name="DefaultScale" id="DefaultScale" class="form-control monitor-form-value">
                  <option value="400"<?php if($response['DefaultScale'] === "400") { echo " selected"; } ?>>4x</option>
                  <option value="300"<?php if($response['DefaultScale'] === "300") { echo " selected"; } ?>>3x</option>
                  <option value="200"<?php if($response['DefaultScale'] === "200") { echo " selected"; } ?>>2x</option>
                  <option value="150"<?php if($response['DefaultScale'] === "150") { echo " selected"; } ?>>1.5x</option>
                  <option value="100"<?php if($response['DefaultScale'] === "100") { echo " selected"; } ?>>Actual</option>
                  <option value="75"<?php if($response['DefaultScale'] === "75") { echo " selected"; } ?>>3/4x</option>
                  <option value="50"<?php if($response['DefaultScale'] === "50") { echo " selected"; } ?>>1/2x</option>
                  <option value="33"<?php if($response['DefaultScale'] === "33") { echo " selected"; } ?>>1/3x</option>
                  <option value="25"<?php if($response['DefaultScale'] === "25") { echo " selected"; } ?>>1/4x</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>Signal Check Colour</th>
              <td><input type="color" id="SignalCheckColour" name="SignalCheckColour" class="form-control monitor-form-value" value="<?=$response['SignalCheckColour']?>"></td>
            </tr>
            <tr>
              <th>Monitor Colour</th>
              <td><input type="color" id="WebColour" name="WebColour" class="form-control monitor-form-value" value="<?=$response['WebColour']?>"></td>
            </tr>
          </table>
          <button type="submit" class="saveall btn btn-primary">Save All</button>
        </div>
        <!-- end misc tab -->
      </form>
    </div>
<?php
  }
  else {
    die("ERROR: No camera chosen!");
  }
?>