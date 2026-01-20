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

    function cloneTemplate( $repeater, fieldName ) {
        var templateId = $repeater.attr( 'data-wysiwyg-template' );
        if ( ! templateId ) {
            return null;
        }
        var template = document.getElementById( templateId );
        if ( ! template ) {
            return null;
        }
        
        // Generate new unique ID
        var timestamp = Date.now();
        var newId = 'hi_hat_editor_' + fieldName + '_' + timestamp;
        
        // Replace the placeholder __EDITOR_ID__ with the new unique ID
        var html = template.innerHTML.replace( /__EDITOR_ID__/g, newId );
        
        return { html: html, id: newId };
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

            var templateData = cloneTemplate( $repeater, fieldName );
            if ( ! templateData ) {
                return;
            }

            // Remove any existing TinyMCE instance with this ID (shouldn't happen, but be safe)
            if ( typeof tinymce !== 'undefined' ) {
                var existingEditor = tinymce.get( templateData.id );
                if ( existingEditor ) {
                    existingEditor.remove();
                }
            }

            // Append the editor structure
            var $newItem = $( templateData.html );
            $wrapper.append( $newItem );

            // Ensure textarea is visible initially (will be hidden when TinyMCE loads)
            var $textarea = $( '#' + templateData.id );
            if ( $textarea.length ) {
                $textarea.removeAttr( 'aria-hidden' );
            }

            // Initialize the editor - WordPress will initialize TinyMCE
            setTimeout( function() {
                if ( typeof wp !== 'undefined' && wp.editor ) {
                    // Remove any existing editor instance first
                    if ( typeof tinymce !== 'undefined' ) {
                        var existingEditor = tinymce.get( templateData.id );
                        if ( existingEditor ) {
                            existingEditor.remove();
                        }
                    }

                    // Initialize with WordPress defaults
                    wp.editor.initialize( templateData.id, {
                        tinymce: true,
                        quicktags: true,
                        mediaButtons: true
                    } );
                    
                    // Wait for TinyMCE to be ready and ensure proper state
                    if ( typeof tinymce !== 'undefined' ) {
                        var editorReady = false;
                        
                        // Listen for editor being added
                        var addEditorHandler = function( event ) {
                            if ( event.editor && event.editor.id === templateData.id ) {
                                editorReady = true;
                                tinymce.off( 'AddEditor', addEditorHandler );
                                
                                // Wait a bit for DOM to settle
                                setTimeout( function() {
                                    var $wrap = $( '#wp-' + templateData.id + '-wrap' );
                                    if ( $wrap.length ) {
                                        // Ensure wrap is in Visual mode
                                        $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                                        var $textarea = $( '#' + templateData.id );
                                        if ( $textarea.length ) {
                                            $textarea.attr( 'aria-hidden', 'true' );
                                        }
                                        
                                        // Ensure iframe is visible
                                        var $iframe = $wrap.find( 'iframe' );
                                        if ( $iframe.length ) {
                                            $iframe.show();
                                        }
                                        
                                        // Focus the editor
                                        var editor = tinymce.get( templateData.id );
                                        if ( editor && ! editor.isHidden() ) {
                                            editor.focus();
                                        }
                                    }
                                }, 100 );
                            }
                        };
                        tinymce.on( 'AddEditor', addEditorHandler );
                        
                        // Fallback: check if editor is ready
                        var checkReady = setInterval( function() {
                            var editor = tinymce.get( templateData.id );
                            if ( editor && editor.initialized && ! editorReady ) {
                                editorReady = true;
                                clearInterval( checkReady );
                                tinymce.off( 'AddEditor', addEditorHandler );
                                
                                // Ensure wrap is in Visual mode
                                var $wrap = $( '#wp-' + templateData.id + '-wrap' );
                                if ( $wrap.length ) {
                                    $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                                    var $textarea = $( '#' + templateData.id );
                                    if ( $textarea.length ) {
                                        $textarea.attr( 'aria-hidden', 'true' );
                                    }
                                    
                                    // Ensure iframe is visible
                                    var $iframe = $wrap.find( 'iframe' );
                                    if ( $iframe.length ) {
                                        $iframe.show();
                                    }
                                }
                                
                                // Focus the editor
                                if ( ! editor.isHidden() ) {
                                    editor.focus();
                                }
                            }
                        }, 100 );
                        
                        // Clear interval after 5 seconds
                        setTimeout( function() {
                            clearInterval( checkReady );
                            tinymce.off( 'AddEditor', addEditorHandler );
                            
                            // Final focus attempt
                            var editor = tinymce.get( templateData.id );
                            if ( editor && editor.initialized && ! editor.isHidden() ) {
                                editor.focus();
                            } else {
                                var textarea = document.getElementById( templateData.id );
                                if ( textarea ) {
                                    textarea.focus();
                                }
                            }
                        }, 5000 );
                    } else {
                        // No TinyMCE, focus textarea
                        setTimeout( function() {
                            var textarea = document.getElementById( templateData.id );
                            if ( textarea ) {
                                textarea.focus();
                            }
                        }, 200 );
                    }
                    
                    // Intercept tab switching to prevent WordPress errors
                    var $wrap = $( '#wp-' + templateData.id + '-wrap' );
                    if ( $wrap.length ) {
                        $wrap.find( '.wp-switch-editor' ).on( 'click', function( e ) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            
                            var $button = $( this );
                            var mode = $button.hasClass( 'switch-tmce' ) ? 'tmce' : 'html';
                            var editor = typeof tinymce !== 'undefined' ? tinymce.get( templateData.id ) : null;
                            var $editorWrap = $( '#wp-' + templateData.id + '-wrap' );
                            var $textarea = $( '#' + templateData.id );
                            
                            if ( mode === 'tmce' ) {
                                // Switch to Visual
                                $editorWrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                                $editorWrap.find( '.switch-tmce' ).addClass( 'wp-switch-editor-switch' );
                                $editorWrap.find( '.switch-html' ).removeClass( 'wp-switch-editor-switch' );
                                
                                if ( $textarea.length ) {
                                    $textarea.attr( 'aria-hidden', 'true' );
                                }
                                
                                if ( editor ) {
                                    editor.show();
                                    setTimeout( function() {
                                        editor.focus();
                                    }, 50 );
                                }
                            } else {
                                // Switch to Text
                                $editorWrap.removeClass( 'tmce-active' ).addClass( 'html-active' );
                                $editorWrap.find( '.switch-html' ).addClass( 'wp-switch-editor-switch' );
                                $editorWrap.find( '.switch-tmce' ).removeClass( 'wp-switch-editor-switch' );
                                
                                if ( editor && ! editor.isHidden() ) {
                                    editor.save();
                                    editor.hide();
                                }
                                
                                if ( $textarea.length ) {
                                    $textarea.removeAttr( 'aria-hidden' );
                                    setTimeout( function() {
                                        $textarea.focus();
                                    }, 50 );
                                }
                            }
                            
                            return false;
                        } );
                    }
                }
            }, 150 );
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
