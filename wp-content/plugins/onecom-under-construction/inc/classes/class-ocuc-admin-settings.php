<?php

/**
 * Defines admin settings functions (sections and fields)
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Admin_Settings
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Admin_Settings
{
	private $settings_api;

	function __construct()
	{
		$this->settings_api = new OCUC_Admin_Settings_API;
		add_action('admin_init', array($this, 'uc_settings_init_fn'));
		add_action('admin_menu', array($this, 'uc_add_page_fn'));
		add_action('admin_init', array($this->settings_api, 'settings_init'));
		add_action('admin_head', array($this, 'uc_menu_icon_css_fn'));
	}

	// Add sections/groups for different fields
	function get_settings_sections()
	{
		$sections = array(
			array(
				'id'    => 'onecom_under_construction_settings',
				'title' => __('General settings', ONECOM_UC_TEXT_DOMAIN),
				'desc'	=> '',
				'callback' => 'callback_section'
			),
			array(
				'id'    => 'onecom_under_construction_content',
				'title' => __('Content', ONECOM_UC_TEXT_DOMAIN),
				'callback' => 'callback_section'
			),
			array(
				'id'    => 'onecom_under_construction_customization',
				'title' => __('Customization', ONECOM_UC_TEXT_DOMAIN),
				'callback' => 'callback_section'
			)
		);
		return $sections;
	}

	/**
	 * Returns all the settings fields for above sections
	 *
	 * @return array settings fields
	 */
	function get_settings_fields()
	{
		// prepare users array to whitelist via multicheck option
		$role_info = wp_roles();
		$users_list = $role_info->role_names;

		$settings_fields = array(
			'onecom_under_construction_settings' => array(

				array(
					'name'    => 'uc_status',
					'label'   => __('Status', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
					'desc'	  => __('Enable under construction, or maintenance mode page on your website', ONECOM_UC_TEXT_DOMAIN)
				),

				array(
					'name'    => 'uc_theme',
					'label'   => __('Select design', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Choose a design for under construction page', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'radio_image',
					'options' => array(
						'theme-1' => 'design-1.png',
						'theme-2' => 'design-2.png',
						'theme-3' => 'design-3.png',
					)
				),

				array(
					'name'    => 'uc_http_mode',
					'label'   => __('Mode', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'radio',
					'options' => array(
						'200' => __('Coming soon', ONECOM_UC_TEXT_DOMAIN) .
							' <p class="description" style="margin-bottom:6px;">' .
							__('Returns standard 200 HTTP OK response code to indexing robots', ONECOM_UC_TEXT_DOMAIN) .
							'</p>',
						'503' => __('Maintenance mode', ONECOM_UC_TEXT_DOMAIN) .
							' <p class="description" style="margin-bottom:6px;">' .
							__('Returns 503 HTTP Service unavailable code to indexing robots', ONECOM_UC_TEXT_DOMAIN) .
							'</p>',
					)
				),

				array(
					'name'    => 'uc_timer_switch',
					'label'   => __('Countdown timer', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Would you like to show countdown timer?', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
				),

				array(
					'name'              => 'uc_timer',
					'label'             => '',
					'type'              => 'datetime',
					'placeholder'           => __('Select date', ONECOM_UC_TEXT_DOMAIN),
					'desc'				=> __('Set countdown timer. Current Wordpress time: ', ONECOM_UC_TEXT_DOMAIN) .
						current_time('Y-m-d H:i') . '. <a href="' . admin_url('options-general.php') . '" target="_blank">' .
						__('Change timezone', ONECOM_UC_TEXT_DOMAIN) .
						'</a>',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'    => 'uc_subscribe_form',
					'label'   => __('Subscribe form', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Would you like to show subscription form?', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
				),

				array(
                    'name'    => 'uc_whitelisted_roles',
                    'label'   => __( 'Whitelisted user roles', ONECOM_UC_TEXT_DOMAIN ),
					'desc'    => __( 'Selected user roles will always see the normal site, instead of under construction page.', ONECOM_UC_TEXT_DOMAIN ),
                    'type'    => 'multicheck',
                    'options' => $users_list
                ),

				array(
					'name'        => 'submit',
					'label'       => '',
					'type'        => 'submit'
				),
			),

			/* Design Settings */
			'onecom_under_construction_content' => array(
				array(
					'name'    => 'uc_logo',
					'label'   => __('Logo', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'button_label' => __('Select Image', ONECOM_UC_TEXT_DOMAIN)
					),
					'desc'             => __('Site title will be displayed if no image uploaded.', ONECOM_UC_TEXT_DOMAIN) . ' ' . __('Site title', ONECOM_UC_TEXT_DOMAIN) . ': ' . get_bloginfo('blogname')

				),

				array(
					'name'              => 'uc_headline',
					'label'             => __('Headline', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'    => 'uc_description',
					'label'   => __('Description', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'wysiwyg',
					'default' => ''
				),

				array(
					'name'              => 'uc_copyright',
					'label'             => __('Copyright Text', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'              => 'uc_facebook_url',
					'label'             => __('Facebook', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://facebook.com/profile'
				),
				array(
					'name'              => 'uc_twitter_url',
					'label'             => __('Twitter', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://twitter.com/profile'
				),

				array(
					'name'              => 'uc_instagram_url',
					'label'             => __('Instagram', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://instagram.com/profile'
				),

				array(
					'name'              => 'uc_linkedin_url',
					'label'             => __('LinkedIn', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://linkedin.com/profile'
				),

				array(
					'name'              => 'uc_youtube_url',
					'label'             => __('YouTube', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://youtube.com/profile'
				),

				array(
					'name'        => 'uc_submit',
					'label'       => '',
					'type'        => 'submit'
				),

			),

			'onecom_under_construction_customization' => array(
				array(
					'name'    => 'uc_page_bg_color',
					'label'   => __('Background Color', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'color',
					'default' => ''
				),

				array(
					'name'    => 'uc_primary_color',
					'label'   => __('Primary color', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'color',
					'default' => '',
					'desc'    => __('Set color for site title and button', ONECOM_UC_TEXT_DOMAIN),
				),

				array(
					'name'    => 'uc_page_bg_image',
					'label'   => __('Background image', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Choose between having a solid color background or uploading an image. By default images will cover the entire background.', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'button_label' => __('Select Image', ONECOM_UC_TEXT_DOMAIN)
					)
				),

				array(
					'name'        => 'uc_custom_css',
					'label'       => __('Custom CSS', ONECOM_UC_TEXT_DOMAIN),
					'placeholder' => '.selector { property-name: property-value; }',
					'desc'        => __('Add custom CSS code', ONECOM_UC_TEXT_DOMAIN),
					'type'        => 'textarea'
				),

				array(
					'name'        => 'uc_scripts',
					'label'       => __('Analytics code', ONECOM_UC_TEXT_DOMAIN),
					'placeholder' => '&lt;script&gt;
  &lt;!-- Analytics code --&gt;
&lt;/script&gt;',
					'desc'        => __('Paste in your universal or classic google analytics code', ONECOM_UC_TEXT_DOMAIN),
					'type'        => 'textarea',
				),

				array(
					'name'        => 'uc_submit',
					'label'       => '',
					'type'        => 'submit'
				),
			)

		);

		return $settings_fields;
	}

	/**
	 * Initialize and registers the settings sections and fileds to WordPress
	 *
	 * Usually this should be called at `admin_init` hook.
	 *
	 * This function gets the initiated settings sections and fields. Then
	 * registers them to WordPress and ready for use.
	 */

	public function uc_settings_init_fn()
	{
		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->get_settings_fields());
	}

	// Add sub page to the Settings Menu
	public function uc_add_page_fn()
	{
		// @todo - move out as public var if getting used at multiple places
		$menu_title = __("Under Construction", ONECOM_UC_TEXT_DOMAIN);
		add_menu_page($menu_title, $menu_title, 'manage_options', 'onecom-wp-under-construction', array($this, 'uc_page_fx'), 'dashicons-admin-generic', 6);
	}

	// add uc settings menu icon
	function uc_menu_icon_css_fn()
	{
		define('OCUC_MENU_ICON_GREY', ONECOM_UC_DIR_URL . 'assets/images/uc-menu-icon-grey.svg');
		define('OCUC_MENU_ICON_BLUE', ONECOM_UC_DIR_URL . 'assets/images/uc-menu-icon-blue.svg');

		echo "<style>.toplevel_page_onecom-wp-under-construction > .wp-menu-image{display:flex !important;align-items: center;justify-content: center;}.toplevel_page_onecom-wp-under-construction > .wp-menu-image:before{content:'';background-image:url('" . OCUC_MENU_ICON_GREY . "');font-family: sans-serif !important;background-repeat: no-repeat;background-position: center center;background-size: 18px 18px;background-color:#fff;border-radius: 100px;padding:0 !important;width:18px;height: 18px;}.toplevel_page_onecom-wp-under-construction.current > .wp-menu-image:before{background-size: 16px 16px; background-image:url('" . OCUC_MENU_ICON_BLUE . "');}.ab-top-menu #wp-admin-bar-purge-all-varnish-cache .ab-icon:before,#wpadminbar>#wp-toolbar>#wp-admin-bar-root-default>#wp-admin-bar-onecom-wp .ab-item:before, .ab-top-menu #wp-admin-bar-onecom-staging .ab-item .ab-icon:before{top: 2px;}a.current.menu-top.toplevel_page_onecom-wp-under-construction.menu-top-last{word-spacing: 10px;}@media only screen and (max-width: 960px){.auto-fold #adminmenu a.menu-top.toplevel_page_onecom-wp-under-construction{height: 55px;}}</style>";
		return true;
	}

	// Display the admin options page
	function uc_page_fx()
	{
?>
		<div class="wrap one_uc_wrap">
			<?php
			$this->uc_admin_head();
			// Show message after settings save
			settings_errors();
			?>

			<div class="metabox-holder ocuc-setting-wrap">

				<form method="post" action="options.php">
					<?php
					settings_fields(ONECOM_UC_OPTION_FIELD);
					do_settings_sections(ONECOM_UC_OPTION_FIELD);
					?>
			</div>
		</div>
	<?php
		$this->settings_api->script();
	}

	function uc_admin_head()
	{ ?>

		<h2 class="one-logo">
			<div class="textleft"><span><?php echo __("Under Construction", ONECOM_UC_TEXT_DOMAIN); ?></span></div>
			<div class="textright">
				<img src="<?php echo ONECOM_UC_DIR_URL . '/assets/images/one.com-logo@2x.svg' ?>" alt="one.com" srcset="<?php echo ONECOM_UC_DIR_URL . '/assets/images/one.com-logo@2x.svg 2x' ?>" />
			</div>
		</h2>
<?php }
}
