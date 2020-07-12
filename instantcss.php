<?php

/**
 * Plugin Name: Instant CSS
 * Description: Write your styles beautifully with the power of Visual Studio Code
 * Version:     2.0.0
 * Author:      Dylan Blokhuis
 * Author URI:  https://github.com/dylanblokhuis
 * License:     GPL-3.0
 * License URI: https://raw.githubusercontent.com/dylanblokhuis/instantcss-wp/master/LICENSE
 */

use InstantCSS\Plugin;

require_once __DIR__.'/vendor/autoload.php';

define('ICSS_VERSION', '2.0.0');

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

//require_once( __DIR__ . '/classes/InstantCSS.php' );
//require_once( __DIR__ . '/classes/Template.php' );
//require_once( __DIR__ . '/classes/InstantCSS_REST.php' );
// require_once( __DIR__ . '/classes/class.instantcss_ajax.php');

new Plugin();

