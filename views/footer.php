<div  style="margin:0;padding:0;position:fixed;bottom:0;width:100%;">
  <div id="footer">
    <div class="left">
      <a href="?view=state" class="colorbox" style="color:#036;"><?=$status?></a> <span>-</span> 
      <?= makePopupLink( '?view=version', 'zmVersion', 'version', "v".ZM_VERSION, canEdit( 'System' ) ) ?>
    </div>
    <div class="right">
      <?php   if ( ZM_OPT_USE_AUTH ){ ?>
      <?= $SLANG['LoggedInAs'] ?> <a href="?view=logout" class="colorbox" style="color:#036;" ><?=$user['Username']?></a>, <?= strtolower( $SLANG['ConfiguredFor'] ) ?>
      <?php   }else echo $SLANG['ConfiguredFor'] ?>
      <?= makePopupLink( '?view=bandwidth', 'zmBandwidth', 'bandwidth', $bwArray[$_COOKIE['zmBandwidth']], ($user && $user['MaxBandwidth'] != 'low' ) ) ?> <?= $SLANG['Bandwidth'] ?>
    </div>
  </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="skins/<?=$skin?>/assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script src="skins/<?=$skin?>/assets/js/vendor/bootstrap.min.js"></script>

<?php
  if($title == "Live") {
?>
    <!--<script type="text/javascript" src="skins/<?=$skin?>/assets/js/jquery.colorbox.js"></script>-->
    <!--<script type="text/javascript" src="skins/<?=$skin?>/assets/js/live.colorbox.js"></script>-->
    <script type="text/javascript" src="skins/<?=$skin?>/assets/js/live.js"></script>
    <!--<script type="text/javascript" src="tools/mootools/mootools-core-1.3.2-nc.js"></script>-->
    <!--<script type="text/javascript" src="tools/mootools/mootools-more-1.3.2.1-nc.js"></script>-->
<?php
  }
?>

<script src="skins/<?=$skin?>/assets/js/main.js"></script>

<!-- <?= var_dump($title); ?> -->

</body>
</html>

