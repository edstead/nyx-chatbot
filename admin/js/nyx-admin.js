/**
 * Admin JavaScript functionality
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/admin/js
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers
        $('.nyx-color-field').wpColorPicker({
            change: function(event, ui) {
                // Update color preview
                $(this).closest('.nyx-color-picker').find('.nyx-color-preview').css('background-color', ui.color.toString());
            }
        });

        // Toggle API key visibility
        $('.nyx-api-key-toggle').on('click', function() {
            var input = $(this).closest('.nyx-api-key-field').find('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).text('Hide');
            } else {
                input.attr('type', 'password');
                $(this).text('Show');
            }
        });

        // Media uploader for floating button image
        $('#nyx-upload-button').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var imagePreview = $('#nyx-floating-button-preview');
            var imageInput = $('#nyx_chatbot_floating_button_image');
            
            var frame = wp.media({
                title: 'Select or Upload Floating Button Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                imageInput.val(attachment.url);
                imagePreview.attr('src', attachment.url).show();
            });
            
            frame.open();
        });

        // Display mode toggle
        $('input[name="nyx_chatbot_display_mode"]').on('change', function() {
            var mode = $(this).val();
            if (mode === 'floating' || mode === 'both') {
                $('#nyx-floating-settings').show();
            } else {
                $('#nyx-floating-settings').hide();
            }
        });
        
        // Initialize display mode toggle
        var currentMode = $('input[name="nyx_chatbot_display_mode"]:checked').val();
        if (currentMode === 'floating' || currentMode === 'both') {
            $('#nyx-floating-settings').show();
        } else {
            $('#nyx-floating-settings').hide();
        }

        // Feature toggles
        $('#nyx_chatbot_enable_file_upload').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nyx-file-upload-settings').show();
            } else {
                $('#nyx-file-upload-settings').hide();
            }
        });
        
        $('#nyx_chatbot_enable_voice').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nyx-voice-settings').show();
            } else {
                $('#nyx-voice-settings').hide();
            }
        });
        
        $('#nyx_chatbot_enable_rate_limit').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nyx-rate-limit-settings').show();
            } else {
                $('#nyx-rate-limit-settings').hide();
            }
        });
        
        // Initialize feature toggles
        if ($('#nyx_chatbot_enable_file_upload').is(':checked')) {
            $('#nyx-file-upload-settings').show();
        } else {
            $('#nyx-file-upload-settings').hide();
        }
        
        if ($('#nyx_chatbot_enable_voice').is(':checked')) {
            $('#nyx-voice-settings').show();
        } else {
            $('#nyx-voice-settings').hide();
        }
        
        if ($('#nyx_chatbot_enable_rate_limit').is(':checked')) {
            $('#nyx-rate-limit-settings').show();
        } else {
            $('#nyx-rate-limit-settings').hide();
        }
    });

})(jQuery);
