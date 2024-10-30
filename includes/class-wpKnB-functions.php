<?php
/**
 * wpKnB General Functions
 *
 * Contains all the general functions used by the plugin.
 *
 * @since      1.0.0
 * @package    wpKnB
 * @subpackage wpKnB/includes
 * @author     phpbits
 */

class wpKnB_F {

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
		global $wpknb;

		$this->loader = $loader;
		$this->name = $name;
		$this->version = $version;
		$this->opts = $wpknb;
		add_action('init', array( $this, "register_post_type_and_taxonomies" ));
		add_filter('pre_get_posts', array( $this, "pre_get_posts" ));
		add_action('init', array( $this, "register_shortcode" ));
		// add_action('save_post', array( $this, "save_post" ));
		// add_action('edit_post', array( $this, "save_post" ));
		add_action("wp_ajax_wpknb_vote", array( $this, "wpknb_vote" ));
		add_action("wp_ajax_nopriv_wpknb_vote", array( $this, "wpknb_vote" ));
	}

 	/**
	* Registers post types and taxonomies.
	*
	* @since 1.0.0
	*
	*/
	function register_post_type_and_taxonomies() {

		$labels = apply_filters( 'wpKnB_object_labels', array(
			'name' => __( $this->opts['plural'], 'wpKnB' ),
			'all_items' => __( 'All ' . $this->opts['plural'], 'wpKnB' ),
			'singular_name' => __( $this->opts['singular'], 'wpwpKnBp' ),
			'add_new' => __( 'Add New', 'wpKnB' ),
			'add_new_item' => __( 'Add New ' . $this->opts['singular'], 'wpKnB' ),
			'edit_item' => __( 'Edit ' . $this->opts['singular'], 'wpKnB' ),
			'new_item' => __( 'New '. $this->opts['singular'], 'wpKnB' ),
			'view_item' => __( 'View '. $this->opts['singular'], 'wpKnB' ),
			'search_items' => __( 'Search ' . $this->opts['singular'], 'wpKnB' ),
			'not_found' => __( 'No '. $this->opts['plural'] .' found', 'wpKnB' ),
			'not_found_in_trash' => __( 'No '. $this->opts['plural'] .' found in Trash', 'wpKnB' ),
			'parent_item_colon' => ''
		) );

		$supports=array('title','editor','author','thumbnail','excerpt','comments','custom-fields','revisions', 'post-formats');

		// Register custom post types
		register_post_type( 'knowledge_base', array(
			'labels' => $labels,
			'show_in_nav_menus' => false,
			'public' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => $this->opts['slug']
				),
			'query_var' => true,
			'supports' => $supports,
		) );

		//Register Taxonomy	
		$tax_labels = apply_filters( 'wpKnB_taxonomy_labels', array(
				'name' => __( $this->opts['category_plural'], 'wpKnB' ),
				'singular_name' => __( 'Category', 'wpKnB' ),
				'search_items' => __( 'Search ' . $this->opts['category_plural'], 'wpKnB' ),
				'popular_items' => __( 'Popular ' . $this->opts['category_plural'], 'wpKnB' ),
				'all_items' => __( 'All ' . $this->opts['category_plural'], 'wpKnB' ) ,
				'edit_item' => __( 'Edit ' . $this->opts['category'], 'wpKnB' ) , 
				'update_item' => __( 'Update ' . $this->opts['category'], 'wpKnB' ),
				'add_new_item' =>  __( 'Add New', 'wpKnB' ) ,
				'new_item_name' => __( 'New ' . $this->opts['category'], 'wpKnB' ),
				'menu_name' => __( $this->opts['category_plural'], 'wpKnB' ),
			));
			
		$args = array(
						'hierarchical' => false,
						'labels' => $tax_labels,
						'show_ui' => true,
						'query_var' => true,
						'show_tagcloud' => true,
						'rewrite' => array(
							'slug' => $this->opts['cat_slug']
						)
					);			
		register_taxonomy('knb-category','knowledge_base',$args);
	}

	/**
	* Registers shortcode options.
	*
	* @since 1.0.0
	*
	*/
	function register_shortcode(){
		add_shortcode('knowledge_base', array($this, 'display_KnB'));
		add_shortcode('knowledge_base_vote', array($this, 'vote_KnB'));
		add_shortcode('knowledge_base_search', array($this, 'search_KnB'));
	}
	function display_KnB($atts, $content = null){
		extract(shortcode_atts(array(
			'type'			=>	'default',
			'category'		=>	null,
			'exclude'		=>	null,
			'post_formats'	=>	null,
			'columns'		=>	2,
			'alignment'		=>	'left',
			'layout'		=>	'grid',
			'items'			=>	8,
			'icons'			=> 'true',
			'show_title'	=> 'true',
			'sub_category'	=> 'true', //optional upgrade to pro version 
			'all_link'		=>	'true',
			'search'		=> 'true',
			'source'		=>	'default',
			'orderby'		=>	'date',
			'order'			=>	'desc'
	   ), $atts));

		//start html output
		$html = '<div class="wpknb">';

		//prepare wp-query args
		$args = array(
			'post_type' 		=> 'knowledge_base',
			'post_status'		=>	'publish',
			'posts_per_page'	=>	intval($items)
		);

		//order and orderby
		$args['orderby'] = $orderby;
		$args['order'] = $order;

		//explode categories
		if(empty($category)){
			$categories = get_terms('knb-category', array( 'hide_empty' => true ));
			$category = array();
			foreach ($categories as $ckey => $cvalue) {
				$category[] = $cvalue->term_id;
			}
		}else{
			$category = explode(',', $category);
		}
		$post_formats = explode(',', $post_formats);
		// $relation = '';
		// $tax_query = '';
		$count = 0;
		if($search == 'true'){
			$html .= do_shortcode('[knowledge_base_search]');;
		}
		foreach ($category as $key => $cat) {
			$count++;
			/*
			Remove Post Format Support
			if(!empty($post_formats) && !empty($post_formats[0])){
				$formats = array();
				foreach ($post_formats as $format_key => $format_value) {
					echo $formats[$format_key] = 'post-format-' . $format_value; echo '<br />';
				}
				$relation = 'AND';
				$tax_query = array(
						'taxonomy' 	=> 'post_format',
            			'field' 	=> 'slug',
            			'terms' 	=> $formats,
            			'operator'	=> 'IN'
					);
			}*/
			$args['tax_query'] = array(
						array(
							'taxonomy' => 'knb-category',
							'field'    => 'ID',
							'terms'    => $cat,
							'include_children' => false
						)
				);
			$query = new WP_Query( $args );
			$term = get_term( $cat, 'knb-category' );
			if($query->have_posts()){
				// print_r($term);
				$html .= '<div class="wpknb-category wpknb-col-'. $columns .' wpknb-category-'. $term->slug .' wpknb-align-'. $alignment .' wpknb-source-'. $source .'"><div class="wpknb-inner">';
				if($show_title == 'true'){
					$html .= '<h3 class="wpknb-cat-title"><a href="'. get_term_link( $term ) .'" title="'. __('View all '. $term->name , 'wpKnB') .'">'. $term->name .'</a></h3>';
				}
					$html .= '<ul class="wpknb-lists">';

					//start loop
					while ($query->have_posts()) { $query->the_post();
						$html .= '<li><a href="'. get_the_permalink() .'">';
						if($icons == 'true'){
							$html .= '<i class="knb-icon knb-format-'. get_post_format( get_the_ID() ) .'"></i>';
						}
							$html .= get_the_title();
						$html .= '</a></li>';
					}
					$html .= '</ul>';
					if($all_link == 'true'){
						if(!isset($this->opts['all_text']) || $this->opts['all_text'] == 'View all %s articles'){
							$html .=  '<span class="wpknb-full-category-count"><a href="'. get_term_link( $term )  .'">'. sprintf( _n('View all %s article', 'View all %s articles', $term->count, 'wpKnB'), $term->count) .'</a></span>';
						}else{
							$html .=  '<span class="wpknb-full-category-count"><a href="'. get_term_link( $term )  .'">'. sprintf( __($this->opts['all_text'], 'wpKnB'), $term->count) .'</a></span>';
						}
						
					}
				$html .= '</div></div>';
				if($count == $columns){
					$html .= '<div class="wpknb-clear"></div>';
				}
				if($count == 2){
					$html .= '<div class="wpknb-clear-responsive"></div>';
				}
				wp_reset_postdata();
			}else{
				$html .= '<div class="wpknb-category wpknb-col-'. $columns .' wpknb-category-'. $term->slug .' wpknb-align-'. $alignment .' wpknb-source-'. $source .'"><div class="wpknb-inner">';
				if($show_title == 'true'){
					$html .= '<h3 class="wpknb-cat-title"><a href="'. get_term_link( $term ) .'" title="'. __('View all '. $term->name , 'wpKnB') .'">'. $term->name .'</a></h3>';
				}
					$html .= __( 'No Items Found.', 'wpKnB' );
				$html .= '</div></div>';
				if($count == $columns){
					$html .= '<div class="wpknb-clear"></div>';
				}
				if($count == 2){
					$html .= '<div class="wpknb-clear-responsive"></div>';
				}
			}
		}
		$html .= '<div class="wpknb-clear"></div>';
		$html .= '</div>';

		return $html;
	}

	//search form
	function search_KnB() { 
	ob_start();
	?>
	    <form role="search" method="get" id="wpknbsearchform" action="<?php echo home_url('/'); ?>" >
	        <div class="wpknb-search">
	            <input type="text" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" id="s" placeholder="<?php _e('Enter Keyword...', 'wpKnB')?>" /><span><input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>" /></span>
	            <input type="hidden" name="post_type" value="knowledge_base" />
	        </div>
	    </form>
	    <?php
	    return ob_get_clean();
	}

	function pre_get_posts( $query ){
		if ( is_tax( 'knb-category') && $query->is_main_query() && $query->is_archive )  {
            $tax_query = $query->tax_query->queries;
            $tax_query['include_children'] = 0;
            $query->set( 'post_type', array('knowledge_base') );
        }
        // return $query;
	}

}
?>