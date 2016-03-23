smoothScroll.init();

var $grid = $('#gallery').masonry({
  itemSelector: '.image',
  columnWidth: '.grid-sizer',
  percentPosition: true
});

$grid.imagesLoaded().progress(function() {
  $grid.masonry('layout');
});

$('#home').css({'background-image': 'url(../images/bg' + Math.floor(Math.random()*5) + '.png)'});
