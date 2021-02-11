<?php

/**
 * Hook into frontend to load under construction theme
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Themes
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Themes
{
	// Main hook to call under construction feature
	public function __construct()
	{
		add_action('template_redirect', array($this, 'under_construction'));
	}

	// Render under construction feature and exit (die) to prevent wp theme loading
	function under_construction()
	{
		// Get UC Options data
		$uc_data = new OCUC_Render_Views();
		$uc_option = $uc_data->get_uc_option();

		// Check if current user is logged-in and whitelisted
		if (is_user_logged_in()) {
			$whitelisted_users = isset($uc_option['uc_whitelisted_roles']) && !empty($uc_option['uc_whitelisted_roles']) ? $uc_option['uc_whitelisted_roles'] : array();
			// get current user role/roles (yes, multiple is possible :O )
			$user = wp_get_current_user();
			$user_roles = array_values($user->roles);
			$whitelisted_users = array_values($whitelisted_users);
			// match if current role exists in whitelisted users, returns empty if no match
			$current_whitelisted = array_intersect($user_roles, $whitelisted_users);
		} else {
			// empty means no whitelisted role
			$current_whitelisted =  array();
		}

		/**
		 * If under construction status enabled and
		 * * no current user role is whitelisted
		 * * * show under construction page
		 */
		if ($uc_option['uc_status'] === 'on' && (count($current_whitelisted)) === 0) {

			// Send 503 headers if maintenance mode
			$uc_http_mode = $uc_option['uc_http_mode'];
			if ($uc_http_mode == '503') {
				header('HTTP/1.1 503 Service Unavailable');
			}

			// render selected theme design (default: theme-1)
			$theme_folder = isset($uc_option['uc_theme']) && strlen($uc_option['uc_theme']) ? $uc_option['uc_theme'] : 'theme-1';
			include_once ONECOM_UC_PLUGIN_URL . 'themes/' . $theme_folder . '/index.php';
			
			// @todo - comment before phpunit & uncomment before deploy
			die();
		}
	}
}

$main = new OCUC_Themes();
