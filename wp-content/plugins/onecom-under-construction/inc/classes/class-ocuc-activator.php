<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Activator
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

final class OCUC_Activator
{

	/**
	 * On activation, set default under-construction settings and data if not exists
	 * Note: onecom plugins and WP plugins both returns $pagenow as plugins_page
	 */
	public function __construct()
	{

		// trigger plugin activation log
		global $pagenow;
		if ($pagenow === 'plugins.php') {
			$referrer = 'plugins_page';
		} else {
			$referrer = 'install_wizard';
		}

		// @todo - uncomment before deploy - comment before phpunit
		(class_exists('OCPushStats') ? \OCPushStats::push_stats_event_themes_and_plugins('activate', 'plugin', ONECOM_UC_PLUGIN_SLUG, $referrer) : '');

		// if no option data exists for uc, set default (on first time activation)
		if (get_option('onecom_under_construction_info') === false) {
			$uc_data = array(
				'uc_status' => 'off',
				'uc_http_mode' => '200',
				'uc_theme' => 'theme-1',
				'uc_timer_switch' => 'on',
				'uc_subscribe_form' => 'on',
				'uc_whitelisted_roles' =>  array('administrator' => 'administrator'),
				'uc_headline' => 'Something is happening. Check in later!',
				'uc_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ultrices neque ornare aenean euismod elementum nisi quis eleifend. Sagittis eu volutpat odio facilisis mauris. Sed risus pretium quam vulputate. Fermentum dui faucibus in ornare.',
				'uc_page_bg_color' => '#e5e5e5',
				'uc_primary_color' => '#0078c8',
				'uc_copyright' => 'Copyright © 2020. All rights reserved'
			);
			update_option('onecom_under_construction_info', $uc_data);
		}
	}
}
