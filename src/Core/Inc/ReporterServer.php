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

use DateTime;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Used to implement System report handler class responsible for generating a report for the server environment.
 *
 * @since 1.0.0
 *
 **/
final class ReporterServer {

	/**
	 * ReporterServer.
	 *
     * @since 1.0.0
	 * @var ReporterServer
	 **/
	private static $instance;

	/**
	 * Get server environment reporter title.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return string - Report title.
	 **/
	public function get_title(): string {

		return esc_html__( 'Server Environment', 'AdUnlocker' );

	}

	/**
	 * Retrieve the required fields for the server environment report.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array - Required report fields with field ID and field label.
	 **/
	public function get_fields(): array {

	    $tabs = Plugin::get_tabs();
        $status_tab = $tabs['status'];
        $server_checks = $status_tab['reports']['server'];

        $checks = [];
        $checks = $this->add_check( $checks, $server_checks, 'os', esc_html__( 'Operating System', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'software', esc_html__( 'Software', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'mysql_version', esc_html__( 'MySQL version', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'php_version', esc_html__( 'PHP Version', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'write_permissions', esc_html__( 'Write Permissions', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'zip_installed', esc_html__( 'ZIP Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'elementor_installed', esc_html__( 'Elementor Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'allow_url_fopen', esc_html__( 'allow_url_fopen', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'dom_installed', esc_html__( 'DOM Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'xml_installed', esc_html__( 'XML Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'bcmath_installed', esc_html__( 'BCMath Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'mbstring_installed', esc_html__( 'mbstring Installed', 'AdUnlocker' ) );
        $checks = $this->add_check( $checks, $server_checks, 'server_time', esc_html__( 'Server Time Sync', 'AdUnlocker' ) );

		return $checks;

	}

    /**
     * Add server check if it's enabled.
     *
     * @param array $checks - List of enabled server checks.
     * @param array $server_checks - List of server checks from settings.
     * @param string $key - name of check.
     * @param string $label - Label for result.
     *
     * @return array - Required report fields with field ID and field label.
     *@since  1.0.0
     * @access public
     *
     */
	private function add_check( array $checks, array $server_checks, string $key, string $label ): array {

        if ( isset( $server_checks[ $key ] ) && $server_checks[ $key ] ) {
            $checks[ $key ] = $label;
        }

	    return $checks;

    }

	/**
	 * Get allow_url_fopen enabled.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the allow_url_fopen is enabled, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the allow_url_fopen is enabled, False otherwise.
	 * }
	 **/
	public function get_allow_url_fopen(): array {

		$allow_url_fopen = ini_get( 'allow_url_fopen' );

		return [
			'value' => $allow_url_fopen ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker' ) : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $allow_url_fopen,
			'recommendation' => esc_html__( 'You must enable allow_url_fopen option in PHP. Contact the support service of your hosting provider. They know what to do.', 'AdUnlocker' )
		];

	}

	/**
	 * Get server operating system.
	 * Retrieve the server operating system.
	 *
     * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server operating system.
	 * }
	 **/
	public function get_os(): array {
		return [
			'value' => PHP_OS,
		];
	}

	/**
	 * Get server software.
	 * Retrieve the server software.
	 *
     * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server software.
	 * }
	 **/
	public function get_software(): array {
		return [
			'value' => $_SERVER['SERVER_SOFTWARE'],
		];
	}

	/**
	 * Get PHP version.
	 * Retrieve the PHP version.
	 *
     * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value          PHP version.
	 *    @type string $recommendation Minimum PHP version recommendation.
	 *    @type bool   $warning        Whether to display a warning.
	 * }
	 **/
	public function get_php_version(): array {
		$result = [
			'value' => PHP_VERSION,
		];

		if ( version_compare( $result['value'], '5.6', '<' ) ) {
			$result['recommendation'] = esc_html__( 'We recommend to use php 5.6 or higher', 'AdUnlocker' );

			$result['warning'] = true;
		}

		return $result;
	}

	/**
	 * Get ZIP installed.
	 * Whether the ZIP extension is installed.
	 *
     * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   Yes if the ZIP extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the ZIP extension is installed, False otherwise.
	 * }
	 **/
	public function get_zip_installed() {
		$zip_installed = extension_loaded( 'zip' );

		return [
			'value' => $zip_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker') : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $zip_installed,
		];
	}

	/**
	 * Get Elementor installed.
	 * Whether the Elementor builder is installed.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array Report data.
	 *          @type string $value   YES if the Elementor builder is installed, NO otherwise.
	 *          @type bool   $warning Whether to display a warning.
	 **/
	public function get_elementor_installed() {

		/** Check if Elementor installed and activated. */
		$elementor_installed = did_action( 'elementor/loaded' );

		return [
			'value' => $elementor_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker') : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $elementor_installed,
			'recommendation' => esc_html__( 'You need install and activate Elementor builder. Go to Elementor site (elementor.com) for details.', 'AdUnlocker' )
		];

	}

	/**
	 * Get DOM installed.
	 * Whether the DOM extension is installed.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the DOM extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the DOM extension is installed, False otherwise.
	 * }
	 **/
	public function get_dom_installed() {

		$dom_installed = extension_loaded( 'dom' );

		return [
			'value' => $dom_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker') : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $dom_installed,
			'recommendation' => esc_html__(' You must enable DOM extension (Document Object Model) in PHP. It\'s used for HTML processing. Contact the support service of your hosting provider. They know what to do.', 'AdUnlocker' )
		];

	}

	/**
	 * Get XML installed.
	 * Whether the XML extension is installed.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the XML extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the XML extension is installed, False otherwise.
	 * }
	 **/
	public function get_xml_installed() {

		$xml_installed = extension_loaded( 'xml' );

		return [
			'value' => $xml_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker' ) : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $xml_installed,
			'recommendation' => esc_html__( 'You must enable XML extension in PHP. It\'s used for XML processing. Contact the support service of your hosting provider. They know what to do.', 'AdUnlocker' )
		];

	}

	/**
	 * Get BCMath installed.
	 * Whether the BCMath extension is installed.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the BCMath extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the BCMath extension is installed, False otherwise.
	 * }
	 **/
	public function get_bcmath_installed() {

		$bcmath_installed = extension_loaded( 'bcmath' );

		return [
			'value' => $bcmath_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'YES', 'AdUnlocker' ) : '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'NO', 'AdUnlocker' ),
			'warning' => ! $bcmath_installed,
			'recommendation' => esc_html__( 'You must enable BCMath extension (Arbitrary Precision Mathematics) in PHP. Contact the support service of your hosting provider. They know what to do.', 'AdUnlocker' )
		];

	}

    /**
     * Get mbstring installed.
     * Whether the mbstring extension is installed.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array {
     *    Report data.
     *
     *    @type string $value   YES if the mbstring extension is installed, NO otherwise.
     *    @type bool   $warning Whether to display a warning. True if the mbstring extension is installed, False otherwise.
     * }
     **/
    public function get_mbstring_installed() {

        $mbstring_installed = extension_loaded( 'mbstring' );

        return [
            'value' => $mbstring_installed ? '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__('YES', 'AdUnlocker' ) : '<i class="material-icons ppc-system-no">error</i>' . esc_html__('NO', 'AdUnlocker' ),
            'warning' => ! $mbstring_installed,
            'recommendation' => esc_html__('You must enable mbstring extension (Multibyte String) in PHP. Contact the support service of your hosting provider. They know what to do.', 'AdUnlocker' )
        ];
    }

	/**
	 * Get MySQL version.
	 * Retrieve the MySQL version.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value MySQL version.
	 * }
	 **/
	public function get_mysql_version() {

		global $wpdb;

		$db_server_version = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );

		return [
			'value' => $db_server_version['version_comment']->Value . ' v' . $db_server_version['innodb_version']->Value,
		];

	}

	/**
	 * Get write permissions.
	 * Check whether the required folders has writing permissions.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   Writing permissions status.
	 *    @type bool   $warning Whether to display a warning. True if some required
	 *                          folders don't have writing permissions, False otherwise.
	 * }
	 **/
	public function get_write_permissions() {

		$paths_to_check = [
			ABSPATH => esc_html__( 'WordPress root directory', 'AdUnlocker' )
		];

		$write_problems = [];

		$wp_upload_dir = wp_upload_dir();

		if ( $wp_upload_dir[ 'error' ] ) {
			$write_problems[] = esc_html__( 'WordPress root uploads directory', 'AdUnlocker' );
		}

		$htaccess_file = ABSPATH . '/.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			$paths_to_check[ $htaccess_file ] = esc_html__( '.htaccess file', 'AdUnlocker' );
		}

		foreach ( $paths_to_check as $dir => $description ) {

			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}

		if ( $write_problems ) {

			$value = '<i class="material-icons ppc-system-no">error</i>' . esc_html__( 'There are some writing permissions issues with the following directories/files:', 'AdUnlocker' ) . "<br> &nbsp;&nbsp;&nbsp;&nbsp;– ";
			$value .= implode( "<br> &nbsp;&nbsp;&nbsp;&nbsp;– ", $write_problems );

		} else {

			$value = '<i class="material-icons ppc-system-yes">check_circle</i>' . esc_html__( 'All right', 'AdUnlocker' );

		}

		return [
			'value' => $value,
			'warning' => (bool) $write_problems,
		];

	}

	/**
	 * Get report.
	 * Retrieve the report with all it's containing fields.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array {
	 *    Report fields.
	 *
	 *    @type string $name Field name.
	 *    @type string $label Field label.
	 * }
	 **/
	public function get_report() {

		$result = [];

		foreach ( $this->get_fields() as $field_name => $field_label ) {

			$method = 'get_' . $field_name;

			$reporter_field = [
				"name" => $field_name,
				'label' => $field_label,
			];

			/** @noinspection SlowArrayOperationsInLoopInspection */
			$reporter_field        = array_merge( $reporter_field, $this->$method() );
			$result[ $field_name ] = $reporter_field;

		}

		return $result;

	}

	/**
	 * Main ReporterServer Instance.
	 *
	 * Insures that only one instance of ReporterServer exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
	 * @return ReporterServer
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class ReporterServer.
