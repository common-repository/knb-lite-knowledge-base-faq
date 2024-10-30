<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpKnB
 * @subpackage wpKnB/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    wpKnB
 * @subpackage wpKnB/includes
 * @author     phpbits
 */
class wpKnB_Loader {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      wpKnB_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      string               $hook             The name of the WordPress action that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the action is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      string               $hook             The name of the WordPress filter that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the filter is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @var      string               $hook             The name of the WordPress filter that is being registered.
	 * @var      object               $component        A reference to the instance of the object on which the filter is defined.
	 * @var      string               $callback         The name of the function definition on the $component.
	 * @var      int      Optional    $priority         The priority at which the function should be fired.
	 * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   type                                   The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

	/*
	 * Get Settings Option
	 */
	public function get_opts(){
		$opts = get_option('wpknb-settings');
		return unserialize($opts);
	}

	/**
	 * Create Default WP_Post array contents
	 *
	 */
	public function wp_post_default($args){
	    return array_merge(array(
	        'ID'                    => 0,
	        'post_status'           => 'publish',
	        'post_author'           => 0,
	        'post_parent'           => 0,
	        'post_type'             => 'page',
	        'post_date'             => 0,
	        'post_date_gmt'         => 0,
	        'post_modified'         => 0,
	        'post_modified_gmt'     => 0,
	        'post_content'          => '',
	        'post_title'            => '',
	        'post_excerpt'          => '',
	        'post_content_filtered' => '',
	        'post_mime_type'        => '',
	        'post_password'         => '',
	        'post_name'             => '',
	        'guid'                  => '',
	        'menu_order'            => 0,
	        'pinged'                => '',
	        'to_ping'               => '',
	        'ping_status'           => '',
	        'comment_status'        => 'closed',
	        'comment_count'         => 0,
	        'filter'                => 'raw',    

	    ),$args);
	}

	/**
	* 
	* get the proper File based on the $case 
	* 
	* @param string $case
	* @return string correct file path
	*/
	function get_template_files($case = 'single'){

	$default_path = WPKNB_PLUGIN_DIR . '/public/templates/';
	$theme_path   = get_stylesheet_directory()  . '/wpknb/'; 

	switch($case){
	    case 'search':
	        $filename       = 'search.php';
	        break;
	    case 'archive':
	        $filename       = 'archive.php';
	        break;
	    case 'single':
	    default :
	        $filename       = 'single.php';
	        break;
	    case 'category':
	        $filename       = 'category.php';
	        break;
	    case 'knowledgebase':
	        $filename       = 'knowledgebase.php';
	        break;
	}

	$default_file = $default_path . $filename;
	$theme_file   = $theme_path   . $filename;

	return ((file_exists($theme_file))?$theme_file:$default_file);
	}


	function load_file($filename){
	ob_start();
		include $filename;
	return ob_get_clean();
	}
}
