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
//echo $monitor['Id']." $scale ".$monitor['Width'];

	//$scale = isset( $_REQUEST['scale'] ) ? validInt($_REQUEST['scale']) : (!defined(ZM_WEB_DEFAULT_SCALE) ? 40 : ZM_WEB_DEFAULT_SCALE);

	$connkey = $monitor['connKey']; // Minor hack
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

	//$height = 180;
	//$width = $height * $monitor['Width'] / $monitor['Height'];
	//$width = "100%";

	$width = (int)$width;
	$height = (int)$height;
	
	// output image
	if ( $streamMode === "mpeg" ) outputVideoStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), ZM_MPEG_LIVE_FORMAT, $monitor['Name'] );
	elseif ( $streamMode == "jpeg" ) {
		if ( canStreamNative() ) outputImageStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
		elseif ( canStreamApplet() ) outputHelperStream( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
	}
	else outputImageStill( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
}

function outputImageStillModern( $id, $src, $width, $height, $alt="", $class="" )
{
?>
<img id="<?= $id ?>" class="<?= $class ?>" src="<?= $src ?>" alt="<?= $alt ?>" width="<?= $width ?>" height="<?= $height ?>">
<?php
}

function xhtmlHeaders( $file, $title )
{
    $skinCssFile = getSkinFile( 'css/skin.css' );
    $skinCssPhpFile = getSkinFile( 'css/skin.css.php' );
    $skinJsFile = getSkinFile( 'js/skin.js' );
    $skinJsPhpFile = getSkinFile( 'js/skin.js.php' );

    $basename = basename( $file, '.php' );
    $viewCssFile = getSkinFile( 'views/css/'.$basename.'.css' );
    $viewCssPhpFile = getSkinFile( 'views/css/'.$basename.'.css.php' );
    $viewJsFile = getSkinFile( 'views/js/'.$basename.'.js' );
    $viewJsPhpFile = getSkinFile( 'views/js/'.$basename.'.js.php' );

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
 <link rel="icon" type="image/ico" href="graphics/favicon.ico"/>
 <link rel="shortcut icon" href="graphics/favicon.ico"/>
 <link rel="stylesheet" href="css/reset.css" type="text/css"/>
 <link rel="stylesheet" href="<?= $skinCssFile ?>" type="text/css" media="screen"/>
 <link rel="stylesheet" href="skins/modern/css/header.css" type="text/css" media="screen"/>
<?php if($title != 'Zone' && !preg_match("/Feed/", $title)) { ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="skins/<?=$skin?>/css/jquery/dark/jquery-ui-1.10.3.custom.min.css" type="text/css" media="all" />
 <script src="skins/<?=$skin?>/js/bootstrap.min.js"></script>
 <link rel="stylesheet" href="skins/<?=$skin?>/css/bootstrap.min.css">
 <link rel="stylesheet" href="skins/<?=$skin?>/css/bootstrap-theme.min.css">
 <link rel="stylesheet" href="skins/<?=$skin?>/css/main.css">
<?php } ?>
 <link type="text/css" media="screen" rel="stylesheet" href="skins/modern/css/colorbox.css">
 <!--<link type="text/css" media="screen" rel="stylesheet" href="skins/modern/css/jquery/jquery-ui-1.8.custom.css">-->
<?php if ($title == "Console") { ?>
 <script type="text/javascript" src="skins/modern/js/jquery.colorbox.js"></script>
 <!--<script type="text/javascript" src="skins/modern/js/console.colorbox.js"></script>-->
 <script type="text/javascript" src="skins/modern/js/console.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>
<?php } ?>
<?php
 if ($title == "Monitor") {
?>
<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>
<script type="text/javascript" src="js/mootools.ext.js"></script>
<?php
 }
?>


<?php
 if ($title == "System Log") {
?>
<script type="text/javascript">var $j = jQuery.noConflict();</script>
<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>
  <script type="text/javascript" src="js/mootools.ext.js"></script>
<?php
 }
?>




<?php
 if ($title == "Zone") { ?>
<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>
  <script type="text/javascript" src="js/mootools.ext.js"></script> 
<?php
 }
?>



<?php if (preg_match("/Feed/", $title)) { ?>
  <link media="screen" type="text/css" href="skins/classic/views/css/watch.css" rel="stylesheet">
<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>
<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>
<?php
 }
?>
<?php
 if ($title == "Admin") {
?>
  <script type="text/javascript" src="skins/modern/js/admin.js"></script>
  <script type="text/javascript" src="skins/modern/js/jquery.colorbox.js"></script>
<?php
 }
?>
<?php
 if ($title == "Events") {
?>
<script type="text/javascript" src="skins/modern/js/jquery.colorbox.js"></script>
<script type="text/javascript" src="skins/modern/js/events.js"></script>
<script type="text/javascript" src="skins/modern/js/events_search.js"></script>

<!--[if IE]><script type="text/javascript" src="skins/modern/js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="skins/modern/js/jquery.flot.min.js"></script>
<script type="text/javascript" src="skins/modern/js/jquery.flot.selection.min.js"></script>
<script type="text/javascript" src="skins/modern/js/jquery.tooltip.pack.js"></script>
<script type="text/javascript" src="skins/modern/js/dateFormat.js"></script>

<?php
 }
?>
<?php
 if ($title == "Full") {
?>
<script type="text/javascript" src="skins/modern/js/full.js"></script>
<?php
 }
?>
<?php
 if ($title == "Event") {
?>
<script type="text/javascript" src="skins/modern/js/event.js"></script>
<script type="text/javascript" src="skins/modern/js/preloadImage.js"></script>
<?php
 }
?>
<?php
 if ($view=="playback") {
?>
<style type="text/css">
<?php
  foreach(dbFetchAll("SELECT Monitors.Id, Monitors.WebColour FROM Monitors") as $monitor) {
    echo ".timeline-event.monitor" . $monitor['Id'] . " { background-color: " . $monitor['WebColour'] . "; }\n";
    echo "#monitor-stream-" . $monitor['Id'] . " .glyphicon-stop { color: " . $monitor['WebColour'] . "; }\n";
  }
?>
</style>
<script src="skins/<?=$skin?>/views/js/datetimepicker/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="skins/<?=$skin?>/views/js/datetimepicker/jquery-ui-timepicker-addon.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="skins/<?=$skin?>/views/js/timeline/timeline.css" type="text/css" media="screen"/>
<script src="skins/<?=$skin?>/views/js/noty/jquery.noty.js"></script>
<script src="skins/<?=$skin?>/views/js/noty/themes/default.js"></script>
<script src="skins/<?=$skin?>/views/js/noty/layouts/top.js"></script>
<script src="skins/<?=$skin?>/views/js/noty/layouts/topRight.js"></script>
<script src="skins/<?=$skin?>/views/js/timeline/timeline-min.js"></script>
<script src="skins/<?=$skin?>/views/js/moment.min.js"></script>
<?php
 }
?>

<?php
    if ( $viewCssFile )
    {
?>
  <link rel="stylesheet" href="<?= $viewCssFile ?>" type="text/css" media="screen"/>
<?php
    }
    if ( $viewCssPhpFile )
    {
?>
  <style type="text/css">
<?php
        require_once( $viewCssPhpFile );
?>
  </style>
<?php
    }
?>
<?php
    if ( $skinJsPhpFile )
    {
?>
  <script type="text/javascript">
<?php
    require_once( $skinJsPhpFile );
?>
  </script>
<?php
    }
    if ( $viewJsPhpFile )
    {
?>
  <script type="text/javascript">
<?php
        require_once( $viewJsPhpFile );
?>
  </script>
<?php
    }
?>
  <script type="text/javascript" src="<?= $skinJsFile ?>"></script>
<?php
    if ( $viewJsFile )
    {
?>
  <script type="text/javascript" src="<?= $viewJsFile ?>"></script>
<?php
    }
?>
</head>
<?php
}
?>
