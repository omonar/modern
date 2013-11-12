$(document).ready(function() {
  $(document).on("click", ".dialog", function(event) {
    event.preventDefault();
    var monitorId = $(this).attr("data-monitorid");
    $.ajax({
      type: "GET",
      url: 'index.php?view=monitor',
      data: {mid: monitorId},
      success: function(data) {
        var newDialog = data;
        newDialog.dialog({
          modal: true,
          title: "Edit Monitor Settings",
        });
      }
    });
  });
});