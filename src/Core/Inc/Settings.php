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
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0.0
 *
 **/
final class Settings {

	/**
	 * Plugin settings.
     *
     * @since 1.0.0
     * @access public
	 * @var array
	 **/
	public $options = [];

	/**
	 * Settings.
	 *
     * @since 1.0.0
     * @access private
	 * @var Settings
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
     * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function __construct() {

		/** Get plugin settings. */
	    $this->get_options();

        /** Add plugin settings page. */
        $this->add_settings_page();

    }

	/**
	 * Render Tabs Headers.
	 *
     * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function render_tabs() {

	    /** Selected tab key. */
        $current = $this->get_current_tab();

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** Render Tabs. */
		?>
        <aside class="ppc-drawer">
            <div class="ppc-drawer__content">
                <nav class="ppc-list">

                    <?php $this->render_logo(); ?>
<!--                    <hr class="ppc-plugin-menu">-->
                    <hr class="ppc-list-divider">

                    <h6 class="ppc-list-group__subheader"><?php echo esc_html__( 'Plugin Settings', 'AdUnlocker' ) ?></h6>
					<?php

					foreach ( $tabs as $tab => $value ) {

                        /** Skip disabled tabs. */
					    if ( ! Tab::is_tab_enabled( $tab ) ) { continue; }

						/** Prepare CSS classes. */
						$classes = [];
						$classes[] = 'ppc-list-item';
                        $classes[] = "pp-menu-tab-{$tab}";

						/** Mark Active Tab. */
						if ( $tab === $current ) {
							$classes[] = 'ppc-list-item--activated';
						}

						/** Prepare link. */
						$link = '?page=pp_AdUnlocker_settings&tab=' . $tab;

						?>
                        <a class="<?php esc_attr_e( implode( ' ', $classes ) ); ?>" href="<?php esc_attr_e( $link ); ?>">
                            <i class='material-icons ppc-list-item__graphic' aria-hidden='true'><?php esc_html_e( $value['icon'] ); ?></i>
                            <span class='ppc-list-item__text'><?php esc_html_e( $value['label'] ); ?></span>
                        </a>
						<?php

					}

					/** Helpful links. */
					$this->support_link();

					?>
                </nav>
            </div>
        </aside>
		<?php
	}

	/**
	 * Displays useful links for an activated and non-activated plugin.
     *
     * @since 1.0.0
     * @access private
	 *
     * @return void
	 **/
	private function support_link() {

	    /** Disable this method for custom type plugins. */
	    if ( 'custom' === Plugin::get_type() ) { return; }

	    ?>
        <hr class="ppc-list-divider">
        <h6 class="ppc-list-group__subheader"><?php echo esc_html__( 'Support', 'AdUnlocker' ) ?></h6>

<!--        <a class="ppc-list-item" href="#" target="_blank">-->
<!--            <i class="material-icons ppc-list-item__graphic" aria-hidden="true">integration_instructions</i>-->
<!--            <span class="ppc-list-item__text">--><?php //echo esc_html__( 'Documentation', 'AdUnlocker' ) ?><!--</span>-->
<!--        </a>-->

        <a class="ppc-list-item" href="mailto:support@pixpal.net" target="_blank">
            <i class="material-icons ppc-list-item__graphic" aria-hidden="true">mail</i>
            <span class="ppc-list-item__text"><?php echo esc_html__( 'Get help', 'AdUnlocker' ) ?></span>
        </a>
        <a class="ppc-list-item" href="https://wordpress.org/support/plugin/adunlocker/reviews/" target="_blank">
            <i class="material-icons ppc-list-item__graphic" aria-hidden="true">thumb_up</i>
            <span class="ppc-list-item__text"><?php echo esc_html__( 'Rate this plugin', 'AdUnlocker' ) ?></span>
        </a>

<!--        <a class="ppc-list-item" href="#" target="_blank">-->
<!--            <i class="material-icons ppc-list-item__graphic" aria-hidden="true">store</i>-->
<!--            <span class="ppc-list-item__text">--><?php //echo esc_html__( 'More plugins', 'AdUnlocker' ) ?><!--</span>-->
<!--        </a>-->
		<?php

	}

	/**
	 * Add plugin settings page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 1000 );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Generate Settings Page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function settings_init() {

        /** Add settings foreach tab. */
        foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

            /** Skip tabs without handlers. */
            if ( empty( $tab['class'] ) ) { continue; }

            /** Call add_settings from appropriate class for each tab. */
            call_user_func( [ $tab['class'], 'get_instance' ] )->add_settings( $tab_slug );

        }

	}

	/**
	 * Add admin menu for plugin settings.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function add_admin_menu() {

	    /** Submenu for Elementor plugins. */
        if ( 'elementor' === Plugin::get_type() ) {

            $this->add_submenu_elementor();

        /** Root level menu for WordPress plugins. */
        } else {

            $this->add_menu_wordpress();

        }

	}

    /**
     * Add admin menu for Elementor plugins.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
	private function add_submenu_elementor() {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';
        if ( did_action( 'elementor/loaded' ) ) {
            $parent = 'elementor';
            //$parent = 'edit-comments.php';

        }

        add_submenu_page(
            $parent,
            esc_html__( 'AdUnlocker Settings', 'AdUnlocker' ),
            esc_html__( 'AdUnlocker for ', 'AdUnlocker' ),
            'manage_options',
            'pp_AdUnlocker_settings',
            [ $this, 'options_page' ]
        );

    }

    /**
     * Add admin menu for WordPress plugins.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
	private function add_menu_wordpress() {

        add_menu_page(
            esc_html__( 'AdUnlocker Settings', 'AdUnlocker' ),
            esc_html__( 'AdUnlocker', 'AdUnlocker' ),
            'manage_options',
            'pp_AdUnlocker_settings',
            [ $this, 'options_page' ],
            $this->get_admin_menu_icon(),
            $this->get_admin_menu_position()
        );

    }

    /**
     * Return path to admin menu icon or base64 encoded image.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
	private function get_admin_menu_icon() {

	    return 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( Plugin::get_path() . 'images/logo-menu.svg' ) );

    }

    /**
     * Calculate admin menu position based on plugin slug value.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
	private function get_admin_menu_position() {

        $hash = md5( Plugin::get_slug() );

        $int = (int) filter_var( $hash, FILTER_SANITIZE_NUMBER_INT );
        $int =  (int) ( $int / 1000000000000 );

        return '58.' . $int;

    }

	/**
	 * Plugin Settings Page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) { return; } ?>

        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">
				<?php

				/** Render "Settings saved!" message. */
				$this->render_nags();

				/** Render Tabs Headers. */
				?><section class="pp-aside"><?php $this->render_tabs(); ?></section><?php

				/** Render Tabs Body. */
				?><section class="pp-tab-content pp-tab-name-<?php esc_attr_e( $this->get_current_tab() ) ?>"><?php

                /** Call settings from appropriate class for current tab. */
                foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

                    /** Work only on current tab. */
                    if ( ! $this->is_tab( $tab_slug ) ) { continue; }

                    /** Skip tabs without handlers. */
                    if ( empty( $tab['class'] ) ) { continue; }

                    call_user_func( [ $tab['class'], 'get_instance' ] )->do_settings( $tab_slug );

                }

                ?>
                </section>
            </div>
        </form>

		<?php
	}

    /**
     * Return current selected tab or first tab.
     *
     * @since  1.0.0
     * @access private
     *
     * @return string
     **/
	private function get_current_tab() {

        $tab = key( Plugin::get_tabs() ); // First tab is default tab

        if ( isset ( $_GET['tab'] ) ) {

            $tab = strval($_GET['tab']);

        }

        return $tab;
    }

    /**
     * Check if passed tab is current tab and tab is enabled.
     *
     * @param string $tab - Tab slug to check.
     *
     * @since  1.0.0
     * @access private
     *
     * @return bool
     **/
	private function is_tab( $tab ) {

        $current_tab = $this->get_current_tab();

        return ( $tab === $current_tab ) && Tab::is_tab_enabled( $current_tab );

    }

	/**
	 * Render nags on after settings save.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
	 **/
	private function render_nags() {

        /** Exit if this is not settings update. */
		if ( ! isset( $_GET['settings-updated'] ) ) { return; }

        /** Render "Settings Saved" message. */
        $this->render_nag_saved();

	}

    /**
     * Render "Settings Saved" message.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function render_nag_saved() {

        /** Exit if settings saving was not successful. */
        if ( 'true' !== $_GET['settings-updated'] ) { return; }

        /** Render "Settings Saved" message. */
        UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'AdUnlocker' ) );

    }

	/**
	 * Render logo and Save changes button in plugin settings.
	 *
     * @since 1.0.0
	 * @access private
     *
	 * @return void
	 **/
	private function render_logo() {

		?>
        <div class="ppc-drawer__header ppc-plugin-fixed">
            <a class="ppc-list-item pp-plugin-title" href="#">
                <i class="ppc-list-item__graphic" aria-hidden="true">
                    <img width="20px" src="<?php echo esc_attr( Plugin::get_url() . 'images/logo-color.svg' ); ?>" alt="<?php esc_html_e( 'AdUnlocker', 'AdUnlocker' ) ?>">
                </i>
                <span class="ppc-list-item__text">
                    <?php if ( 'wordpress' === Plugin::get_type() ) : ?>
                        <?php esc_html_e( 'AdUnlocker', 'AdUnlocker' ) ?>
                    <?php else: ?>
                        <?php esc_html_e( 'AdUnlocker', 'AdUnlocker' ) ?>
                    <?php endif; ?>
                </span>
                <span class="ppc-list-item__text">
                    <sup>
                        <?php esc_html_e( 'v.', 'AdUnlocker' ); ?><?php esc_attr_e( Plugin::get_version() ); ?>
                    </sup>
                </span>
            </a>
            <button type="submit" name="submit" id="submit" class="ppc-button ppc-button--dense ppc-button--raised">
                <span class="ppc-button__label"><?php esc_html_e( 'Save changes', 'AdUnlocker' ) ?></span>
            </button>
        </div>
		<?php

	}

    /**
     * Return settings array with default values.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array
     **/
	private function get_default_settings() {

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        $default = [];

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

            /** If current tab haven't fields. */
            if ( empty( $tab['fields'] ) ) { continue; }

            /** Collect default values from each field. */
            foreach ( $tab['fields'] as $field_slug => $field ) {

                $default[$field_slug] = $field['default'];

            }

        }

        return $default;

    }

	/**
	 * Get plugin settings with default values.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function get_options() {

        /** Default values. */
        $defaults = $this->get_default_settings();

        $results = [];

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

	        $opt_name = "pp_AdUnlocker_{$tab_slug}_settings";
            $options = get_option( $opt_name );
            $results = wp_parse_args( $options, $defaults );
            $defaults = $results;

        }

		$this->options = $results;

	}

	/**
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Settings
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
