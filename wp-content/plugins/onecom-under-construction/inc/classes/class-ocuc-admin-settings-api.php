<?php

/**
 * Defines admin settings functions
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Admin_Settings_API
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Admin_Settings_API
{
	// Constructor
	public function __construct()
	{
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
	}

	/**
	 * Enqueue scripts and styles
	 */
	function admin_enqueue_scripts()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_media();
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('jquery');
	}

	// Set settings sections
	function set_sections($sections)
	{
		$this->settings_sections = $sections;

		return $this;
	}

	// Set settings fields
	function set_fields($fields)
	{
		$this->settings_fields = $fields;
		return $this;
	}

	/**
	 * Loop through all settings and fields
	 */
	public function settings_init()
	{

		if (false == get_option(ONECOM_UC_OPTION_FIELD)) {
			add_option(ONECOM_UC_OPTION_FIELD);
		}

		// add settings sections
		foreach ($this->settings_sections as $section) {

			if (isset($section['callback'])) {
				$callback = array($this, $section['callback']);
			} else {
				$callback = null;
			}

			add_settings_section($section['id'], $section['title'], $callback, ONECOM_UC_OPTION_FIELD);
		}

		// add settings fields
		foreach ($this->settings_fields as $section => $fields_inner) {
			// add fields to section
			foreach ($fields_inner as $option) {
				$name = $option['name'];
				$type = isset($option['type']) ? $option['type'] : 'text';
				$label = isset($option['label']) ? $option['label'] : '';
				$callback = isset($option['callback']) ? $option['callback'] : array($this, 'callback_' . $type);

				$args = array(
					'id'                => $name,
					'class'             => isset($option['class']) ? $option['class'] : $name,
					'label_for'         => "{$name}",
					'desc'              => isset($option['desc']) ? $option['desc'] : '',
					'name'              => $label,
					'section'           => ONECOM_UC_OPTION_FIELD,
					'size'              => isset($option['size']) ? $option['size'] : null,
					'options'           => isset($option['options']) ? $option['options'] : '',
					'std'               => isset($option['default']) ? $option['default'] : '',
					'sanitize_callback' => isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '',
					'type'              => $type,
					'placeholder'       => isset($option['placeholder']) ? $option['placeholder'] : '',
					'min'               => isset($option['min']) ? $option['min'] : '',
					'max'               => isset($option['max']) ? $option['max'] : '',
					'step'              => isset($option['step']) ? $option['step'] : '',
				);

				// Register section and its fields
				add_settings_field($name, $label, $callback, ONECOM_UC_OPTION_FIELD, $section, $args);
			}
		}

		register_setting(ONECOM_UC_OPTION_FIELD, ONECOM_UC_OPTION_FIELD, array($this, 'sanitize_options'));

		return $this;
	}

	// Sanitize callback for Settings API
	function sanitize_options($options)
	{

		if (!$options) {
			return $options;
		}

		foreach ($options as $option_slug => $option_value) {
			$sanitize_callback = $this->get_sanitize_callback($option_slug);

			// If callback is set, call it
			if ($sanitize_callback) {
				$options[$option_slug] = call_user_func($sanitize_callback, $option_value);
				continue;
			}
		}

		// Admin Notice after settings saved
		$message = __('Settings saved.', ONECOM_UC_TEXT_DOMAIN) .
			'<br/>' . __('Remember to delete cache in case you are using any caching plugin.', ONECOM_UC_TEXT_DOMAIN);

		add_settings_error('onecom_under_construction', 'onecom_under_construction', $message, 'success');

		return $options;
	}

	// Get sanitization callback for given option slug
	function get_sanitize_callback($slug = '')
	{
		if (empty($slug)) {
			return false;
		}

		// Iterate over registered fields and see if we can find proper callback
		foreach ($this->settings_fields as $options) {
			foreach ($options as $option) {
				if ($option['name'] != $slug) {
					continue;
				}

				// Return the callback name
				return isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
			}
		}

		return false;
	}


	// Section HTML, displayed before the first option
	public function  callback_section($section)
	{
		echo '<h3 class="postbox postbox-header">' . $section['title'] . '</h3>';
	}

	// Get the value of a settings field
	function get_option($option, $section = ONECOM_UC_OPTION_FIELD, $default = '')
	{
		$options = get_option($section);

		if (isset($options[$option])) {
			return $options[$option];
		}

		return $default;
	}

	// Get field description for display
	public function get_field_description($args)
	{
		if (!empty($args['desc'])) {
			$desc = sprintf('<p class="description">%s</p>', $args['desc']);
		} else {
			$desc = '';
		}

		return $desc;
	}

	/**
	 *  Displays a submit button via settings field with custom class
	 * 	Useful if displaying submit button at multiple places
	 */
	function callback_submit()
	{
		submit_button( __('Save', ONECOM_UC_TEXT_DOMAIN), 'uc-submit-button oc-uc-btn' );
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_text($args)
	{
		$value       = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
		$size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
		$type        = isset($args['type']) ? $args['type'] : 'text';
		$placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		$html        = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder);
		$html       .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Displays a datetime field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_datetime($args)
	{

		$value       = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
		$size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
		$type        = 'text';
		$placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		$html        = sprintf('<input type="%1$s" class="%2$s-datetime picker-datetime regular-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder);
		$html       .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Displays a url field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_url($args)
	{
		$value       = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
		$size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
		$type        = 'url';
		$placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		$html        = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder);
		$html       .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_checkbox($args)
	{

		$value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));

		$html  = '<fieldset>';

		$html  .= sprintf('<label class="oc_switch_label" for="wpuf-%1$s[%2$s]">', $args['section'], $args['id']);
		$html  .= '<span class="oc_uc_switch">';
		$html  .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
		$html  .= sprintf('<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked($value, 'on', false));
		$html  .= '<span class="oc_uc_slider"></span></span>';
		$html  .= sprintf('<span class="description">%s</span></label>', $args['desc']);
		$html  .= '</fieldset>';

		echo $html;
	}

	/**
     * Displays a multicheckbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_multicheck( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$html  = '<fieldset>';
		$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $checked  = isset( $value[$key] ) ? $value[$key] : '0';
			$html    .= sprintf( '<label class="oc_switch_label" for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
			$html 	 .= '<span class="oc_uc_switch">';
			$html    .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
			$html    .= '<span class="oc_uc_slider"></span></span>';
			$html  .= sprintf('<span>%s</span></label><br/>', $label);
		}

		$html  .= sprintf('<p class="description">%s</p>', $args['desc']);
        $html .= '</fieldset>';

        echo $html;
    }

	/**
	 * Displays a radio button for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_radio($args)
	{

		$value = $this->get_option($args['id'], $args['section'], $args['std']);
		$html  = '<fieldset>';

		foreach ($args['options'] as $key => $label) {
			$html .= sprintf('<label for="wpuf-%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key);
			$html .= sprintf('<input type="radio" class="radio" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($value, $key, false));
			$html .= sprintf('%1$s</label><br/>', $label);
		}

		$html .= $this->get_field_description($args);
		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * Displays a radio button for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_radio_image($args)
	{

		$value = $this->get_option($args['id'], $args['section'], $args['std']);
		$html  = '<fieldset class="ocp-radio-image" >';

		foreach ($args['options'] as $key => $label) {
			$image = ONECOM_UC_DIR_URL . 'assets/images/' . $args['options'][$key];
			$html .= sprintf('<label for="wpuf-%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key);
			$html .= sprintf(
				'<input type="radio" class="radio" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />
				<img src="%5$s" title="" />',
				$args['section'],
				$args['id'],
				$key,
				checked($value, $key, false),
				$image
			);
			$html .= "</label>";
		}

		$html .= $this->get_field_description($args);
		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_textarea($args)
	{

		$value       = esc_textarea($this->get_option($args['id'], $args['section'], $args['std']));
		$size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
		$placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		$html        = sprintf('<textarea onkeyup="onload(this)" rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value);
		$html        .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Displays the html for a settings field
	 *
	 * @param array   $args settings field args
	 * @return string
	 */
	function callback_html($args)
	{
		echo $this->get_field_description($args);
	}

	/**
	 * Displays a rich text textarea for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_wysiwyg($args)
	{

		$value = $this->get_option($args['id'], $args['section'], $args['std']);
		$size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : '500px';

		echo '<div style="max-width: ' . $size . ';">';

		// @todo - Following css is not getting applied to description editor
		$editor_style = '<style type="text/css">
           .onecom_under_construction_info-uc_description p{
			font-family: sans-serif;
			font-size: 15px;}
           </style>';

		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10,
			'editor_css' => $editor_style,
		);

		if (isset($args['options']) && is_array($args['options'])) {
			$editor_settings = array_merge($editor_settings, $args['options']);
		}

		wp_editor($value, $args['section'] . '-' . $args['id'], $editor_settings);

		echo '</div>';

		echo $this->get_field_description($args);
	}

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_color($args)
	{

		$value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
		$size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

		$html  = sprintf('<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std']);
		$html  .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Displays a file upload field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	function callback_file($args)
	{

		$value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
		$size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
		$id    = $args['section']  . '[' . $args['id'] . ']';
		$label = isset($args['options']['button_label']) ? $args['options']['button_label'] : __('Choose File');

		$html  = sprintf('<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
		$html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
		$html  .= $this->get_field_description($args);

		echo $html;
	}

	/**
	 * Scripts for file upload, color picker etc
	 */
	function script()
	{
?>
		<script>
			jQuery(document).ready(function($) {
				//Initiate Color Picker
				$('.wp-color-picker-field').wpColorPicker();

				$('.wpsa-browse').on('click', function(event) {
					event.preventDefault();

					var self = $(this);

					// Create the media frame.
					var file_frame = wp.media.frames.file_frame = wp.media({
						title: self.data('uploader_title'),
						button: {
							text: self.data('uploader_button_text'),
						},
						multiple: false
					});

					file_frame.on('select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();
						self.prev('.wpsa-url').val(attachment.url).change();
					});

					// Finally, open the modal
					file_frame.open();
				});
			});
		</script>
		<?php
		$this->_style_fix();
	}

	function _style_fix()
	{
		global $wp_version;

		if (version_compare($wp_version, '3.8', '<=')) {
		?>
			<style type="text/css">
				/** WordPress 3.8 Fix **/
				.form-table th {
					padding: 20px 10px;
				}

				.onecom_under_construction_info-uc_description,
				.onecom_under_construction_info-uc_description p {
					font-family: sans-serif;
					font-size: 15px;
				}

				#wpbody-content .metabox-holder {
					padding-top: 5px;
				}
			</style>
<?php
		}
	}
}
