;(function( $ ) {
    var editorContentStyle = 'body { color: #121212; background-color: #fff; }';
    var baseTinymceConfig = {
        toolbar1: 'formatselect bold italic strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_adv',
        toolbar2: 'forecolor pastetext removeformat charmap outdent indent undo redo wp_help',
        plugins: 'charmap colorpicker hr lists paste tabfocus textcolor fullscreen wordpress wpeditimage wpgallery wplink wpdialogs wpview',
        resize: 'vertical',
        menubar: false,
        wpautop: true,
        body_class: 'id post-type-post post-status-publish post-format-standard'
    };

    function ensureVisual(editorId) {
        if ( ! editorId ) {
            return;
        }

        if ( typeof switchEditors !== 'undefined' && switchEditors.go ) {
            switchEditors.go( editorId, 'tmce' );
            return;
        }

        var $wrap = $( '#wp-' + editorId + '-wrap' );
        var $textarea = $( '#' + editorId );
        if ( $wrap.length ) {
            $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
        }
        if ( $textarea.length ) {
            $textarea.attr( 'aria-hidden', 'true' );
        }
        if ( typeof tinymce !== 'undefined' ) {
            var editor = tinymce.get( editorId );
            if ( editor ) {
                editor.show();
            }
        }
    }
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

    function cloneTemplate( $repeater, fieldSlug ) {
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
        var newId = 'hi_hat_editor_' + fieldSlug + '_' + timestamp;
        
        // Replace the placeholder __EDITOR_ID__ with the new unique ID
        var html = template.innerHTML.replace( /__EDITOR_ID__/g, newId );
        
        return { html: html, id: newId };
    }

    function updateRemoveButtons( $wrapper ) {
        var show = $wrapper.find( '.hi-hat-repeater-item' ).length > 1;
        $wrapper.find( '.hi-hat-repeater-remove-button' ).toggle( show );
    }

    function initialize_field( $field ) {
        var $wrapper = $field.find( '.hi-hat-repeater-items-wrap' );
        var $repeater = $field.find( '.acf-hi-hat-repeater' ).first();
        var fieldType = getFieldType( $repeater );
        var fieldName = getFieldName( $repeater );
        var fieldSlug = fieldName.replace( /[^\w-]/g, '_' );

        updateRemoveButtons( $wrapper );

        // Remove any existing handlers to avoid duplicates
        $field.off( 'click', '.hi-hat-repeater-add-button' );
        
        $field.on( 'click', '.hi-hat-repeater-add-button', function( e ) {
            console.log( 'Add button clicked', { fieldType: fieldType, fieldName: fieldName } );
            e.preventDefault();
            e.stopPropagation();

            if ( fieldType === 'textarea' ) {
                var $newItem = $wrapper.find( '.hi-hat-repeater-item' ).first().clone();
                var $textarea = $newItem.find( 'textarea' );
                $textarea.val( '' );
                var timestamp = Date.now();
                $textarea.attr( 'id', 'hi_hat_textarea_' + fieldName + '_' + timestamp );
                $wrapper.append( $newItem );
                updateRemoveButtons( $wrapper );
                return;
            }

            if ( fieldType === 'image' ) {
                var $existingItem = $wrapper.find( '.hi-hat-repeater-item' ).first();
                var $newItem;
                var timestamp = Date.now();
                var newInputId = 'hi_hat_image_' + fieldSlug + '_' + timestamp;
                
                // Get the field name from an existing input or from the data attribute
                var fieldNameAttr = fieldName;
                var $existingInput = $wrapper.find( '.hi-hat-repeater-image-input' ).first();
                if ( $existingInput.length ) {
                    // Extract field name from existing input's name attribute (remove the [])
                    var existingName = $existingInput.attr( 'name' );
                    if ( existingName ) {
                        fieldNameAttr = existingName.replace( /\[\]$/, '' );
                    }
                }
                
                if ( $existingItem.length ) {
                    // Clone existing item
                    $newItem = $existingItem.clone();
                    
                    // Clear the image data
                    var $input = $newItem.find( '.hi-hat-repeater-image-input' );
                    $input.val( '' ).attr( 'id', newInputId );
                    
                    // Clear preview
                    var $preview = $newItem.find( '.hi-hat-repeater-image-preview' );
                    $preview.hide().find( 'img' ).remove();
                    
                    // Update button text
                    var $selectButton = $newItem.find( '.hi-hat-repeater-image-select-button' );
                    $selectButton.text( 'Select Image' );
                    
                    // Hide remove image button
                    var $removeImageButton = $newItem.find( '.hi-hat-repeater-image-remove-button' );
                    $removeImageButton.hide();
                } else {
                    // Create new item from scratch using jQuery for safety
                    $newItem = $( '<div class="hi-hat-repeater-item"></div>' );
                    var $wrapperDiv = $( '<div class="hi-hat-repeater-image-wrapper"></div>' );
                    var $input = $( '<input>', {
                        type: 'hidden',
                        class: 'hi-hat-repeater-image-input',
                        name: fieldNameAttr + '[]',
                        value: '',
                        id: newInputId
                    } );
                    var $preview = $( '<div class="hi-hat-repeater-image-preview" style="display:none;"></div>' );
                    var $selectButton = $( '<button>', {
                        type: 'button',
                        class: 'button hi-hat-repeater-image-select-button',
                        text: 'Select Image'
                    } );
                    var $removeImageButton = $( '<button>', {
                        type: 'button',
                        class: 'button hi-hat-repeater-image-remove-button',
                        style: 'display:none;',
                        text: 'Remove Image'
                    } );
                    var $removeButton = $( '<a>', {
                        href: '#',
                        class: 'hi-hat-repeater-remove-button button button-small',
                        style: 'margin-top:14px',
                        text: 'Remove'
                    } );
                    
                    $wrapperDiv.append( $input, $preview, $selectButton, $removeImageButton );
                    $newItem.append( $wrapperDiv, $removeButton );
                }
                
                $wrapper.append( $newItem );
                updateRemoveButtons( $wrapper );
                return;
            }

            if ( fieldType !== 'wysiwyg' ) {
                console.warn( 'Unknown field type:', fieldType );
                return;
            }

            // Try cloning an existing working editor first
            var $existingItem = $wrapper.find( '.hi-hat-repeater-item' ).first();
            var $newItem;
            var newEditorId;
            
            if ( $existingItem.length ) {
                console.log( 'Cloning existing editor item' );
                // Clone existing editor
                $newItem = $existingItem.clone();
                var timestamp = Date.now();
                newEditorId = 'hi_hat_editor_' + fieldSlug + '_' + timestamp;
                
                // Remove existing TinyMCE instance from cloned item
                var $oldTextarea = $newItem.find( 'textarea.wp-editor-area' );
                var oldEditorId = $oldTextarea.attr( 'id' );
                
                if ( typeof tinymce !== 'undefined' && oldEditorId ) {
                    var oldEditor = tinymce.get( oldEditorId );
                    if ( oldEditor ) {
                        oldEditor.remove();
                    }
                }
                
                // Remove any TinyMCE iframes and containers from the clone
                $newItem.find( '.mce-container, iframe, .mce-tinymce' ).remove();
                // Drop the existing toolbar markup so WordPress can rebuild it cleanly
                $newItem.find( '.wp-editor-tools, .wp-editor-tabs, .wp-media-buttons' ).remove();
                
                // Replace all IDs in the cloned item that reference the old editor ID
                if ( oldEditorId ) {
                    var idRegex = new RegExp( oldEditorId.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ), 'g' );
                    $newItem.find( '[id]' ).each( function() {
                        var $el = $( this );
                        var oldId = $el.attr( 'id' );
                        if ( oldId && oldId.indexOf( oldEditorId ) !== -1 ) {
                            var newId = oldId.replace( idRegex, newEditorId );
                            $el.attr( 'id', newId );
                        }
                    } );
                    
                    // Also update data attributes and other references
                    $newItem.find( '[data-wp-editor-id="' + oldEditorId + '"]' ).attr( 'data-wp-editor-id', newEditorId );
                    $newItem.find( '[for="' + oldEditorId + '"]' ).attr( 'for', newEditorId );
                }
                
                // Update textarea
                $oldTextarea.attr( 'id', newEditorId ).val( '' ).removeAttr( 'aria-hidden' );
                
                // Ensure the wrap has the correct ID
                var $oldWrap = $newItem.find( '.wp-editor-wrap' );
                if ( $oldWrap.length ) {
                    var oldWrapId = $oldWrap.attr( 'id' );
                    if ( oldWrapId ) {
                        $oldWrap.attr( 'id', 'wp-' + newEditorId + '-wrap' );
                    }
                }
                
            } else {
                // Fallback to template if no existing item
                console.log( 'No existing item, using template' );
                var templateData = cloneTemplate( $repeater, fieldSlug );
                if ( ! templateData ) {
                    console.error( 'Failed to clone template' );
                    return;
                }
                newEditorId = templateData.id;
                $newItem = $( templateData.html );
            }
            
            // Append the new item
            console.log( 'Appending new item with editor ID:', newEditorId );
            $wrapper.append( $newItem );
            
            updateRemoveButtons( $wrapper );

            // Force existing editors to switch back to Visual so they keep their toolbar
            $wrapper.find( 'textarea.wp-editor-area' ).each( function() {
                var existingId = this.id;
                if ( existingId && existingId !== newEditorId ) {
                    ensureVisual( existingId );
                }
            } );

            // Get references to the editor elements
            var $textarea = $( '#' + newEditorId );
            var $wrap = $( '#wp-' + newEditorId + '-wrap' );
            var $container = $( '#wp-' + newEditorId + '-editor-container' );
            
            if ( ! $textarea.length || ! $wrap.length ) {
                console.error( 'Editor elements not found:', { textarea: $textarea.length, wrap: $wrap.length, id: newEditorId } );
                return;
            }

            // Ensure textarea is visible initially and properly configured
            $textarea.show().removeAttr( 'aria-hidden' ).css( 'display', 'block' );
            
            // Ensure container exists and is set up correctly
            if ( ! $container.length ) {
                // Create container if it doesn't exist
                $textarea.wrap( '<div id="wp-' + newEditorId + '-editor-container" class="wp-editor-container"></div>' );
                $container = $( '#wp-' + newEditorId + '-editor-container' );
            }
            
            // Ensure wrap has correct classes
            $wrap.addClass( 'wp-core-ui wp-editor-wrap tmce-active' ).removeClass( 'html-active' );
            
            // Wait for WordPress editor to be ready, then initialize
            var initEditor = function() {
                if ( typeof tinymce !== 'undefined' ) {
                    var existingEditor = tinymce.get( newEditorId );
                    if ( existingEditor ) {
                        existingEditor.remove();
                    }
                }

                if ( typeof wp !== 'undefined' && wp.editor && wp.editor.initialize ) {
                    try {
                        var tinymceConfig = $.extend( true, {}, baseTinymceConfig, {
                            selector: '#' + newEditorId,
                            content_style: editorContentStyle
                        } );

                        wp.editor.initialize( newEditorId, {
                            tinymce: tinymceConfig,
                            quicktags: true,
                            mediaButtons: true
                        } );
                    } catch ( e ) {
                        console.error( 'wp.editor.initialize error:', e );
                    }
                } else {
                    console.warn( 'wp.editor.initialize not available' );
                }
            };
            
            // Wait a bit for DOM to settle, then initialize
            setTimeout( initEditor, 300 );
            
            // Set up tab switching handlers - WordPress should handle this, but ensure it works
            setTimeout( function() {
                var $tmceButton = $( '#' + newEditorId + '-tmce' );
                var $htmlButton = $( '#' + newEditorId + '-html' );
                
                if ( $tmceButton.length && $htmlButton.length ) {
                    // Use WordPress's switchEditors if available
                    if ( typeof switchEditors !== 'undefined' ) {
                        $tmceButton.off( 'click' ).on( 'click', function( e ) {
                            e.preventDefault();
                            switchEditors.go( newEditorId, 'tmce' );
                            return false;
                        } );
                        
                        $htmlButton.off( 'click' ).on( 'click', function( e ) {
                            e.preventDefault();
                            switchEditors.go( newEditorId, 'html' );
                            return false;
                        } );
                    } else {
                        // Fallback: manual tab switching
                        $tmceButton.off( 'click' ).on( 'click', function( e ) {
                            e.preventDefault();
                            var editor = typeof tinymce !== 'undefined' ? tinymce.get( newEditorId ) : null;
                            $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                            $textarea.attr( 'aria-hidden', 'true' );
                            if ( editor ) {
                                editor.show();
                                editor.focus();
                            }
                            return false;
                        } );
                        
                        $htmlButton.off( 'click' ).on( 'click', function( e ) {
                            e.preventDefault();
                            var editor = typeof tinymce !== 'undefined' ? tinymce.get( newEditorId ) : null;
                            $wrap.removeClass( 'tmce-active' ).addClass( 'html-active' );
                            if ( editor && ! editor.isHidden() ) {
                                editor.save();
                                editor.hide();
                            }
                            $textarea.removeAttr( 'aria-hidden' );
                            $textarea.focus();
                            return false;
                        } );
                    }
                }
            }, 600 );
            
            // Wait for editor to be initialized and verify it's working
            if ( typeof tinymce !== 'undefined' ) {
                // Listen for editor being added
                var addEditorHandler = function( event ) {
                    if ( event.editor && event.editor.id === newEditorId ) {
                        tinymce.off( 'AddEditor', addEditorHandler );
                        
                        // Ensure the editor is visible and has toolbar
                        setTimeout( function() {
                            var editor = tinymce.get( newEditorId );
                            if ( editor ) {
                                // Make sure editor is shown
                                if ( editor.isHidden() ) {
                                    editor.show();
                                }
                                
                                // Ensure wrap is in Visual mode
                                $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                                $textarea.attr( 'aria-hidden', 'true' );
                                
                                // Focus the editor
                                editor.focus();
                            }
                        }, 200 );
                    }
                };
                tinymce.on( 'AddEditor', addEditorHandler );
                
                // Fallback: check if editor is ready
                var checkReady = setInterval( function() {
                    var editor = tinymce.get( newEditorId );
                    if ( editor && editor.initialized ) {
                        clearInterval( checkReady );
                        tinymce.off( 'AddEditor', addEditorHandler );
                        
                        // Ensure editor is visible
                        if ( editor.isHidden() ) {
                            editor.show();
                        }
                        
                        // Ensure wrap is in Visual mode
                        $wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
                        $textarea.attr( 'aria-hidden', 'true' );
                        
                        // Focus the editor
                        editor.focus();
                    }
                }, 200 );
                
                // Clear interval after 5 seconds
                setTimeout( function() {
                    clearInterval( checkReady );
                    tinymce.off( 'AddEditor', addEditorHandler );
                    
                    // Final check - if editor still not initialized, show textarea
                    var editor = tinymce.get( newEditorId );
                    if ( ! editor || ! editor.initialized ) {
                        // Fallback to textarea mode
                        $wrap.removeClass( 'tmce-active' ).addClass( 'html-active' );
                        $textarea.removeAttr( 'aria-hidden' ).show();
                        $textarea.focus();
                    }
                }, 5000 );
            }
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

                updateRemoveButtons( $wrapper );
            }
        });
    }

    // Set up image field handlers once at document level (not per field)
    // This ensures they work even if fields are added dynamically
    $( document ).ready( function() {
        // Handle image selection - use ACF's media uploader pattern
        $( document ).on( 'click', '.hi-hat-repeater-image-select-button', function( e ) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $( this );
            var $item = $button.closest( '.hi-hat-repeater-item' );
            if ( ! $item.length ) {
                console.error( 'Could not find repeater item' );
                return;
            }
            
            var $input = $item.find( '.hi-hat-repeater-image-input' );
            var $preview = $item.find( '.hi-hat-repeater-image-preview' );
            var $removeImageButton = $item.find( '.hi-hat-repeater-image-remove-button' );
            var attachmentId = $input.val();

            // Check if wp.media is available - wait a bit if not ready
            if ( typeof wp === 'undefined' || ! wp.media ) {
                console.error( 'WordPress media library is not available. Make sure acf_enqueue_uploader() is called.' );
                // Try to wait for it
                setTimeout( function() {
                    if ( typeof wp !== 'undefined' && wp.media ) {
                        $button.trigger( 'click' );
                    } else {
                        alert( 'Media library is not available. Please refresh the page.' );
                    }
                }, 100 );
                return;
            }

            // Create a new media frame for this selection
            var mediaFrame = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // When an image is selected, run a callback
            mediaFrame.on( 'select', function() {
                var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
                var imageId = attachment.id;
                $input.val( imageId );
                
                // Use thumbnail if available, otherwise use full size
                var imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                if ( ! imageUrl && attachment.url ) {
                    imageUrl = attachment.url;
                }
                
                if ( imageUrl ) {
                    $preview.html( '<img src="' + imageUrl + '" alt="' + ( attachment.alt || '' ) + '" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />' ).show();
                    $button.text( 'Change Image' );
                    $removeImageButton.show();
                }
            });

            // Set the selected image if one exists
            if ( attachmentId ) {
                var attachment = wp.media.attachment( attachmentId );
                attachment.fetch().done( function() {
                    mediaFrame.state().get( 'selection' ).add( [ attachment ] );
                } );
            }

            // Open the media frame
            mediaFrame.open();
        });

        // Handle image removal
        $( document ).on( 'click', '.hi-hat-repeater-image-remove-button', function( e ) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $( this );
            var $item = $button.closest( '.hi-hat-repeater-item' );
            var $input = $item.find( '.hi-hat-repeater-image-input' );
            var $preview = $item.find( '.hi-hat-repeater-image-preview' );
            var $selectButton = $item.find( '.hi-hat-repeater-image-select-button' );

            $input.val( '' );
            $preview.hide().find( 'img' ).remove();
            $selectButton.text( 'Select Image' );
            $button.hide();
        } );
    } );

    if ( typeof acf.add_action !== 'undefined' ) {
        acf.add_action( 'ready_field/type=hi_hat_repeater_wysiwyg', initialize_field );
        acf.add_action( 'append_field/type=hi_hat_repeater_wysiwyg', initialize_field );
        acf.add_action( 'ready_field/type=hi_hat_repeater_textarea', initialize_field );
        acf.add_action( 'append_field/type=hi_hat_repeater_textarea', initialize_field );
        acf.add_action( 'ready_field/type=hi_hat_repeater_image', initialize_field );
        acf.add_action( 'append_field/type=hi_hat_repeater_image', initialize_field );
    }
})( jQuery );
