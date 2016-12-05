jQuery( document ).ready( function($) {

	// hide preloader	
	$('.preloader').hide();

	// hide thankyou message
	$( '.send-email-response' ).hide();

	// show review hyperlocal field on area .change()
	$('#review_area').change( function() {
		// reset value to nothing on change
		$('#review_hyperlocal').val('');

		// update placeholder text with correct locality
		var placeholder_text = $('#review_area option:selected').data('id');
		$('#clr-local').attr("placeholder", 'e.g ' + placeholder_text);
	});

	// ajax on submit
	$('#send-review-email-form').submit( function(e) {

		// get values from input
		var form_values = $('#send-review-email-form').serialize();

		// hide success message if already sent email
		$( '.send-email-response' ).hide();

		var authorname = $('#clr-author').val();

		$.ajax({
	        type: 'POST',
	        url: ajax_send_email_data.ajax_url,
	        data: form_values,

	        beforeSend: function ()
	        {
	        	
	            // Show loader
	            $('.preloader').show();
	        },
	        success: function(data)
	        {
	            // Hide loader
	            $('#send-review-email-form').trigger('reset');
	            $('.preloader').hide();
	            $( '.send-email-response' ).show();

	            $('.send-email-to').html( authorname );
	            
	            $('html, body').animate({
			        scrollTop: $('#wpcontent').offset().top
			    }, 500 );

	        },
	        error: function()
	        {
	            // Show message
	            $( '.send-email-response' ).show();
	            $( '.send-email-response' ).html( '<p>Sorry, there has been an error. Please try again.</p>' );

	        }
	    });

		// prevent form from actually submitting
	    e.preventDefault();
	    return false;

	});

});