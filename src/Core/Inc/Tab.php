<?php
/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

namespace Core\AdUnlocker\Inc;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Base methods for Tabs Classes.
 *
 * @since 1.0.0
 *
 **/
abstract class Tab {

    /**
     * Check if tab exist and enabled.
     *
     * @param string|null $tab_slug - Slug of tub to check.
     *
     * @return bool - True if Tab is enabled, false otherwise.
     * @since  1.0.0
     * @access protected
     *
     */
    protected function is_enabled( string $tab_slug = null ): bool {

        /** Foolproof. */
        if ( null === $tab_slug ) { return false; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Check if status tab exist. */
        if ( ! isset( $tabs[ $tab_slug ] ) ) { return false; }

        /** Check if 'enabled' field of status tab exist. */
        if ( ! isset( $tabs[ $tab_slug ][ 'enabled' ] ) ) { return false; }

        /** Check if status tab is enabled. */
        return true === $tabs[ $tab_slug ][ 'enabled' ];

    }

    /**
     * Render tab title.
     *
     * @param string|null $tab_slug - Slug of tub to check.
     *
     * @return void
     * @since  1.0.0
     * @access protected
     *
     */
    protected function render_title( string $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Get selected to process tab. */
        $tab = $tabs[ $tab_slug ];

        /** If title enabled. */
        if ( true ===  $tab[ 'show_title' ] ) {

            /** Render Title. */
            echo '<h3>' . esc_html__( $tab[ 'title' ] ) . '</h3>';

        }

    }

    /**
     * Output nonce, action, and option_page fields for a settings page.
     * Prints out all settings sections added to a particular settings page
     *
     * @param string|null $tab_slug - Slug of tub to check.
     *
     * @return void
     * @since  1.0.0
     * @access protected
     *
     */
    protected function do_settings_base( string $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        settings_fields( 'AdUnlocker' . $tab_slug . 'OptionsGroup' );
        do_settings_sections( 'AdUnlocker' . $tab_slug. 'OptionsGroup' );

    }

    /**
     * Registers a setting and its data.
     * Add a new section to a settings page.
     *
     * @param string|null $tab_slug - Slug of tub to check.
     *
     * @return void
     * @since  1.0.0
     * @access protected
     *
     */
    protected function add_settings_base( string $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        /** Status Tab. */
        register_setting( 'AdUnlocker' . $tab_slug . 'OptionsGroup', 'pp_AdUnlocker_' . $tab_slug . '_settings' );
        add_settings_section( 'pp_AdUnlocker_' . $tab_slug . '_page_status_section', '', null, 'AdUnlocker' . $tab_slug . 'OptionsGroup' );

    }

    /**
     * Check if tab is enabled by tab slug.
     *
     * @param string $tab_slug - Tab slug.
     *
     * @return bool
     * @since  1.0.0
     * @access private
     *
     */
    public static function is_tab_enabled( string $tab_slug ): bool {

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        return isset( $tabs[ $tab_slug ][ 'enabled' ] ) && $tabs[ $tab_slug ][ 'enabled' ];

    }

}
