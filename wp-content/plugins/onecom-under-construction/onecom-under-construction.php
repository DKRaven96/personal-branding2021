<?php

/**
 * Plugin name: one.com Under Construction
 * Plugin uri: https://one.com
 * Author: one.com
 * Version: 0.3.0
 * Plugin URI: https://www.one.com/en/wordpress-hosting
 * Description: Create a Coming soon, Under construction, or Maintenance page. You can customize the page by adding a logo, heading, description, timer, subscribe form, social icons and more.
 * Text Domain: onecom-uc
 * Domain Path: /languages
 * License: GPL v2 or later
 * 
 * 	Copyright 2021 one.com
 * 
 * 	This program is free software; you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation; either version 2 of the License, or
 * 	(at your option) any later version.
 * 
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Define essential constants.
define('ONECOM_UC_PLUGIN_MAIN_FILE', __FILE__);
define('ONECOM_UC_PLUGIN_URL', plugin_dir_path(__FILE__));
define('ONECOM_UC_DIR_URL', plugin_dir_url(__FILE__));

// The code that runs during plugin activation.
function activate_under_construction()
{
	include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-activator.php';
	new OCUC_Activator();
}

// The code that runs during plugin de-activation.
function deactivate_under_construction()
{
	include_once ONECOM_UC_PLUGIN_URL . 'inc/classes/class-ocuc-deactivator.php';
	new OCUC_Deactivator();
}

// Activation and deactivation hook
register_activation_hook(__FILE__, 'activate_under_construction');
register_deactivation_hook(__FILE__, 'deactivate_under_construction');

// Because ajax handles works well outside class
add_action('wp_ajax_oc_newsleter_sub', ['OCUC_Newsletter', 'newsletter_cb']);
add_action('wp_ajax_nopriv_oc_newsleter_sub', ['OCUC_Newsletter', 'newsletter_cb']);

/**
 * The main plugin class that is used to define:
 * * admin-specific hooks,
 * * and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__) . 'inc/classes/class-ocuc-loader.php';
