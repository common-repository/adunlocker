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

use RuntimeException;
use Core\AdUnlocker\Inc\Plugin;
use Core\AdUnlocker\Inc\Helper;
use Core\AdUnlocker\Inc\Settings;
use Core\AdUnlocker\Inc\TabAssignments;
use Core\AdUnlocker\Inc\UI;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Caster class contain main plugin logic.
 *
 * @since 1.0.0
 **/
final class Caster {

	/**
	 * Caster.
	 *
     * @since 1.0.0
     * @access private
	 * @var Caster
	 **/
	private static $instance;

    /**
     * Setup the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function setup() {

        /** Define hooks that runs on both the front-end and the dashboard. */
        $this->both_hooks();

        /** Define public hooks. */
        $this->public_hooks();

        /** Define admin hooks. */
        $this->admin_hooks();

    }

    /**
     * Define hooks that runs on both the front-end and the dashboard.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function both_hooks() { }

    /**
     * Register all the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function public_hooks() {

        /** Work only on frontend area. */
        if ( is_admin() ) { return; }

        /** Load CSS Styles for Frontend Area. */
        add_action( 'wp_enqueue_scripts', [$this, 'styles' ] ); // CSS.

        /** Load JavaScript for Frontend Area. */
        add_action( 'wp_enqueue_scripts', [$this, 'scripts'] ); // JS.

        /** JavaScript Required. */
        add_action( 'wp_footer', [$this, 'javascript_required'] );

        /** We need Sessions */
        add_action( 'init', [ Helper::class, 'start_session' ], 1 );
        add_action( 'wp_logout', [ Helper::class, 'end_session' ] );
        add_action( 'wp_login', [ Helper::class, 'end_session' ] );

        /** Run one of selected algorithm. */
        $this->run_algorithm();

    }

    /**
     * Register all the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function admin_hooks() {

        /** Work only in admin area. */
        if ( ! is_admin() ) { return; }

        /**
         * The adBlock extensions blocks us in admin area too, so we add scripts and styles inline.
         **/

        /** Remove unity css and JS. */
        add_action( 'admin_enqueue_scripts', [$this, 'dequeue_unity_assets'], 1000 );

        /** Add inline JS and CSS for Backend Area. */
        add_action( 'admin_footer', [ $this, 'admin_styles' ], 1000 );
        add_action( 'admin_footer', [ $this, 'admin_scripts' ], 1000 );

    }

    /**
     * Protect site if JavaScript is Disabled.
     *
     * @since 2.0.0
     * @access public
     **/
    public function javascript_required() {

        /** Custom JavaScript is not allowed in AMP. */
        if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        /** Get plugin settings */
        $options = Settings::get_instance()->options;

        if ( 'on' !== $options['javascript'] ) { return; }

        ?>
        <noscript>
            <div id='pp-AdUnlocker-js-disabled'>
                <div><?php echo wp_kses_post( $options['javascript_msg'] ); ?></div>
            </div>
            <style>
                #pp-AdUnlocker-js-disabled {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100%;
                    width: 100%;
                    z-index: 999999;
                    text-align: center;
                    background-color: #FFFFFF;
                    color: #000000;
                    font-size: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>
        </noscript>
        <?php

    }

    /**
     * Add CSS Styles for Frontend Area.
     *
     * @return void
     * @since 3.0.0
     **/
    public function styles() {

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        /** Add custom CSS. */
        echo sprintf( '<style>%s</style>', Settings::get_instance()->options['custom_css'] );

    }

    /**
     * Add JavaScript for the public-facing side of the site.
     *
     * @return void
     * @since 1.0.0
     **/
    public function scripts() {

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        wp_enqueue_script( 'pp-AdUnlocker-ads', Plugin::get_url() . 'js/ads' . Plugin::get_suffix() . '.js', [], Plugin::get_version(), true );

    }

    /**
     * Remove unity css and JS.
     *
     * @since 3.0.0
     * @access private
     *
     * @return void
     **/
    public function dequeue_unity_assets() {

        /** Dequeue CSS. */
        wp_dequeue_style( 'pp-plugins' );
        wp_dequeue_style( 'pp-AdUnlocker-ui' );
        wp_dequeue_style( 'pp-AdUnlocker-unity-admin' );
        wp_dequeue_style( 'pp-AdUnlocker-admin' );
        wp_dequeue_style( 'pp-AdUnlocker-plugin-install' );

        /** Dequeue JS. */
        wp_dequeue_script( 'pp-AdUnlocker-ui' );
        wp_dequeue_script( 'pp-AdUnlocker-unity-admin' );
        wp_dequeue_script( 'pp-AdUnlocker-admin' );
        wp_dequeue_script( 'pp-plugins' );
        wp_dequeue_script( 'pp-AdUnlocker-assignments' );

    }

    /**
     * Add JS for admin area.
     *
     * @since 3.0.0
     * @access public
     *
     * @return void
     **/
    public function admin_scripts() {

        /** Get current screen to add styles on specific pages. */
        $screen = get_current_screen();
        if ( null === $screen ) { return; }

        /** Plugin Settings Page. */
        if ( in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {

            ?>
            <script>
                window.ppAdUnlocker = {
                    "ajaxURL":"<?php echo esc_url( admin_url('admin-ajax.php') ); ?>",
                    "nonce":"<?php esc_attr_e( wp_create_nonce( 'AdUnlocker' ) ); ?>"
                };
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/js/pp-ui' . Plugin::get_suffix() . '.js' ); ?>
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/js/assignments' . Plugin::get_suffix() . '.js' ); ?>
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/js/admin' . Plugin::get_suffix() . '.js' ); ?>
                <?php echo file_get_contents( Plugin::get_path() . 'js/admin' . Plugin::get_suffix() . '.js' ); ?>
            </script>
            <?php

        }

        /** Add script only on WP Plugins page. */
        if ( 'plugins' === $screen->base ) {

            ?>
            <script>
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/js/plugins' . Plugin::get_suffix() . '.js' ); ?>
            </script>
            <?php

         }

    }

    /**
     * Add CSS for admin area.
     *
     * @since 3.0.0
     * @access public
     *
     * @return void
     **/
    public function admin_styles() {

        /** Get current screen to add styles on specific pages. */
        $screen = get_current_screen();
        if ( null === $screen ) { return; }

        /** Plugin Settings Page. */
        if ( in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {

            ?>
            <style>
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/css/pp-ui' . Plugin::get_suffix() . '.css' ); ?>
                <?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/css/admin' . Plugin::get_suffix() . '.css' ); ?>
                <?php echo file_get_contents( Plugin::get_path() . 'css/admin' . Plugin::get_suffix() . '.css' ); ?>
            </style>
            <?php

        }

        /** Plugin popup on update. Styles only for our plugin. */
        if ( ( 'plugin-install' === $screen->base ) && isset( $_GET[ 'plugin' ] ) && $_GET[ 'plugin' ] === 'AdUnlocker' ) {

            ?><style><?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/css/plugin-install' . Plugin::get_suffix() . '.css' ); ?></style><?php

        }

        /** Add styles only on WP Plugins page. */
        if ( 'plugins' === $screen->base ) {

            ?><style><?php echo file_get_contents( Plugin::get_path() . 'src/Core/Inc/assets/css/plugins' . Plugin::get_suffix() . '.css' ); ?></style><?php

        }

    }

    /**
     * Run one of selected algorithm.
     *
     * @since  2.0.1
     * @access private
     *
     * @return void
     **/
    private function run_algorithm() {

        /** Get algorithm from plugin settings. */
        $algorithm = Settings::get_instance()->options['algorithm'];

        if ( 'inline' === $algorithm ) {

            $this->inline_algorithm();

        } elseif ( 'random-folder' === $algorithm ) {

            $this->random_folder_algorithm();

            /** Proxies all scripts. */
        } elseif ( 'proxy' === $algorithm ) {

            $this->proxy_algorithm();

        }

    }

    /**
     * Pass variables to JS.
     *
     * @return void
     * @since 2.0.2
     **/
    public function localize_AdUnlocker() {

        /** Get plugin settings. */
        $options = Settings::get_instance()->options;

        wp_localize_script( 'pp-AdUnlocker', 'ppAdUnlocker',
            [
                'style'         => $options['style'],
                'timeout'       => $options['timeout'],
                'closeable'     => $options['closeable'],
                'title'         => $options['title'],
                'content'       => $options['content'],
                'bg_color'      => $options['bg_color'],
                'modal_color'   => $options['modal_color'],
                'text_color'    => $options['text_color'],
                'blur'          => $options['blur'],
                'prefix'        => $this->generate_random_name(),
            ]
        );

    }

    /**
     * Add obfuscated inline script on page.
     * Very fast. But low reliability.
     *
     * @since  2.0.1
     * @access private
     * @return void
     **/
    private function inline_algorithm() {

        /** Load inline JavaScript for Frontend Area. */
        add_action( 'wp_footer', [$this, 'footer_scripts'], mt_rand( 1, 60 ) ); // JS.

    }

    /**
     * Load inline JavaScript for Frontend Area.
     *
     * @return void
     * @since 1.0.0
     **/
    public function footer_scripts() {

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        /**
         * Get Randomized Script.
         * @noinspection PhpIncludeInspection
         **/
        $js = require Plugin::get_path() . 'src/Core/AdUnlocker/AdUnlockerJS.php';

        ?><script><?php echo $js; ?></script><?php

    }

    /**
     * Create a random folder once at day. A quick, fairly reliable way to bypass ad blockers.
     *
     * @since  2.0.1
     * @access private
     * @return void
     **/
    private function random_folder_algorithm() {

        /** Is the plugins folder writable? */
        if ( ! is_writable( WP_CONTENT_DIR ) ) {

            /** Switch to inline algorithm, as the safest in this case. */
            $this->inline_algorithm();

            return;
        }

        /** Is it time to generate a new folder? */
        $opt_name = 'pp_AdUnlocker_random_folder_generated';
        $generated = get_transient( $opt_name );
	    $uploads_dir = wp_upload_dir();

        /** Generate new random folder. */
        if ( false === $generated ) {

            $options = Settings::get_instance()->options;

            /** Get the name of the old folder. */
            $fake_folder = get_option( 'pp_AdUnlocker_random_folder_fake_folder' );

            /** Remove old folder. */
            if ( $fake_folder ) {
                Helper::get_instance()->remove_directory( $uploads_dir['basedir'] . '/' . $fake_folder );
            }

            /** Create new folder with scripts. */
            $this->random_folder_create_fake_folder();

            /** Regenerate after 24 hours. */
            set_transient( $opt_name, 'true', intval( $options[ 'lifetime' ] ) * DAY_IN_SECONDS ); // 24 hours

        }

        /** Load JavaScript for Frontend Area. */
        add_action( 'wp_enqueue_scripts', [$this, 'random_folder_algorithm_scripts'] ); // JS.

    }

    /**
     * Add JavaScript for the public-facing side of the site.
     * For random folder algorithm.
     *
     * @return void
     * @since 2.0.1
     **/
    public function random_folder_algorithm_scripts() {

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        $folder_name = get_option( 'pp_AdUnlocker_random_folder_fake_folder' );
        $file_name = get_option( 'pp_AdUnlocker_random_folder_fake_file' );
	    $uploads_dir = wp_upload_dir();

        /** If script file not exist. */
        if ( ! file_exists( $uploads_dir['basedir'] . '/' . $folder_name . '/' . $file_name ) ) {
            /** Create new folder with scripts. */
            $this->random_folder_create_fake_folder();
            return;
        }

        wp_enqueue_script( 'pp-AdUnlocker', $uploads_dir['baseurl'] . '/' . $folder_name . '/' . $file_name, [], Plugin::get_version(), true );
        $this->localize_AdUnlocker();

    }

    /**
     * Create random folder and random script file.
     *
     * @since  2.0.1
     * @access private
     * @return void
     **/
    private function random_folder_create_fake_folder() {

        /** Create Random Names. */
        $folder = $this->generate_random_name();
        $file = $this->generate_random_name();
        $file .= '.js';
	    $uploads_dir = wp_upload_dir();

        /** Create Folder. */
        if ( ! wp_mkdir_p($concurrent_directory = $uploads_dir['basedir'] . '/' . $folder ) && ! is_dir( $concurrent_directory ) ) {

            throw new RuntimeException( sprintf( 'Directory "%s" was not created', $concurrent_directory ) );

        }

        /** Create script min File. */

        /**
         * Get Randomized Script.
         * @noinspection PhpIncludeInspection
         **/
        $AdUnlocker_script = require Plugin::get_path() . 'src/Core/AdUnlocker/AdUnlockerJS.php';
        file_put_contents( $uploads_dir['basedir'] . '/' . $folder . '/' . $file, $AdUnlocker_script );

        /** Remember folder and script name. */
        update_option( 'pp_AdUnlocker_random_folder_fake_folder', $folder );
        update_option( 'pp_AdUnlocker_random_folder_fake_file', $file );

    }

    /**
     * Return random alphanumeric name.
     *
     * @since  2.0.1
     * @access private
     * @return string
     **/
    public function generate_random_name(): string {

        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';

        /** Prepare random parts. */
        $part_1 = substr( str_shuffle( $permitted_chars ), 0, mt_rand( 4, 8 ) );
        $part_2 = substr( str_shuffle( $permitted_chars ), 0, mt_rand( 4, 8 ) );

        /** Add random dash. */
        $dash = '';
        if ( mt_rand( 0, 1 ) ) {
            $dash = '-';
        }

        /** Add random wp. */
        $wp = '';
        if ( mt_rand( 0, 1 ) ) {
            $wp = 'wp-';
        }

        return $wp . $part_1 . $dash . $part_2;

    }

    /**
     * Most powerful algorithm. Proxies all scripts and is randomly added self to the end of some one.
     * The disadvantages include a slight slowdown in loading and unstable operation with some caching systems.
     *
     * @since  2.0.1
     * @access private
     * @return void
     **/
    private function proxy_algorithm() {

        /** Let's try to saddle any other script to avoid blocking. */
        add_action( 'wp_print_scripts', [$this, 'list_scripts'], PHP_INT_MAX );

        /** Return proxied scripts. */
        add_action( 'template_redirect', [$this, 'do_stuff_on_404'] );

    }

    /**
     * Let's try to saddle any other script to avoid blocking.
     *
     * @since 2.0.0
     * @access private
     * @return void
     **/
    public function list_scripts() {

        global $wp_scripts;

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        /** Prepare relative paths to plugins and themes. */
        $rel_plugin_path = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
        $rel_theme_path = str_replace( ABSPATH, '', get_theme_root() );

        /** Create MD5 hashes. */
        $md5_plugin_path = md5( $rel_plugin_path );
        $md5_theme_path = md5( $rel_theme_path );

        /** Select random script. */
        $victim = $this->get_random_script_number( $wp_scripts->queue );

        /** Replace paths to MD5 hashes. */
        foreach( $wp_scripts->queue as $key => $handle ) {

            /** Remember victims. */
            if ( $victim === $key ) {
                $_SESSION['pp_AdUnlocker_victim'] = $wp_scripts->registered[$handle]->src;
            }

            /** We send all scripts through our handler. */
            $wp_scripts->registered[$handle]->src = str_replace( $rel_plugin_path, $md5_plugin_path, $wp_scripts->registered[$handle]->src );
            $wp_scripts->registered[$handle]->src = str_replace( $rel_theme_path, $md5_theme_path, $wp_scripts->registered[$handle]->src );

        }

    }

    private function get_random_script_number( $queue ) {

        global $wp_scripts;

        if ( empty( $queue ) ) { return 0; }

        /** Let's remove our own script. */
        if ( ( $key = array_search( 'pp-AdUnlocker-ads', $queue, true ) ) !== false ) {
            unset( $queue[ $key ] );
        }

        /** Name of victim. */
        $victim_name = $queue[array_rand( $queue )];

        /** Key of victim. */
        $key =  array_search( $victim_name, $wp_scripts->queue, true );

        return $key;

    }

    /**
     * Return proxied scripts.
     *
     * @since 2.0.0
     * @access private
     *
     * @return void
     **/
    public function do_stuff_on_404() {

        global $wp;

        /** We are interested in requests for nonexistent files. */
        if ( ! is_404() ) { return; }

        /** We are interested in js files. */
        if ( 'js' !== strtolower( pathinfo( $wp->request, PATHINFO_EXTENSION ) ) ) { return; }

        /** Prepare relative paths to plugins and themes. */
        $rel_plugin_path = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
        $rel_theme_path = str_replace( ABSPATH, '', get_theme_root() );

        /** Create MD5 hashes. */
        $md5_plugin_path = md5( $rel_plugin_path );
        $md5_theme_path = md5( $rel_theme_path );

        /** Reverse replace MD5 to path. */
        $url = $wp->request;

        $url = str_replace( [$md5_plugin_path, $md5_theme_path], [$rel_plugin_path, $rel_theme_path], $url );

        /** Path to script. */
        $script_path = ABSPATH . $url;

        $add = false;
        if ( isset( $_SESSION['pp_AdUnlocker_victim'] ) ) {
            $victim = strval($_SESSION['pp_AdUnlocker_victim']);
            if ( strpos( $victim, $url ) !== false ) {
                $add = true;
            }
        }

        /** Return script. */
        if ( file_exists( $script_path ) ) {
            header( 'HTTP/1.1 200 OK' );
            header( 'Content-Type: application/javascript' );

            echo self::get_js_contents( $script_path, $add );
            die();

        }

    }

    /**
     * Return js file and add AdUnlocker scripts.
     *
     * @param      $path
     * @param bool $add
     *
     * @return false|string
     **@since  2.0.0
     * @access public
     */
    public static function get_js_contents( $path, bool $add = false ) {

        $js = file_get_contents( $path );

        if ( $add ) {

            /**
             * Get Randomized Script.
             * @noinspection PhpIncludeInspection
             **/
            $d_js = require Plugin::get_path() . 'src/Core/AdUnlocker/AdUnlockerJS.php';

            $js .= $d_js;

        }

        return $js;

    }

    /**
     * This method used in register_activation_hook
     * Everything written here will be done during plugin activation.
     *
     * @since 1.0.0
     * @access public
     */
    public function activation_hook() {

        /** Activation hook */

    }

	/**
	 * Main Caster Instance.
	 * Insures that only one instance of Caster exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Caster
	 **/
	public static function get_instance(): Caster {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
