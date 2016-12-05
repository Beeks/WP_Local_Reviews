jQuery(document).ready( function($) {

	/* USER GUIDE AJAX */
	// hide success message
	$( '.send-user-guide-response' ).hide();

	// ajax on submit
	$('#send-user-guide').click( function(e) {
		e.preventDefault;

		// get values from input
		var emailrecipient = $('#user_guide_email').val();

		// remove error class if present
		$( '#user_guide_email' ).removeClass( 'error-input' );

		// check to see if email field has been filled in
		if( emailrecipient == '' ) {

			// add error class & amend response text
			$( '#user_guide_email' ).addClass( 'error-input' );
			$( '.send-user-guide-response' ).html( '<p>Please specify an email address.</p>' );
			$( '.send-user-guide-response' ).show();

		// else do ajax
		} else {

			// hide success message if already sent email
			$( '.send-user-guide-response' ).hide();

			$.ajax({
		        type: 'POST',
		        url: ajax_send_user_guide.ajax_url,
		        data: {
		        	recipient: emailrecipient,
		        	action: 'send_user_guide'
		        },

		        beforeSend: function ()
		        {
		            // Show loader
		            $('.preloader').show();
		        },
		        success: function(data)
		        {
		            // Hide loader
		            $('#send-user-guide-form').trigger('reset');
		            $('.preloader').hide();
		            $( '.send-user-guide-response' ).html( '<p>The user guide has been sent.</p>' );
		            $( '.send-user-guide-response' ).show();

		        },
		        error: function()
		        {
		            // Show message
		            $( '.send-user-guide-response' ).show();
		            $( '.send-user-guide-response' ).html( '<p>Sorry, there has been an error. Please try again.</p>' );

		        }
		    });

		// endif
		}

		// prevent form from actually submitting
	    e.preventDefault();
	    return false;

	});

});