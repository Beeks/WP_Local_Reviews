jQuery( document ).ready( function($) {

	// hide preloader
	$('.preloader').hide();

	// hide response
	//$('.post-review-response').hide();

	// show review hyperlocal field on area .change()
	$('#review_area').change( function() {
		// show hidden field
		$('#review_hyperlocal').show();
		$('#review_hyperlocal').removeClass( 'no-display' );

		// reset value to nothing on change
		$('#review_hyperlocal').val('');

		// update placeholder text with correct locality
		var placeholder_text = $('#review_area option:selected').data('id');
		$('#review_hyperlocal').attr("placeholder", 'Area (e.g ' + placeholder_text + ")");
	});

	// ajax on submit
	$('#post-review-form').submit( function(e) {

		$("#post-review-form .service-checkbox label").removeClass( 'checkbox-error' );

		if( $("#post-review-form .service-checkbox").length ) {

			var checked = $("#post-review-form .service-checkbox input:checked").length > 0;

			if ( !checked ) {
	    		$("#post-review-form .service-checkbox label").addClass( 'checkbox-error' );
	        	alert("Please check at least one service checkbox.");
	        	return false;
	    	}

	    }

		// get values from input
		var form_values = $('#post-review-form').serialize();

		// hide success message if already sent email
		$( '.post-review-response' ).hide();

		$.ajax({
	        type: 'POST',
	        dataType: 'json',
	        url: ajax_post_review.ajax_url,
	        data: form_values,
	        
	        beforeSend: function ()
	        {	
	            // Show loader
	            $('.preloader').show();
	        },
	        success: function( data )
	        {
	            // Hide loader
	            $( '#post-review-form' ).hide();
	            $( '.post-review-response' ).show();
	            $( '.post-review-response' ).html( data.thankyou );

	            if( data.review_again ) {
	            	$( '.post-review-response' ).append( data.review_again );
	            }
	            $( '#post-review-form' )[0].reset();
	        },
	        error: function()
	        {
	            // Show message
	            $( '.post-review-response' ).show();
	            $( '.post-review-response' ).html( '<p>Sorry, there has been an error. Please try again.</p>' );

	        }

    	});

		// prevent form from actually submitting
	    e.preventDefault();
	    return false;

	});

});