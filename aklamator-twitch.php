<?php
/*
Plugin Name: Aklamator Twitch
Plugin URI: https://www.aklamator.com/wordpress
Description: Aklamator Twitch Classic service enables you to sell PR announcements, cross promote web sites using RSS feed and provide new services to your clients in digital advertising.
Version:1.2
Author: Aklamator
Author URI: https://www.aklamator.com/
License: GPL2

Copyright 2017 Aklamator.com (email : info@aklamator.com)

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/


if(!defined('AKLATWITCH_PR_PLUGIN_NAME')){
    define('AKLATWITCH_PR_PLUGIN_NAME', plugin_basename(__FILE__));
}

if (!defined('AKLATWITCH_PR_PLUGIN_DIR')) {
    define('AKLATWITCH_PR_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('AKLATWITCH_PR_PLUGIN_URL')) {
    define('AKLATWITCH_PR_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once AKLATWITCH_PR_PLUGIN_DIR . "includes/class-aklamatorTwitch-pr.php";


/*
 * Activation Hook
 */
register_activation_hook( __FILE__, array('aklamatorTwitchPrWidget','set_up_options'));
/*
 * Uninstall Hook
 */
register_uninstall_hook(__FILE__, array('aklamatorTwitchPrWidget','aklamator_uninstall'));


//Widget Section
require_once AKLATWITCH_PR_PLUGIN_DIR . "includes/class-aklamatorTwitch-widget-pr.php";

// Start plugin
AklamatorTwitchPrWidget::init();