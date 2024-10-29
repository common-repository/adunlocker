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

use Core\AdUnlocker\Inc\Plugin;
use Core\AdUnlocker\Inc\Settings;
use Core\AdUnlocker\Inc\TabGeneral;

final class Config {

	/**
	 * Settings.
	 *
     * @since 1.0.0
     * @access private
	 * @var Config
	 **/
	private static $instance;

    /**
     * Prepare plugin settings by modifying the default one.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function prepare_settings() {

        /** Get default plugin settings. */
        $tabs = Plugin::get_tabs();

        /** Change General tab title. */
        $tabs['general']['title'] = esc_html__( 'AdUnlocker Settings', 'AdUnlocker' );

        /** Set System Requirements. */
	    $tabs['status']['reports']['server']['bcmath_installed'] = false;

        # Algorithm
        $tabs['general']['fields']['algorithm'] = [
            'type'              => 'select',
            'label'             => esc_html__( 'Algorithm:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Algorithm', 'AdUnlocker' ),
            'description'       => esc_html__('The AdUnlocker supports several algorithms. Choose the most suitable for your needs. ', 'AdUnlocker' ),
//                esc_html__('Read more ', 'AdUnlocker' ) .
//                '<a href="https://pixpal.net/algorithms-of-the-adunlocker-wordpress-plugin/" target="_blank" rel="noopener">' .
//                esc_html__('about algorithms', 'AdUnlocker' ) .
//                '</a>' .
//                esc_html__(' in the documentation.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'inline',
            'options'           => [
                'inline'        => esc_html__( 'Inline', 'AdUnlocker' ),
                'random-folder' => esc_html__( 'Random Folder', 'AdUnlocker' ),
                'proxy'         => esc_html__( 'Script Proxy', 'AdUnlocker' ),
            ]
        ];

		# Random folder lifetime
	    $tabs['general']['fields']['lifetime'] = [
			'type'              => 'slider',
			'label'             => esc_html__( 'Lifetime:', 'AdUnlocker' ),
			'show_label'        => true,
			'description'       => esc_html__( 'Random folder lifetime ', 'AdUnlocker' ) .
			                       ' <strong>' .
			                       esc_html( ( ! empty( Settings::get_instance()->options['lifetime'] ) ) ? Settings::get_instance()->options['lifetime'] : '1' ) .
			                       '</strong>' . esc_html__( ' days.', 'AdUnlocker' ),
			'show_description'  => true,
			'min'               => 1,
			'max'               => 365,
			'step'              => 1,
			'default'           => 1,
			'discrete'          => true,
		];

        # Modal Style
        $tabs['general']['fields']['style'] = [
            'type'              => 'select',
            'label'             => esc_html__( 'Modal Style:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Modal Style', 'AdUnlocker' ),
            'description'       => esc_html__('AdUnlocker modal window style.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'compact',
            'options'           => [
                'compact'               => esc_html__( 'Compact', 'AdUnlocker' ),
                'compact-right-top'     => esc_html__( 'Compact: Upper Right Corner', 'AdUnlocker' ),
                'compact-left-top'      => esc_html__( 'Compact: Upper Left Corner', 'AdUnlocker' ),
                'compact-right-bottom'  => esc_html__( 'Compact: Bottom Right Corner', 'AdUnlocker' ),
                'compact-left-bottom'   => esc_html__( 'Compact: Bottom Left Corner', 'AdUnlocker' ),
                'full'                  => esc_html__( 'Full Screen', 'AdUnlocker' ),
            ]
        ];

        # Title
        $tabs['general']['fields']['title'] = [
            'type'              => 'text',
            'label'             => esc_html__( 'Title:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Title', 'AdUnlocker' ),
            'description'       => esc_html__( 'Modal window title.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => esc_html__( 'It Looks Like You Have AdBlocker Enabled', 'AdUnlocker' ),
            'attr'              => [
                'maxlength' => '1000'
            ]
        ];

        # Content
        $tabs['general']['fields']['content'] = [
            'type'              => 'editor',
            'label'             => esc_html__( 'Content:', 'AdUnlocker' ),
            'show_label'        => true,
            'description'       => '',
            'show_description'  => false,
            'default'           => '<p>' . esc_html__( 'Please disable AdBlock to proceed to the destination page.', 'AdUnlocker' ) . '<p>',
            'attr'              => [
                'textarea_rows' => '3'
            ]
        ];

        # Overlay Color
        $tabs['general']['fields']['bg_color'] = [
            'type'              => 'colorpicker',
            'label'             => esc_html__( 'Overlay Color:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Overlay Color', 'AdUnlocker' ),
            'description'       => esc_html__( 'Page overlay Background Color.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'rgba(255,0,0,0.75)',
            'attr'              => [
                'readonly'      => 'readonly',
            ]
        ];

        # Modal Color
        $tabs['general']['fields']['modal_color'] = [
            'type'              => 'colorpicker',
            'label'             => esc_html__( 'Modal Color:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Modal Color', 'AdUnlocker' ),
            'description'       => esc_html__( 'Modal window Background Color.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'rgba(255,255,255,1)',
            'attr'              => [
                'readonly'      => 'readonly',
            ]
        ];

	    $tabs['general']['fields']['close_color'] = [
		    'type'              => 'colorpicker',
		    'label'             => esc_html__( 'Close Button Color:', 'AdUnlocker' ),
		    'show_label'        => true,
		    'placeholder'       => esc_html__( 'Close Button Color', 'AdUnlocker' ),
		    'description'       => esc_html__( 'Close button color inside a modal window.', 'AdUnlocker' ),
		    'show_description'  => true,
		    'default'           => '#23282d',
		    'attr'              => [
			    'readonly'      => 'readonly',
		    ]
	    ];


	    # Text Color
        $tabs['general']['fields']['text_color'] = [
            'type'              => 'colorpicker',
            'label'             => esc_html__( 'Text Color:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Text Color', 'AdUnlocker' ),
            'description'       => esc_html__( 'Text color inside a modal window.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => '#23282d',
            'attr'              => [
                'readonly'      => 'readonly',
            ]
        ];

        # Is it possible to close?
        $tabs['general']['fields']['closeable'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Is it possible to close?', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Closable', 'AdUnlocker' ),
            'description'       => esc_html__( 'The user can close the window and continue browsing the site.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        # Blur Content
        $tabs['general']['fields']['blur'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Blur Content:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Blur Content', 'AdUnlocker' ),
            'description'       => esc_html__( 'Effects like blur or color shifting on an element\'s rendering before the element is displayed.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        # JavaScript Required
        $tabs['general']['fields']['javascript'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'JavaScript Required:', 'AdUnlocker' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect if JavaScript is Disabled', 'AdUnlocker' ),
            'description'       => esc_html__( 'Block page content if JS is disabled.', 'AdUnlocker' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        # JavaScript Message
        $tabs['general']['fields']['javascript_msg'] = [
            'type'              => 'editor',
            'label'             => esc_html__( 'JavaScript Message:', 'AdUnlocker' ),
            'show_label'        => true,
            'description'       => esc_html__( 'Message to show if JavaScript is Disabled.', 'AdUnlocker' ),
            'show_description'  => false,
            'default'           => '<h3>' . esc_html__( 'Please Enable JavaScript in your Browser to Visit this Site.', 'AdUnlocker' ) . '<h3>',
            'attr'              => [
                'textarea_rows' => '3'
            ]
        ];

        /** Behaviour Tab */
	    $tabs = $this->tab_behaviour( $tabs );
	    $tabs = $this->refresh_settings( $tabs );
	    $tabs = $this->tab_behaviour( $tabs );

        /** Set updated tabs. */
        Plugin::set_tabs( $tabs );

        /** Refresh settings. */
        Settings::get_instance()->get_options();

    }

	/**
	 * Refresh settings
	 *
	 * @param $tabs
	 *
	 * @return array
	 */
	private function refresh_settings( $tabs ): array {

		/** Set updated tabs. */
		Plugin::set_tabs( $tabs );

		/** Refresh settings. */
		Settings::get_instance()->get_options();

		/** Get default plugin settings. */
		return Plugin::get_tabs();

	}

	/**
	 * Create Behaviour tab.
	 *
	 * @param array $tabs - List of tabs with all settings and fields.
	 *
	 * @return array - List of tabs with all settings and fields.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	private function tab_behaviour( array $tabs ): array {

		/** Shorthand access to plugin settings. */
		$options = Settings::get_instance()->options;

		/** Add Behaviour tab after General. */
		$offset = 1; // Position for new tab.
		$tabs = array_slice( $tabs, 0, $offset, true ) +
		        ['behaviour' => [
			        'enabled'       => true,
			        'class'         => TabGeneral::class,
			        'label'         => esc_html__( 'Behaviour', 'readabler' ),
			        'title'         => esc_html__( 'Behaviour Settings', 'readabler' ),
			        'show_title'    => true,
			        'icon'          => 'tune',
			        'fields'        => []
		        ] ] +
		        array_slice( $tabs, $offset, NULL, true );

		# Delay
		$tabs['behaviour']['fields']['timeout'] = [
			'type'              => 'slider',
			'label'             => esc_html__( 'Delay:', 'AdUnlocker' ),
			'show_label'        => true,
			'description'       => esc_html__( 'Modal window or redirect act after ', 'AdUnlocker' ) .
			                       ' <strong>' .
			                       esc_html( ( ! empty( Settings::get_instance()->options['timeout'] ) ) ? Settings::get_instance()->options['timeout'] : '0' ) .
			                       '</strong>' . esc_html__( ' milliseconds.', 'AdUnlocker' ),
			'show_description'  => true,
			'min'               => 0,
			'max'               => 10000,
			'step'              => 100,
			'default'           => 0,
			'discrete'          => true,
		];

		$tabs['behaviour']['fields']['is_redirect'] = [
			'type'              => 'switcher',
			'label'             => esc_html__( 'Redirect', 'AdUnlocker' ),
			'show_label'        => true,
			'placeholder'       => esc_html__( 'Redirect', 'AdUnlocker' ),
			'description'       => esc_html__( 'Redirect to another URL instead of a display of popup', 'AdUnlocker' ),
			'show_description'  => true,
			'default'           => 'off',
		];

		$tabs['behaviour']['fields']['redirect'] = [
			'type'              => 'text',
			'label'             => esc_html__( 'Redirect URL:', 'AdUnlocker' ),
			'show_label'        => true,
			'placeholder'       => esc_html__( 'Redirect URL', 'AdUnlocker' ),
			'description'       => esc_html__( 'Redirect link', 'AdUnlocker' ),
			'show_description'  => true,
			'default'           => '',
		];

		/** If no options on this tab - disable it. */
		if ( ! count( $tabs['behaviour']['fields'] ) ) {
			$tabs['behaviour']['enabled'] = false;
		}

		return $tabs;

	}

	/**
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Config
	 **/
	public static function get_instance(): Config {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
