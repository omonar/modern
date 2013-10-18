var $m = document.id; //mootools no conflict;
var thisUrl = "<?= ZM_BASE_URL.$_SERVER['PHP_SELF'] ?>";
var AJAX_TIMEOUT = <?= ZM_WEB_AJAX_TIMEOUT ?>;
var STATE_IDLE = <?= STATE_IDLE ?>;
var STATE_PREALARM = <?= STATE_PREALARM ?>;
var STATE_ALARM = <?= STATE_ALARM ?>;
var STATE_ALERT = <?= STATE_ALERT ?>;
var STATE_TAPE = <?= STATE_TAPE ?>;
var CMD_QUERY = <?= CMD_QUERY ?>;
var SOUND_ON_ALARM = <?= ZM_WEB_SOUND_ON_ALARM ?>;
var POPUP_ON_ALARM = <?= ZM_WEB_POPUP_ON_ALARM ?>;

var statusRefreshTimeout = <?= 1000*ZM_WEB_REFRESH_STATUS ?>;
var requestQueue = new Request.Queue( { concurrent: 2 } );

function Monitor( index, id, connKey ) {
  this.index = index;
  this.id = id;
  this.connKey = connKey;
  this.status = null;
  this.alarmState = STATE_IDLE;
  this.lastAlarmState = STATE_IDLE;
  this.streamCmdParms = "view=request&request=stream&connkey="+this.connKey;
  this.streamCmdTimer = null;


  this.start = function( delay ) {
    this.streamCmdTimer = this.streamCmdQuery.delay( delay, this );

  }

  this.setStateClass = function( element, stateClass ) {
    if ( !element.hasClass( stateClass ) ) {
      if ( stateClass != 'alarm' ) element.removeClass( 'alarm' );
      if ( stateClass != 'alert' ) element.removeClass( 'alert' );
      if ( stateClass != 'idle' ) element.removeClass( 'idle' );
      element.addClass( stateClass );
    }
  }


  this.getStreamCmdResponse = function( respObj, respText ) {
    if ( this.streamCmdTimer ) this.streamCmdTimer = $clear( this.streamCmdTimer );

    if ( respObj.result == 'Ok' ) {
      this.status = respObj.status;
      this.alarmState = this.status.state;

      var stateClass = "";
      if ( this.alarmState == STATE_ALARM ) stateClass = "alarm";
      else if ( this.alarmState == STATE_ALERT ) stateClass = "alert";
      else stateClass = "idle";

      this.setStateClass( $m('monitor'+this.index), stateClass );

      //Stream could be an applet so can't use moo tools
      var stream = document.getElementById( "liveStream"+this.id );
          stream.className = stateClass;

      var isAlarmed = ( this.alarmState == STATE_ALARM || this.alarmState == STATE_ALERT );
      var wasAlarmed = ( this.lastAlarmState == STATE_ALARM || this.lastAlarmState == STATE_ALERT );

      var newAlarm = ( isAlarmed && !wasAlarmed );
      var oldAlarm = ( !isAlarmed && wasAlarmed );

      if ( newAlarm ) {
        if ( false && SOUND_ON_ALARM ) {
          // Enable the alarm sound
          $m('alarmSound').removeClass( 'hidden' );
        }
        if ( POPUP_ON_ALARM ) { windowToFront(); }
      }
      if ( false && SOUND_ON_ALARM ) {
        if ( oldAlarm ) {
          // Disable alarm sound
          $m('alarmSound').addClass( 'hidden' );
        }
      }
    }
    else {
      console.error( respObj.message );
    }
    var streamCmdTimeout = statusRefreshTimeout;
    if ( this.alarmState == STATE_ALARM || this.alarmState == STATE_ALERT ) streamCmdTimeout = streamCmdTimeout/5;
    this.streamCmdTimer = this.streamCmdQuery.delay( streamCmdTimeout, this );
    this.lastAlarmState = this.alarmState;
  }

  this.streamCmdQuery = function( resent ) {
    //if ( resent )
        //console.log( this.connKey+": Resending" );
    //this.streamCmdReq.cancel();
    this.streamCmdReq.send( this.streamCmdParms+"&command="+CMD_QUERY );
  }

  this.streamCmdReq = new Request.JSON( { url: thisUrl, method: 'post', timeout: AJAX_TIMEOUT, onSuccess: this.getStreamCmdResponse.bind( this ), onTimeout: this.streamCmdQuery.bind( this, true ), link: 'cancel' } );

  requestQueue.addRequest( "cmdReq"+this.id, this.streamCmdReq );

}