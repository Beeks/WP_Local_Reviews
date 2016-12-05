function setStarRating( myInput ) {

	if( jQuery( '.star-rating' ).hasClass( 'display' ) == false ) {

		// get star rating value
		var newrating = jQuery( myInput ).data( 'rating' );

		// set newrating if undefined
		if( newrating == undefined ) {
			var newrating = '0';
		}
		
		// remove active class
		jQuery( '.star-rating li' ).removeClass( 'active' );

		// loop through all stars and give active class
		for( var i = 1; i <= newrating; i++ ) {
			jQuery( '.star-rating li#star-' + i ).addClass( 'active' );
		}

		// update rating choice text
		jQuery('.rating-choice').text( newrating + ' stars' );

	}

}

jQuery(document).ready( function($) {

	// get checked input
	var checkedInput = $( '.star-rating input:checked' );
	setStarRating( checkedInput );

	// Star rating click function 
	$( '.star-rating input' ).click( function() {
		setStarRating( $(this) );
	});

});