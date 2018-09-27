
$(function(){
    $('.view-source.hide1').hide();
    $a = $('.view-source a');
    $a.on('click', function(event) {
      event.preventDefault();
      $a.not(this).next().slideUp(500);
      $(this).next().slideToggle(500);
    });
});