<?php
  //
  // ZoneMinder web function library, $Date: 2008-07-08 16:06:45 +0100 (Tue, 08 Jul 2008) $, $Revision: 2484 $
  // Copyright (C) 2001-2008 Philip Coombes
  // 
  // This program is free software; you can redistribute it and/or
  // modify it under the terms of the GNU General Public License
  // as published by the Free Software Foundation; either version 2
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

function getUserDefaultPresetId($userId) {
  $query = "SELECT * FROM Users WHERE Id='{$userId}'";
  $result = dbFetchOne($query);
  if(array_key_exists('defaultPreset', $result)) {
    return $result['defaultPreset'];
  }
  else {
    return false;
  }
}

function outputlivestream($monitor,$inwidth=0,$inheight=0) {
	$scale = isset( $_REQUEST['scale'] ) ? validInt($_REQUEST['scale']) : reScale( SCALE_BASE, $monitor['DefaultScale'], ZM_WEB_DEFAULT_SCALE );
	$connkey = $monitor['connKey']; // Minor hack
  //$connKey = generateConnKey();
	if ( ZM_WEB_STREAM_METHOD == 'mpeg' && ZM_MPEG_LIVE_FORMAT ) {
		$streamMode = "mpeg";
		$streamSrc = getStreamSrc( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "bitrate=".ZM_WEB_VIDEO_BITRATE, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "format=".ZM_MPEG_LIVE_FORMAT, "buffer=".$monitor['StreamReplayBuffer'] ) );
	}
	elseif ( canStream() ) {
		$streamMode = "jpeg";
		$streamSrc = getStreamSrc( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "buffer=".$monitor['StreamReplayBuffer'] ) );
	}
	else {
		$streamMode = "single";
		$streamSrc = getStreamSrc( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale ) );
	}
	$width = !empty($inwidth) ? $inwidth : 150;
	$height = empty($inheight) ? $width * $monitor['Height'] / $monitor['Width'] : $inheight;
	$width = (int)$width;
	$height = (int)$height;
	if ( $streamMode === "mpeg" ) outputVideoStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), ZM_MPEG_LIVE_FORMAT, $monitor['Name'] );
	elseif ( $streamMode == "jpeg" ) {
		if ( canStreamNative() ) outputImageStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
		elseif ( canStreamApplet() ) outputHelperStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
	}
	else outputImageStill( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
}

function outputImageStillModern( $id, $src, $width, $height, $alt="", $class="" ) {
?>
  <img id="<?= $id ?>" class="<?= $class ?>" src="<?= $src ?>" alt="<?= $alt ?>" width="<?= $width ?>" height="<?= $height ?>">
<?php
}

function xhtmlHeaders($file, $title) {
  $basename = basename( $file, '.php' );
  $viewCssFile = getSkinFile( 'views/assets/css/'.$basename.'.css' );
  $viewCssPhpFile = getSkinFile( 'views/assets/css/'.$basename.'.css.php' );
  $viewJsFile = getSkinFile( 'views/assets/js/'.$basename.'.js' );
  $viewJsPhpFile = getSkinFile( 'views/assets/js/'.$basename.'.js.php' );

  extract( $GLOBALS, EXTR_OVERWRITE );
?>
<!DOCTYPE html>
  <!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
  <!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
  <!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
  <!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="UTF-8">
    <title><?= ZM_WEB_TITLE_PREFIX ?> - <?= validHtmlStr($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/ico" href="assets/images/favicon.ico"/>
    <link rel="shortcut icon" href="assets/images/favicon.ico"/>
    <!-- jquery -->
    <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery.min.js"></script>
    <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/css/jquery/dark/jquery-ui-1.10.3.custom.min.css" type="text/css" media="all" />
    <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery-ui-timepicker-addon.js"></script>
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/css/jquery-ui-timepicker-addon.css" type="text/css" media="screen"/>

    <!-- bootstrap -->
    <script src="skins/<?=$skin?>/views/assets/vendor/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/css/bootstrap.min.css">
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/css/bootstrap-theme.min.css">

    <!-- noty -->
    <script src="skins/<?=$skin?>/views/assets/vendor/js/noty/jquery.noty.js"></script>
    <script src="skins/<?=$skin?>/views/assets/vendor/js/noty/themes/default.js"></script>
    <script src="skins/<?=$skin?>/views/assets/vendor/js/noty/layouts/top.js"></script>
    <script src="skins/<?=$skin?>/views/assets/vendor/js/noty/layouts/topRight.js"></script>

    <!-- colorbox -->
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/js/colorbox/colorbox.css">
    <script type="text/javascript" src="skins/<?=$skin?>/views/assets/vendor/js/colorbox/jquery.colorbox-min.js"></script>

    <!-- main css -->
    <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/css/main.css">

    <?php
     if ($title == "Admin" || $title == "Events") {
    ?>
        <script type="text/javascript">
          var skinVersion = "<?=file_get_contents('skins/modern/VERSION');?>";
        </script>
        <script src="skins/<?=$skin?>/views/assets/vendor/js/chartjs/chart.min.js"></script>
        <?php
          if($title == "Events") {
        ?>
            <script src="skins/<?=$skin?>/views/assets/js/admin.js"></script>
            <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/css/admin.css">
      <?php
          }
       }
      ?>

      <?php
        if($view=="playback"||$view="events") {
      ?>
          <script src="skins/<?=$skin?>/views/assets/vendor/js/moment.min.js"></script>
      <?php
        }
        if ($view=="playback") {
      ?>
          <style type="text/css">
          <?php
            foreach(dbFetchAll("SELECT Monitors.Id, Monitors.WebColour FROM Monitors") as $monitor) {
              echo ".timeline-event.monitor" . $monitor['Id'] . " { background: " . $monitor['WebColour'] . "; }\n";
              echo "#monitor-stream-" . $monitor['Id'] . " .glyphicon-stop { color: " . $monitor['WebColour'] . "; }\n";
            }
          ?>
          </style>
          <link rel="stylesheet" href="skins/<?=$skin?>/views/assets/vendor/js/timeline/timeline.css" type="text/css" media="screen"/>
          <script src="skins/<?=$skin?>/views/assets/vendor/js/timeline/timeline-min.js"></script>
          <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery.mousewheel.js"></script>
          <script src="skins/<?=$skin?>/views/assets/vendor/js/jquery.panzoom.min.js"></script>
      <?php
       }
      ?>

      <?php
          if ($viewCssFile) {
      ?>
            <link rel="stylesheet" href="<?= $viewCssFile ?>" type="text/css" media="screen"/>
      <?php
          }
          if ($viewCssPhpFile) {
      ?>
            <style type="text/css">
              <?php
                require_once($viewCssPhpFile);
              ?>
            </style>
      <?php
          }

          if ($viewJsPhpFile) {
      ?>
            <script type="text/javascript">
              <?php
                require_once($viewJsPhpFile);
              ?>
            </script>
      <?php
          }
      ?>
      <?php
          if ($viewJsFile) {
      ?>
            <script type="text/javascript" src="<?= $viewJsFile ?>"></script>
      <?php
        }
      ?>
  </head>
<?php
  }
?>