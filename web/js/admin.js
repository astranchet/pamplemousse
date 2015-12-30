// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;

$(function() {
  // Now that the DOM is fully loaded, create the dropzone, and setup the
  // event listeners
  var dropzone = new Dropzone(".dropzone", {
    'dictDefaultMessage' : 'Déposez les images ici !'
  });
})
