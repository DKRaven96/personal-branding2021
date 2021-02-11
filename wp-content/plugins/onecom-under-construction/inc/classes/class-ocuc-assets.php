<?php

/**
 * Defines assets functions
 *
 * This class includes all assets for admin and public.
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Assets
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Assets
{

	/**
	 * Constructor to add actions for enqueue styles and scripts
	 */
	public function __construct()
	{
		add_action('admin_enqueue_scripts', array($this, 'admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles()
	{

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Register admin styles.
		wp_register_style('onecom_uc_flatpickr_styles', ONECOM_UC_DIR_URL . '/assets/css/flatpickr.css', array(), ONECOM_UC_VERSION);
		wp_register_style('onecom_uc_admin_styles', ONECOM_UC_DIR_URL . '/assets/css/admin.css', array(), ONECOM_UC_VERSION);

		// Enqueue style only on required plugin pages
		if (in_array($screen_id, array('toplevel_page_onecom-wp-under-construction'))) {
			wp_enqueue_style('onecom_uc_flatpickr_styles');
			wp_enqueue_style('onecom_uc_admin_styles');
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts()
	{
		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';

		// Register scripts.
		wp_register_script('onecom_uc_flatpickr_script', ONECOM_UC_DIR_URL . '/assets/js/flatpickr.js', array('jquery'), ONECOM_UC_VERSION, true);
		wp_register_script('onecom_uc_admin_script', ONECOM_UC_DIR_URL . '/assets/js/admin.js', array('jquery'), ONECOM_UC_VERSION, true);

		// Enqueue script only on plugin pages
		if (in_array($screen_id, array('toplevel_page_onecom-wp-under-construction'))) {
			wp_enqueue_script('onecom_uc_flatpickr_script');
			wp_enqueue_script('onecom_uc_admin_script');
		}
	}
}

$assets = new OCUC_Assets();
