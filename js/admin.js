/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

jQuery( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        /**
         * Hide or close next tr after switch
         * @param $element
         * @param num
         */
        function switchSingle( $element, num ) {

            for ( let i = 0; i < num; i++ ) {

                $element.is( ':checked' ) ?
                    $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).show( 300 ) :
                    $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).hide( 300 );

            }

        }

        /**
         * Init single switch
         * @param $element
         * @param num
         */
        function initSingleSwitch( $element, num = 1 ) {

            switchSingle( $element, num );

            $element.on( 'change', () => {

                switchSingle( $element, num );

            } );

        }

        /**
         * Init single select
         * @param $element
         * @param condition
         * @param num
         */
        function initSingleSelect( $element, condition, num = 1 ) {

            selectSingle( $element, num, condition );

            $element.on( 'change', () => {

                selectSingle( $element, num, condition );

            } );

        }

        /**
         * Hide or close next tr after select
         * @param $element
         * @param num
         * @param conditionValue
         */
        function selectSingle( $element, num, conditionValue ) {

            console.log( 'changed' );

            for ( let i = 0; i < num; i++ ) {

                if ( typeof conditionValue === 'object' ) {

                    let showElement = true
                    conditionValue.forEach( conditionValue => {

                        showElement = $element.val() !== conditionValue && showElement;

                    } );

                    showElement ?
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).show( 300 ) :
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).hide( 300 );

                } else {

                    $element.val() !== conditionValue ?
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).show( 300 ) :
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).hide( 300 );

                }

            }

        }

        /**
         * Init meta-boxes user interface
         */
        function initUI() {

            /** Show/Hide fields on switcher check. */
            initSingleSwitch( $( '#pp_AdUnlocker_general_settings_javascript' ) );
            initSingleSwitch( $( '#pp_AdUnlocker_behaviour_settings_is_redirect' ) );

            /** Show/Hide fields related to select */
            initSingleSelect( $( '#pp_AdUnlocker_general_settings_algorithm' ), [ 'inline', 'proxy' ], 1 );

        }

        initUI();

    } );

} );
