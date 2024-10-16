<?php

/**
 * Class to boot up plugin.
 *
 * @link    https://adeleyeayodeji.com/
 * @since   1.0.0
 *
 * @author  Adeleye Ayodeji (https://adeleyeayodeji.com)
 * @package Tower_Of_Hanoi_Api
 *
 * @copyright (c) 2024, Adeleye Ayodeji (https://adeleyeayodeji.com)
 */

namespace Tower_Of_Hanoi_Api;

use Tower_Of_Hanoi_Api\App\Api\Api;
use Tower_Of_Hanoi_Api\Base;

// If this file is called directly, abort.
defined('WPINC') || die;

final class Loader extends Base
{
	/**
	 * Settings helper class instance.
	 *
	 * @since 1.0.0
	 * @var object
	 *
	 */
	public $settings;

	/**
	 * Minimum supported php version.
	 *
	 * @since  1.0.0
	 * @var float
	 *
	 */
	public $php_version = '7.4';

	/**
	 * Minimum WordPress version.
	 *
	 * @since  1.0.0
	 * @var float
	 *
	 */
	public $wp_version = '6.1';

	/**
	 * Initialize functionality of the plugin.
	 *
	 * This is where we kick-start the plugin by defining
	 * everything required and register all hooks.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		if (!$this->can_boot()) {
			//log error
			error_log('Tower of Hanoi API: Plugin could not boot, PHP version is less than ' . $this->php_version . ' and WP version is less than ' . $this->wp_version);
			return;
		}

		$this->init();
	}

	/**
	 * Main condition that checks if plugin parts should continue loading.
	 *
	 * @return bool
	 */
	private function can_boot()
	{
		/**
		 * Checks
		 *  - PHP version
		 *  - WP Version
		 * If not then return.
		 */
		global $wp_version;

		return (
			version_compare(PHP_VERSION, $this->php_version, '>') &&
			version_compare($wp_version, $this->wp_version, '>')
		);
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function init()
	{
		//init api
		Api::init();
	}
}
