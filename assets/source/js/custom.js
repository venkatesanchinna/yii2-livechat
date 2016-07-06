$( document ).ready(function() {
	

 
	/******************code for book appointment ****************/
	$('.book_head,.book_head_in').click(function() {
	$('#booking-type').val('in-person');
		if (!$('.plus').hasClass('minus')) {
			$(".book_head").css({
				bottom: '0',
				opacity: '1','z-index':'99999'
			}).show().animate({
				opacity: '1',
				width: '322'
			}, 150).animate({
				bottom: '370'
			}, 300);
			$(".book_form").css({
				bottom: '-370px',
				opacity: '1'
			}).show().delay(150).animate({
				bottom: '11',
				opacity: '1'
			}, 300);

			$('.plus').addClass('minus');
			$('.book_appointment').addClass('mob_form').removeClass('plus-minus') ;
		//	$('body').css('overflow','hidden');
			$('#name').focus();
		} else {
			$(".book_form").animate({
				bottom: '-370px',
				opacity: '0'
			}, 300);
			$(".book_head").animate({
				bottom: '0',
				opacity: '1'
			}, 300).delay(150).animate({
				width: '322'
			}, 150);

			$('.plus').removeClass('minus');
			$('.book_appointment').removeClass('mob_form').addClass('plus-minus') ;;
			$('body').css('overflow','auto');					
		}
		
	});
	
	$('.book_head_in').click(function() {
		if (!$('.plus').hasClass('minus')) {
		$(".book_head").css({'z-index':'1003'
		})
		} else {
		$(".book_head").css({'z-index':'0'});
					
		}
		
	});
	
	
	
	
	
	$(".book_head ").click(function(){
	    $(".fa-plus").toggleClass("fa-minus");
	});
	
	$(".book_head_in ").click(function(){
	    $(".fa-plus").toggleClass("fa-minus");
	});
	
	$(".book_head_video ").click(function(){
	    $(".fa-plus").toggleClass("fa-minus");
	});
	
	 
	 
});
