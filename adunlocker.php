<?php
/**
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 *
 * @wordpress-plugin
 * Plugin Name: AdUnlocker
 * Plugin URI: https://pixpal.net/adunlocker
 * Description: Powerful browser adblock blocker.
 * Donate link: https://paypal.me/kazitihum
 * Version: 1.0.1
 * Requires at least: 3.0
 * Requires PHP: 5.6
 * Author: PixPal
 * Author URI: https://pixpal.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: AdUnlocker
 * Domain Path: /languages
 * Tested up to: 5.8.1
 * Elementor tested up to: 3.9
 * Elementor Pro tested up to: 3.9
 **/

namespace AdUnlocker;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/** Include plugin autoloader. */
require __DIR__ . '/src/autoload.php';

use Core\AdUnlocker\Caster;
use Core\AdUnlocker\Config;
use Core\AdUnlocker\Inc\Unity;

/**
 * SINGLETON: Core class used to implement a plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since 1.0.0
 *
 **/
final class AdUnlocker {

    /**
     * AdUnlocker.
     *
     * @var AdUnlocker
     * @since 1.0.0
     * @access private
     **/
    private static $instance;

    /**
     * Sets up a new plugin instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** Initialize unity and Main variables. */
        Unity::get_instance();
    }

	/**
	 * Setup the plugin.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function setup() {

        /** Do critical compatibility checks and stop work if fails. */
		if ( ! Unity::get_instance()->initial_checks( ['php56'] ) ) { return; }

        /** Prepare custom plugin settings. */
        Config::get_instance()->prepare_settings();

		/** Setup the unity. */
        Unity::get_instance()->setup();

        /** Custom setups for plugin. */
        Caster::get_instance()->setup();

	}

    /**
     * Called when a plugin is activated.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public static function on_activation() {

        /** Call unity on plugin activation.  */
        Unity::on_activation();

        /** Call AdUnlocker on plugin activation */
		Caster::get_instance()->activation_hook();

	}

    /**
     * Called when a plugin is deactivated.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public static function on_deactivation() {

        /** MP on plugin deactivation.  */
        Unity::on_deactivation();

    }

	/**
	 * Main Instance.
	 *
	 * Insures that only one instance of plugin exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
     *
     * @return AdUnlocker
	 **/
	public static function get_instance(): AdUnlocker {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}

/** Run 'on_activation' when the plugin is activated. */
register_activation_hook( __FILE__, [ AdUnlocker::class, 'on_activation' ] );

/** Run 'on_deactivation' when the plugin is deactivated. */
register_deactivation_hook( __FILE__, [ AdUnlocker::class, 'on_deactivation' ] );

/** Run Plugin class once after activated plugins have loaded. */
add_action( 'plugins_loaded', [ AdUnlocker::get_instance(), 'setup' ] );
