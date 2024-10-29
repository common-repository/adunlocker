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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Autoload class used to register custom __autoload() implementation for plugin.
 *
 * @since 1.0.0
 *
 **/
final class Autoload {

    /**
     * Plugin root Namespace.
     *
     * @since 1.0.0
     * @var string
     **/
    private static $namespace = 'Core\\';

    /**
     * Shorthand for DIRECTORY_SEPARATOR.
     * Brevity is the Soul of Wit.
     *
     * @since 1.0.0
     * @var string
     **/
    private static $DS = DIRECTORY_SEPARATOR;

    /**
     * Custom autoloader for plugin.
     *
     * @param string $class - Called class name.
     *
     * @static
     * @return void
     * @since  1.0.0
     * @access public
     *
     */
    public static function load( string $class ) {

        /** Bail if the class is not in our namespace. */
        if ( 0 !== strpos( $class, self::$namespace ) ) { return; }

        /** Classes from AdUnlocker. */
        $file_p = self::get_plugin_class_file( $class );

        /** If Class exists in AdUnlocker - load it. */
        self::include_class( $file_p );

        /** Classes from unity. */
        $file_u = self::get_unity_class_file( $class );

        /** Secondly we load classes from unity. */
        self::include_class( $file_u );

    }

    /**
     * Build file path for classes from unity directory.
     *
     * @param string $class - Called class name.
     *
     * @static
     * @return string - Path to class file.
     * @since  1.0.0
     * @access private
     *
     */
    private static function get_unity_class_file( string $class ): string {

        /** Build the filename. */
        $file = realpath( __DIR__ );

        return $file . self::$DS . str_replace( ['AdUnlocker\\', '\\'], ['', self::$DS], $class ) . '.php';

    }

    /**
     * Build file path for classes from AdUnlocker directory.
     *
     * @param string $class - Called class name.
     *
     * @static
     * @return string - Path to class file.
     * @since  1.0.0
     * @access private
     *
     */
    private static function get_plugin_class_file( string $class ): string {

        /** Build the filename. */
        $file = realpath( __DIR__ );

        return $file . self::$DS . str_replace( '\\', self::$DS, $class ) . '.php';

    }

    /**
     * Includes and evaluates the specified file only once.
     *
     * @param string $file - Path to class file.
     *
     * @static
     * @return void - Path to class file.
     * @since  1.0.0
     * @access private
     *
     */
    private static function include_class( string $file ): void {

        /** If Class file exists - load it. */
        if ( file_exists( $file ) ) {

            /** @noinspection PhpIncludeInspection */
            include_once( $file );

        }

    }

}

/** Register plugin custom autoloader. */
spl_autoload_register( __NAMESPACE__ .'\Autoload::load' );

