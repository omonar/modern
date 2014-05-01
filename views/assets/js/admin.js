var originalValues = {};
var editedValues = {};

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

function toggleSourceTabElements() {
  switch($("#Type option:selected").val()) {
    case "Local":
      $(".localSource").show();
      $(".remoteSource").hide();
      $(".miscSource").hide();
      break;
    case "Remote":
      $(".localSource").hide();
      $(".remoteSource").show();
      $(".miscSource").hide();
      break;
    default:
      $(".localSource").hide();
      $(".remoteSource").hide();
      $(".miscSource").show();
      break;
  }
}

function pullValuesFromForm() {
  originalValues = {};
  $(".monitor-form-value").each(function() {
      originalValues[$(this).attr("id")] = ($(this).val());
  });
}

$(document).ready(function() { /* begin document ready */

  $(".init-colorbox").colorbox({
    iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100,
    onClosed: function() {
      $.cookie('zmSkin', 'modern', { expires: 365   });
    }
  });

  $(document).on("click", "#changepassword-opener", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=changepassword',
      success: function(data) {
        $(".container").html(data); 
      }
    });
  });

  $(document).on("click", "#zm-new-zone-opener", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=newzone',
      success: function(data) {
        $(".container").html(data); 
      }
    });
  });

  $(document).on("click", "#zm-new-zone", function(event) {
    event.preventDefault();
    $.colorbox({
      href: "?skin=classic&view=zone&mid=" + $("select#newzone").val() + "&zid=0",
      iframe: true,
      innerWidth: $(window).width() - 100,
      innerHeight: $(window).height() - 100,
      onClosed: function() {
        $.cookie('zmSkin', 'modern', { expires: 365   });
      }
    });
  });

  $(document).on("click", "#zm-change-state-opener", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=state',
      success: function(data) {
        $(".container").html(data); 
      }
    });
  });

  $(document).on("click", "#zm-change-state", function() {
    noty({text: "Starting state change...", type: "info"});
    var newstate = $("#state").find(":checked").val();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=state',
      data: {changestate: true, newstate: newstate},
      success: function(data) {
        jQuery.ajax({
          type: "POST",
          url: 'index.php?view=getstatus',
          success: function(data) {
            switch(data) {
              case "1":
                if(newstate == "restart" || newstate == "start") {
                  var stateChangeSuccess = true;
                }
                else {
                  var stateChangeSuccess = "info";
                }
                break;
              case "0":
                if(newstate == "stop") {
                  var stateChangeSuccess = true;
                }
                else {
                  var stateChangeSuccess = false;
                }
            }
            if(stateChangeSuccess === true) {
              clearInterval(checkStatus);
              noty({text: 'State changed successfully!', type: 'success'});
              window.setTimeout(function() { window.location.reload(); }, 6000);
            }
            else {
              noty({text: 'Failed to change state...', type: 'error'});
            }
          }
        });
      }
    });
    var checkStatus = setInterval(function() {
      noty({text: "Processing state change...", type: "info"});
    },5000);
  });

  $(document).on("click", "#userlist", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=userlist',
      cache: false,
      success: function(data) {
        $(".container").html(data);
        $(".init-dynamic-colorbox").colorbox({
          iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100,
          onClosed: function() {
            window.location.href = "http://" + location.hostname + "/" + location.pathname + "?skin=modern&view=admin";
          }
        });
      }
    });
  });

  $(document).on("click", "#presetmanagement", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=presetmanagement',
      cache: false,
      success: function(data) {
        $(".container").html(data);
      }
    });
  });

  $(document).on("click", "#changepassword", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=changepassword',
      data: {changepassword: true, currentpassword: $("#currentpassword").val(), newpassword: $("#newpassword").val(), newpasswordconfirmation: $("#newpasswordconfirmation").val()},
      success: function(data) {
        if(data === "success") {
          $("#changepassword-panel").remove();
          noty({text: 'Changed password successfully', type: 'success'});
        }
        else {
          noty({text: data, type: 'error'});
        }
      }
    });
  });

  $(document).on("click", ".delete-user", function(event) {
    event.preventDefault();
    if(confirm("Are you sure you want to permenently delete this user?") === true) {
      var uid = $(this).attr("data-userid");
      if($("#user-table tr").length > 2) {
        jQuery.ajax({
          type:   "POST",
          url: "index.php?view=deleteuser",
          data: {deleteuser: true, uid: uid },
          success: function(data) {
            if(data === "success") {
              $("#delete-user-" + uid).parent().parent().remove();
              noty({text: 'Removed user successfully', type: 'success'});
            }
            else {
              noty({text: data, type: 'error'});
            }
          }
        });
      }
      else {
        noty({text: 'Cannot delete the only user account for this system', type: 'error'});
      }
    }
  });

  $(document).on("click", ".delete-preset", function(event) {
    event.preventDefault();
    if(confirm("Are you sure you want to permenently delete this preset?") === true) {
      var presetID = $(this).attr("data-presetid");
      jQuery.ajax({
        type:   "POST",
        url: "index.php?view=deletepreset",
        data: {presetID: presetID },
        success: function(data) {
          if(data === "success") {
            $("tr#preset-" + presetID).remove();
            noty({text: 'Deleted preset successfully', type: 'success'});
          }
          else {
            noty({text: data, type: 'error'});
          }
        }
      });
    }
  });

  $(document).on("click", "#add-new-preset", function(event) {
    event.preventDefault();
    $("#add-new-preset-panel").show();
    $("#preset-management-panel").hide();
  });

  $(document).on("click", ".edit-preset", function(event) {
    event.preventDefault();
    var monitorIDs = $("tr#preset-" + $(this).attr("data-presetid")).attr("data-monitorids").split(",");
    $(monitorIDs).each(function(index, value) {
      $("option#monitorid-" + value).prop("selected", true);
    });
    var presetName = $(this).parent().parent().attr("data-presetname");
    $("#edit-preset-panel .panel-title").text("Edit Preset - " + presetName);
    $("#edit-preset-panel .panel-title").attr("data-presetname", presetName);
    $("#edit-preset-name").attr("data-presetid", $(this).attr("data-presetid"));
    $("#edit-preset-name").val(presetName);
    $("#edit-preset-panel").show();
    $("#preset-management-panel").hide();
  });

  $(document).on("click", ".back-to-preset-management", function(event) {
    event.preventDefault();
    switch($(this).attr("data-calledfrom")) {
      case "presetmanagement":
        $("#add-new-preset-panel").hide();
        $("#edit-preset-panel").hide();
        $("#preset-management-panel").show();
        break;
      case "userlist":
        $("#user-list-panel").show();
        $("#add-new-user-panel").hide();
        break;
    }
  });

  $(document).on("click", "#save-preset", function(event) {
    event.preventDefault();
    $.ajax({
      url: "index.php?view=updatepreset",
      type: "POST",
      data: {presetID: $("#edit-preset-name").attr("data-presetid"), presetName: $("#edit-preset-name").val(), presetMonitorIDs: $("#edit-preset-cameras").val().join(",")},
      success: function(data) {
        if(data === "success") {
          noty({ text: "Successfully updated preset!", type: "success" });
          jQuery.ajax({
            type: "POST",
            url: 'index.php?view=presetmanagement',
            cache: false,
            success: function(data) {
              $(".container").html(data);
            }
          });
        }
        else {
          console.log(data);
          noty({ text: data, type: "error" });
        }
      }
    });
  });

  $(document).on("click", "#save-new-preset", function(event) {
    event.preventDefault();
    $.ajax({
      url: "index.php?view=newpreset",
      type: "POST",
      data: {presetName: $("#new-preset-name").val(), presetMonitorIDs: $("#new-preset-cameras").val().join(",")},
      success: function(data) {
        if(data === "success") {
          noty({ text: "Successfully created preset!", type: "success" });
          jQuery.ajax({
            type: "POST",
            url: 'index.php?view=presetmanagement',
            cache: false,
            success: function(data) {
              $(".container").html(data);
            }
          });
        }
        else {
          noty({ text: data, type: "error" });
        }
      }
    });
  });

  $(document).on("click", "#updates", function(event) {
    event.preventDefault();
    $.ajax({
      url: "index.php?view=onefiletorulethemall",
      cache: false,
      data: {getVersionFromGithub: true},
      success: function(data) {
        var dataArray = data.split(".");
        var skinArray = skinVersion.split(".");
        var skinUpdateAvailable = false;
        $.each(dataArray, function(index, value) {
          if(parseInt(value) > parseInt(skinArray[index])) {
            skinUpdateAvailable = true;
          }
        });
        if(skinUpdateAvailable === true) {
          $("#skinVersionMessage").replaceWith("<li id=\"skinVersionMessage\"><a href=\"https://github.com/kjvarley/modern/archive/master.zip\"><span class=\"fa fa-picture-o\"></span> Skin Update Available <span class=\"badge\">" + data + "</span></a></li>");
        }
        else {
          $("#skinVersionMessage").replaceWith("<li id=\"skinVersionMessage\"><a href=\"#\"><span class=\"fa fa-picture-o\"></span> Skin Up-To-Date</a></li>");
        }
      }
    });
    $.ajax({
      url: "index.php?view=version",
      cache: false,
      success: function(data) {
        switch(data) {
          case "mismatch":
            $("#zmVersionMessage").replaceWith("<li id=\"zmVersionMessage\"><a href=\"?skin=classic&view=version\" class=\"init-colorbox\"><span class=\"fa fa-camera\"></span> ZoneMinder Version Mismatch!</a></li>");
            break;
          case "noupdate":
            $("#zmVersionMessage").replaceWith("<li id=\"zmVersionMessage\"><a href=\"#\"><span class=\"fa fa-camera\"></span> ZoneMinder Up-To-Date</a></li>");
            break;
          case "update":
            $("#zmVersionMessage").replaceWith("<li id=\"zmVersionMessage\"><a href=\"?skin=classic&view=version\" class=\"init-colorbox\"><span class=\"fa fa-camera\"></span> ZoneMinder Update Available</a></li>");
            break;
        }
      }
    });
  });

  $(document).on("click", "#add-new-monitor", function(event) {
    event.preventDefault();
    $.ajax({
      url: "index.php?view=newmonitor",
      success: function(data) {
        $(".container").html(data);
        toggleSourceTabElements();
      }
    });
  });

  $(document).on("change", "#Type", function() {
    toggleSourceTabElements();
  });

  $(document).on("click", ".newsaveall", function(event) {
    event.preventDefault();
    editedValues = {};
    $(".monitor-form-value").each(function() {
        editedValues[$(this).attr("id")] = ($(this).val());
    });
    $.ajax({
      url: "index.php?view=addnewmonitor",
      data: { editedValues: editedValues },
      success: function(data) {
        if(data === "success") {
          noty({ text: "Successfully added camera..", type: "success" });
          $(".container").empty();
        }
        else {
          console.log(data);
          noty({ text: data, type: "error" });
        }
      }
    });
  });

  $(document).on("click", "#add-new-user-opener", function() {
    $("#add-new-user-panel").show();
    $("#user-list-panel").hide();
  });

  $(document).on("click", "#add-new-user", function() {
    if(($("#Username").val().length > 1) && ($("#Password").val().length > 5) && ($("#Password").val() === $("#PasswordConfirmation").val())){
      var formValues = {};
      $(".new-user-form-value").each(function() {
        formValues[$(this).attr("id")] = $(this).val();
      });
      $.ajax({
        url: "index.php?view=addnewuser",
        data: { formValues: formValues },
        success: function(data) {
          if(data === "success") {
            noty({ text: "Successfully added user...", type: "success" });
            $(".container").empty();
          }
          else {
            console.log(data);
            noty({ text: data, type: "error" });
          }
        }
      });
    }
    else {
      noty({ text: "Check username and password fields!", type: "error" });
    }
  });

}); /* end document ready */