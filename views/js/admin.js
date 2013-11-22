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

$(document).ready(function() { /* begin document ready */
  $(".init-colorbox").colorbox({
    iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100
  });

  /*$(document).on("click", ".dialog", function(event) {
    event.preventDefault();
    var monitorId = $(this).attr("data-monitorid");
    var outputHolder = $("<div id=\"tab-content-" + monitorId + "\"></div>").appendTo("body");
    outputHolder.load($(this).attr("href"), null, function() {
      outputHolder.dialog({
        modal: true,
        title: "Edit Monitor Settings",
        width: 'auto',
        resizable: false,
        draggable: true,
        dialogClass: "modal-" + monitorId
      });
    });
    return false;
  });*/

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

  $(document).on("click", "#userlist", function(event) {
    event.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: 'index.php?view=userlist',
      cache: false,
      success: function(data) {
        $(".container").html(data);
        $(".init-dynamic-colorbox").colorbox({
          iframe: true, innerWidth: $(window).width() - 100, innerHeight: $(window).height() - 100
        });
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

}); /* end document ready */