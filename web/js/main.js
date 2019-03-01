smoothScroll.init();

var $grid = $('#gallery').masonry({
  itemSelector: '.image',
  columnWidth: '.grid-sizer',
  percentPosition: true
});

$grid.imagesLoaded().progress(function() {
  $grid.masonry('layout');
});

$('#home').css({'background-image': 'url(/images/bg' + Math.floor(Math.random()*5) + '.jpg)'});


var appendPhotosToGallery = function(content) {
  $grid.append(content).masonry('appended', content).masonry('reloadItems');
  // layout Masonry after each image loads
  $grid.imagesLoaded().progress( function() {
    $grid.masonry('layout');
  });

  $(".swipe").on('click', function(e) {
      e.preventDefault();
      var index = $("#gallery .swipe").index($(this));
      openPhotoSwipe(index);
  });
};

var loadMorePhotos = function() {
  var lastDate = $(".swipe img").last()[0].getAttribute("data-date-taken");
  $.get("from/"+lastDate+window.location.search, {}, function(data) {
    if (!data) {
      $("#load").hide();
    } else {
      appendPhotosToGallery($(data));
    }
  });
};

// Load last images
$("#load").click(loadMorePhotos);
