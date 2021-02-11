<?php

/**
 * Fired at different events/actions in the plugin
 *
 * This class push analytics stats & logs
 *
 * @since      0.2.0
 * @package    Under_Construction
 * @subpackage OCUC_Stats
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Stats
{

	// Constructor
	public function __construct()
	{
		// update_option_name fires when options actully updated with new value
		add_action('update_option_onecom_under_construction_info', array($this, 'uc_stats_info'), 10, 2);
	}

	/**
	 * UC enable/disable stats
	 */
	public function uc_stats_info($old_uc_data, $new_uc_data)
	{
		global $pagenow;
		if ($pagenow === 'options.php') {
			$referrer = 'plugins_page';
		} else {
			$referrer = 'install_wizard';
		}

		// prepare information for log/stats push
		$new_uc_data['uc_logo'] = isset($new_uc_data['uc_logo']) && !empty($new_uc_data['uc_logo']) ? '1' : '0';
		$new_uc_data['uc_headline'] = isset($new_uc_data['uc_headline']) && !empty($new_uc_data['uc_headline']) ? '1' : '0';
		$new_uc_data['uc_description'] = isset($new_uc_data['uc_description']) && !empty($new_uc_data['uc_description']) ? '1' : '0';
		$new_uc_data['uc_custom_css'] = isset($new_uc_data['uc_custom_css']) && !empty($new_uc_data['uc_custom_css']) ? '1' : '0';
		$new_uc_data['uc_scripts'] = isset($new_uc_data['uc_scripts']) && !empty($new_uc_data['uc_scripts']) ? '1' : '0';
		$new_uc_data['uc_copyright'] = isset($new_uc_data['uc_copyright']) && !empty($new_uc_data['uc_copyright']) ? '1' : '0';
		$new_uc_data['uc_facebook_url'] = isset($new_uc_data['uc_facebook_url']) && !empty($new_uc_data['uc_facebook_url']) ? '1' : '0';
		$new_uc_data['uc_twitter_url'] = isset($new_uc_data['uc_twitter_url']) && !empty($new_uc_data['uc_twitter_url']) ? '1' : '0';
		$new_uc_data['uc_instagram_url'] = isset($new_uc_data['uc_instagram_url']) && !empty($new_uc_data['uc_instagram_url']) ? '1' : '0';
		$new_uc_data['uc_linkedin_url'] = isset($new_uc_data['uc_linkedin_url']) && !empty($new_uc_data['uc_linkedin_url']) ? '1' : '0';
		$new_uc_data['uc_youtube_url'] = isset($new_uc_data['uc_youtube_url']) && !empty($new_uc_data['uc_youtube_url']) ? '1' : '0';
		$new_uc_data['uc_page_bg_image'] = isset($new_uc_data['uc_page_bg_image']) && !empty($new_uc_data['uc_page_bg_image']) ? '1' : '0';
		$uc_status = isset($new_uc_data['uc_status']) ? $new_uc_data['uc_status'] : '';

		// prevent information from log/stats push
		unset($new_uc_data['uc_timer'], $new_uc_data['uc_page_bg_color'], $new_uc_data['uc_primary_color'], $new_uc_data['uc_whitelisted_roles']);

		// trigger plugin enable/disable stats
		if ($uc_status === 'on') {
			$this->ocuc_stats_push('enable', 'setting', ONECOM_UC_PLUGIN_SLUG, $referrer, $new_uc_data);
		} else if ($uc_status === 'off') {
			$this->ocuc_stats_push('disable', 'setting', ONECOM_UC_PLUGIN_SLUG, $referrer, $new_uc_data);
		}
	}

	/**
	 * Function to push stats for events of under-construction
	 */
	public static function ocuc_stats_push(
		$event_action,
		$item_category = null,
		$item_name = null,
		$referrer = null,
		$additional_info = array()
	) {
		if (class_exists('OCPushStats')) {
			$result = oc_set_premi_flag(true);
			$item_avail = (int)oc_pm_features('ins', $result['data']);
			$base_params = \OCPushStats::stats_base_parametres();
			$dynamic_params = [
				\OCPushStats::HIT_TYPE => \OCPushStats::EVENT,
				\OCPushStats::EVENT_ACTION => $event_action,
				\OCPushStats::ITEM_CATEGORY => $item_category,
				\OCPushStats::ITEM_NAME => $item_name,
				\OCPushStats::REFERRER => $referrer,
				\OCPushStats::ITEM_AVAIL => "$item_avail"
			];

			$dynamic_params = array_filter($dynamic_params, function ($value) {
				return !is_null($value) && $value !== '';
			});

			if (!empty($additional_info)) {
				$dynamic_params = array_merge($dynamic_params, $additional_info);
			}
			$payload = json_encode(array_merge($base_params, $dynamic_params));

			return \OCPushStats::curl_request($payload);
		}
	}
}

$stats = new OCUC_Stats();
