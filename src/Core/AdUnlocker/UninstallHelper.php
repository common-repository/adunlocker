<?php
/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

namespace Core\AdUnlocker;

use FilesystemIterator;
use Core\AdUnlocker\Inc\Helper;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: UninstallHelper class contain main plugin logic.
 *
 * @since 1.0.0
 **/
final class UninstallHelper {

	/**
	 * UninstallHelper.
	 *
     * @since 1.0.0
     * @access private
	 * @var UninstallHelper
	 **/
	private static $instance;

    /**
     * Implement plugin uninstallation.
     *
     * @param string $uninstall_mode - Uninstall mode: plugin, plugin+settings, plugin+settings+data
     *
     * @return void
     * @since  1.0.0
     * @access public
     *
     */
    public function uninstall( $uninstall_mode ) {

        if ( 'plugin+settings+data' === $uninstall_mode ) {

            /** Remove all folders with random scripts. */
            $this->remove_random_folders();

        }

    }

    /**
     * Remove all random folders.
     *
     * @since 3.0.0
     * @access public
     **/
    private function remove_random_folders() {

        /** Get all folders in plugins. */
        $directories = glob( plugin_dir_path( __FILE__ ) . '../*' , GLOB_ONLYDIR );

        /** Foreach plugin */
        foreach ( $directories as $plugin_dir ) {

            $fi = new FilesystemIterator( $plugin_dir, FilesystemIterator::SKIP_DOTS );

            /** In our Random Folder algorithm in each folder only one JS file. So, skip other folders. */
            if ( 1 !== iterator_count( $fi ) ) { continue; }

            /** Remove folder only if file inside contain 'ppAdUnlocker' string. */
            foreach ( $fi as $file ) {

                if ( strpos( file_get_contents( $file ), 'ppAdUnlocker' ) !== false ) {
                    Helper::get_instance()->remove_directory( $plugin_dir );
                }

            }

        }

    }

	/**
	 * Main UninstallHelper Instance.
	 * Insures that only one instance of UninstallHelper exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return UninstallHelper
	 **/
	public static function get_instance(): UninstallHelper {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
