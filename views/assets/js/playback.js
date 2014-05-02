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
var activity = {};
var frames = [];
var chosencameras = [];
var currenteventarrays = [];
var timelinedata = [];
var timers = [];
var ajaxRequests = [];
var currentlyplaying = [];
var playing = false;
var liveview = true;
var shouldbeplaying = false;
var paused = false;
var gaplessPlayback = true;
var fullscreen = false;
var stopped = false;
var buffering = false;
var playbackspeed = 200;
var playheadspeed = 1000;
var timeline = {};
var currentevent = 0;
var options = {
  'width':  '100%',
  'height': '182px',
  'editable': false,
  'cluster': true,
  'showCustomTime': true,
  'showCurrentTime': false,
  'style': 'box'
};
$.noty.defaults = {
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
var errorImageSrc = "skins/modern/views/assets/images/onerror.png";
var poweruser = false;

/* begin third party code */
/* http://www.unseenrevolution.com/$-ajax-error-handling-function/ */
$(function() {
  $.ajaxSetup({
    error: function(jqXHR, exception) {
      if (jqXHR.status === 0) {
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

$(document).ajaxStop(function() {
  if(ajaxRequests.length > 0) {
    if(ajaxRequests.length === 1) {
      ajaxRequests.splice(0, 1);
    }
    else {
      for(var i = 0; i < (ajaxRequests.length -1); i++) {
        ajaxRequests.splice(i, 1);
      }
    }
  }
});

function stopLiveStreams() {
  $("img.monitor-stream-image").each(function() {
    $(this).attr("data-livesrc", $(this).attr("src"));
    $(this).attr("src", errorImageSrc);
  });
}

function resumeLiveStreams() {
  $("img.monitor-stream-image").each(function() {
    $(this).attr("src", $(this).attr("data-livesrc"));
  });
}

function addMonitor(cameraId, showall) {
  $.each(cameras, function(index, camera) {
    if(camera.Id === cameraId) {
      cameraIndex = index;
    }
  });

  if(arguments.length === 1) {
    showall = false;
  }
  if(liveview === false) {
    if(playing === true || shouldbeplaying === true || paused === true) {
      clearPlayback();
      newPlayheadTimer();
      stopped = false;
    }
  }
  if(showall === false) {
    noty({text: 'Adding camera...', type: 'info'});
  }
  if($.inArray(cameraId, chosencameras) === -1) {
    window["currentevents" + cameraId] = new Array();
    currenteventarrays.push("currentevents" + cameraId);

    chosencameras.push(cameraId);

    if(liveview === true) {
      $('<!--' + cameras[cameraIndex].Name + ' --> <div id=\"monitor-stream-' + cameraId + '\" class=\"monitor-stream\" data-monitorid=\"' + cameraId + '\"><div class=\"col-container\"><div class=\"monitor-stream-info\"><p class=\"monitor-stream-info-name\" data-rel=\"tooltip\" title=\"The name assigned to this camera\">' + cameras[cameraIndex].Name + '</p><p class=\"monitor-stream-info-right\"><button class=\"monitor-stream-info-colour\" data-rel=\"tooltip\" title=\"The colour assigned to this camera on the timeline\"><span class=\"fa fa-stop\"></span></button><button class=\"monitor-stream-info-close\" data-rel=\"tooltip\" title=\"Hide this camera from view\"><span class=\"fa fa-times\"></span></button></p><img id=\"liveStream' + cameraId + '\" class=\"monitor-stream-image\" src=\"' + cameras[cameraIndex].LiveSrc + '\" alt=\"' + cameraId + '\" width=\"' + cameras[cameraIndex].Width + '\" height=\"' + cameras[cameraIndex].Height + '\" onerror=\"imgError(this);\" data-livesrc=\"' + cameras[cameraIndex].LiveSrc + '\""></div></div>').appendTo('div#monitor-streams');
    }
    else {
      $('<!-- ' + cameras[cameraIndex].Name + ' --> <div id=\"monitor-stream-' + cameraId + '\" class=\"monitor-stream\" data-monitorid=\"' + cameraId + '\"><div class=\"col-container\"><div class=\"monitor-stream-info\"><p class=\"monitor-stream-info-name\" data-rel=\"tooltip\" title=\"The name assigned to this camera\">' + cameras[cameraIndex].Name + '</p><p class=\"monitor-stream-info-right\"><button class=\"monitor-stream-info-colour\" data-rel=\"tooltip\" title=\"The colour assigned to this camera on the timeline\"><span class=\"fa fa-stop\"></span></button><button class=\"monitor-stream-info-close\" data-rel=\"tooltip\" title=\"Hide this camera from view\"><span class=\"fa fa-times\"></span></button></p><img id=\"liveStream' + cameras[cameraIndex].Id + '\" class=\"monitor-stream-image\" data-livesrc=\"' + cameras[cameraIndex].LiveSrc + '\" src=\"' + errorImageSrc + '\" alt=\"' + cameras[cameraIndex].Id + '\" width=\"' + cameras[cameraIndex].Width + '\" height=\"' + cameras[cameraIndex].Height + '\" onerror=\"imgError(this);\"></div></div>').appendTo('div#monitor-streams');
    }
    $("div#monitor-stream-" + cameraId + " .monitor-stream-info-events").tooltip({placement: 'bottom'});
    $("div#monitor-stream-" + cameraId + " .monitor-stream-info-close").tooltip({placement: 'bottom'});
    $("div#monitor-stream-" + cameraId + " .monitor-stream-info-colour").tooltip({placement: 'bottom'});
    $("div#monitor-stream-" + cameraId + " .monitor-stream-info-name").tooltip({placement: 'bottom'});

    $("#choose-cameras #monitor-stream-thumbnail-" + cameraId).parent().append("<span class=\"fa fa-times-circle-o\"></span>");
    $("#choose-cameras #monitor-stream-thumbnail-" + cameraId).parent().parent().parent().addClass("monitor-selected");

    if((showall === false)&&(liveview===false)) {
      requeryTimeline();
    }
    if((showall === true)&&(cameraId==cameras[cameras.length-1].Id)&&(liveview===false)) {
      requeryTimeline();
    }

    if(chosencameras.length > 1) {
      $("div#monitor-stream-" + cameraId).css("width", $("div.monitor-stream").first().css("width"));
      $("div#monitor-stream-" + cameraId + " img.monitor-stream-image").css("width", "100%");
    }
  }
}

function stopPlayback() {
  shouldbeplaying = false;
  playing = false;
  timeline.options.showCustomTime = false;
  timeline.draw(null, options);
  $("button#pause").html("<span class=\"fa fa-play\"></span>");
  $("button#pause").attr("id", "play");
}

function clearTimer(eventId) {
  if(typeof(timers[eventId]) !== "undefined" && typeof(timers[eventId]) !== null) {
    clearInterval(timers[eventId]);
    timers[eventId] = 0;
  }
}

function clearTimers() {
  $.each(currenteventarrays, function(index, value) {
    var monid = value.substr(value.length - 1);
    $.each(window[value], function(key, val) {
      clearTimer(val);
    });
  });
  currentlyplaying = null;
  timers = [];
}

function clearPlayback() {
  shouldbeplaying = false;
  playing = false;
  stopped = true;
  buffering = false;
  clearInterval(window.playheadtimer);
  window.playheadtimer = 0;
  clearTimers();
  currentevent = null;
  $.each(currenteventarrays, function(index, value) {
    window[value] = [];
  });
  $.each(cameras, function(index, value) {
    frames[value.Id-1] = [];
  });
  timeline.setCustomTime(new Date());
  clearCameraFrames();
}

function clearCameraFrames() {
  $("div#monitor-streams img").attr("src", errorImageSrc);
}

function pausePlayback() {
  shouldbeplaying = false;
  playing = false;
  paused = true;
}

function resumePlayback() {
  shouldbeplaying = true;
  playing = true;
  paused = false;

}

function toggleBufferingState(shouldbebuffering) {
  if(shouldbebuffering === false) {
    shouldbeplaying = true;
    playing = true;
    paused = false;
    buffering = false;
    newPlayheadTimer();
  }
  else {
    shouldbeplaying = false;
    playing = false;
    paused = true;
    buffering = true;
    clearInterval(window.playheadtimer);
    window.playheadtimer = 0;
  }
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

$.fn.exists = function(){
  if(this.length>0) {
    return true;
  }
  else {
    return false;
  }
}

function imgError(image) {
  image.onerror = "";
  image.src = errorImageSrc;
  return true;
}

function setTime(element, refresh, formatting) {
  setInterval(function(){
    $(element).text(moment().format(formatting));
  },refresh);
}

function loadUserDefaultPreset() {
  if($("div#preset-selection input[name=defaultpreset]:checked").val()!=="-1") {
    var presetName = $("div#preset-selection input[name=defaultpreset]:checked").parent().find(".preset-list-link").text();
    var presetMonitorIds = $("div#preset-selection input[name=defaultpreset]:checked").parent().find(".preset-list-link").attr("data-value");
    var presetMonitorIds = presetMonitorIds.split(",");
    $.each(presetMonitorIds, function(index, value) {
      addMonitor(value, true);
    });
    noty({text: "Loaded \"" + presetName + "\" preset", type: 'success'});
  }
  else {
    $(cameras).each(function(i, v) {
      addMonitor(v.Id, true);
    });
    noty({text: "Loaded all cameras", type: 'success'});
  }
}

function displayFrame(monitorId, img) {
    //console.log("displayFrame(" + img + ");");
    $("img#liveStream" + monitorId).attr('src', img);
    //$("#monitor-stream-"+monitorId+" .monitor-stream-image").attr('src', img);
}

function requeryTimeline() {
  console.log("INFO: Requerying timeline...");
  if(chosencameras.length > 0) {
    $("div.timeline-frame").css("background-color","red");
    var startformatted = moment(start).format('YYYY-MM-DD HH:mm') + ':00';
    var endformatted = moment(end).format('YYYY-MM-DD HH:mm') + ':00';
    var ajaxRequestId = ajaxRequests.length;
    //console.log("adding request in requeryTimeline " + ajaxRequestId);
    ajaxRequests[ajaxRequestId] = $.ajax({
      type: "POST",
      url: 'index.php?view=onefiletorulethemall',
      data: {timeline: 'ok', cameras: chosencameras.join(","), start: startformatted, end: endformatted},
      beforeSend: function() {
        noty({ text: "Downloading data...", type: "info" });
      },
      success: function(data) {
        noty({ text: "Processing data", type: "info" });
        activity = JSON.parse(data);
        var timelineWorker = new Worker("/zm/skins/modern/views/assets/js/processtimeline.js");
        noty({ text: "Updating timeline...", type: "info" });

        timelineWorker.addEventListener('message', function(e) {
          if(e.data.indexOf("###") != -1) {
            var x = e.data.split("###");
            switch(x[0]) {
              case "timelinedata":
                window.timelinedata = JSON.parse(x[1]);
                break;
              case "myframes":
                window.frames = JSON.parse(x[1]);
                break;
            }
          }
          else {
            if(e.data === "success") {
              window.processedtimelinedata = [];
              $.each(timelinedata, function(index, value) {
                value.start = moment(value.start);
                value.end = moment(value.end);
                window.processedtimelinedata.push(value);
              });
              delete window.timelinedata;
              timelinedata = window.processedtimelinedata;
              timeline.draw(timelinedata, options);
              timeline.applyRange(start, end);
              timeline.redraw();
              $("div.timeline-frame").css("background-color","");
              noty({text: 'Timeline updated (' + timelinedata.length + " events)", type: 'success'});
            }
            else {
              console.log(e.data);
            }
          }
        }, false);

        timelineWorker.postMessage("cameras###" + JSON.stringify(cameras));
        timelineWorker.postMessage("activity###" + data);
        timelineWorker.postMessage("start");

      },
      complete: function() {
        //console.log("removing request in requeryTimeline " + ajaxRequestId);
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
  if(!timers.hasOwnProperty(eventId)) {
    timers[eventId] = setInterval(function(){
      // prevent stuttering
      if(window["currentevents" + monitorId].length > 1) {
        //console.log("Current events > 1 /// " + window["currentevents" + monitorId]);
        //get the item before this item in the array
        var previousEventIndex = window["currentevents" + monitorId].length-2;
        var previousEventId = window["currentevents" + monitorId][previousEventIndex];
        //console.log("Removing event /// " + previousEventIndex);
        window["currentevents" + monitorId].splice(previousEventIndex, 1);
        clearTimer(previousEventId);
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
          clearTimer(eventId);
          displayFrame(monitorId, errorImageSrc);
          window["currentevents" + monitorId].splice(window["currentevents" + monitorId].indexOf(eventId), 1);
          // zero the frames array for the current monitor to keep memory usage low
          frames[monitorId-1] = [];
          // if gaplessPlayback enabled & the event finishes tidily
          if(gaplessPlayback === true) {
            //jumpToNearestEvent(timeline.getCurrentTime());
            window.setTimeout(function() {
              jumpToNearestEvent(timeline.getCustomTime());
            }, 2000);
          }
        }
        x++;
      }
      // if an event shouldn't be playing
      else {
        // if an event has come to an end neatly
        if(paused === false) {
          clearTimer(eventId);
          displayFrame(monitorId, errorImageSrc);
        }
        else {
          displayFrame(monitorId, $("img#liveStream" + monitorId).attr("src"));
        }
        // if an event has come to an end tidily we should no longer be playing
        if(window["currentevents" + monitorId].length === 1) {
          playing = false;
          shouldbeplaying = false;
        }
        // remove the event from the relevant array
        if(paused === false) {
          window["currentevents" + monitorId].splice(window["currentevents" + monitorId].indexOf(eventId), 1);
          // zero the frames array for the current monitor to keep memory usage low
          frames[monitorId-1] = [];
        }
      }
    },playbackspeed);
  }
}

function preloadFrames(imgarray) {
  for(var i = 0; i < imgarray.length; i++) {
    var imgObj = new Image();
    imgObj.src = imgarray[i];
    if(i === (imgarray.length-1)) {
      toggleBufferingState(false);
      return true;
    }
  }
}

function playEvent(monitorId, eventId, startdatetime, numberofframes) {
  toggleBufferingState(true);
  currentevent = eventId;
  /*This shouldn't be nedeched, but leaving it here for a few commits in case I haven't fixed the problem
  console.log("monitorId="+monitorId+" & typeof(monitorId)="+typeof(monitorId));
  console.log("chosencameras[0]=" + chosencameras[0] + " & typeof(chosencameras[0])=" + typeof(chosencameras[0]));
  if(typeof(monitorId)!="number") {
    monitorId = parseInt(monitorId);
  }*/
  if($.inArray(monitorId, chosencameras) === -1) {
    addMonitor(monitorId);
  }
  window["currentevents" + monitorId].push(eventId);
  console.log("INFO: Playing event" + eventId + " on monitor " + monitorId + " at " + $("#speed option:selected").text() + ".");
  liveview = false;
  shouldbeplaying = true;

  frames[Number(monitorId)-1][Number(eventId)] = [];

  for(var counter = 1; counter <= Number(numberofframes); counter++) {
     if(counter<=9) {
        frames[Number(monitorId)-1][Number(eventId)].push("/zm/events/" + monitorId + "/" + moment(startdatetime, "YYYY-MM-DD HH:mm:ss").format("YY/MM/DD/HH/mm/ss") + "/00" + counter + "-capture.jpg");
      }
      else {
        if(counter<=99) {
          frames[Number(monitorId)-1][Number(eventId)].push("/zm/events/" + monitorId + "/" + moment(startdatetime, "YYYY-MM-DD HH:mm:ss").format("YY/MM/DD/HH/mm/ss") + "/0" + counter + "-capture.jpg");
        }
        else {
         frames[Number(monitorId)-1][Number(eventId)].push("/zm/events/" + monitorId + "/" + moment(startdatetime, "YYYY-MM-DD HH:mm:ss").format("YY/MM/DD/HH/mm/ss") + "/" + counter + "-capture.jpg");
        }
      }
    }

  if(preloadFrames(frames[monitorId-1][eventId]) === true) {
    //console.log("Preloaded event " + eventId + " on " + monitorId);
    playbackFrames(monitorId, eventId, frames[monitorId-1][eventId]);
  }
}

function jumpToNearestEvent(datetime, direction) {
  //console.log("jumpToNearestEvent called at " + datetime);
  direction = (typeof direction === "undefined") ? "forward" : direction;
  var matchFound = false;
  $.each(activity, function(i, v) {
    if(matchFound === false) {
      if (direction === "backward") {
        if(Date.createFromMysql(v.StartTime) <= datetime) {
          matchFound = true;
          timeline.setCustomTime(moment(Date.createFromMysql(v.StartTime)).subtract('seconds', 1));
        }
      }
      else {
        if(Date.createFromMysql(v.StartTime) >= datetime) {
          matchFound = true;
          timeline.setCustomTime(moment(Date.createFromMysql(v.StartTime)).subtract('seconds', 1));
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
      $("p.currently-playing").css("visibility", "visible");
      $("p.playback-date").css("visibility", "visible");
      $("p.playback-time").css("visibility", "visible");

      if(playing === true) {
        clearPlayback();
        stopped = false;
        newPlayheadTimer();
      }

      var sel = timeline.getSelection();

      timeline.setSelection(null);

      if (sel.length) {
        if(sel[0].row != undefined) {
          var itemobj = timeline.getItem(sel[0].row);
          if($("button.playpause-button").attr("id") === "play") {
            togglePlayPauseButton();
          }
          timeline.setCustomTime(itemobj.start);
          timeline.repaintCustomTime();
        }
      }
    }
  }
  links.events.addListener(timeline, 'select', onselect);
  timeline.draw(null, options);

  $(document).on("mousewheel", "#timeline", function(event) {
      event.preventDefault();

      timeline.recalcConversion();

      var frameLeft = links.Timeline.getAbsoluteLeft(timeline.dom.content);
      var mouseX = links.Timeline.getPageX(event);
      var zoomAroundDate = (mouseX != undefined && frameLeft != undefined) ? timeline.screenToTime(mouseX - frameLeft) : undefined;

      if((event.originalEvent.deltaY <= 0)&&(event.originalEvent.deltaY != -0)) {
        timeline.zoom(0.3, zoomAroundDate);
        timeline.trigger("rangechange");
        timeline.trigger("rangechanged");
      }
      else {
        timeline.zoom(-0.3, zoomAroundDate);
        timeline.trigger("rangechange");
        timeline.trigger("rangechanged");
    }
  });
}

function toggleShowAllButton(override) {
  if(arguments.length === 0) {
    override = false;
  }
  if(override === false) {
    if($("button.show-all-cameras").exists()) {
      if(chosencameras.length === cameras.length) {
        $("button.show-all-cameras").replaceWith("<button class=\"hide-all-cameras show-hide-cameras\" data-rel=\"tooltip\" title=\"Click here to show / hide all cameras\"><span class=\"fa fa-eye-slash\"></span></button>");
      }
    }
    else {
      if(chosencameras.length < cameras.length) {
        $("button.hide-all-cameras").replaceWith("<button class=\"show-all-cameras show-hide-cameras\" data-rel=\"tooltip\" title=\"Click here to show / hide all cameras\"><span class=\"fa fa-eye\"></span></button>");
      }
    }
  }
  else {
    if($("span.fa.fa-eye-slash").exists()) {
      $("button.hide-all-cameras").replaceWith("<button class=\"show-all-cameras show-hide-cameras\" data-rel=\"tooltip\" title=\"Click here to show / hide all cameras\"><span class=\"fa fa-eye\"></span></button>");
    }
    else{
      $("button.show-all-cameras").replaceWith("<button class=\"hide-all-cameras show-hide-cameras\" data-rel=\"tooltip\" title=\"Click here to show / hide all cameras\"><span class=\"fa fa-eye-slash\"></span></button>");
    }
  }
  $("button.show-hide-cameras").tooltip({ placement: "bottom" });
}

function togglePlayPauseButton() {
  if($("button.playpause-button").attr("id") === "play") {
    $("button#play").html("<span class=\"fa fa-pause\"></span>");
    $("button#play").attr("id", "pause");
  }
  else {
    $("button#pause").html("<span class=\"fa fa-play\"></span>");
    $("button#pause").attr("id", "play");
  }
}

function toggleMode() {
  if(liveview === true) {
    liveview = false;

    $(".range-datepicker-opener").css("visibility", "visible");

    // if there have been cameras selected
    if($.trim($("div#monitor-streams").html()).length) {
      $("img.monitor-stream-image").each(function() {
        $(this).attr("data-livesrc", $(this).attr("src"));
        $(this).attr("src", errorImageSrc);
      });
    }

    $("button#playback").tooltip('destroy');
    $("button#playback").html("<span class=\"fa fa-dot-circle-o\"></span>");
    $("button#playback").attr("title", "Enter Live View Mode");
    $("button#playback").attr("id", "liveview");
    $("button#liveview").tooltip();

    $("select#speed").show();
    $("button#play").show();
    $("button#play").prop("disabled", false);
    $("input#rangestart").prop("disabled", false);
    $("input#rangeend").prop("disabled", false);
    $("button#load-events").prop("disabled", false);

    $("p.currently-playing").css("visibility", "visible");
    $("p.playback-date").css("visibility", "visible");
    $("p.playback-time").css("visibility", "visible");

    $(".monitor-stream-image").css("-moz-backface-visibility", "hidden");

    requeryTimeline();
  }
  else {
    liveview = true;

    $(".range-datepicker-opener").css("visibility", "hidden");

    $("button#liveview").tooltip('destroy');
    $("button#liveview").html("<span class=\"fa fa-film\"></span>");
    $("button#liveview").attr("title", "Enter Playback Mode")
    $("button#liveview").attr("id", "playback");
    $("button#playback").tooltip();

    $("p.currently-playing").css("visibility", "hidden");
    $("p.playback-date").css("visibility", "hidden");
    $("p.playback-time").css("visibility", "hidden");

    $(".monitor-stream-image").css("-moz-backface-visibility", "visible");

    $("select#speed").hide();
    $("button#play").hide();
    $("button#play").prop("disabled", true);
    $("input#rangestart").prop("disabled", true);
    $("input#rangeend").prop("disabled", true);
    $("button#load-events").prop("disabled", true);

    $("p.playback-date").text("0000-00-00");
    $("p.playback-time").text("00:00:00");

    $("button.show-hide-cameras").show();
    $("li.preset-list-item a.show-all-cameras").parent().show();
    $(".zm .ui-dialog li:nth-child(even)").css("background-color", "#333");
    $(".zm .ui-dialog li:nth-child(odd)").css("background-color", "#000");

    stopPlayback();

    clearPlayback();
    // if there have been cameras selected
    window.setTimeout(function() {
      if($.trim($("div#monitor-streams").html()).length) {
        $("img.monitor-stream-image").each(function() {
          $(this).attr("src", $(this).attr("data-livesrc").split('&rand')[0] + "&rand=" + new Date().getTime());
          $(this).removeAttr("data-livesrc");
        });
      }
    }, 1000);
  }
}

function getEventIds(start, end) {
  var eventIds = new Array();
  $.each(activity, function(i, v) {
    var tempDate = Date.createFromMysql(v.StartTime);
    if ((tempDate >= start)&&(tempDate <= end)) {
      if(!(v.MonitorId in eventIds)) {
        eventIds[v.MonitorId] = new Array();
      }
      eventIds[v.MonitorId].push(v.Id);
    }
  });
  return eventIds;
}

function newPlayheadTimer() {
  window.playheadtimer = setInterval(function() {
    if(paused === false && stopped === false && liveview === false) {
      timeline.setCustomTime(moment(timeline.getCustomTime()).add('seconds', 1));
    }
    if((liveview === false)&&(paused === false)) {
      var eventsToPlay = new Array();
      //remove var date = moment(timeline.getCurrentTime()).format('YYYY-MM-DD');
      //remove var time = moment(timeline.getCurrentTime()).format('HH:mm:ss');
      var date = moment(timeline.getCustomTime()).format('YYYY-MM-DD');
      var time = moment(timeline.getCustomTime()).format('HH:mm:ss');
      //var datetime = moment(timeline.getCurrentTime()).format('YYYY-MM-DD HH:mm:ss');
      var datetime = moment(timeline.getCustomTime()).subtract('seconds', 1).format('YYYY-MM-DD HH:mm:ss');
      //console.log("checking " + datetime);
      $("p.playback-date").text(date);
      $("p.playback-time").text(time);
      $.each(activity, function(i, v) {
        if (v.StartTime == datetime) {
            if($.inArray(v.Id, window["currentevents" + v.MonitorId]) == -1) {
              //playEvent(v.MonitorId, v.Id);
              //playing = true;
              eventsToPlay.push(v.MonitorId + "," + v.Id + "," + v.StartTime + "," + v.Frames);
            }
        }
      });
      //console.log(eventsToPlay);
      if(eventsToPlay.length > 0) {
        $.each(eventsToPlay, function(index, value) {
          var x = value.split(",");
          playEvent(x[0], x[1], x[2], x[3]);
          playing = true;
        });
      }
    }
  }, playheadspeed);
}

function prequeryTimeline() {
  if(playing === true || shouldbeplaying === true || paused === true) {
    clearPlayback();
    newPlayheadTimer();
    stopped = false;
    togglePlayPauseButton();
  }
  start = moment($("input#rangestart").val(), 'D/M/YYYY h:mm').toDate();
  end = moment($("input#rangeend").val(), 'D/M/YYYY h:mm').toDate();
  requeryTimeline();
}

/* begin third party code */
/* Mozilla Contributors - Developer Wiki */
/* https://developer.mozilla.org/en-US/docs/Web/Guide/API/DOM/Using_full_screen_mode */
function toggleFullscreen() {
  if (!document.fullscreenElement &&
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
    else {
      noty({ text: "Cannot enter fullscreen mode automatically. Try manually pressing F11!", type: "error" });
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
    else {
      noty({ text: "Cannot exit fullscreen mode automatically. Try manually pressing F11!", type: "error" });
    }
  }
}
/* end third party code */

$(document).ready(function() { /* begin document ready */

  $.each(cameras, function(index, value) {
    frames[value.Id-1] = [];
  });

  $("[data-rel='tooltip']").tooltip();

  $('img.monitor-thumbnail').capty({ animation: 'fixed' });

  setupTimeline();

  $("div#choose-cameras").dialog({
    autoOpen: false,
    resizable: true,
    minWidth: 535,
    height: 'auto'
  });

  $("div#preset-selection").dialog({
    autoOpen: false,
    resizable: true,
    minWidth: 400
  });

  $("div#set-default-preset").dialog({
    autoOpen: false,
    resizable: true,
    width: 'auto'
  });

  if(liveview === true || poweruser === true) {
    $("<button class=\"show-all-cameras show-hide-cameras\" data-rel=\"tooltip\" title=\"Click here to show / hide all cameras\"><span class=\"fa fa-eye\"></span></button>").appendTo($("span#ui-id-1").parent());
    $("button.show-hide-cameras").tooltip({ placement: "bottom" });
  }

  $('input#rangestart').datetimepicker({
    dateFormat: "dd/mm/yy",
    stepMinute: 5,
    closeText: "Select",
    showOn: "button",
    buttonImage: "skins/modern/views/assets/vendor/images/calendar.png",
    buttonImageOnly: true,
    onClose: function() {
      $("input#rangeend").val(moment($("input#rangestart").val(), "DD/MM/YYYY HH:mm").add(12, "hours").format("DD/MM/YYYY HH:mm"));
    }
  }).next(".ui-datepicker-trigger").addClass("range-datepicker-opener");

  $('input#rangeend').datetimepicker({
    dateFormat: "dd/mm/yy",
    stepMinute: 5,
    closeText: "Select",
    showOn: "button",
    buttonImage: "skins/modern/views/assets/vendor/images/calendar.png",
    buttonImageOnly: true
  }).next(".ui-datepicker-trigger").addClass("range-datepicker-opener");

  $('input#rangestart').val(moment(start).format('DD/MM/YYYY HH:mm'));
  $('input#rangeend').val(moment(end).format('DD/MM/YYYY') + ' ' + moment().format('HH:mm'));

  loadUserDefaultPreset();

  $(document).on("change", 'input[name="defaultpreset"]:radio', function() {
    noty({text: "Changing default preset...", type: 'info'});

    $("#choose-cameras .monitor-stream-thumbnail-item span").remove();
    $("#choose-cameras .monitor-stream-thumbnail-item").removeClass("monitor-selected");

    var newDefaultPresetName = $(this).parent().find("a.preset-list-link").text();
    if(newDefaultPresetName != "All Cameras") {
      var monitorIds = $(this).parent().find("a").attr("data-value").split(",");
    }
    else {
      var monitorIds = [];
      $(cameras).each(function(i, v) {
        monitorIds.push(v.Id);
      });
    }
    var ajaxRequestId = ajaxRequests.length;
    //console.log("adding request " + ajaxRequestId);
    ajaxRequests[ajaxRequestId] = $.ajax({
      type: "POST",
      url: 'index.php?view=onefiletorulethemall',
      data: {updateUserDefaultPreset: true, defaultPresetId: $(this).attr("value")},
      success: function(data) {
        if(data === "success") {
          noty({text: '\'' + newDefaultPresetName + '\' set as default', type: 'success'});
          $("div#preset-selection").dialog("close");
          if(newDefaultPresetName != "All Cameras") {
            noty({text: 'Loading data', type: 'info'});
            clearAjaxRequests();

            stopLiveStreams();
            $("div#monitor-streams").empty();

            chosencameras = [];
            shouldbeplaying = false;
            playing = false;
            $.each(monitorIds, function(index, value) {
              addMonitor(value, true);
            });
          }
          else {
            stopLiveStreams();
            $("div#monitor-streams").empty();

            chosencameras = [];
            shouldbeplaying = false;
            playing = false;
            $.each(monitorIds, function(index, value) {
              addMonitor(value, true);
            });
            toggleShowAllButton(true);
          }
        }
        else {
          noty({text: 'Failed to save default preset', type: 'error'});
          console.log(data);
        }
        
        ajaxRequests.splice(ajaxRequestId, 1);
      }
    });
  });

  $(document).on("click", "button#scale-increase", function() {
    $("div.monitor-stream").css("width", $("img.monitor-stream-image").first().width() / 0.75 + "px");
    $("div.monitor-streams img.monitor-stream-image").css("width", "100%");
    noty({ text: "Cameras size increased", type: "success" });
  });

  $(document).on("click", "button#scale-reset", function() {
    $("div.monitor-streams div.col-container").css("width", "");
    $("div.monitor-streams img.monitor-stream-image").css("width", "");
    $("div.monitor-stream").css("width", "480px");
    noty({ text: "Cameras size reset to default", type: "success" });
  });

  $(document).on("click", "button#scale-decrease", function() {
    $("div.monitor-stream").css("width", $("img.monitor-stream-image").first().width() * 0.75 + "px");
    $("div.monitor-streams img.monitor-stream-image").css("width", "100%");
    noty({ text: "Cameras size decreased", type: "success" });
  });

  $(document).on("click", "button.monitor-stream-info-close", function(event) {
    if(liveview === false) {
      if(playing === true || shouldbeplaying === true || paused === true) {
        clearPlayback();
        newPlayheadTimer();
        stopped = false;
      }
    }
    
    var monitorClass = $(this).parent().parent().parent().parent().attr("id");
    var monitorId = monitorClass.substr(monitorClass.length -1);
    chosencameras.splice(chosencameras.indexOf(monitorId), 1);
    //console.log("Spliced " + monitorId + " / " + cameras[monitorId-1].Name + " from chosencameras");
    $("#choose-cameras img#monitor-stream-thumbnail-" + monitorId).parent().find("span").remove();
    $("#choose-cameras img#monitor-stream-thumbnail-" + monitorId).parent().parent().parent().removeClass("monitor-selected");
    $(this).parent().parent().parent().parent().remove();
    var monitorSrc = $(this).parent().parent().parent().find("img.monitor-stream-image").attr("src");
    $(this).parent().parent().parent().find("img.monitor-stream-image").attr("src", errorImageSrc);
    if(liveview === true) {
      $("img.monitor-stream-image").each(function() {
        $(this).attr('src', $(this).attr('src').split('&rand')[0] + "&rand=" + new Date().getTime());
      });
    }
    if(liveview === false) {
      requeryTimeline();
    }
  });

  $(document).on("change", "select#speed", function() {
    var current = currentevent;
    //console.log("Change called on #speed");
    playbackspeed = $(this).val();
    //console.log("Playback speed is " + playbackspeed + " but should be " + $(this).val());
    clearInterval(window.playheadtimer);
    window.playheadtimer = 0;
    switch(playbackspeed) {
      case "400":
        playheadspeed = 2000;
        break;
      case "200":
        playheadspeed = 1000;
        break;
      case "100":
        playheadspeed = 500;
        break;
      case "50":
        playheadspeed = 250;
        break;
    }
    if(playing === true || shouldbeplaying === true || paused === true) {
      clearPlayback();
      stopped = false;
      if(paused === true) {
        togglePlayPauseButton();
      }
      paused = false;
      shouldbeplaying = true;

      var value = false;
      $.each(activity, function(i, v) {
        if(v.Id == current) {
          currentEventStartTime = moment(v.StartTime, "YYYY-MM-DD HH:mm:ss").toDate();
          return false;
        }
      });

      noty({ text: "Restarting playback at " + $("#speed").find(":selected").text() + " speed...", type: "success" });

      window.setTimeout(function() {
        timeline.setCustomTime(currentEventStartTime)
        jumpToNearestEvent(moment(currentEventStartTime).subtract('seconds', 1));
      }, 3000);
    }
    newPlayheadTimer();
  });

  $(document).on("dblclick", "img.monitor-stream-fullscreen", function() {
    $(this).panzoom("reset");
  });

  $(document).on("click", "#monitor-limit-dialog img.monitor-thumbnail", function() {
    if($("#monitor-limit-dialog .monitor-selected").length < 3 || $(this).parent().parent().parent().hasClass("monitor-selected")) {
      if(!$(this).parent().parent().parent().hasClass("monitor-selected")) {
        $(this).parent().parent().parent().addClass("monitor-selected");
        $(this).parent().append("<span class=\"fa fa-check-circle-o\"></span>");
      }
      else {
        $(this).parent().parent().parent().removeClass("monitor-selected");
        $(this).parent().find(".fa-check-circle-o").remove();
      }
    }
    else {
      noty({ text: "Only a maximum of 3 cameras can be selected for playback mode.", type: "error" });
    }
  });

  $(document).on("click", "img.monitor-stream-image", function() {
    if(fullscreen === false) {
      if(liveview === true) {
        stopLiveStreams();
      }
      var monitorID = $(this).attr("id").match(/\d+/);
      var width = ($(window).width()-10);
      var height = ($(window).height()-10);
      var monitorWidth = $(this).width();
      var monitorHeight = $(this).height();
      var originalMonitorMarkup = $(this).clone();
      var monitorMarkup = $(this).clone();
      $(monitorMarkup).addClass("monitor-stream-fullscreen");
      $(monitorMarkup).addClass("monitor-stream-fullscreen-" + monitorID);
      if(liveview === true) {
        $(monitorMarkup).attr("src", $(monitorMarkup).attr("data-livesrc"));
      }
      $(this).remove();
      var dialogContent = "<div class=\"monitor-stream-dialog\"></div>";
      $(dialogContent).dialog({
        modal: false,
        height: height,
        width: width,
        resizable: true,
        draggable: true,
        open: function() {
          $(monitorMarkup).appendTo(".monitor-stream-dialog");
          fullscreen = true;
          console.log($(".monitor-stream-fullscreen").width());
        },
        close: function(event, ui) {
          $(this).dialog('destroy').remove();
          $(originalMonitorMarkup).appendTo("#monitor-stream-" + monitorID);
          if(liveview === true) {
            $("img#liveStream" + monitorID).attr("src", $("img#liveStream" + monitorID).attr("src").split('&rand')[0] + "&rand=" + new Date().getTime());
            $("div#monitor-stream-" + monitorID + " div.col-container").removeAttr("style");
            resumeLiveStreams();
          }
          fullscreen = false;
        }
      });

      if($(window).width() > $(window).height()) {
        if(monitorWidth > monitorHeight) {
          $("img.monitor-stream-fullscreen").css("height", "100%");
          $("img.monitor-stream-fullscreen").css("width", "auto");
        }
        else {
          $("img.monitor-stream-fullscreen").css("height", "auto");
          $("img.monitor-stream-fullscreen").css("width", "100%");
        }
      }
      else {
        if(monitorWidth > monitorHeight) {
          $("img.monitor-stream-fullscreen").css("height", "auto");
          $("img.monitor-stream-fullscreen").css("width", "100%");
        }
        else {
          $("img.monitor-stream-fullscreen").css("height", "100%");
          $("img.monitor-stream-fullscreen").css("width", "auto");
        }
      }

      $("img.monitor-stream-fullscreen").css("display", "block");
      $("img.monitor-stream-fullscreen").css("margin", "0 auto");

      // enable zoom
      var $section = $('div.monitor-stream-dialog');
      var $panzoom = $section.find('img#liveStream' + monitorID).panzoom();
      // handle mousewheel scroll zooming
      $panzoom.parent().on('mousewheel.focal', function( e ) {
        e.preventDefault();
        var delta = e.delta || e.originalEvent.wheelDelta;
        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        $panzoom.panzoom('zoom', zoomOut, {
          increment: 0.1,
          minScale: 1,
          focal: e
        });
      });

    }
  });

  $(document).on("click", ".show-all-cameras", function(event) {
    event.preventDefault();
    $("img.monitor-stream-image").each(function() {
      $(this).attr("src", errorImageSrc);
    });
    stopLiveStreams();
    $("div#monitor-streams").empty();
    chosencameras = [];
    $.when(
      $(cameras).each(function(i, v) {
        addMonitor(v.Id, true);
      })
    ).then(function() {
      noty({text: 'Added all cameras', type: 'success'});
      toggleShowAllButton(true);
    });
  });

  $(document).on("click", ".hide-all-cameras", function(event) {
    event.preventDefault();
    stopLiveStreams();
    $("div#monitor-streams").empty();
    chosencameras = [];
    requeryTimeline();
    toggleShowAllButton(true);
  });

  $(document).on("click", "a.preset-list-link:not(.show-all-cameras)", function(event) {
    event.preventDefault();
    var presetName = $(this).text();
    noty({text: 'Loading data', type: 'info'});
    clearAjaxRequests();

    stopLiveStreams();
    $("div#monitor-streams").empty();

    $("#choose-cameras .monitor-stream-thumbnail-item span").remove();
    $("#choose-cameras .monitor-stream-thumbnail-item").removeClass("monitor-selected");

    chosencameras = [];
    shouldbeplaying = false;
    playing = false;
    var monitorIds = $(this).attr("data-value").split(",");
    $.when(
      $.each(monitorIds, function(index, value) {
        addMonitor(value, true);
      })
    ).then(function() {
        noty({ text: "Loaded \"" + presetName + "\" preset", type: "success" });
    });
  });

  $(document).on("click", "button#pause", function(event) {
    event.preventDefault();
    window.stop();
    $("button#pause").html("<span class=\"fa fa-play\"></span>");
    $("button#pause").attr("id", "play");
    pausePlayback();
  });

  $(document).on("click", "button#play", function(event) {
    event.preventDefault();
    if (paused === true) {
      window.stop();
      resumePlayback();
      togglePlayPauseButton();
    }
    else {
      timeline.setCustomTime(start);
      jumpToNearestEvent(start);
      togglePlayPauseButton();
    }
  });

  $(document).on("click", "button#load-events", function() {
    var validDateTimeRegex = new RegExp("^[0-3][0-9]\/[0-1][0-9]\/[0-9][0-9][0-9][0-9] [0-2][0-9]:[0-5][0-9]$");
    if(validDateTimeRegex.test($("#rangestart").val()) && validDateTimeRegex.test($("#rangestart").val())) {
      if(moment($("#rangestart").val(), 'DD/MM/YYYY HH:mm').isValid() && moment($("#rangeend").val(), 'DD/MM/YYYY HH:mm').isValid()) {
        if(ajaxRequests.length == 0) {
          if( moment($("#rangestart").val(), 'DD/MM/YYYY HH:mm') < moment($("#rangeend").val(), 'DD/MM/YYYY HH:mm')) {
            if((moment(start).format('DD/MM/YYYY HH:mm') !== $("#rangestart").val())||(moment(end).format('DD/MM/YYYY HH:mm') !== $("#rangeend").val())) {
              if((chosencameras.length>2)&&(Math.abs(moment(moment($("#rangestart").val())).diff(end, "hour")) > 24)) {
                noty({ text: "Large range detected...", type: "warning" });
                window.setTimeout(function() {
                  noty({ text: "Limiting to first 24 hours...", type: "info" });
                  window.setTimeout(function() {
                    $("#rangeend").val( moment($("#rangestart").val(), "DD/MM/YYYY HH:mm").add(24, "hours").format("DD/MM/YYYY HH:mm") );
                    prequeryTimeline();
                   }, 3000);
                }, 2000);
              }
              else {
                prequeryTimeline();
              }
            }
          }
          else {
            noty({text: 'Range start cannot be after range end!', type: 'error'});
          }
        }
        else {
          noty({ text: "Still processing existing request...", type: "info" });
        }
      }
      else {
        noty({ text: "Invalid start or end date", type: "error"});
      }
    }
    else {
      noty({ text: "Invalid start or end date format, please use format DD/MM/YYYY HH:mm", type: "error"});
    }
  });

  $(document).on("click", "button#liveview", function() {
    toggleMode();
    $(".preset-list-link:not(.show-all-cameras)").parent().show();
    $("button.show-hide-cameras").show();
  });

  $(document).on("click", "button#playback", function() {
    if(poweruser === false) {
      if(chosencameras.length>3) {
        var dialogContent = "<div id=\"monitor-limit-dialog\" class=\"monitor-limit-dialog choose-cameras\"></div>";
        $(dialogContent).dialog({
          modal: true,
          minWidth: 535,
          height: 'auto',
          resizable: false,
          draggable: false,
          autoOpen: true,
          buttons: {
            "Cancel": function() {
              $( this ).dialog( "close" );
            },
            "Enter Playback Mode": function() {
              $(".monitor-stream").each(function() {
                var mid = $(this).attr("data-monitorid");
                if( !$("#monitor-limit-dialog #monitor-stream-thumbnail-" + $(this).attr("data-monitorid") ).parent().parent().parent().hasClass("monitor-selected") ) {
                  chosencameras.splice(chosencameras.indexOf(mid), 1);
                  $(this).remove();
                }
              });
              toggleMode();
              $(this).dialog( "close" );
              $("#preset-selection").dialog("close");
            }
          }
        });
        $("#choose-cameras span") .remove();
        $("#choose-cameras .monitor-stream-thumbnail-item").each(function() {
          $(this).removeClass("monitor-selected");
        });
        $("#monitor-limit-dialog").html($("#choose-cameras").clone().html());
        $("#monitor-limit-dialog").dialog("option", "position", "center");
        $("#monitor-limit-dialog").prepend("<p class=\"monitor-limit-dialog-message\">Playback mode has a maximum of three cameras! Please select the cameras you wish to keep.</p>");
      }
      else {
        toggleMode();
      }

      $("li.preset-list-item a.show-all-cameras").parent().hide();
      $("button.show-hide-cameras").hide();
      $(".zm .ui-dialog li:nth-child(even)").css("background-color", "#000");
      $(".zm .ui-dialog li:nth-child(odd)").css("background-color", "#333");

      $(".preset-list-link:not(.show-all-cameras)").each(function() {
        if($(this).attr("data-value").length > 5) {
          $(this).parent().hide();
        }
      });
    }
  });

  $(document).on("click", "button#choose-cameras-opener", function(event) {
    if($("div#choose-cameras").dialog("isOpen")!==true) {
      toggleShowAllButton();
      $.each(chosencameras, function(i, v) {
        $("#monitor-stream-thumbnail-" + v).parent().parent().parent().addClass("monitor-selected");
        $("#monitor-stream-thumbnail-" + v).parent().append("<span class=\"fa fa-check-circle-o\"></span>");
      });
      $("div#choose-cameras").dialog("open");
    }
    else {
      $("div#choose-cameras").dialog("close");
    }
  });

  $(document).on("click", "button#preset-selection-opener", function(event) {
    if($("div#preset-selection").dialog("isOpen")!==true) {
      $("div#preset-selection").dialog("open");
    }
    else {
      $("div#preset-selection").dialog("close");
    }
  });

  $("#choose-cameras img.monitor-thumbnail").click(function() {
    if(poweruser === false && chosencameras.length >= 3) {
      if($.inArray($(this).attr("data-monitorid"), chosencameras) === -1) {
        noty({ text: "Maxmimum number of cameras selected, please deselect a camera...", type: "error" });
      }
      else {
        $(this).parent().find("span").remove();
        $(this).parent().parent().parent().removeClass("monitor-selected");
        $("#monitor-stream-" + $(this).attr("data-monitorid")).remove();
        chosencameras.splice(chosencameras.indexOf($(this).attr("data-monitorid")), 1);
        requeryTimeline();
      }
    }
    else {
      if(ajaxRequests.length == 0) {
        var monitorClass = $(this).attr("id");
        var monitorName = $(this).attr("alt");
        var monitorId = monitorClass.substr(monitorClass.length - 1);
        $(this).parent().append("<span class=\"fa fa-check-circle-o\"></span>");
        $(this).parent().parent().parent().addClass("monitor-selected");
        if($('#monitor-stream-' + monitorId).length == 0) {
          $.when(
            addMonitor(monitorId)
          ).then(function() {
            noty({ text: "Added camera \"" + monitorName + "\"", type: "success" });
          });
        }
        else {
          $(this).parent().find("span").remove();
          $(this).parent().parent().parent().removeClass("monitor-selected");
          $("#monitor-stream-" + monitorId).remove();
          chosencameras.splice(chosencameras.indexOf(monitorId), 1);
          requeryTimeline();
          // Why was this here ?
          //if(!$(".monitor-stream")[0]) {
          //  liveview = false;
          //}
        }
      }
      else {
        noty({ text: "Please wait, still adding another camera...", type: "warning" });
      }
    }
  });

  $(document).on("click", "div#timeline", function(event) {
    if(liveview === false) {
      if(event.target.className !== "timeline-event" && event.target.className !== "timeline-event-content") {
        paused = false;
        clearPlayback();
        stopped = false;
        newPlayheadTimer();
        var offset = $(this).offset();
        timeline.recalcConversion();
        jumpToNearestEvent(timeline.screenToTime(event.clientX - offset.left));
        if($("button.playpause-button").attr("id") === "play") {
          togglePlayPauseButton();
        }
      }
    }
  });

  $(document).on("click", "button#page-refresh", function(event) {
    event.preventDefault();
    stopLiveStreams();
    noty({ text: "Refreshing...", type: "info" });
    window.setTimeout(function() {
      location.reload();
    }, 2000);
  });

  $(document).on("click", "button#fullscreen", function(event) {
    event.preventDefault();
    toggleFullscreen();
  });

  window.playheadtimer = setInterval(function() {
    if(paused === false && stopped === false && liveview === false) {
      timeline.setCustomTime(moment(timeline.getCustomTime()).add('seconds', 1));
    }
    if((liveview === false)&&(paused === false)) {
      var eventsToPlay = new Array();
      var date = moment(timeline.getCustomTime()).format('YYYY-MM-DD');
      var time = moment(timeline.getCustomTime()).format('HH:mm:ss');
      var datetime = moment(timeline.getCustomTime()).subtract('seconds', 1).format('YYYY-MM-DD HH:mm:ss');
      $("p.playback-date").text(date);
      $("p.playback-time").text(time);
      $.each(activity, function(i, v) {
        if (v.StartTime == datetime) {
            if($.inArray(v.Id, window["currentevents" + v.MonitorId]) == -1) {
              eventsToPlay.push(v.MonitorId + "," + v.Id + "," + v.StartTime + "," + v.Frames);
            }
        }
      });
      if(eventsToPlay.length > 0) {
        $.each(eventsToPlay, function(index, value) {
          var x = value.split(",");
          playEvent(x[0], x[1], x[2], x[3]);
          playing = true;
        });
      }
    }
  }, playheadspeed);

  $(window).resize(function() {
    if(fullscreen === true) {
      $(".monitor-stream-dialog").dialog({ width: ($(window).width()-10), height: ($(window).height()-10) });
      $(".monitor-stream-dialog").dialog("option", "position", "center");

      if($(window).width() > $(window).height()) {
        if(cameras[$(".monitor-stream-dialog").find(".monitor-stream-fullscreen").attr("id").substr(-1, 1) - 1].Width > cameras[$(".monitor-stream-dialog").find(".monitor-stream-fullscreen").attr("id").substr(-1, 1) - 1].Height) {
          $("img.monitor-stream-fullscreen").css("height", "100%");
          $("img.monitor-stream-fullscreen").css("width", "auto");
        }
        else {
          $("img.monitor-stream-fullscreen").css("height", "auto");
          $("img.monitor-stream-fullscreen").css("width", "100%");
        }
      }
      else {
        if(cameras[$(".monitor-stream-dialog").find(".monitor-stream-fullscreen").attr("id").substr(-1, 1) - 1].Width > cameras[$(".monitor-stream-dialog").find(".monitor-stream-fullscreen").attr("id").substr(-1, 1) - 1].Height) {
          $("img.monitor-stream-fullscreen").css("height", "auto");
          $("img.monitor-stream-fullscreen").css("width", "100%");
        }
        else {
          $("img.monitor-stream-fullscreen").css("height", "100%");
          $("img.monitor-stream-fullscreen").css("width", "auto");
        }
      }

      $("img.monitor-stream-fullscreen").css("display", "block");
      $("img.monitor-stream-fullscreen").css("margin", "0 auto");
    }
  });

  /* refresh camera thumbnails every 20 seconds */
  setInterval(function(){
    var timestamp = (new Date()).getTime();
    $("img.monitor-thumbnail").each(function() {
      $(this).attr("src", $(this).attr("src").split('&rand')[0] + "&rand=" + timestamp);
    });
  },20000);

}); /* end document ready */