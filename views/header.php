<?php

$running = daemonCheck();
$status = $running?$SLANG['Running']:$SLANG['Stopped'];


if ( $group = dbFetchOne( "select * from Groups where Id = '".(empty($_COOKIE['zmGroup'])?0:dbEscape($_COOKIE['zmGroup']))."'" ) )
    $groupIds = array_flip(split( ',', $group['MonitorIds'] ));

$monitors = dbFetchAll( "select * from Monitors order by Sequence asc" );

$displayMonitors = array();
for ( $i = 0; $i < count($monitors); $i++ )
{
    if ( !visibleMonitor( $monitors[$i]['Id'] ) )
    {
        continue;
    }
    if ( $group && !empty($groupIds) && !array_key_exists( $monitors[$i]['Id'], $groupIds ) )
    {
        continue;
    }
    $monitors[$i]['zmc'] = zmcStatus( $monitors[$i] );
    $monitors[$i]['zma'] = zmaStatus( $monitors[$i] );
    $monitors[$i]['ZoneCount'] = dbFetchOne( "select count(Id) as ZoneCount from Zones where MonitorId = '".$monitors[$i]['Id']."'", "ZoneCount" );
    $displayMonitors[] = $monitors[$i];
}

if(isset($_REQUEST['view'])) {
    $_view = $_REQUEST['view'];
}
if(empty($_view)) $_view = 'console';

?>
<!-- inline styling stops mootools taking over - yes, inline-styles are bad -->
<!--<div class="btn-group" style="display: inline-block !important;">
  <button type="button" class="btn dropdown-toggle mainnav-btn btn-fixedpos ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" data-toggle="dropdown">
    Menu <span class="ui-button-text"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="?view=console">Live View</a></li>
    <li><a href="?view=playback">Playback</a></li>
    <li><a href="?view=admin">Admin</a></li>
  </ul>
</div>-->