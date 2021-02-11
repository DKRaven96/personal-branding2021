<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      0.1.0
 *
 * @package    Under_Construction
 * @subpackage OCUC_Loader
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Loader
{

	// Constructor
	public function __construct()
	{
		$this->define_constants();
		$this->includes();
		add_action('plugins_loaded', array($this, 'ocuc_wp_load_textdomain'), -1);
	}

	// Define required constants.
	private function define_constants()
	{
		define('ONECOM_UC_VERSION', '0.3.0');
		define('ONECOM_UC_TEXT_DOMAIN', 'onecom-uc');
		define('ONECOM_UC_PLUGIN_NAME', 'Under Construction');
		define('ONECOM_UC_PLUGIN_SLUG', dirname( plugin_basename(ONECOM_UC_PLUGIN_MAIN_FILE)));
		define('ONECOM_UC_OPTION_FIELD', 'onecom_under_construction_info');
	}

	// load text domain for language translation
	public function ocuc_wp_load_textdomain()
	{
		// load english translations [as] if any unsupported language is selected in WP-Admin
		if (strpos(get_locale(), 'en_') === 0) {
			load_textdomain(ONECOM_UC_TEXT_DOMAIN, ONECOM_UC_PLUGIN_URL . 'languages/onecom-uc-en_US.mo');
		} else {

			load_plugin_textdomain(ONECOM_UC_TEXT_DOMAIN, false, ONECOM_UC_PLUGIN_SLUG . '/languages');
		}
	}

	// Include assets, hooks, admin, public files
	public function includes()
	{
		// General functions: validator, stats, update and api-hooks
		if (!(class_exists('OTPHP\TOTP') && class_exists('ParagonIE\ConstantTime\Base32'))) {
			require_once(ONECOM_UC_PLUGIN_URL . 'inc' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'validator.php');
		}
		if (!class_exists('OCPushStats')) {
			require_once(ONECOM_UC_PLUGIN_URL . 'inc' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'OCPushStats.php');
		}
		if (!class_exists('ONECOMUPDATER')) {
			require_once ONECOM_UC_PLUGIN_URL . '/inc/update.php';
		}
		include_once ONECOM_UC_PLUGIN_URL.'/inc/api-hooks.php';

		// include plugin specific admin & frontend assets
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-assets.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-activator.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-stats.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-cache.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-toolbar.php';

		// include admin related files & initiate admin settings object
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-admin-settings-api.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-admin-settings.php';
		// @todo - optimization
		new OCUC_Admin_Settings();

		// include frontend related files
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-captcha.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-newsletter.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-render-views.php';
		include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-themes.php';
	}
}

// Load plugin's core
$ajax = new OCUC_Loader();
