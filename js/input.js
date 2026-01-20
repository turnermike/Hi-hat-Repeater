(function( $ ) {
	function getFieldType( $repeater ) {
		if ( ! $repeater.length ) {
			return 'wysiwyg';
		}
		var type = $repeater.attr( 'data-field-type' );
		return type ? type : 'wysiwyg';
	}

function getFieldName( $repeater ) {
	if ( ! $repeater.length ) {
		return '';
	}
	return $repeater.data( 'name' ) || $repeater.attr( 'data-name' ) || '';
}

function updateEditorIds( $item, oldId, newId ) {
	$item.find( 'textarea.wp-editor-area' ).attr( 'id', newId );

	$item.find( '[id]' ).each( function() {
		var $el = $( this );
		var currentId = $el.attr( 'id' );
		if ( currentId && currentId.indexOf( oldId ) !== -1 ) {
			var newElementId = currentId.replace( new RegExp( oldId.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ), 'g' ), newId );
			$el.attr( 'id', newElementId );
		}
	} );

	$item.find( '[data-wp-editor-id]' ).attr( 'data-wp-editor-id', newId );
	$item.find( '[for]' ).each( function() {
		var $el = $( this );
		var currentFor = $el.attr( 'for' );
		if ( currentFor && currentFor.indexOf( oldId ) !== -1 ) {
			var newFor = currentFor.replace( new RegExp( oldId.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ), 'g' ), newId );
			$el.attr( 'for', newFor );
		}
	} );
}

	function initialize_field( $field ) {
		var $wrapper = $field.find( '.hi-hat-repeater-items-wrap' );
		var $repeater = $field.find( '.acf-hi-hat-repeater' ).first();
		var fieldType = getFieldType( $repeater );
		var fieldName = getFieldName( $repeater );

		$field.on( 'click', '.hi-hat-repeater-add-button', function( e ) {
			e.preventDefault();

			if ( fieldType === 'textarea' ) {
				var $newItem = $wrapper.find( '.hi-hat-repeater-item' ).first().clone();
				var $textarea = $newItem.find( 'textarea' );
				$textarea.val( '' );
				var timestamp = Date.now();
				$textarea.attr( 'id', 'hi_hat_textarea_' + fieldName + '_' + timestamp );
				$wrapper.append( $newItem );
				return;
			}

			if ( fieldType !== 'wysiwyg' ) {
				return;
			}

			var $firstItem = $wrapper.find( '.hi-hat-repeater-item' ).first();
			if ( ! $firstItem.length ) {
				return;
			}

			var $newItem = $firstItem.clone( true, true );
			var $oldTextarea = $firstItem.find( 'textarea.wp-editor-area' );
			var oldId = $oldTextarea.attr( 'id' );

			if ( ! oldId ) {
				return;
			}

			var timestamp = Date.now();
			var newId = 'hi_hat_editor_' + fieldName + '_' + timestamp;

			updateEditorIds( $newItem, oldId, newId );
			$newItem.find( 'textarea.wp-editor-area' ).val( '' );

			if ( typeof tinymce !== 'undefined' ) {
				var clonedEditor = tinymce.get( newId );
				if ( clonedEditor ) {
					clonedEditor.remove();
				}
			}

			$wrapper.append( $newItem );

			setTimeout( function() {
				if ( typeof wp !== 'undefined' && wp.editor ) {
					wp.editor.initialize( newId, {
						tinymce: true,
						quicktags: true,
						mediaButtons: true
					} );
				}
			}, 100 );
		});

		$field.on( 'click', '.hi-hat-repeater-remove-button', function( e ) {
			e.preventDefault();
			var $wrapper = $( this ).closest( '.acf-hi-hat-repeater' ).find( '.hi-hat-repeater-items-wrap' );
			if ( $wrapper.find( '.hi-hat-repeater-item' ).length > 1 ) {
				var $item = $( this ).closest( '.hi-hat-repeater-item' );
				var editorId = $item.find( 'textarea.wp-editor-area' ).attr( 'id' );

				if ( typeof tinymce !== 'undefined' && editorId ) {
					var editor = tinymce.get( editorId );
					if ( editor ) {
						editor.remove();
					}
				}

				$item.remove();
			}
		});
	}

	if ( typeof acf.add_action !== 'undefined' ) {
		acf.add_action( 'ready_field/type=hi_hat_repeater_wysiwyg', initialize_field );
		acf.add_action( 'append_field/type=hi_hat_repeater_wysiwyg', initialize_field );
		acf.add_action( 'ready_field/type=hi_hat_repeater_textarea', initialize_field );
		acf.add_action( 'append_field/type=hi_hat_repeater_textarea', initialize_field );
	}
})( jQuery );
