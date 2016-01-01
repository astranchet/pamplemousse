// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;

$(function() {
  // Now that the DOM is fully loaded, create the dropzone, and setup the
  // event listeners
  var dropzone = new Dropzone(".dropzone", {
    'dictDefaultMessage' : 'Déposez les images ici !'
  });

  dropzone.on("queuecomplete", function(file) {
    var ids = $.map(dropzone.getAcceptedFiles(), function(file, i) {
      return file.xhr.responseText;
    });
    console.log(ids);
  });
})
