$(document).ready(function(){
	$('.view-image').on('click', function(e){
		e.preventDefault();
		$('.modal_slider .slider_imgs').remove();
		var img = $(this).data('image');
		var name = $(this).data('name');
		var order = $(this).data('order');

		$('.image-modal .name').text(name);
		$('.image-modal .order-number').text(order);
			var $button = $(this).children('.slider_imgs').clone();
  			$('.modal_slider').append($button);

		ViewImages();
		$('.image-modal').show();
	});
	$('.close_btn').on('click', function(e){
		e.preventDefault();
		$('.image-modal').hide();
	});

///////////////////////////
  $('#checkbox').change(function(){
    setInterval(function () {
        moveRight();
    }, 3000);
  });
	function ViewImages(){  
		var slideCount = $('#slider ul li').length;
		var slideWidth = $('#slider ul li').width();
		var slideHeight = $('#slider ul li').height();
		var sliderUlWidth = slideCount * slideWidth;
		
		$('#slider').css({ width: slideWidth, height: slideHeight });
		
		$('#slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
		
	    $('#slider ul li:last-child').prependTo('#slider ul');

	    function moveLeft() {
	        $('#slider ul').animate({
	            left: + slideWidth
	        }, 200, function () {
	            $('#slider ul li:last-child').prependTo('#slider ul');
	            $('#slider ul').css('left', '');
	        });
	    };

	    function moveRight() {
	        $('#slider ul').animate({
	            left: - slideWidth
	        }, 200, function () {
	            $('#slider ul li:first-child').appendTo('#slider ul');
	            $('#slider ul').css('left', '');
	        });
	    };

	    $('a.control_prev').click(function (e) {
	    	e.preventDefault();
	        moveLeft();
	    });

	    $('a.control_next').click(function (e) {
	    	e.preventDefault();
	        moveRight();
	    });
	}

});