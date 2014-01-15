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
      <form action="?view=newmonitor" method="post">
      <table class="table">
        <tr>
          <th>Name</th>
          <td><input type="text" id="Name" name="Name" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Function</th>
          <td>
              <select id="Function" name="Function" class="form-control monitor-form-value">
                <option value="None">None</option>
                <option value="Monitor">Monitor</option>
                <option value="Modect">Motion Detect</option>
                <option value="Record">Record</option>
                <option value="Mocord">Motion Record</option>
                <option value="Nodect">No Detection</option>
              </select>
          </td>
        </tr>
        <tr>
          <th>Enabled</th>
          <td><input type="checkbox" id="Enabled" name="Enabled" class="monitor-form-value"></td>
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
                foreach($response2 as $monitor) {
                  echo "<option value=\"$monitor[Id]\">$monitor[Name]</option>";
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>Maximum FPS</th>
          <td><input type="number" id="MaxFPS" name="MaxFPS" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Alarm Maximum FPS</th>
          <td><input type="number" id="AlarmMaxFPS" name="AlarmMaxFPS" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Reference Image Blend Percentage</th>
          <td><input type="number" id="RefBlendPerc" name="RefBlendPerc" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Triggers</th>
          <td>None Available</td>
        </tr>
      </table>
      <button type="submit" class="newsaveall btn btn-primary">Save All</button>
    </div>
    <!-- end general tab -->
    
    <!-- begin source tab -->
    <div class="tab-pane" id="source">
      <table class="table">
        <tr>
          <th>Source Type</th>
          <td>
              <select id="Type" name="Type" class="form-control monitor-form-value">
                <option value="Local">Local</option>
                <option value="Remote">Remote</option>
                <option value="File">File</option>
                <option value="Ffmpeg">FFMPEG</option>
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
              <option value="http">HTTP</option>
              <option value="rtsp">RTSP</option>
            </select>
          </td>
        </tr>
        <tr class="remoteSource">
          <th>Remote Method</th>
          <td>
            <select id="Method" name="Method" class="form-control monitor-form-value">
              <option value="simple">Simple</option>
              <option value="regexp">Regexp</option>
            </select>
          </td>
        </tr>
        <tr class="remoteSource">
          <th>Remote Source Name</th>
          <td><input type="text" id="Host" name="Host" class="form-control monitor-form-value" placeholder="Enter IP address"></td>
        </tr>
        <tr class="remoteSource">
          <th>Remote Host Port</th>
          <td><input type="number" id="Port" name="Port" class="form-control monitor-form-value" placeholder="Port Number"></td>
        </tr>
        <tr class="remoteSource">
          <th>Remote Host Path</th>
          <td><input type="text" id="Path" name="Path" class="form-control monitor-form-value" placeholder="Enter path"></td>
        </tr>
        <tr class="remoteSource">
          <th>Remote Image Colours</th>
          <td>
            <select id="monitorRemoteImageColours" name="monitorRemoteImageColours" class="form-control monitor-form-value">
              <option value="24">24-Bit Colour</option>
              <option value="8">8-Bit Colour</option>
            </select>
          </td>
        </tr>
        <tr class="miscSource">
          <th>Source Path</th>
          <td><input type="text" id="monitorSourcePath" name="monitorSourcePath" class="form-control monitor-form-value"></td>
        </tr>
        <tr class="miscSource">
          <th>Source Colours</th>
          <td>
            <select id="monitorSourceColours" name="monitorSourceColours" class="form-control monitor-form-value">
              <option value="24">24-Bit Colour</option>
              <option value="8">8-Bit Colour</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>Capture Width (pixels)</th>
          <td><input type="text" id="Width" name="Width" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Capture Height (pixels)</th>
          <td><input type="text" id="Height" name="Height" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Preserve Aspect Ratio</th>
          <td>Not yet implemented <input type="checkbox" id="monitorPreserveAspectRatio" name="monitorPreserveAspectRatio" class="monitor-form-value"></td>
        </tr>
        <tr>
          <th>Orientation</th>
          <td>
            <select id="monitorOrientation" name="monitorOrientation" class="form-control monitor-form-value">
              <option value="0">Normal</option>
              <option value="90">Rotate Right</option>
              <option value="180">Inverted</option>
              <option value="270">Rotate Left</option>
              <option value="hori">Flipped Horizontally</option>
              <option value="vert">Flipped Vertically</option>
            </select>
          </td>
        </tr>
      </table>
      <button type="submit" class="newsaveall btn btn-primary">Save All</button>
    </div>
    <!-- end source tab -->
    
    <!-- begin timestamp tab -->
    <div class="tab-pane" id="timestamp">
      <table class="table">
        <tr>
          <th>Timestamp Label Format</th>
          <td><input type="text" id="LabelFormat" name="LabelFormat" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Timestamp Label Position X</th>
          <td><input type="number" id="LabelX" name="LabelX" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Timestamp Label Position Y</th>
          <td><input type="number" id="LabelY" name="LabelY" class="form-control monitor-form-value"></td>
        </tr>
      </table>
      <button type="submit" class="newsaveall btn btn-primary">Save All</button>
    </div>
    <!-- end timestamp tab -->
    
    <!-- begin buffers tab -->
    <div class="tab-pane" id="buffers">
      <table class="table">
        <tr>
          <th>Image Buffer Size (frames)</th>
          <td><input type="number" id="ImageBufferCount" name="ImageBufferCount" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Warmup Frames</th>
          <td><input type="number" id="WarmupCount" name="WarmupCount" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Pre Event Image Count</th>
          <td><input type="number" id="PreEventCount" name="PreEventCount" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Post Event Image Count</th>
          <td><input type="number" id="PostEventCount" name="PostEventCount" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Stream Replay Image Buffer</th>
          <td><input type="number" id="StreamReplayBuffer" name="StreamReplayBuffer" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Alarm Frame Count</th>
          <td><input type="number" id="AlarmFrameCount" name="AlarmFrameCount" class="form-control monitor-form-value"></td>
        </tr>
      </table>
      <button type="submit" class="newsaveall btn btn-primary">Save All</button>
    </div>
    <!-- end buffers tab -->
    
    <!-- begin misc tab -->
    <div class="tab-pane" id="misc">
      <table class="table">
        <tr>
          <th>Event Prefix</th>
          <td><input type="text" id="EventPrefix" name="EventPrefix" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Section Length</th>
          <td><input type="number" id="SectionLength" name="SectionLength" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Frame Skip</th>
          <td><input type="number" id="FrameSkip" name="FrameSkip" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>FPS Report Interval</th>
          <td><input type="number" id="FPSReportInterval" name="FPSReportInterval" class="form-control monitor-form-value"></td>
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
              <option value="10000">100x</option>
              <option value="5000">50x</option>
              <option value="2500">25x</option>
              <option value="1000">10x</option>
              <option value="400">4x</option>
              <option value="200">2x</option>
              <option value="100">Real</option>
              <option value="50">1/2x</option>
              <option value="25">1/4x</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>Default Scale</th>
          <td>
            <select name="DefaultScale" id="DefaultScale" class="form-control monitor-form-value">
              <option value="400">4x</option>
              <option value="300">3x</option>
              <option value="200">2x</option>
              <option value="150">1.5x</option>
              <option value="100">Actual</option>
              <option value="75">3/4x</option>
              <option value="50">1/2x</option>
              <option value="33">1/3x</option>
              <option value="25">1/4x</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>Signal Check Colour</th>
          <td><input type="color" id="SignalCheckColour" name="SignalCheckColour" class="form-control monitor-form-value"></td>
        </tr>
        <tr>
          <th>Monitor Colour</th>
          <td><input type="color" id="WebColour" name="WebColour" class="form-control monitor-form-value"></td>
        </tr>
      </table>
      <button type="submit" class="newsaveall btn btn-primary">Save All</button>
    </div>
    <!-- end misc tab -->
  </form>
</div>