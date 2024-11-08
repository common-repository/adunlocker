/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        let ppAdUnlocker = window.ppAdUnlocker;

        /** Logo click - smooth scroll. */
        $( '.ppc-drawer__header > a.pp-plugin-title' ).on( 'click', function ( e ) {
            e.preventDefault();

            $( 'html, body' ).animate( {
                scrollTop: 0
            }, 500 );

        } );

        /** Subscribe form. */
        let $subscribeBtn = $('#pp-subscribe');
        $subscribeBtn.on( 'click', function (e) {

            e.preventDefault();

            let $mail = $('#pp-subscribe-mail');
            let $name = $('#pp-subscribe-name');
            let plugin = 'AdUnlocker';
            let mailIndex = $mail.parent().data('ppc-index');

            if ( $mail.val().length > 0 && window.PixPalMaterial[mailIndex].valid) {

                const noticeArea = document.querySelector( '.pp-subscribe-form-message' );
                $name.prop("disabled", true);
                $mail.prop("disabled", true);
                $('#pp-subscribe').prop("disabled", true);

                $.ajax({
                    type: "GET",
                    url: "https://pixpal.net/wp-content/plugins/pp-validators/subscriber/subscribe.php",
                    crossDomain: true,
                    data: 'name=' + $name.val() + '&mail=' + $mail.val() + '&plugin=' + plugin,
                    success: function (data) {

                        if (true === data) {

                            noticeArea.style.display = 'block';
                            noticeArea.classList.add( 'pp-subscribe-form-message-success' );
                            noticeArea.innerHTML = noticeArea.dataset.success;

                            setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                        } else {

                            noticeArea.style.display = 'block';
                            noticeArea.classList.add( 'pp-subscribe-form-message-error' );
                            noticeArea.innerHTML = noticeArea.dataset.error;

                            setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                        }

                    },
                    error: function (err) {

                        noticeArea.style.display = 'block';
                        noticeArea.classList.add( 'pp-subscribe-form-message-error' );
                        noticeArea.innerHTML = noticeArea.dataset.warn;

                        $('#pp-subscribe-name').prop( "disabled", false );
                        $('#pp-subscribe-mail').prop( "disabled", false );
                        $('#pp-subscribe').prop( "disabled", false );

                        setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                    }
                });

            } else {
                window.PixPalMaterial[mailIndex].valid = false;
            }

        });

        /** Check for Updates. */
        let $checkUpdatesBtn = $( '#pp-updates-btn' );
        $checkUpdatesBtn.on( 'click', function ( e ) {

            e.preventDefault();

            /** Disable button and show process. */
            $checkUpdatesBtn.attr( 'disabled', true ).addClass( 'pp-spin' ).find( '.material-icons' ).text( 'refresh' );

            /** Prepare data for AJAX request. */
            let data = {
                action: 'check_updates',
                nonce: ppAdUnlocker.nonce,
                checkUpdates: true
            };

            /** Do AJAX request. */
            $.post( ppAdUnlocker.ajaxURL, data, function( response ) {

                if ( response ) {
                    console.info( 'Latest version information updated.' );
                    location.reload();
                } else {
                    console.warn( response );
                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.error( response );
                alert( 'Looks like an Error has occurred. Please try again later.' );

            } ).always( function() {

                /** Enable button again. */
                $checkUpdatesBtn.attr( 'disabled', false ).removeClass( 'pp-spin' ).find( '.material-icons' ).text( 'autorenew' );

            } );

        } );

        /** Custom CSS */
        function custom_css_init() {

            let $custom_css_fld = $( '#pp_custom_css_fld' );

            if ( ! $custom_css_fld.length ) { return; }

            let editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror, {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css'
                }
            );

            let css_editor;
            css_editor = wp.codeEditor.initialize( 'pp_custom_css_fld', editorSettings );

            css_editor.codemirror.on( 'change', function( cMirror ) {
                css_editor.codemirror.save(); // Save data from CodeEditor to textarea.
                $custom_css_fld.change();
            } );

        }
        custom_css_init();

        /** Initialise Chosen fields. */
        let $chosenSelect = $( '.pp-chosen.chosen-select' );
        if ( $chosenSelect.length > 0 ) {

            $chosenSelect.chosen( {
                width: '100%',
                search_contains: true,
                disable_search_threshold: 7,
                inherit_select_classes: true,
                no_results_text: 'Oops, nothing found'
            } );

        }

        /** Layout Select */
        $( '.pp-layout .pp-nav-dropdown a' ).on( 'click', function( e ) {

            e.preventDefault();

            let layoutImg = e.target;
            let layoutA = e.target.parentElement;

            if ( e.target.tagName !== 'IMG' ) {

                layoutImg = e.target.querySelector( 'img' );
                layoutA = e.target;

            }

            const val = layoutA.getAttribute( 'data-val' );
            const name = layoutImg.alt;
            const img = $( '.pp-layout button img' );

            /** Change image on thumb */
            img.attr( 'src', img.attr( 'src' ).replace(/(.*)\/.*(\.svg$)/i, '$1/' + val + '$2') );
            img.attr( 'alt', name );

            /** Change setting value in the input */
            $( this ).closest( 'td' ).find( 'input' ).val( val ).change();

            /** Close dropdown */
            setTimeout( function() {
                $( '.pp-layout' ).removeClass( 'pp-open' );
            }, 100 );

            /** Select new item as active */
            $( '.pp-layout .pp-nav-dropdown a' ).removeClass( 'pp-active' );
            $( this ).addClass( 'pp-active' );

        } );

    } )

} ( jQuery ) );