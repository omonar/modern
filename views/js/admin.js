$(document).ready(function() { /* begin document ready */
  $( "#tabs" ).tabs({
    beforeLoad: function(event, ui) {
      ui.jqXHR.error(function() {
        ui.panel.html(
          "Couldn't load this tab. We'll try to fix this as soon as possible. " +
          "If this wouldn't be a demo." );
      });
    }
  });

  $(document).on("click", ".dialog", function(event) {
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
  });
}); /* end document ready */