/**
 * Hooks into the global Plupload instance ('uploader'), which is set when includes/admin/metaboxes.php calls media_form()
 * We hook into this global instance and apply our own changes during and after the upload.
 *
 * @since 1.3.1.3
 */

(function( $ ) {
    $(function() {

        if ( typeof uploader !== 'undefined' ) {

            // Set a custom progress bar
            $('#soliloquy .drag-drop-inside').append( '<div class="soliloquy-progress-bar"><div></div></div>' );
            var soliloquy_bar      = $('#soliloquy .soliloquy-progress-bar'),
                soliloquy_progress = $('#soliloquy .soliloquy-progress-bar div'),
                soliloquy_output   = $('#soliloquy-output');

            // Files Added for Uploading
            uploader.bind( 'FilesAdded', function ( up, files ) {
                $( soliloquy_bar ).fadeIn();
            });

            // File Uploading - show progress bar
            uploader.bind( 'UploadProgress', function( up, file ) {
                $( soliloquy_progress ).css({
                    'width': up.total.percent + '%'
                });
            });

            // File Uploaded - AJAX call to process image and add to screen.
            uploader.bind( 'FileUploaded', function( up, file, info ) {

                // AJAX call to soliloquy to store the newly uploaded image in the meta against this Gallery
                $.post(
                    soliloquy_media_uploader.ajax,
                    {
                        action:  'soliloquy_load_image',
                        nonce:   soliloquy_media_uploader.load_image,
                        id:      info.response,
                        post_id: soliloquy_media_uploader.id
                    },
                    function(res){
                        // Prepend or append the new image to the existing grid of images,
                        // depending on the media_position setting
                        switch ( soliloquy_media_uploader.media_position ) {
                            case 'before':
                                $(soliloquy_output).prepend(res);
                                break;
                            case 'after':
                            default:
                                $(soliloquy_output).append(res);
                                break;
                        }

                        $(res).find('.wp-editor-container').each(function(i, el){
                            var id = $(el).attr('id').split('-')[4];
                            quicktags({id: 'soliloquy-caption-' + id, buttons: 'strong,em,link,ul,ol,li,close'});
                            QTags._buttonsInit(); // Force buttons to initialize.
                        });
                    },
                    'json'
                );
            });

            // Files Uploaded
            uploader.bind( 'UploadComplete', function() {

                // Hide Progress Bar
                $( soliloquy_bar ).fadeOut();

            });

            // File Upload Error
            uploader.bind('Error', function(up, err) {

                // Show message
                $('#soliloquy-upload-error').html( '<div class="error fade"><p>' + err.file.name + ': ' + err.message + '</p></div>' );
                up.refresh();

            });

        }

    });
})( jQuery );
