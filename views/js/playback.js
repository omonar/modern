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
var currentevent = null;
var currentevents = new Array();
var paused = false;
var breakplayback = false;
var start = null;
var end = null;
var times = "";
var options = {
  'width':  '100%',
  'height': '170px',
  'editable': false,
  'showCustomTime': true,
  'style': 'box'
};

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

function clearCurrentEvent() {
  shouldbeplaying = false;
  playing = false;
  clearTimer(currentevent);
  console.log("cleared current event " + currentevent);
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

function clearTimer(eventId) {
  clearInterval(timers[eventId]);
  timers[eventId] = 0;
  console.log("Cleared timer " + eventId);
}

function requeryTimeline() {
  jQuery("#timeline").css("background-color","red");
  var startformatted = moment(start).format('YYYY-MM-DD HH:mm') + ':00';
  var endformatted = moment(end).format('YYYY-MM-DD HH:mm') + ':00';
  console.log(startformatted);
  console.log(endformatted);
  jQuery.ajax({
    type: "POST",
    url: 'index.php?view=onefiletorulethemall',
    data: {timeline: 'ok', start: startformatted, end: endformatted},
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
    }
  });
}

function playbackFrames(monitorId, eventId, imgarray) {
  var x = 0;
  timers[eventId] = setInterval(function(){
      if (shouldbeplaying === true) {
        if (x < imgarray.length) {
          playing = true;
          displayFrame(monitorId, imgarray[x]);
        }
        else {
          clearTimer(eventId);
          displayFrame(monitorId, '/zm/skins/modern/views/images/onerror.png');
          currentevents.splice(currentevents.indexOf(eventId), 1);
          currentevent=null;
        }
        x++;
      }
      else {
        if(paused === false) {
          clearTimer(eventId);
          displayFrame(monitorId, '/zm/skins/modern/views/images/onerror.png');
        }
        playing = false;
        shouldbeplaying = false;
        currentevents.splice(currentevents.indexOf(eventId), 1);
        currentevent = null;
      }
  },200);
}

function addCamera(monitorId) {
  chosencameras.push(monitorId);
  if(jQuery('#monitor-stream-' + monitorId).length == 0) {
    jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-close unit one-of-three\"><a href=\"#\">X</a></p><img class=\"monitor-stream-image\" src=\"' + cameras[monitorId-1].Protocol + '://' + cameras[monitorId-1].Host + ':' + cameras[monitorId-1].Port + cameras[monitorId-1].Path + '\" onerror=\"imgError(this);\"></div>').appendTo('#monitor-streams');
  }
}

function resumeLiveView() {
  jQuery(chosencameras).each(function(i) {
    addCamera(cameras[i].Id);
  });
}

function playEvent(monitorId, eventId) {
  if(jQuery.inArray(monitorId, chosencameras) == -1) {
    addCamera(monitorId);
  }
  currentevent = eventId;
  currentevents.push(eventId);
  console.log("PLAYING: " + monitorId + " " + eventId);
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
  console.log("jumpToNearestEvent called with " + datetime);
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
        $("#play").html("<img src=\"skins/modern/views/images/playback/pause.png\" alt=\"pause\">");
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

  $("#choose-cameras").dialog({
    autoOpen: false,
    show: {
      effect: "blind",
      duration: 1000
    },
    hide: {
      effect: "explode",
      duration: 1000
    }
  });

  $("#editPresets-dialog").dialog({
    autoOpen: false
  });

  $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create an account": function() {
          var bValid = true;
          allFields.removeClass( "ui-state-error" );
 
          bValid = bValid && checkLength( name, "username", 3, 16 );
          bValid = bValid && checkLength( email, "email", 6, 80 );
          bValid = bValid && checkLength( password, "password", 5, 16 );
 
          bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
          // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
          bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
          bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
 
          if ( bValid ) {
            $( "#users tbody" ).append( "<tr>" +
              "<td>" + name.val() + "</td>" +
              "<td>" + email.val() + "</td>" +
              "<td>" + password.val() + "</td>" +
            "</tr>" );
            $( this ).dialog( "close" );
          }
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      },
      close: function() {
        allFields.val( "" ).removeClass( "ui-state-error" );
      }
    });

  start = new Date();
  end = new Date();
  start.setDate(end.getDate()-1);

  $('#rangestart').datetimepicker({dateFormat: "dd/mm/yy"});
  $('#rangeend').datetimepicker({dateFormat: "dd/mm/yy"});

  $('#rangestart').val(moment(end).format('DD/MM/YYYY') + ' 00:01');
  $('#rangeend').val(moment(end).format('DD/MM/YYYY' + ' 23:59'));

  jQuery( '#timeline' ).
    bind( 'mousewheel DOMMouseScroll', function ( e ) {
        var delta = e.wheelDelta || -e.detail;
        this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
        e.preventDefault();
    });
  jQuery(".monitor-thumbnail").click(function() {
    liveview = true;
    var monitorClass = jQuery(this).attr("id");
    var monitorId = monitorClass.substr(monitorClass.length - 1);
    chosencameras.push(monitorId);
    if(jQuery('#monitor-stream-' + monitorId).length == 0) {
      jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-close unit one-of-three\"><a href=\"#\">X</a></p><img class=\"monitor-stream-image\" src=\"' + cameras[monitorId-1].Protocol + '://' + cameras[monitorId-1].Host + ':' + cameras[monitorId-1].Port + cameras[monitorId-1].Path + '\" onerror=\"imgError(this);\"></div>').appendTo('#monitor-streams');
    }
  });
  jQuery(document).on("click", ".monitor-stream-info-close", function(event) {
    event.preventDefault();
    window.stop();
    var monitorClass = jQuery(this).parent().parent().attr("id");
    var monitorId = monitorClass.substr(monitorClass.length -1);
    chosencameras.splice(chosencameras.indexOf(monitorId), 1);
    jQuery(this).parent().parent().remove();
    jQuery(".monitor-stream-image").each(function() {
      jQuery(this).attr('src', jQuery(this).attr('src').split('&rand')[0] + "&rand=" + new Date().getTime());
    });
  });
  jQuery(document).on("click", ".show-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    liveview = true;
    jQuery(cameras).each(function(i) {
      chosencameras.push(i+1);
      jQuery('<div id=\"monitor-stream-' + (i+1) + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[i].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[i].Events + ' events</p><p class=\"monitor-stream-info-close unit one-of-three\"><a href=\"#\">X</a></p><img class=\"monitor-stream-image\" src=\"' + cameras[i].Protocol + '://' + cameras[i].Host + ':' + cameras[i].Port + cameras[i].Path + '\" onerror=\"imgError(this);\"></div>').appendTo('#monitor-streams');
      i++;
    });
  });
  jQuery(document).on("click", ".hide-all-cameras", function(event) {
    event.preventDefault();
    window.stop();
    jQuery("#monitor-streams").empty();
    chosencameras = [];
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
    $("#pause").html("<img src=\"skins/modern/views/images/playback/play.png\" alt=\"play\">");
    $("#pause").attr("id", "play");
    pausePlayback();
  });
  jQuery(document).on("click", "#play", function(event) {
    event.preventDefault();
    window.stop();
    $("#play").html("<img src=\"skins/modern/views/images/playback/pause.png\" alt=\"pause\">");
    $("#play").attr("id", "pause");
    if (paused === true) {
      resumePlayback();
    }
  });
  jQuery('#rangestart').change(function() {
    console.log("changed");
    if((moment(start).format('DD/MM/YYYY HH:mm') !== $("#rangestart").val())||(moment(end).format('DD/MM/YYYY HH:mm') !== $("#rangeend").val())) {
      start = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
      end = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate()
;      requeryTimeline();
    }
  });
  jQuery('#rangeend').change(function() {
    console.log("changed");
    if((moment(start).format('DD/MM/YYYY HH:mm') !== $("#rangestart").val())||(moment(end).format('DD/MM/YYYY HH:mm') !== $("#rangeend").val())) {
      start = moment($("#rangestart").val(), 'D/M/YYYY h:mm').toDate();
      end = moment($("#rangeend").val(), 'D/M/YYYY h:mm').toDate();
      requeryTimeline();
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
    //clearCurrentEvent();
    shouldbeplaying = false;
    playing = false;
    clearCameraFrames();
    timeline.setCurrentTime(start);
    timeline.applyRange(start, end);
    timeline.redraw();
  });
  jQuery(document).on("click", "#jumpforward", function(event) {
    //clearCurrentEvent();
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
      if(v.Id == parseInt(currentevents[currentevents.length-1])-1) {
        //previous event selected
        shouldbeplaying = false;
        playing = false;
        timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
      }
    });

    /*if(currentevents.length > 0) {
      jQuery.each(activity, function(i, v) {
        if(parseInt(v.Id) === (parseInt(currentevent)-1)) {
          breakplayback = true;
          timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
        }
      });
    }*/

  });
  jQuery(document).on("click", "#fastforward", function(event) {
    console.log("FFWD");
    event.preventDefault();
    window.stop();
    jQuery.each(activity, function(i, v) {
      //console.log(v.Id + " / " + (parseInt(currentevents[currentevents.length-1]) +1) + " / " + currentevents[currentevents.length-1]);
      if(v.Id == (parseInt(currentevents[currentevents.length-1]) + 1)) {
        //next event selected
        //console.log("Match found");
        shouldbeplaying = false;
        playing = false;
        timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
      }
    });

    jQuery("#editPresets").click(function(event) {
      console.log("OK");
      event.preventDefault();
      console.log("OK");
      $("#editPresets-dialog").dialog( "open" );
    });

    /*if(currentevent !== null) {
      jQuery.each(activity, function(i, v) {
        if(parseInt(v.Id) === (parseInt(currentevent)+1)) {
          breakplayback = true;
          clearCurrentEvent();
          timeline.setCurrentTime(Date.createFromMysql(v.StartTime));
          return false;
        }
      });
    }*/

  });

  jQuery(document).on("click", "#choose-cameras-opener", function(event) {
    $("#choose-cameras").dialog( "open" );
  });

  jQuery(".monitor-thumbnail").click(function() {
    liveview = true;
    var monitorClass = jQuery(this).attr("id");
    var monitorId = monitorClass.substr(monitorClass.length - 1);
    chosencameras.push(monitorId);
    if(jQuery('#monitor-stream-' + monitorId).length == 0) {
      jQuery('<div id=\"monitor-stream-' + monitorId + '\" class=\"monitor-stream unit one-of-three\"><div class=\"monitor-stream-info grid\"><p class=\"monitor-stream-info-name unit one-of-three\">' + cameras[monitorId-1].Name + '</p><p class=\"monitor-stream-info-events unit one-of-three\">' + cameras[monitorId-1].Events + ' events</p><p class=\"monitor-stream-info-close unit one-of-three\"><a href=\"#\">X</a></p><img class=\"monitor-stream-image\" src=\"' + cameras[monitorId-1].Protocol + '://' + cameras[monitorId-1].Host + ':' + cameras[monitorId-1].Port + cameras[monitorId-1].Path + '\" onerror=\"imgError(this);\"></div>').appendTo('#monitor-streams');
    }
  });

  jQuery(document).on("click", "#timeline", function(event) {
    if(event.target.className.length === 0) {

      if(liveview === true) {
        clearCameraFrames();
      }

      noty({text: 'Jumping to next event'});
      shouldbeplaying = false;
      playing = false;
      var offset = $(this).offset();
      timeline.recalcConversion();
      jumpToNearestEvent(timeline.screenToTime(event.clientX - offset.left));
      $("#play").html("<img src=\"skins/modern/views/images/playback/pause.png\" alt=\"pause\">");
      $("#play").attr("id", "pause");
    }
  });

  $(window).bind("load", function() {
    getFrames();

    setInterval(function() {
    //console.log(currentevents);
    var date = moment(timeline.getCurrentTime()).format('YYYY-MM-DD');
    var time = moment(timeline.getCurrentTime()).format('HH:mm:ss');
    var datetime = moment(timeline.getCurrentTime()).format('YYYY-MM-DD HH:mm:ss');
    jQuery(".playback-date").text(date);
    jQuery(".playback-time").text(time);
    jQuery.each(activity, function(i, v) {
      if (v.StartTime == datetime) {
        /*if(currentevent !== v.Id) { */
          if(jQuery.inArray(v.Id, currentevents) == -1) {
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