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

$('.filter-year').click(function() {
  $(".months").hide();
  $(this).children().toggle();
});

// Load last images
$("#load").click(function() {
  var lastDate = $(".swipe img").last()[0].getAttribute("data-date-taken");
  $.get("from/"+lastDate, {}, function(data) {
    if (!data) {
      $("#load").hide();
      $("#load").after('<div class="warning">Désolé, c\'est fini !</div>');
    } else {
      $("#gallery").append(data);
      var $content = $(data);
      $grid.append($content).masonry('appended', $content).masonry('reloadItems');

      $(".swipe").on('click', function(e) {
          e.preventDefault();
          var index = $("#gallery .swipe").index($(this));
          openPhotoSwipe(index);
      });
    }
  });
});
