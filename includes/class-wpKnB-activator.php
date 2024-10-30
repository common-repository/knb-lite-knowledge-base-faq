<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpKnB
 * @subpackage wpKnB/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    wpKnB
 * @subpackage wpKnB/includes
 * @author     phpbits
 */
class wpKnB_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$options = array(
				'title'				=>	'Knowledge Base',
				'all_text'			=>	'View all %s articles',
				'link_color'		=>	'#222222',
				'vote_color'		=>	'#ffffff',
				'up_color'			=>	'#94bc1a',
				'down_color'		=>	'#c84848',
				'singular'			=>	'Knowledge Base',
				'plural'			=>	'Knowledge Base',
				'category'			=>	'Category',
				'category_plural'	=>	'Categories',
				'slug'				=>	'knowledgebase',
				'cat_slug'			=>	'knb-category',
				'override_category'	=>	'1',
				'override_single'	=>	'1',
				'override_search'	=>	'1',
			);
		$get_opts =get_option('wpknb-settings');
		if(empty($get_opts)){
			add_option('wpknb-settings', serialize($options));
		}

	}

}
