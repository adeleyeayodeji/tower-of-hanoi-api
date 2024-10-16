<?php

/**
 * Plugin Name:     Tower Of Hanoi Api
 * Plugin URI:      https://github.com/adeleyeayodeji/tower-of-hanoi-api
 * Description:     A REST API for the Tower of Hanoi game
 * Author:          Adeleye Ayodeji
 * Author URI:      https://github.com/adeleyeayodeji
 * Text Domain:     tower-of-hanoi-api
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tower_Of_Hanoi_Api
 */

//check for security
if (! defined('ABSPATH')) {
	die("You are not allowed to access this file directly.");
}

//define constants
define('TOWER_OF_HANOI_API_VERSION', '0.1.0');
define('TOWER_OF_HANOI_API_PATH', plugin_dir_path(__FILE__));
define('TOWER_OF_HANOI_API_URL', plugin_dir_url(__FILE__));

// Support for site-level autoloading.
require_once __DIR__ . '/vendor/autoload.php';

//Crash analytics would be integrated here

//init Loader
Tower_Of_Hanoi_Api\Loader::instance();
