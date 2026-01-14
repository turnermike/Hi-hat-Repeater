(function( $ ) {
	function initialize_field( $field ) {
		$field.on( 'click', '.hi-hat-repeater-add-button', function( e ) {
			e.preventDefault();
			var $wrapper = $field.find( '.hi-hat-repeater-items-wrap' );
			var $item = $wrapper.find( '.hi-hat-repeater-item' ).first().clone();
			$item.find( 'textarea' ).val( '' );
			$wrapper.append( $item );
		});

		$field.on( 'click', '.hi-hat-repeater-remove-button', function( e ) {
			e.preventDefault();
			var $wrapper = $( this ).closest( '.acf-hi-hat-repeater' ).find( '.hi-hat-repeater-items-wrap' );
			if ( $wrapper.find( '.hi-hat-repeater-item' ).length > 1 ) {
				$( this ).closest( '.hi-hat-repeater-item' ).remove();
			}
		});
	}

	if ( typeof acf.add_action !== 'undefined' ) {
		acf.add_action( 'ready_field/type=hi_hat_repeater', initialize_field );
		acf.add_action( 'append_field/type=hi_hat_repeater', initialize_field );
	}
})( jQuery );
