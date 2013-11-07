<?php
	function outputImageStreamModern( $id, $src, $width, $height, $title="" ) {
	?>
	<img id="<?= $id ?>" class="monitor-stream-image" src="<?= $src ?>" alt="<?= validHtmlStr($title) ?>" width="<?= $width ?>" height="<?= $height ?>" onerror="imgError(this);">
	<?php
	}

	function outputLiveStreamModern($monitor,$inwidth=0,$inheight=0) {
	  $scale = isset( $_REQUEST['scale'] ) ? validInt($_REQUEST['scale']) : reScale( SCALE_BASE, $monitor['DefaultScale'], ZM_WEB_DEFAULT_SCALE );

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

	  $width = (int)$width;
	  $height = (int)$height;
	  
	  outputImageStreamModern( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
	}

	if(isset($_REQUEST['monitor'])) {
		echo outputLiveStreamModern($_REQUEST['monitor'], $_REQUEST['width'], $_REQUEST['height']);
		//echo "<img class=\"monitor-stream-image\" src=\"" . outputlivestream($_REQUEST['monitor'], $_REQUEST['width'], $_REQUEST['height']) . "\" onerror=\"imgError(this);\">";
	}
?>