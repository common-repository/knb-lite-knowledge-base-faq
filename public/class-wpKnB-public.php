<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpKnB
 * @subpackage wpKnB/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    wpKnB
 * @subpackage wpKnB/admin
 * @author     Your Name <email@example.com>
 */
class wpKnB_Public {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      wpKnB_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version, $loader ) {

		$this->loader = $loader;
		$this->name = $name;
		$this->version = $version;

		// add_action('template_redirect' ,array($this,'template_redirect'));
		add_action('wp_footer' ,array($this,'footer_css'));
		add_filter( 'body_class', array($this,'body_class') );
		// add_filter( 'the_content', array($this,'the_content'), 10 ); available on pro version
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wpKnB_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wpKnB_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'bootstrap-tooltip', plugin_dir_url( __FILE__ ) . 'css/tooltip.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/wpknb.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wpKnB_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wpKnB_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'bootstrap-tooltip', plugin_dir_url( __FILE__ ) . 'js/bootstrap-tooltip.min.js', array( 'jquery' ), $this->version, FALSE );
		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/wpknb.js', array( 'jquery' ), $this->version, FALSE );
		wp_localize_script( $this->name, 'wpknb_vars', array('ajaxurl' =>  admin_url('admin-ajax.php')));
	}

	function footer_css(){
		global $wpknb; 
		ob_start();
		?>
		<style>
			<?php if(isset($wpknb['category_color']) && !empty($wpknb['category_color'])):?>
			.wpknb .wpknb-inner h3.wpknb-cat-title a{
				color: <?php echo $wpknb['category_color'];?>;
			}
			<?php endif;?>
			<?php if(isset($wpknb['link_color']) && !empty($wpknb['link_color'])):?>
			.wpknb .wpknb-category .wpknb-inner ul.wpknb-lists a{
				color: <?php echo $wpknb['link_color'];?>;
			}
			<?php endif;?>
			<?php if(isset($wpknb['all_color']) && !empty($wpknb['all_color'])):?>
			.wpknb .wpknb-inner .wpknb-full-category-count a{
				color: <?php echo $wpknb['all_color'];?>;
			}
			<?php endif;?>
			<?php if(isset($wpknb['css']) && !empty($wpknb['css'])){
				echo stripcslashes($wpknb['css']);
			}?>
		</style>
		<?php
		$style = ob_get_clean();
		echo $style = trim(preg_replace('/\s\s+/', ' ', $style));
	}

	function body_class( $classes ){
		global $wpknb;

		if(is_singular('knowledge_base') && isset($wpknb['override_single']) && $wpknb['override_single'] == '1'){
			$classes[] = 'wpknb-override-single';
		}elseif( is_tax( 'knb-category') && isset($wpknb['override_category']) && $wpknb['override_category'] == '1'){
			$classes[] = 'wpknb-override-category';
		}elseif( is_search() && isset($wpknb['override_search']) && $wpknb['override_search'] == '1'){
			$classes[] = 'wpknb-override-search';
		}

		return $classes;
	}

}
