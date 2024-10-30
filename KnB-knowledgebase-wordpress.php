<?php

/**
 * 
 * Plugin Name:       KnB Lite - Wordpress Knowledge Base / FAQ Plugin
 * Plugin URI:        http://codecanyon.net/user/phpbits?ref=phpbits
 * Description:       Add Knowledge Base, Wiki or even FAQ on any part of your wordpress website easily using this powerful shortcode plugin. <strong>Please DEACTIVATE this plugin if you wish to <a href="http://codecanyon.net/item/knb-wordpress-knowledge-base-wiki-shortcode-/8937609?ref=phpbits" target="_blank">upgrade to pro version</a></strong>. Thanks
 * Version:           1.0.0
 * Author:            phpbits
 * Author URI:        http://codecanyon.net/user/phpbits?ref=phpbits
 * Text Domain:       wpKnB
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WPKNB_PLUGIN_DIR',dirname(__FILE__));
add_filter('widget_text', 'do_shortcode');

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpKnB-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpKnB-deactivator.php';

/** This action is documented in includes/class-wpKnB-activator.php */
register_activation_hook( __FILE__, array( 'wpKnB_Activator', 'activate' ) );

/** This action is documented in includes/class-wpKnB-deactivator.php */
register_activation_hook( __FILE__, array( 'wpKnB_Deactivator', 'deactivate' ) );

/*
 * Create Global Variable
 */
global $wpknb;
$wpknb = get_option('wpknb-settings');
if(!empty($wpknb)){
	$wpknb = unserialize($wpknb);
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpKnB.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpKnB() {

	$plugin = new wpKnB();
	$plugin->run();

}
run_wpKnB();
