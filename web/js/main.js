smoothScroll.init();

var $grid = $('#gallery').masonry({
  itemSelector: '.image',
  columnWidth: '.grid-sizer',
  percentPosition: true
});

$grid.imagesLoaded().progress(function() {
  $grid.masonry('layout');
});
