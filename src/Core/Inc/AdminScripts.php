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
 * Class adds admin js scripts.
 *
 * @since 1.0.0
 *
 **/
final class AdminScripts {

	/**
	 * AdminScripts.
	 *
	 * @var AdminScripts
	 **/
	private static $instance;

	/**
	 * Sets up a new AdminScripts instance.
	 *
	 * @access public
	 **/
	private function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Add JavaScrips for admin area.
	 *
	 * @return void
	 **/
	public function admin_scripts() {

	    /** Add scripts on plugin setting page. */
		$this->settings_scripts();

	}

	/**
	 * Scripts for plugin setting page.
	 *
	 * @return void
	 **/
	private function settings_scripts() {

		/** Add styles only on setting page */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles only on plugin settings page */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

        wp_enqueue_script( 'pp-AdUnlocker-ui', Plugin::get_url() . 'src/Core/Inc/assets/js/pp-ui' . Plugin::get_suffix() . '.js', [], Plugin::get_version(), true );

        /** Prepare values to pass to JS. */
        $to_js = [
            'ajaxURL'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce( 'AdUnlocker' ), // Nonce for security.
        ];

        wp_enqueue_script( 'pp-AdUnlocker-unity-admin', Plugin::get_url() . 'src/Core/Inc/assets/js/admin' . Plugin::get_suffix() . '.js', [ 'jquery' ], Plugin::get_version(), true );
        wp_localize_script( 'pp-AdUnlocker-unity-admin', 'ppAdUnlocker', $to_js );
        wp_enqueue_script( 'pp-AdUnlocker-admin', Plugin::get_url() . 'js/admin' . Plugin::get_suffix() . '.js', [ 'jquery' ], Plugin::get_version(), true );

	}

	/**
	 * Main AdminScripts Instance.
	 * Insures that only one instance of AdminScripts exists in memory at any one time.
	 *
	 * @static
	 * @return AdminScripts
	 **/
	public static function get_instance() {

        /** @noinspection SelfClassReferencingInspection */
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AdminScripts ) ) {

			self::$instance = new AdminScripts;

		}

		return self::$instance;

	}

}
