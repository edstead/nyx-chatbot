/**
 * Voice input/output functionality for Nyx Chatbot
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public/js
 */

(function($) {
    'use strict';

    // Speech recognition and synthesis objects
    let recognition;
    let synth;
    let isRecording = false;
    let isSpeaking = false;

    $(document).ready(function() {
        // Initialize voice functionality
        initVoice();
    });

    /**
     * Initialize voice functionality
     */
    function initVoice() {
        // Check if browser supports speech recognition and synthesis
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.warn('Speech recognition not supported in this browser.');
            return;
        }

        if (!('speechSynthesis' in window)) {
            console.warn('Speech synthesis not supported in this browser.');
            return;
        }

        // Initialize speech recognition
        recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US'; // Default to English

        // Initialize speech synthesis
        synth = window.speechSynthesis;

        // Add voice buttons to controls
        if ($('.nyx-controls').length && !$('.nyx-voice-controls').length) {
            $('.nyx-controls').append(`
                <div class="nyx-voice-controls">
                    <button class="nyx-control-button nyx-voice-record-button">
                        ${nyx_vars.strings.voice_start}
                    </button>
                    <button class="nyx-control-button nyx-voice-play-button" style="display:none;">
                        ${nyx_vars.strings.voice_play}
                    </button>
                </div>
            `);
        }

        // Set up event listeners
        setupVoiceEventListeners();
    }

    /**
     * Set up voice event listeners
     */
    function setupVoiceEventListeners() {
        // Record button click
        $('.nyx-voice-record-button').on('click', function() {
            if (!isRecording) {
                startRecording();
            } else {
                stopRecording();
            }
        });

        // Play button click
        $('.nyx-voice-play-button').on('click', function() {
            if (!isSpeaking) {
                playLastResponse();
            } else {
                stopSpeaking();
            }
        });

        // Speech recognition events
        recognition.onstart = function() {
            isRecording = true;
            $('.nyx-voice-record-button').text(nyx_vars.strings.voice_stop);
            $('.nyx-voice-record-button').addClass('recording');
        };

        recognition.onend = function() {
            isRecording = false;
            $('.nyx-voice-record-button').text(nyx_vars.strings.voice_start);
            $('.nyx-voice-record-button').removeClass('recording');
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            $('#user-input').val(transcript);
            
            // Automatically send message
            if (typeof window.sendMessage === 'function') {
                window.sendMessage();
            } else {
                // Fallback if sendMessage function is not available
                $('.nyx-send-button').click();
            }
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            isRecording = false;
            $('.nyx-voice-record-button').text(nyx_vars.strings.voice_start);
            $('.nyx-voice-record-button').removeClass('recording');
        };

        // Speech synthesis events
        synth.onvoiceschanged = function() {
            // Get available voices
            const voices = synth.getVoices();
            
            // Select a good English voice if available
            let voice = voices.find(v => v.name.includes('Google') && v.lang.includes('en')) || 
                        voices.find(v => v.lang.includes('en')) || 
                        voices[0];
                        
            // Store selected voice
            window.selectedVoice = voice;
        };
    }

    /**
     * Start recording
     */
    function startRecording() {
        try {
            recognition.start();
        } catch (e) {
            console.error('Speech recognition error:', e);
        }
    }

    /**
     * Stop recording
     */
    function stopRecording() {
        try {
            recognition.stop();
        } catch (e) {
            console.error('Speech recognition error:', e);
        }
    }

    /**
     * Play last AI response
     */
    function playLastResponse() {
        // Get last AI message
        const lastAiMessage = $('.ai-message').last().text();
        const aiContent = lastAiMessage.replace('Nyx:', '').trim();
        
        if (!aiContent) {
            return;
        }
        
        // Create utterance
        const utterance = new SpeechSynthesisUtterance(aiContent);
        
        // Set voice if available
        if (window.selectedVoice) {
            utterance.voice = window.selectedVoice;
        }
        
        // Set other properties
        utterance.rate = 1;
        utterance.pitch = 1;
        
        // Events
        utterance.onstart = function() {
            isSpeaking = true;
            $('.nyx-voice-play-button').text(nyx_vars.strings.voice_pause);
        };
        
        utterance.onend = function() {
            isSpeaking = false;
            $('.nyx-voice-play-button').text(nyx_vars.strings.voice_play);
        };
        
        utterance.onerror = function(event) {
            console.error('Speech synthesis error:', event.error);
            isSpeaking = false;
            $('.nyx-voice-play-button').text(nyx_vars.strings.voice_play);
        };
        
        // Speak
        synth.speak(utterance);
        
        // Show play button
        $('.nyx-voice-play-button').show();
    }

    /**
     * Stop speaking
     */
    function stopSpeaking() {
        synth.cancel();
        isSpeaking = false;
        $('.nyx-voice-play-button').text(nyx_vars.strings.voice_play);
    }

    // Expose functions to global scope for use by other scripts
    window.startRecording = startRecording;
    window.stopRecording = stopRecording;
    window.playLastResponse = playLastResponse;
    window.stopSpeaking = stopSpeaking;

})(jQuery);
