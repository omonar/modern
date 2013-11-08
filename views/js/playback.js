//
// Copyright (C) 2013 Kevin Varley at Emergent Ltd
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

var start = new Date();
var end = new Date();
start.setHours(0,0,0,0);
var activity, cameras;
var frames;
var chosencameras = new Array();
var timelinedata = [];
var playing = false;
var timers = new Array;
var ajaxRequests = new Array;
var currentlyplaying = [];
var timeline;
var data;
var haschosencameras = false;
var liveview = true;
var shouldbeplaying = false;
var timerstimers = new Array();
var currenteventarrays = new Array();
var paused = false;
var breakplayback = false;
var times = "";
var gaplessPlayback = true;
var options = {
  'width':  '100%',
  'height': '170px',
  'editable': false,
  'showCustomTime': false,
  'showCurrentTime': false,
  'style': 'box'
};
jQuery.noty.defaults = {
  layout: 'topRight',
  theme: 'defaultTheme',
  type: 'alert',
  text: '',
  dismissQueue: true, // If you want to use queue feature set this true
  template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
  animation: {
      open: {height: 'toggle'},
      close: {height: 'toggle'},
      easing: 'swing',
      speed: 500 // opening & closing animation speed
  },
  timeout: 2000, // delay for closing event. Set false for sticky notifications
  force: false, // adds notification to the beginning of queue when set to true
  modal: false,
  maxVisible: 5, // you can set max visible notification for dismissQueue true option
  closeWith: ['click'], // ['click', 'button', 'hover']
  callback: {
      onShow: function() {},
      afterShow: function() {},
      onClose: function() {},
      afterClose: function() {}
  },
  buttons: false // an array of buttons
};

/* begin third party code */
/* http://www.unseenrevolution.com/jquery-ajax-error-handling-function/ */
$(function() {
  $.ajaxSetup({
    error: function(jqXHR, exception) {
      if (jqXHR.status === 0) {
        //noty({text: 'Network error: Check internet connection', type: 'error'});
        console.log('Not connect. Verify Network.');
      } else if (jqXHR.status == 404) {
        noty({text: 'Network error: 404', type: 'error'});
        console.log('Requested page not found. [404]');
      } else if (jqXHR.status == 500) {
        noty({text: 'Network error: 500', type: 'error'});
        console.log('Internal Server Error [500].');
      } else if (exception === 'parsererror') {
        noty({text: 'Parse error', type: 'error'});
        console.log('Requested JSON parse failed.');
      } else if (exception === 'timeout') {
        noty({text: 'Network error: Timeout', type: 'error'});
        console.log('Time out error.');
      } else if (exception === 'abort') {
        noty({text: 'Network error: Request aborted', type: 'error'});
        console.log('Ajax request aborted.');
      } else {
        noty({text: 'Network error: Uncaught error', type: 'error'});
        console.log('Uncaught Error.\n' + jqXHR.responseText);
      }
    }
  });
});
/* end third party code */

function addMonitor(monitorId, showall) {
  if(arguments.length === 1) {
    showall = false;
  }
  window["currentevents" + monitorId] = new Array();
  currenteventarrays.push("currentevents" + monitorId);
  if(showall === false) {
    noty({text: 'Adding camera', type: 'info'});
  }
  if(jQuery.inArray(monitorId, chosencameras) === -1) {
    var ajaxRequestId = ajaxRequests[ajaxRequests.length];
    ajaxRequests[ajaxRequestId] = jQuery.ajax({
      type: "POST",
      url: 'index.php?view=framefetcher',
      data: {monitor: monitorId, width: cameras[monitorId-1].Width, height: cameras[monitorId-1].Height, scale: 100},
      success: function(data) {
        chosencameras.push(monitorId);
        //console.log("Pushed " + monitorId + " / " + cameras[monitorId-1].Name + " to chosencameras");
        if(liveview === true) {
          jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-right unit one-of-three\"><button class=\"monitor-stream-info-colour\" title=\"The colour assigned to this camera\"><span class=\"glyphicon glyphicon-stop\"></span></button><button class=\"monitor-stream-info-close\"><span class=\"glyphicon glyphicon-remove\"></span></button></p>' + data + '</div>').appendTo('#monitor-streams');
        }
        else {
          jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-right unit one-of-three\"><button class=\"monitor-stream-info-colour\" title=\"The colour assigned to this camera\"><span class=\"glyphicon glyphicon-stop\"></span></button><button class=\"monitor-stream-info-close\"><span class=\"glyphicon glyphicon-remove\"></span></button></p><img id="liveStream' + cameras[monitorId-1].Id + '" class="monitor-stream-image" src="/zm/skins/modern/views/images/onerror.png" alt="' + cameras[monitorId-1].Id + '" width="' + cameras[monitorId-1].Width + '" height="' + cameras[monitorId-1].Height + '" onerror="imgError(this);"></div>').appendTo('#monitor-streams');
        }
        if((haschosencameras === true)&&(showall === false)) {
          requeryTimeline();
        }
        if((haschosencameras === true)&&(showall === false)) {
          requeryTimeline();
        }
        if((showall === true)&&(monitorId==cameras[cameras.length-1].Id)&&(liveview===false)) {
          requeryTimeline();
        }
        ajaxRequests.splice(ajaxRequestId, 1);
      }
    });
  }
  console.log("Added monitorId = " + monitorId);
}

function eventInstance(monitorId, eventId, currentFrame) {
  this.monitorId=monitorId;
  this.eventId=eventId;
  this.currentFrame=currentFrame;
}

function clearTimers() {
  for (var timer in timers) {
    console.log("Cleared a timer...");
    clearInterval(timer);
  }
  currentlyplaying = null;
  timers = null;
}

function stopPlayback() {
  shouldbeplaying = false;
  playing = false;
  timeline.options.showCustomTime = false;
  timeline.options.showCurrentTime = false;
  timeline.draw(null, options);
  $("#pause").html("<span class=\"glyphicon glyphicon-play\"></span>");
  $("#pause").attr("id", "play");
}

function clearCameraFrames() {
  jQuery("#monitor-streams img").attr("src", '/zm/skins/modern/views/images/onerror.png');
}

function pausePlayback() {
  shouldbeplaying = false;
  playing = false;
  paused = true;
  timeline.options.showCustomTime = true;
  timeline.repaintCustomTime();
  timeline.setCustomTime(timeline.getCurrentTime());
  timeline.options.showCurrentTime = false;
  timeline.repaintCurrentTime();
}

function resumePlayback() {
  shouldbeplaying = true;
  playing = true;
  paused = false;
  timeline.setCurrentTime(timeline.getCustomTime());
  timeline.options.showCurrentTime = true;
  timeline.repaintCurrentTime();
  timeline.setCustomTime(new Date().getTime());
  timeline.options.showCustomTime = false;
  timeline.repaintCustomTime();
}

/* begin third party code */
// Andy E
// http://stackoverflow.com/users/94197/andy-e
// http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
Date.createFromMysql = function(mysql_string) {
  if(typeof mysql_string === 'string') {
    var t = mysql_string.split(/[- :]/);
    //when t[3], t[4] and t[5] are missing they defaults to zero
    return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
  }
  return null;
}
/* end third party code */

jQuery.fn.exists = function(){
  if(this.length>0) {
    return true;
  }
  else {
    return false;
  }
}

function imgError(image) {
  image.onerror = "";
  image.src = "skins/modern/views/images/onerror.png";
  return true;
}
function setTime(element, refresh, formatting) {
  setInterval(function(){
    jQuery(element).text(moment().format(formatting));
  },refresh);
}

function processTimelineData(rawdata) {
  jQuery.each(activity, function(index) {
    timelinedata.push({start: Date.createFromMysql(activity[index].StartTime), end: Date.createFromMysql(activity[index].EndTime), content: activity[index].Id, className: "monitor"+activity[index].MonitorId});
  });
}

function loadUserDefaultPreset() {
  if(jQuery("#preset-selection input[name=defaultpreset]:checked").val()!=="-1") {
    var presetMonitorIds = jQuery("#preset-selection input[name=defaultpreset]:checked").parent().find(".preset-list-link").attr("data-value");
    var presetMonitorIds = presetMonitorIds.split(",");
    jQuery.each(presetMonitorIds, function(index, value) {
      addMonitor(value, true);
    });
    noty({text: 'Added cameras', type: 'success'});
  }
  else {
    jQuery(cameras).each(function(i, v) {
      addMonitor(v.Id, true);
    });
    noty({text: 'Added cameras', type: 'success'});
  }
}

function preloadFrames(imgarray) {
  jQuery.each(imgarray, function(i, source) {
    jQuery.get(source);
  });
}

function displayFrame(monitorId, img) {
    jQuery("#monitor-stream-"+monitorId+" .monitor-stream-image").attr('src', img);
}

function clearTimer(eventId, monitorId) {
  clearInterval(timers[eventId]);
  timers[eventId] = 0;
}

function requeryTimeline() {
  console.log("Requerying...");
  if(chosencameras.length > 0) {
    jQuery("#timeline").css("background-color","red");
    var startformatted = moment(start).format('YYYY-MM-DD HH:mm') + ':00';
    var endformatted = moment(end).format('YYYY-MM-DD HH:mm') + ':00';
    ajaxRequestId = ajaxRequests.length;
    ajaxRequests[ajaxRequestId] = jQuery.ajax({
      type: "POST",
      url: 'index.php?view=onefiletorulethemall',
      data: {timeline: 'ok', cameras: chosencameras.join(","), start: startformatted, end: endformatted},
      success: function(data) {
        timelinedata = [];
        activity = [];
        activity = JSON.parse(data);
        processTimelineData();
        timeline.draw(timelinedata, options);
        timeline.applyRange(start, end);
        timeline.options.showCurrentTime = true;
        timeline.redraw();
        getFrames();
        jQuery("#timeline").css("background-color","");
        noty({text: 'Timeline refreshed', type: 'success'});
        ajaxRequests.splice(ajaxRequestId, 1);
      }
    });
  }
  else {
    timeline.draw(null, options);
    noty({text: 'No cameras selected', type: 'info'});
  }
}

function playbackFrames(monitorId, eventId, imgarray) {
  var x = 0;
  timers[eventId] = setInterval(function(){
    // prevent stuttering
    if(window["currentevents" + monitorId].length > 1) {
      //console.log("Current events > 1 /// " + window["currentevents" + monitorId]);
      //get the item before this item in the array
      var previousEventIndex = window["currentevents" + monitorId].length-2;
      var previousEventId = window["currentevents" + monitorId][previousEventIndex];
      //console.log("Removing event /// " + previousEventIndex);
      window["currentevents" + monitorId].splice(previousEventIndex, 1);
      clearTimer(previousEventId, monitorId);
    }
    // if an event should be being played
    if (shouldbeplaying === true) {
      // if there are still frames to play
      if (x < imgarray.length) {
        playing = true;
        displayFrame(monitorId, imgarray[x]);
      }
      // if there are no more frames to play
      else {
        clearTimer(eventId, monitorId);
        displayFrame(monitorId, '/zm/skins/modern/views/images/onerror.png');
        window["currentevents" + monitorId].splice(window["currentevents" + monitorId].indexOf(eventId), 1);
        // if gaplessPlayback enabled & the event finishes tidily
        if(gaplessPlayback === true) {
          jumpToNearestEvent(timeline.getCurrentTime());
        }
      }
      x++;
    }
    // if an event shouldn't be playing
    else {
      // if an event has come to an end neatly
      if(paused === false) {
        clearTimer(eventId, monitorId);
        displayFrame(monitorId, '/zm/skins/modern/views/images/onerror.png');
      }
      // if an event has come to an end tidily we should no longer be playing
      if(window["currentevents" + monitorId].length === 1) {
        playing = false;
        shouldbeplaying = false;
      }
      // remove the event from the relevant array 
      window["currentevents" + monitorId].splice(window["currentevents" + monitorId].indexOf(eventId), 1);
    }
  },200);
}

function playEvent(monitorId, eventId) {
  /*This shouldn't be needed, but leaving it here for a few commits in case I haven't fixed the problem
  console.log("monitorId="+monitorId+" & typeof(monitorId)="+typeof(monitorId));
  console.log("chosencameras[0]=" + chosencameras[0] + " & typeof(chosencameras[0])=" + typeof(chosencameras[0]));
  if(typeof(monitorId)!="number") {
    monitorId = parseInt(monitorId);
  }*/
  if(jQuery.inArray(monitorId, chosencameras) === -1) {
    addMonitor(monitorId);
  }
  window["currentevents" + monitorId].push(eventId);
  console.log("playing event: " + eventId + " on monitor " + monitorId);
  liveview = false;
  shouldbeplaying = true;
  var tempframes = new Array();
  if(frames[monitorId][eventId]) {
    jQuery.each(frames[monitorId][eventId], function(i, v) {
      tempframes.push(v);
    });
  }
  playbackFrames(monitorId, eventId, tempframes);
}

function getFrames() {
  jQuery.each(activity, function(i, v) {
    if(i!==0) {
      times += ",";
    }
      times += moment(v.StartTime).format("YYYY-MM-DD HH:mm:ss");
  });
  var ajaxRequestId = ajaxRequests.length;
  ajaxRequests[ajaxRequestId] = jQuery.ajax({
    type: "POST",
    url: 'index.php?view=onefiletorulethemall',
    data: {q: times},
    success: function(data) {
      frames = jQuery.parseJSON(data);
      ajaxRequests.splice(ajaxRequestId, 1);
    }
  });
}

function jumpToNearestEvent(datetime, direction) {
  direction = (typeof direction === "undefined") ? "forward" : direction;
  var matchFound = false;
  jQuery.each(activity, function(i, v) {
    if(matchFound === false) {
      if (direction === "backward") {
        if(Date.createFromMysql(v.StartTime) <= datetime) {
          matchFound = true;
          breakplayback = true;
          timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
        }
      }
      else {
        if(Date.createFromMysql(v.StartTime) >= datetime) {
          matchFound = true;
          breakplayback = true;
          timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
        }
      }
    }
  });
}

function clearAjaxRequests() {
  ajaxRequests = [];
}

function setupTimeline() {
  timeline = new links.Timeline(document.getElementById('timeline'));

  timeline.applyRange(start, end);
  timeline.setVisibleChartRange(start, end, true);

  function onselect() {
    if(liveview === false) {
      var sel = timeline.getSelection();

      timeline.setSelection(null);

      if (sel.length) {
        if(sel[0].row != undefined) {
          var itemobj = timeline.getItem(sel[0].row);
          $("#play").html("<span class\"glyphicon glyphicon-pause\"></span>");
          $("#play").attr("id", "pause");
          timeline.options.showCurrentTime = true;
          timeline.options.showCustomTime = false;
          timeline.setCurrentTime(itemobj.start);
          timeline.repaintCurrentTime();
        }
      }
    }
  }
  links.events.addListener(timeline, 'select', onselect);
  timeline.draw(null, options);
}

function toggleShowAllButton(override) {
  if(arguments.length === 0) {
    override = false;
  }
  if(override === false) {
    if(jQuery("button.show-all-cameras").exists()) {
      if(chosencameras.length === cameras.length) {
        $("button.show-all-cameras").replaceWith("<button class=\"hide-all-cameras\"><span class=\"glyphicon glyphicon-eye-close\"></span></button>");
      }
    }
    else {
      if(chosencameras.length < cameras.length) {
        $("button.hide-all-cameras").replaceWith("<button class=\"show-all-cameras\"><span class=\"glyphicon glyphicon-eye-open\"></span></button>");
      }
    }
  }
  else {
    if(jQuery("span.glyphicon.glyphicon-eye-close").exists()) {
      $("button.hide-all-cameras").replaceWith("<button class=\"show-all-cameras\"><span class=\"glyphicon glyphicon-eye-open\"></span></button>");
    }
    else{
      $("button.show-all-cameras").replaceWith("<button class=\"hide-all-cameras\"><span class=\"glyphicon glyphicon-eye-close\"></span></button>");
    }
  }
}

function toggleMode() {
  if(liveview === true) {
    liveview = false;
    $("#playback").tooltip('destroy');
    $("#playback").html("<span class=\"glyphicon glyphicon-record\"></span>");
    $("#playback").attr("title", "Enter Live View Mode");
    $("#playback").attr("id", "liveview");
    $("#liveview").tooltip();

    if($("#choose-cameras").dialog("isOpen")===true) {
      $("#choose-cameras").dialog("close");
    }
    // if there have been cameras selected
    if(jQuery.trim(jQuery("#monitor-streams").html()).length) {
      jQuery(".monitor-stream-image").each(function() {
        $(this).attr("data-livesrc", $(this).attr("src"));
        $(this).attr("src", "/zm/skins/modern/views/images/onerror.png");
      });
    }
    requeryTimeline();
  }
  else {
    liveview = true;
    $("#liveview").tooltip('destroy');
    $("#liveview").html("<span class=\"glyphicon glyphicon-film\"></span>");
    $("#liveview").attr("title", "Enter Playback Mode")
    $("#liveview").attr("id", "playback");
    $("#playback").tooltip();
    stopPlayback();
    // if there have been cameras selected
    window.setTimeout(function() {
      if(jQuery.trim(jQuery("#monitor-streams").html()).length) {
        jQuery(".monitor-stream-image").each(function() {
          $(this).attr("src", $(this).attr("data-livesrc").split('&rand')[0] + "&rand=" + new Date().getTime());
          $(this).removeAttr("data-livesrc");
        });
      }
    }, 1000);
  }
}

jQuery(document).ready(function() { /* begin document ready */
  $("#playback").tooltip();
  $("#settings").tooltip();
  $("#play").tooltip();
  $("#export").tooltip();
  $("#choose-cameras-opener").tooltip();
  $("#preset-selection-opener").tooltip();
  setupTimeline();

  $("#choose-cameras").dialog({
    autoOpen: false,
    resizable: true,
    width: 'auto'
  });

  $("#preset-selection").dialog({
    autoOpen: false,
    resizable: true,
    minWidth: 400
  });

  $("#set-default-preset").dialog({
    autoOpen: false,
    resizable: true,
    width: 'auto'
  })

  $("<button class=\"show-all-cameras\"><span class=\"glyphicon glyphicon-eye-open\"></span></button>").appendTo($("#ui-id-1").parent());
  $(".ui-dialog-titlebar-close").html("<span class=\"glyphicon glyphicon-remove\"></span>");

  $('#rangestart').datetimepicker({
    dateFormat: "dd/mm/yy",
    onClose: function(dateText, inst) {
      setTimeout(function() {
        if( moment($("#rangestart").val(), 'D/M/YYYY h:mm') < moment($("#rangeend").val(), 'D/M/YYYY h:mm')) {
          if((moment(start).format('DD/MM/YYYY HH:mm') !== $("#rangestart").val())||(moment(end).format('DD/MM/YYYY HH:mm') !== $("#rangeend").val())) {
            start = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
            end = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate();
            requeryTimeline();
          }
        }
        else {
          var temprangestart = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate();
          temprangestart.setDate(temprangestart.getDate() - 1);
          $("#rangestart").val(moment(temprangestart).format('D/M/YYYY h:mm'));
          noty({text: 'Range start cannot be after range end!', type: 'error'});
        }
      }, 200);
    }
  });

  $('#rangeend').datetimepicker({
    dateFormat: "dd/mm/yy",
    onClose: function(dateText, inst) {
      setTimeout(function() {
        if( moment($("#rangeend").val(), 'D/M/YYYY h:mm') > moment($("#rangestart").val(), 'D/M/YYYY h:mm')) {
          if((moment(start).format('DD/MM/YYYY HH:mm') !== $("#rangestart").val())||(moment(end).format('DD/MM/YYYY HH:mm') !== $("#rangeend").val())) {
            start = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
            end = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate();
            requeryTimeline();
          }
        }
        else {
          if( moment($("#rangeend").val(), 'D/M/YYYY h:mm').isSame($("#rangestart").val())) {
            var temprangeend = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
            temprangeend.setDate(temprangeend.getDate() + 1);
            $("#rangeend").val(moment(temprangeend).format('D/M/YYYY h:mm'));
            noty({text: 'Range end cannot be before range start!', type: 'error'});
          }
        }
      }, 200);
    }
  });

  $('#rangestart').val(moment(start).format('DD/MM/YYYY HH:mm'));
  $('#rangeend').val(moment(end).format('DD/MM/YYYY') + ' ' + moment().format('HH:mm'));

  jQuery( '#timeline' ).bind( 'mousewheel DOMMouseScroll', function ( e ) {
    var delta = e.wheelDelta || -e.detail;
    this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
    e.preventDefault();
  });

  loadUserDefaultPreset();

  jQuery(document).on("change", 'input[name="defaultpreset"]:radio', function() {
    noty({text: "Changing default preset...", type: 'info'});
    var newDefaultPresetName = jQuery(this).parent().find(".preset-list-link").text();
    var ajaxRequestId = ajaxRequests[ajaxRequests.length];
    ajaxRequests[ajaxRequestId] = jQuery.ajax({
      type: "POST",
      url: 'index.php?view=onefiletorulethemall',
      data: {updateUserDefaultPreset: true, defaultPresetId: $(this).attr("value")},
      success: function(data) {
        if(data === "success") {
          noty({text: '\'' + newDefaultPresetName + '\' set as default', type: 'success'});
          $("#preset-selection").dialog("close");
        }
        else {
          noty({text: 'Failed to save default preset', type: 'error'});
          console.log(data);
        }
        ajaxRequests.splice(ajaxRequestId, 1);
      }
    });
  });

  jQuery(document).on("click", ".monitor-stream-info-close", function(event) {
    var monitorClass = jQuery(this).parent().parent().parent().attr("id");
    var monitorId = monitorClass.substr(monitorClass.length -1);
    chosencameras.splice(chosencameras.indexOf(monitorId), 1);
    //console.log("Spliced " + monitorId + " / " + cameras[monitorId-1].Name + " from chosencameras");
    jQuery(this).parent().parent().parent().remove();
    jQuery(".monitor-stream-image").each(function() {
      if(liveview === true) { 
        jQuery(this).attr('src', jQuery(this).attr('src').split('&rand')[0] + "&rand=" + new Date().getTime());
      }
    });
    if(liveview === false) {
      requeryTimeline();
    }
  });

  jQuery(document).on("click", ".monitor-stream-image", function() {
    if(liveview === true) {
      var width = ($(window).width()-50);
      var height = ($(window).height()-50);
      var monitorWidth = $(this).width();
      var monitorHeight = $(this).height();
      var dialogContent = "<div class=\"monitor-stream-dialog\"><img class=\"monitor-stream-fullscreen\" src=\"" + $(this).attr("src") + "\"></div>";
      $(dialogContent).dialog({
        modal: true,
        height: height,
        width: width,
        resizable: false,
        draggable: false
      });
      $(".ui-dialog-titlebar-close").html("<span class=\"glyphicon glyphicon-remove\"></span>");
      if(monitorWidth > monitorHeight) {
        $(".monitor-stream-fullscreen").css("height", "100%");
      }
      else {
        $(".monitor-stream-fullscreen").css("width", "100%");
      }
    }
  });

  jQuery(document).on("click", ".show-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    chosencameras = [];
    jQuery(cameras).each(function(i, v) {
      addMonitor(v.Id, true);
    });
    noty({text: 'Added cameras', type: 'success'});
    toggleShowAllButton(true);
  });

  jQuery(document).on("click", ".hide-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    chosencameras = [];
    requeryTimeline();
    toggleShowAllButton(true);
  });

  jQuery(document).on("click", ".preset-list-link:not(.show-all-cameras)", function(event) {
    event.preventDefault();
    window.stop();
    haschosencameras = true;
    noty({text: 'Loading data', type: 'info'});
    clearAjaxRequests();
    $("#monitor-streams").empty();
    chosencameras = [];
    shouldbeplaying = false;
    playing = false;
    var monitorIds = $(this).attr("data-value").split(",");
    jQuery.each(monitorIds, function(index, value) {
      addMonitor(value, true);
    });
  });

  jQuery(document).on("click", "#pause", function(event) {
    event.preventDefault();
    window.stop();
    $("#pause").html("<span class=\"glyphicon glyphicon-play\"></span>");
    $("#pause").attr("id", "play");
    pausePlayback();
  });

  jQuery(document).on("click", "#play", function(event) {
    event.preventDefault();
    if (paused === true) {
      window.stop();
      resumePlayback();
      $("#play").html("<span class=\"glyphicon glyphicon-pause\"></span>");
      $("#play").attr("id", "pause");
    }
  });

  jQuery(document).on("click", "#liveview", function(event) {
    event.preventDefault();
    toggleMode();
  });

  jQuery(document).on("click", "#playback", function(event) {
    event.preventDefault();
    toggleMode();
  });

  jQuery(document).on("click", "#choose-cameras-opener", function(event) {
    if($("#choose-cameras").dialog("isOpen")!==true) {
      toggleShowAllButton();
      $("#choose-cameras").dialog("open");
    }
    else {
      $("#choose-cameras").dialog("close");
    }
  });

  jQuery(document).on("click", "#preset-selection-opener", function(event) {
    if($("#preset-selection").dialog("isOpen")!==true) {
      $("#preset-selection").dialog("open");
    }
    else {
      $("#preset-selection").dialog("close");
    }
  });

  jQuery(".monitor-thumbnail").click(function() {
    haschosencameras = true;
    var monitorClass = jQuery(this).attr("id");
    var monitorId = monitorClass.substr(monitorClass.length - 1);
    if(jQuery('#monitor-stream-' + monitorId).length == 0) {
      addMonitor(monitorId);
    }
    else {
      jQuery("#monitor-stream-" + monitorId).remove();
      chosencameras.splice(chosencameras.indexOf(monitorId), 1);
      requeryTimeline();
      if(!jQuery(".monitor-stream")[0]) {
        liveview = false;
      }
    }
  });

  jQuery(document).on("click", "#timeline", function(event) {
    if(liveview === false) {
      if(event.target.className.length === 0) {
        shouldbeplaying = false;
        playing = false;
        var offset = $(this).offset();
        timeline.recalcConversion();
        jumpToNearestEvent(timeline.screenToTime(event.clientX - offset.left));
        $("#play").html("<span class=\"glyphicon glyphicon-pause\"></span>");
        $("#play").attr("id", "pause");
      }
    }
  });

  setInterval(function() {
    if((liveview === false)&&(paused === false)) {
      var date = moment(timeline.getCurrentTime()).format('YYYY-MM-DD');
      var time = moment(timeline.getCurrentTime()).format('HH:mm:ss');
      var datetime = moment(timeline.getCurrentTime()).format('YYYY-MM-DD HH:mm:ss');
      jQuery(".playback-date").text(date);
      jQuery(".playback-time").text(time);
      jQuery.each(activity, function(i, v) {
        if (v.StartTime == datetime) {
            if(jQuery.inArray(v.Id, window["currentevents" + v.MonitorId]) == -1) {
            playEvent(v.MonitorId, v.Id);
            playing = true;
            return;
          }
          else {
            clearTimers();
          }
        }
      });
    }
  },1000);

  /* refresh camera thumbnails every 10 seconds */
  setInterval(function(){
    var timestamp = (new Date()).getTime();
    jQuery(".monitor-thumbnail").each(function() {
      jQuery(this).attr("src", jQuery(this).attr("src").split('&rand')[0] + "&rand=" + timestamp);
    });
  },10000);

}); /* end document ready */