var pagenumber = 1;
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

function getEvents(pagenumber, chosencameras, timeframe, orderby, startdatetime, enddatetime) {
  if(typeof(timeframe) == "undefined") {
    timeframe = "all";
  }
  if(chosencameras.length < 1) {
    chosencameras = null;
  }
  if(arguments.length > 2) {
    if(startdatetime.length > 0 && enddatetime.length > 0) {
      var validDateTimeRegex = new RegExp("^[0-3][0-9]\/[0-1][0-9]\/[0-9][0-9][0-9][0-9] [0-2][0-9]:[0-5][0-9]$");
      if(validDateTimeRegex.test(startdatetime) && validDateTimeRegex.test(enddatetime)) {
        if(moment(startdatetime, "DD/MM/YYYY HH:mm").isValid() && moment(enddatetime, "DD/MM/YYYY HH:mm").isValid()) {
          startdatetime = moment(startdatetime, "DD/MM/YYYY HH:mm").format('YYYY-MM-DD HH:mm');
          enddatetime = moment(enddatetime, "DD/MM/YYYY HH:mm").format('YYYY-MM-DD HH:mm');
          $.ajax({
            type: "POST",
            url: 'index.php?view=fetchevents',
            cache: false,
            data: {page: pagenumber, chosencameras: JSON.stringify(chosencameras), timeframe: timeframe, startdatetime: startdatetime, enddatetime: enddatetime, orderby: orderby},
            success: function(data) {
              $("#events").html(data);
              $(".init-dynamic-colorbox").colorbox({
                iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100
              });
              noty({text: "Loaded events data", type: "success"});
            }
          });
        }
        else {
          noty({ text: "ERROR: Invalid start or end date", type: "error" });  
        }
      }
      else {
        noty({ text: "ERROR: Invalid start or end date time. Please use the format DD/MM/YYYY HH:mm.", type: "error" });
      }
    }
  }
  else {
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=fetchevents',
      cache: false,
      data: {page: pagenumber, chosencameras: JSON.stringify(chosencameras), timeframe: timeframe, orderby: orderby},
      success: function(data) {
        $("#events").html(data);
        $(".init-dynamic-colorbox").colorbox({
          iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100
        });
        noty({text: "Loaded events data", type: "success"});
      }
    });
  }
}

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

$(document).ready(function() {

  $('#startdatetime').datetimepicker({
    dateFormat: "dd/mm/yy"
  });

  $('#enddatetime').datetimepicker({
    dateFormat: "dd/mm/yy"
  });

  $(document).on("click", ".radio", function() {
    if($("#custom-timeframe").is(":checked")) {
      $("#custom-range-filters").show();
      $("#startdatetime").focus();
    }
    else {
      $("#custom-range-filters").hide();
    }
  });

  $(document).on("click", "#show-events", function() {
    chosencameras = [];
    $(".monitor-checkbox:checked").each(function() {
      chosencameras.push($(this).attr("value"));
    });
    var timeframe = $("input[name=timeframe]:checked").attr("value");
    var orderBy = [];
    orderBy.push({orderField: $("#orderby-1").val(), orderDirection: $("#orderdirection-1").val()});
    orderBy.push({orderField: $("#orderby-2").val(), orderDirection: $("#orderdirection-2").val()});
    orderBy.push({orderField: $("#orderby-3").val(), orderDirection: $("#orderdirection-3").val()});
    orderBy.push({orderField: $("#orderby-4").val(), orderDirection: $("#orderdirection-4").val()});
    orderBy.push({orderField: $("#orderby-5").val(), orderDirection: $("#orderdirection-5").val()});
    if(timeframe == "custom") {
      getEvents(null, chosencameras, timeframe, orderBy, $("#startdatetime").val(), $("#enddatetime").val());
    }
    else {
      getEvents(null, chosencameras, timeframe, orderBy);
      
    }
  });

  $(document).on("click", ".event-checkbox", function() {
    $("#delete-selected-events").removeClass("disabled");
    $("#export-selected-events").removeClass("disabled");
  });

  $(document).on("click", "#delete-selected-events", function() {
    var eventIDs = [];
    $(".event-checkbox:checked").each(function() {
      eventIDs.push($(this).attr("data-eid"));
    });
    var eventID = $(this).attr("data-eid");
    if(confirm("Are you sure you want to delete events " + eventIDs.join(", ") + "?") === true) {
      jQuery.ajax({
        type: "POST",
        url: 'index.php?view=deleteevents',
        cache: false,
        data: {deleteevents: "multiple", eids: JSON.stringify(eventIDs)},
        success: function(data) {
          if(data === "success") {
            $(eventIDs).each(function(index, value) {
              $("#delete-event-" + value).parent().parent().remove();
            });
            noty({text: "Successfully deleted events " + eventIDs.join(", "), type: 'success'});
          }
          else {
            noty({text: data, type: 'error'});
          }
        }
      });
    }
  });

  $(document).on("click", "#export-selected-events", function() {
    var eventIDs = {};
    $(".event-checkbox:checked").each(function() {
      eventIDs[$(this).attr("data-eid")] = $(this).attr("data-monitorid");
    });
    if(confirm("Are you sure you want to export the selected events?") === true) {
      window.open('index.php?view=exportevents&exportevents=multiple&eids=' + JSON.stringify(eventIDs), '_blank');
      noty({text: 'Launched download of events', type: 'success'});
    }
  });

  $(document).on("click", "button#clear-filters", function(event) {
    event.preventDefault();
    $("select").prop('selectedIndex', 0);
    $("input[name=timeframe]:checked").prop('checked', false);
    $("input[name=timeframe]").first().prop('checked', true);
    $(".monitor-checkbox").prop("checked", true);
  });

  $(document).on("click", ".delete-event", function(event) {
    event.preventDefault();
    var eventID = $(this).attr("data-eid");
    if(confirm("Are you sure you want to delete event " + eventID + "?") === true) {
      jQuery.ajax({
        type: "POST",
        url: 'index.php?view=deleteevents',
        cache: false,
        data: {deleteevents: "single", eid: eventID},
        success: function(data) {
          if(data != "error") {
            $(eventIDs).each(function(index, value) {
              $("#export-event-" + value).parent().parent().remove();
            });
            noty({text: "Successfully exported event " + eventID, type: 'success'});
            //window.location(data)
          }
          else {
            noty({text: data, type: 'error'});
          }
        }
      });
    }
  });

  $(document).on("click", "#check-all", function() {
    if($(this).prop('checked')) {
      $("input.event-checkbox").prop('checked', true);
      $("#delete-selected-events").removeClass("disabled");
      $("#export-selected-events").removeClass("disabled");
    }
    else {
      $("input.event-checkbox").prop('checked', false);
      $("#delete-selected-events").addClass("disabled");
      $("#export-selected-events").addClass("disabled");
    }
  });

  $(document).on("click", ".export-event", function(event) {
    event.preventDefault();
    var eventID = $(this).attr("data-eid");
    var monitorID = $(this).attr("data-mid");
    if(confirm("Are you sure you want to export event " + eventID + "?") === true) {
      window.open('index.php?view=exportevents&exportevents=single&eid=' + eventID + "&mid=" + monitorID);
      noty({text: 'Launched download of events', type: 'success'});
    }
  });

  $(document).on("click", ".pagination a", function(event) {
    event.preventDefault();
    var goToPage = $(this).text();
    if($(this).attr("id") == "events-next-page") {
      var goToPage = parseInt($(".pagination li.active a").text()) + 1;
    }
    if($(this).attr("id") == "events-previous-page") {
      var goToPage = parseInt($(".pagination li.active a").text()) - 1;
    }
    chosencameras = [];
    $(".monitor-checkbox:checked").each(function() {
      chosencameras.push($(this).attr("value"));
    });
    var timeframe = $("input[name=timeframe]:checked").attr("value");
    if(timeframe == "custom") {
      console.log("Calling custom with " + chosencameras + " " + timeframe + " " + $("#startdatetime").val() + " " + $("#enddatetime").val())
      getEvents(goToPage, chosencameras, timeframe, $("#orderby").val(), $("#orderdirection").val(), $("#startdatetime").val(), $("#enddatetime").val());
    }
    else {
      getEvents(goToPage, chosencameras, timeframe, $("#orderby").val(), $("#orderdirection").val());
    }
  });

});