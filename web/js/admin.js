// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;

$(function() {
  // Now that the DOM is fully loaded, create the dropzone, and setup the
  // event listeners
  var dropzone = new Dropzone(".dropzone", {
    'dictDefaultMessage' : 'Déposez les images ici !'
  });

  dropzone.on("queuecomplete", function(file) {
    var ids = dropzone.getAcceptedFiles().filter(function(file) {
      return file.xhr.status === 200;
    }).map(function(file) {
      return file.xhr.responseText;
    });

    if (ids.length) {
      var url = "edit?" + $.param({ 'ids': ids });
      $("#editForm").on("show.bs.modal", function(e) {
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(url);
      });
      $("#editForm").modal();
    }
  });
})
