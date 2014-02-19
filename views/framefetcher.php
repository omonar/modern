<?php
  function outputImageStreamModern( $id, $src, $width, $height, $title="" ) {
  ?>
  <img id="<?= $id ?>" class="monitor-stream-image" src="<?= $src ?>" alt="<?= validHtmlStr($title) ?>" width="<?= $width ?>" height="<?= $height ?>" onerror="imgError(this);">
  <?php
  }

  function generateAuthHashModern($useRemoteAddr) {
      if ( ZM_OPT_USE_AUTH && ZM_AUTH_RELAY == "hashed" )
      {
          $time = localtime();
          if ( $useRemoteAddr )
          {
              $authKey = ZM_AUTH_HASH_SECRET.$_SESSION['username'].$_SESSION['passwordHash'].$_SESSION['remoteAddr'].$time[2].$time[3].$time[4].$time[5];
          }
          else
          {
              $authKey = ZM_AUTH_HASH_SECRET.$_SESSION['username'].$_SESSION['passwordHash'].$time[2].$time[3].$time[4].$time[5];
          }

          $auth = md5( $authKey );
      }
      else
      {
          $auth = "";
      }
      return( $auth );
  }

  function getStreamSrcModern( $args, $querySep='&amp;' ) {
    $streamSrc = ZM_BASE_URL.ZM_PATH_ZMS;

    if ( ZM_OPT_USE_AUTH )
    {
        if ( ZM_AUTH_RELAY == "hashed" )
        {
            $args[] = "auth=".generateAuthHashModern( ZM_AUTH_HASH_IPS );
        }
        elseif ( ZM_AUTH_RELAY == "plain" )
        {
            $args[] = "user=".$_SESSION['username'];
            $args[] = "pass=".$_SESSION['password'];
        }
        elseif ( ZM_AUTH_RELAY == "none" )
        {
            $args[] = "user=".$_SESSION['username'];
        }
    }
    if ( !in_array( "mode=single", $args ) && !empty($GLOBALS['connkey']) )
    {   
        $args[] = "connkey=".$GLOBALS['connkey'];
    }       
    if ( ZM_RAND_STREAM )
    {
        $args[] = "rand=".time();
    }

    if ( count($args) )
    {
        $streamSrc .= "?".join( $querySep, $args );
    }

    return( $streamSrc );
}

  function outputLiveStreamModern($monitor,$inwidth=0,$inheight=0) {
    $scale = isset( $_REQUEST['scale'] ) ? validInt($_REQUEST['scale']) : reScale( SCALE_BASE, $monitor['DefaultScale'], ZM_WEB_DEFAULT_SCALE );

    $connKey = generateAuthHashModern(false);
    //$connkey = $monitor['connKey']; // Minor hack
    if ( ZM_WEB_STREAM_METHOD == 'mpeg' && ZM_MPEG_LIVE_FORMAT ) {
      $streamMode = "mpeg";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "bitrate=".ZM_WEB_VIDEO_BITRATE, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "format=".ZM_MPEG_LIVE_FORMAT, "buffer=".$monitor['StreamReplayBuffer'] ) );
    }
    elseif ( canStream() ) {
      $streamMode = "jpeg";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "buffer=".$monitor['StreamReplayBuffer'] ) );
    }
    else {
      $streamMode = "single";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale ) );
    }

    $width = !empty($inwidth) ? $inwidth : 150;
    $height = empty($inheight) ? $width * $monitor['Height'] / $monitor['Width'] : $inheight;

    $width = (int)$width;
    $height = (int)$height;
    
    outputImageStreamModern( 'liveStream'.$monitor['Id'], $streamSrc, reScale( $width, $scale ), reScale( $height, $scale ), $monitor['Name'] );
  }

  function outputLiveStreamSrcModern($monitor,$inwidth=0,$inheight=0) {
    $scale = isset( $_REQUEST['scale'] ) ? validInt($_REQUEST['scale']) : reScale( SCALE_BASE, $monitor['DefaultScale'], ZM_WEB_DEFAULT_SCALE );

    $connKey = generateAuthHashModern(false);
    //$connkey = $monitor['connKey']; // Minor hack
    if ( ZM_WEB_STREAM_METHOD == 'mpeg' && ZM_MPEG_LIVE_FORMAT ) {
      $streamMode = "mpeg";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "bitrate=".ZM_WEB_VIDEO_BITRATE, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "format=".ZM_MPEG_LIVE_FORMAT, "buffer=".$monitor['StreamReplayBuffer'] ) );
    }
    elseif ( canStream() ) {
      $streamMode = "jpeg";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale, "maxfps=".ZM_WEB_VIDEO_MAXFPS, "buffer=".$monitor['StreamReplayBuffer'] ) );
    }
    else {
      $streamMode = "single";
      $streamSrc = getStreamSrcModern( array( "mode=".$streamMode, "monitor=".$monitor['Id'], "scale=".$scale ) );
    }

    return $streamSrc;
  }

  if(isset($_REQUEST['monitor'])) {
    echo outputLiveStreamModern($_REQUEST['monitor'], $_REQUEST['width'], $_REQUEST['height']);
    //echo "<img class=\"monitor-stream-image\" src=\"" . outputlivestream($_REQUEST['monitor'], $_REQUEST['width'], $_REQUEST['height']) . "\" onerror=\"imgError(this);\">";
  }
?>