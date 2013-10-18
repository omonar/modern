<?php
  //
  // ZoneMinder web console file, $Date: 2009-02-19 10:05:31 +0000 (Thu, 19 Feb 2009) $, $Revision: 2780 $
  // Copyright (C) 2001-2008 Philip Coombes
  //
  // This program is free software; you can redistribute it and/or
  // modify it under the terms of the GNU General Public License
  // of the License, or (at your option) any later version.
  //
  // This program is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU General Public License for more details.
  //
  // You should have received a copy of the GNU General Public License
  // along with this program; if not, write to the Free Software
  // Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
  //

  noCacheHeaders();

  $maxWidth = 0;
  $maxHeight = 0;
  $cycleCount = 0;

  $cycleWidth = $maxWidth;
  $cycleHeight = $maxHeight;

  xhtmlHeaders( __FILE__, $SLANG['Console'] );
?>
  <body class="zm">
    <!--[if lt IE 7]>
      <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->

    <?php require("header.php"); ?>

  <div class="view-wrapper"> <!-- begin view-wrapper -->

    <!-- begin zm -->
    <div id="content" class="clearfix">
      <div id="dialog" title="Tab Data">
        <fieldset class="ui-helper-reset">
          <label for="tab_title">Title:</label> <input type="text" name="tab_title" id="tab_title" value="" class="ui-widget-content ui-corner-all" />
          <br />
          <label for="selMonitors">Monitors:</label>
          <select id="selMonitors" multiple>
          <?php foreach($monitors2 as $monitor) echo "<option value=\"$monitor[Id]\">$monitor[Name]</option>"; ?>
          </select>
        </fieldset>
      </div>
      <div id="tabs">
        <ul>
          <li><a href="skins/modern/views/monitors-view.php">All</a></li>
          <li style="float:right;"><button id="refresh_monitors"  style="height:22px;">Refresh</button></li>
          <li style="float:right;"><button id="change_view"  style="height:22px;">Views</button></li>
          <li style="float:right;"><button id="add_tab"  style="height:22px;">Add Tab</button></li>
        </ul>
        <div id="all"><ul id="monitors" class="clearfix"></ul></div>
      </div>
    </div>

    <div style="display:none;"> <!-- begin noname -->
      <div id="viewcontrol" > <!-- begin viewcontrol -->
        <table> <!-- begin table -->
          <tr> <!-- begin row -->
            <th>Views</th>
            <th>Columns</th>
            <th>Monitors</th>
          </tr> <!-- end row -->

          <tr> <!-- begin row -->
            <td>
              <div>
                <div style="float:left;"><a href="javascript:loadcameraview(1)"><img id="id-console-views-img-1" alt="" src="<?=getSkinFile('graphics/view_buttons/view_1_unpressed.png')?>"></a></div>
                <div style="float:left;"><a href="javascript:loadcameraview(4)"><img id="id-console-views-img-4" alt="" src="<?=getSkinFile('graphics/view_buttons/view_4_unpressed.png')?>"></a></div>
                <div style="clear:both;"></div>
                <div style="float:left;"><a href="javascript:loadcameraview(6)"><img id="id-console-views-img-6" alt="" src="<?=getSkinFile('graphics/view_buttons/view_6_unpressed.png')?>"></a></div>
                <div style="float:left;"><a href="javascript:loadcameraview(8)"><img id="id-console-views-img-8" alt="" src="<?=getSkinFile('graphics/view_buttons/view_8_unpressed.png')?>"></a></div>
                <div style="clear:both;"></div>
                <div style="float:left;"><a href="javascript:loadcameraview(9)"><img id="id-console-views-img-9" alt="" src="<?=getSkinFile('graphics/view_buttons/view_9_unpressed.png')?>"></a></div>
                <div style="float:left;"><a href="javascript:loadcameraview(10)"><img id="id-console-views-img-10" alt="" src="<?=getSkinFile('graphics/view_buttons/view_10_unpressed.png')?>"></a></div>
                <div style="clear:both;"></div>
                <div style="float:left;"><a href="javascript:loadcameraview(13)"><img id="id-console-views-img-13" alt="" src="<?=getSkinFile('graphics/view_buttons/view_13_unpressed.png')?>"></a></div>
                <div style="float:left;"><a href="javascript:loadcameraview(16)"><img id="id-console-views-img-16" alt="" src="<?=getSkinFile('graphics/view_buttons/view_16_unpressed.png')?>"></a></div>
                <div style="clear:both;"></div>
              </div>
            </td>

            <td>
              <div>
                <div><button class="btngridsize" type="button" onclick="loadcameragrid(this,2);">2 Columns</button></div>
                <div><button class="btngridsize" type="button" onclick="loadcameragrid(this,3);" DISABLED>3 Columns</button></div>
                <div><button class="btngridsize" type="button" onclick="loadcameragrid(this,4);">4 Columns</button></div>
              </div>
            </td>

            <td>
              <div>
                <?php
                  foreach( $displayMonitors as $monitor ) {
                    echo '<div><button class="btnmonitor" type="button" onclick="loadcameramonitor(this,'.$monitor['Id'].');return false;">'.$monitor['Name'].'</button></div>';
                  }
                ?>
              </div>  
            </td>

          </tr> <!-- end row -->
        </table> <!-- end table -->

      </div> <!-- end viewcontrol -->
    </div> <!-- end noname -->
    <!-- end zm -->

  </div> <!-- end view-wrapper -->

  <div class="controls-timeline controls"> <!-- begin controls-timeline -->
    
  </div> <!-- end controls-timeline -->

  <div id="timeline" class="timeline"> <!-- begin timeline -->
    
  </div> <!-- end timeline -->

  <div class="controls-misc controls"> <!-- begin controls-misc -->
    
  </div> <!-- end controls-misc -->

<?php
  require_once("footer.php");
?>