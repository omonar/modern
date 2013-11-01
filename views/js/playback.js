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

var activity, cameras;
var currentTime;
var frames;
var frameTimer = null;
var chosencameras = new Array();
var timelinedata = [];
var playing = false;
var timers = new Array;
var currentlyplaying = [];
var timeline;
var data;
var liveview = false;
var shouldbeplaying = false;
var timerstimers = new Array();
var currenteventarrays = new Array();
var paused = false;
var breakplayback = false;
var start = null;
var end = null;
var times = "";
var gaplessPlayback = true;
var options = {
  'width':  '100%',
  'height': '170px',
  'editable': false,
  'showCustomTime': true,
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
        noty({text: 'Network error: Check internet connection', type: 'error'});
        console.log('Not connect.\n Verify Network.');
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

function addMonitor(monitorId) {
  window["currentevents" + monitorId] = new Array();
  currenteventarrays.push("currentevents" + monitorId);
  noty({text: 'Adding camera', type: 'info'});
  if(liveview == false) {
    liveview = true;
  }
  if(jQuery.inArray(monitorId, chosencameras) === -1) {
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=framefetcher',
      data: {monitor: monitorId, width: cameras[monitorId-1].Width, height: cameras[monitorId-1].Height, scale: 100},
      success: function(data) {
        liveview = true;
        chosencameras.push(monitorId);
        jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-right unit one-of-three\"><button class=\"monitor-stream-info-colour\"><span class=\"glyphicon glyphicon-stop\"></span></button><button class=\"monitor-stream-info-close\"><span class=\"glyphicon glyphicon-remove\"></span></button></p>' + data + '</div>').appendTo('#monitor-streams');
        requeryTimeline();
      }
    });
  }
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
  timeline.setCurrentTime(new Date().getTime());
  jQuery("#monitor-streams").empty();
  resumeLiveView();
}

function clearCameraFrames() {
  jQuery("#monitor-streams img").attr("src", '/zm/skins/modern/views/images/onerror.png');
}

function pausePlayback() {
  shouldbeplaying = false;
  playing = false;
  paused = true;
  timeline.setCustomTime(timeline.getCurrentTime());
  timeline.setCurrentTime(new Date().getTime());
}

function resumePlayback() {
  shouldbeplaying = true;
  playing = true;
  paused = false;
  timeline.setCurrentTime(timeline.getCustomTime());
  timeline.setCustomTime(new Date().getTime());
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

jQuery.fn.exists = function(){return this.length>0;}

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

jQuery.each(activity, function(index) {
  timelinedata.push({start: Date.createFromMysql(activity[index].StartTime), end: Date.createFromMysql(activity[index].EndTime), content: activity[index].Id, className: "monitor"+activity[index].MonitorId});
});

var activitydate = activity[ Object.keys(activity).sort().pop() ].Date;
var activitydatesplit = activitydate.split("-");
var activityyear = activitydatesplit[0];
var activitymonth = activitydatesplit[1]-1;
var activityday = activitydatesplit[2];
var activityrangeend = new Date(activityyear, activitymonth, activityday);

var activitydate = activity[0].Date;
var activitydatesplit = activitydate.split("-");
var activityyear = activitydatesplit[0];
var activitymonth = activitydatesplit[1]-1;
var activityday = activitydatesplit[2];
var activityrangestart = new Date(activityyear, activitymonth, activityday);

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
  //console.log("Cleared timer " + eventId);
}

function requeryTimeline() {
  if(chosencameras.length > 0) {
    jQuery("#timeline").css("background-color","red");
    var startformatted = moment(start).format('YYYY-MM-DD HH:mm') + ':00';
    var endformatted = moment(end).format('YYYY-MM-DD HH:mm') + ':00';
    //console.log(startformatted);
    //console.log(endformatted);
    //console.log("requeryTimeline with ... " + chosencameras.join(",") + " / " + startformatted + " / " + endformatted)
    jQuery.ajax({
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
        timeline.redraw();
        getFrames();
        jQuery("#timeline").css("background-color","");
        noty({text: 'Timeline refreshed', type: 'success'});
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
        console.log("Current events > 1 /// " + window["currentevents" + monitorId]);
        //get the item before this item in the array
        var previousEventIndex = window["currentevents" + monitorId].length-2;
        var previousEventId = window["currentevents" + monitorId][previousEventIndex];
        console.log("Removing event /// " + previousEventIndex);
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
        if(gaplessPlayback === true) {
          jumpToNearestEvent(timeline.getCurrentTime());
        }
      }
  },200);
}

function resumeLiveView() {
  jQuery(chosencameras).each(function(i) {
    addMonitor(cameras[i].Id);
  });
}

function playEvent(monitorId, eventId) {
  if(jQuery.inArray(monitorId, chosencameras) == -1) {
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
  jQuery.ajax({
    type: "POST",
    url: 'index.php?view=onefiletorulethemall',
    data: {q: times},
    success: function(data) {
      frames = jQuery.parseJSON(data);
    }
  });
}

function jumpToNearestEvent(datetime, direction) {
  direction = (typeof direction === "undefined") ? "forward" : direction;
  //console.log("jumpToNearestEvent called with " + datetime);
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

function drawVisualization() {
  timeline = new links.Timeline(document.getElementById('timeline'));

  timeline.applyRange(start, end);
  timeline.setVisibleChartRange(start, end, true);

  function onselect() {
    if(liveview === true) {
      clearCameraFrames();
    }

    var sel = timeline.getSelection();

    timeline.setSelection(null);

    if (sel.length) {
      if(sel[0].row != undefined) {
        var itemobj = timeline.getItem(sel[0].row);
        $("#play").html("<span class\"glyphicon glyphicon-pause\"></span>");
        $("#play").attr("id", "pause");
        timeline.setCurrentTime(itemobj.start);
        timeline.repaintCurrentTime();
        timeline.options.showCurrentTime = true;
        timeline.options.showCustomTime = true;
      }
    }
  }
  links.events.addListener(timeline, 'select', onselect);
  timeline.draw(timelinedata, options);
}

jQuery(document).ready(function() {

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

  $("#ui-id-1").parent()

  $("<button class=\"show-all-cameras\"><span class=\"glyphicon glyphicon-eye-open\"></span></button>").appendTo($("#ui-id-1").parent());
  $(".ui-dialog-titlebar-close").html("<span class=\"glyphicon glyphicon-remove\"></span>");

  start = new Date();
  end = new Date();
  start.setDate(end.getDate()-1);

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
  $('#rangeend').val(moment(end).format('DD/MM/YYYY HH:mm'));

  jQuery( '#timeline' ).
    bind( 'mousewheel DOMMouseScroll', function ( e ) {
        var delta = e.wheelDelta || -e.detail;
        this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
        e.preventDefault();
    });
  jQuery(document).on("click", ".monitor-stream-info-close", function(event) {
    var monitorClass = jQuery(this).parent().parent().parent().attr("id");
    var monitorId = monitorClass.substr(monitorClass.length -1);
    chosencameras.splice(chosencameras.indexOf(monitorId), 1);
    jQuery(this).parent().parent().parent().remove();
    jQuery(".monitor-stream-image").each(function() {
      jQuery(this).attr('src', jQuery(this).attr('src').split('&rand')[0] + "&rand=" + new Date().getTime());
    });
    requeryTimeline();
  });
  jQuery(document).on("click", ".show-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    var i = 1;
    jQuery(cameras).each(function() {
      addMonitor(i);
      i++;
    });
    if($(this).parent().attr("class")!=="preset-list-item") {
      $($(this)).replaceWith("<button class=\"hide-all-cameras\"><span class=\"glyphicon glyphicon-eye-close\"></span></button>");
    }
    else {
      $("button.show-all-cameras").replaceWith("<button class=\"hide-all-cameras\"><span class=\"glyphicon glyphicon-eye-close\"></span></button>");
    }
  });
  jQuery(document).on("click", ".hide-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    chosencameras = [];
    requeryTimeline();
    $(".hide-all-cameras").replaceWith("<button class=\"show-all-cameras\"><span class=\"glyphicon glyphicon-eye-open\"></span></button>");
  });
  jQuery(document).on("click", ".preset-list-link", function(event) {
    event.preventDefault();
    shouldbeplaying = false;
    playing = false;
    $("#monitor-streams").empty();
    if(liveview === false) {
      liveview = true;
    }
    var monitorIds = $(this).attr("data-value").split(",");
    jQuery.each(monitorIds, function(index, value) {
      addMonitor(value);
    });
  });
  jQuery(document).on("click", ".monitor-stream", function() {
    jQuery(".copyright").hide();
    var monitorClass = jQuery(this).attr("id");
    var monitorId = monitorClass.substr(monitorClass.length - 1);
    jQuery("<div class=\"camera-activity-filters\">" + cameras[monitorId-1].Name + " activity - <a class=\"activity-time-filter\" href=\"#\">1h</a> <a class=\"activity-time-filter\" href=\"#\">3h</a> <a class=\"activity-time-filter\" href=\"#\">6h</a> <a class=\"activity-time-filter\" href=\"#\">12h</a> <a class=\"activity-time-filter\" href=\"#\">24h</a><p class=\"page-footer-text\"></p></div>").prependTo(".page-footer .container");
    jQuery(".page-footer .container").css("padding-top", "1.3rem");
  });
  jQuery(document).on("dblclick", ".monitor-stream", function() {
    jQuery(".copyright").show();
    var keep = jQuery(".copyright").detach();
    jQuery('.page-footer .container').empty().append(keep);
    jQuery(".page-footer .container").removeAttr("padding-top");
  });
  jQuery(document).on("click", ".activity-time-filter", function() {
    jQuery(this).text().substring(0, jQuery(this).text().length - 1);
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
    window.stop();
    if (paused === true) {
      resumePlayback();
      $("#play").html("<span class=\"glyphicon glyphicon-pause\"></span>");
      $("#play").attr("id", "pause");
    }
  });
  jQuery(document).on("click", "#requeryTimeline", function(event) {
    event.preventDefault();
    start = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
    end = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate();
    requeryTimeline();
  });
  jQuery(document).on("click", "#stop", function(event) {
    event.preventDefault();
    window.stop();
    stopPlayback();
  });
  jQuery(document).on("click", "#jumpback", function(event) {
    shouldbeplaying = false;
    playing = false;
    clearCameraFrames();
    timeline.setCurrentTime(start);
    timeline.applyRange(start, end);
    timeline.redraw();
  });
  jQuery(document).on("click", "#jumpforward", function(event) {
    shouldbeplaying = false;
    playing = false;
    clearCameraFrames();
    timeline.setCurrentTime(end);
    timeline.applyRange(start, end);
    timeline.redraw();
  });
  jQuery(document).on("click", "#rewind", function(event) {
    console.log("RWD");
    event.preventDefault();
    window.stop();
    jQuery.each(activity, function(i, v) {
      var currentLength = window["currentevents" + monitorId].length;
      if(v.Id == parseInt(window["currentevents" + monitorId][currentLength-1])-1) {
        //previous event selected
        shouldbeplaying = false;
        playing = false;
        timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
      }
    });

  });
  jQuery(document).on("click", "#fastforward", function(event) {
    console.log("FFWD");
    event.preventDefault();
    window.stop();
    jQuery.each(activity, function(i, v) {
      var currentLength = window["currentevents" + monitorId].length;
      if(v.Id == (parseInt(window["currentevents" + monitorId][currentLength-1]) + 1)) {
        shouldbeplaying = false;
        playing = false;
        timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
      }
    });

    jQuery("#editPresets").click(function(event) {
      event.preventDefault();
      $("#editPresets-dialog").dialog( "open" );
    });

  });

  jQuery(document).on("click", "#choose-cameras-opener", function(event) {
    if($("#choose-cameras").dialog("isOpen")!==true) {
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
    if(event.target.className.length === 0) {

      if(liveview === true) {
        clearCameraFrames();
      }

      //noty({text: 'Jumping to next event'});
      shouldbeplaying = false;
      playing = false;
      var offset = $(this).offset();
      timeline.recalcConversion();
      jumpToNearestEvent(timeline.screenToTime(event.clientX - offset.left));
      $("#play").html("<span class=\"glyphicon glyphicon-pause\"></span>");
      $("#play").attr("id", "pause");
    }
  });

  $(window).bind("load", function() {
    getFrames();

    setInterval(function() {
    /*$.each(currenteventarrays, function(index, value) {
      console.log(index + " - " + window[value]);
    });*/
    //console.log(currentevents);
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
  },1000);
  });

});

setInterval(function(){
  var timestamp = (new Date()).getTime();
  jQuery(".monitor-thumbnail").each(function() {
    jQuery(this).attr("src", jQuery(this).attr("src").split('&rand')[0] + "&rand=" + timestamp);
  });
  },5000);