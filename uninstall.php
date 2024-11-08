<?php
/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

namespace AdUnlocker;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Core\AdUnlocker\Inc\Cache;
use Core\AdUnlocker\Inc\Helper;
use Core\AdUnlocker\Inc\Plugin;
use Core\AdUnlocker\UninstallHelper;

/**
 * SINGLETON: Class used to implement plugin uninstallation.
 *
 * @since  1.0.0
 *
 **/
final class Uninstall {

    /**
     * Uninstallation.
     *
     * @since 1.0.0
     * @var Uninstall
     **/
    private static $instance;

    /**
     * Sets up a new Uninstallation instance.
     *
     * @since  1.0.0
     * @access public
     **/
    private function __construct() {

        /** Get Uninstall mode. */
        $uninstall_mode = $this->get_uninstall_mode();

        /** Send uninstall Action to our host. */
        // Helper::get_instance()->send_action( 'uninstall', Plugin::get_slug(), Plugin::get_version() );

        /** Remove Plugin and Settings. */
        if ( 'plugin+settings' === $uninstall_mode ) {

            /** Remove Plugin Settings. */
            $this->remove_settings();

        /** Remove Plugin, Settings and all created data. */
        } elseif ( 'plugin+settings+data' === $uninstall_mode ) {

            /** Remove Plugin Settings. */
            $this->remove_settings();

            /** Remove Plugin Data. */
            $this->remove_data();

        }

        /** Call custom uninstall handler. */
        UninstallHelper::get_instance()->uninstall( $uninstall_mode );

    }

    /**
     * Delete Plugin Options.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function remove_settings() {

        /** Prepare array with options to remove. */
        $settings = [];

        foreach ( wp_load_alloptions() as $option => $value ) {
            if ( strpos( $option, 'pp_AdUnlocker' ) === 0 ) {
                $settings[] = $option;
            }
        }

        /** Remove options for Multisite. */
        if ( is_multisite() ) {

            foreach ( $settings as $key ) {

                if ( ! get_site_option( $key ) ) { continue; }

                delete_site_option( $key );

            }

        /** Remove options for Singular site. */
        } else {

            foreach ( $settings as $key ) {

                if ( ! get_option( $key ) ) { continue; }

                delete_option( $key );

            }

        }

        /** Remove cache table. */
        $cache = new Cache();
        $cache->drop_cache_table();

    }

    /**
     * Delete Plugin data.
     * Remove all tables started with plugin slug and folder from Uploads.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function remove_data() {

        /** Remove all tables started with plugin slug. */
        $this->remove_tables();

        /** Remove folder with slug name from uploads. */
        $this->remove_folder();

    }

    /**
     * Remove all tables started with plugin slug.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function remove_tables() {

        global $wpdb;

        $tables = $wpdb->tables( 'all' );

        foreach ( $tables as $table ) {

            /** Convert plugin slug to table name. */
            $table_slug = str_replace( '-', '_', Plugin::get_slug() );

            /** If table name starts with prefix_plugin_slug */
            if ( strpos( $table, $wpdb->prefix . $table_slug ) === 0 ) {

                /** Remove this table. */
                /** @noinspection SqlNoDataSourceInspection */
                $wpdb->query( "DROP TABLE IF EXISTS {$table}" );

            }

        }

    }

    /**
     * Remove folder with slug name from uploads.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function remove_folder() {

        /** Remove /wp-content/uploads/plugin-slug/ folder. */
        $dir = trailingslashit( wp_upload_dir()['basedir'] ) . Plugin::get_slug();

        if ( is_dir( $dir ) ) {
            Helper::get_instance()->remove_directory( $dir );
        }

    }

    /**
     * Return uninstall mode.
     * plugin - Will remove the plugin only. Settings and Audio files will be saved. Used when updating the plugin.
     * plugin+settings - Will remove the plugin and settings. Audio files will be saved. As a result, all settings will be set to default values. Like after the first installation.
     * plugin+settings+data - Full Removal. This option will remove the plugin with settings and all audio files. Use only if you are sure what you are doing.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string
     **/
    public function get_uninstall_mode() {

        $uninstall_settings = get_option( 'pp_AdUnlocker_uninstall_settings' );

        if ( isset( $uninstall_settings[ 'pp_AdUnlocker_uninstall_settings' ] ) && $uninstall_settings[ 'pp_AdUnlocker_uninstall_settings' ] ) { // Default value.
            $uninstall_settings = [
                'delete_plugin' => 'plugin'
            ];
        }

        return $uninstall_settings[ 'delete_plugin' ];

    }

    /**
     * Main Uninstallation Instance.
     *
     * Insures that only one instance of Uninstall exists in memory at any one time.
     *
     * @static
     * @since 1.0.0
     *
     * @return Uninstall
     **/
    public static function get_instance(): Uninstall {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

            self::$instance = new self;

        }

        return self::$instance;

    }

}

/** Call unity Uninstall. */
Uninstall::get_instance();
