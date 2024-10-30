<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpKnB
 * @subpackage wpKnB/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    wpKnB
 * @subpackage wpKnB/admin
 * @author     phpbits
 */
class wpKnB_Admin {

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
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public $loader;

	public function __construct( $name, $version, $loader ) {
		global $wpknb;

		$this->name = $name;
		$this->version = $version;
		$this->loader = $loader;
		$this->opts = $wpknb;
		$this->notices = '';
		add_action('admin_menu', array( $this, "add_submenu_page" ));
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wpKnB_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wpKnB_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'select2-css', plugin_dir_url( __FILE__ ) . 'js/select2-3.5.1/select2.css', array(), $this->version, 'all' );
		// wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/wpKnB-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wpKnB_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wpKnB_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'select2-jquery', plugin_dir_url( __FILE__ ) . 'js/select2-3.5.1/select2.min.js', array( 'jquery' ), $this->version, FALSE );
		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/wpknb-admin.js', array( 'jquery' ), $this->version, FALSE );

	}

	/**
	 * Create submenu for knowledge page post type
	 *
	 * @since    1.0.0
	 */

	public function add_submenu_page(){
		add_submenu_page('edit.php?post_type=knowledge_base', __('KnB Shortcode Generator', 'wpKnB'), __('Shortcode Generator', 'wpKnB'), 'edit_posts', 'wpknb_shortcode_generator', array($this, "shortcode_generator"));
		$this->pagehook = add_submenu_page('edit.php?post_type=knowledge_base', __('KnB Settings', 'wpKnB'), __('Settings', 'wpKnB'), 'manage_options', 'wpknb_settings', array($this, "settings"));
		add_action('load-'. $this->pagehook  , array(&$this,'wpknb_on_load_settings'));
	}

	/*
	Shortcode Generator
	Available :
		extract(shortcode_atts(array(
			'type'			=>	'default',
			'category'		=>	null,
			'exclude'		=>	null,
			'post-formats'	=>	null,
			'columns'		=>	2,
			'alignment'		=>	'left',
			'layout'		=>	'grid',
			'items'			=>	8,
			'icons'			=> 'true',
			'show_title'	=> 'true',
			'sub_category'	=> 'true',
			'search'		=> 'true',
			'source'		=>	'default',
			'orderby'		=>	'date',
			'order'			=>	'desc'
	   ), $atts));
	@since version 1
	*/
	public function shortcode_generator(){

		//get all knowledgebase categories
		$terms = get_terms('knb-category', array( 'orderby'	=>	'name',  'hide_empty' => 1 ));
		$cat = '';
		foreach ($terms as $key => $value) {
			$parent = '';
			$cat .= "{id: ". $value->term_id .", text: '". $parent . $value->name ."'},";
		}
		$cat = rtrim($cat, ',');

		//get all post formats
		$formats = '';
		/*
		Remove Post Format Support
		if ( current_theme_supports( 'post-formats' ) ) {
		    $post_formats = get_theme_support( 'post-formats' );

		    if ( is_array( $post_formats[0] ) ) {
		    		$formats .= "{id: 'standard', text: 'Standard'},";
		        foreach ($post_formats[0] as $k => $format) {
		        	$formats .= "{id: '". $format ."', text: '". ucfirst($format) ."'},";
		        }
		        $formats = rtrim($formats, ',');
		    }
		}*/

		//get all submitted form values
		$shortcode = '[knowledge_base';
		if( isset($_POST['wpknb_sc_action']) && $_POST['wpknb_sc_action'] == 'save_wpknbv2_sc_generator'){
			$formdata = serialize($_POST['wpknb']);
			$formdata = unserialize($formdata);

			if(isset($formdata['categories']) && !empty($formdata['categories'])){
				$shortcode .=' category="'. $formdata['categories'] .'"';
			}

			if(isset($formdata['formats']) && !empty($formdata['formats'])){
				$shortcode .=' post_formats="'. $formdata['formats'] .'"';
			}

			//layout
			if(isset($formdata['columns']) && !empty($formdata['columns'])){
				$shortcode .=' columns="'. $formdata['columns'] .'"';
			}
			if(isset($formdata['align']) && !empty($formdata['align'])){
				$shortcode .=' alignment="'. $formdata['align'] .'"';
			}
			if(isset($formdata['items']) && !empty($formdata['items'])){
				$shortcode .=' items="'. $formdata['items'] .'"';
			}

			//elements
			if(isset($formdata['title']) && !empty($formdata['title'])){
				$shortcode .=' show_title="false"';
			}
			if(isset($formdata['search']) && !empty($formdata['search'])){
				$shortcode .=' search="false"';
			}
			if(isset($formdata['icons']) && !empty($formdata['icons'])){
				$shortcode .=' icons="false"';
			}
			if(isset($formdata['all']) && !empty($formdata['all'])){
				$shortcode .=' all_link="false"';
			}
			
			//ordering
			if(isset($formdata['orderby']) && !empty($formdata['orderby'])){
				$shortcode .=' orderby="'. $formdata['orderby'] .'"';
			}
			if(isset($formdata['order']) && !empty($formdata['order'])){
				$shortcode .=' order="'. $formdata['order'] .'"';
			}
		}
		$shortcode .= ']';
		?>
		<div id="howto-metaboxes-general" class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2><?php _e('KnB - Shortcode Generator','wpKnB');?></h2><br /><br />
			<?php
			if(!empty($shortcode) && $shortcode != '[knowledge_base]'){
				echo '<strong>'. __('Copy this code and paste it into your post, page or text widget content.', 'wpKnB') .'</strong>';
				echo '<input type="text" style="color: #fff;background: #7e4e0b;border: none;width: 100%;-webkit-border-radius: 6px;border-radius: 6px;" value=\''. $shortcode .'\' />';
			}
			?>
			
			<form action="<?php _e( str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) ); ?>" method="post">
				<?php wp_nonce_field('wpKnB-shortcode-page'); ?>
				<input type="hidden" name="wpknb_sc_action" value="save_wpknbv2_sc_generator" />

				<div id="poststuff" class="metabox-holder">
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content" style="margin-bottom:0px;">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable"><div id="wpknbv2-sc-metabox" class="postbox" style="display: block;">
							<h3 class="hndle"><span><?php _e('Shortcode Generator', 'wpKnB');?></span></h3>
							<div class="inside">
									<div style="float:left; width:49%; min-width:350px;">
										<h3><?php _e('Display','wpKnB');?></h3>
										<table class="form-table">
											<tbody>
												<tr valign="top">
													<th scope="row"><label for="wpknb_categories"><?php _e('Categories', 'wpKnB');?></label></th>
													<td>
														<input type="text" name="wpknb[categories]" class="widefat" id="wpknb_categories" value="" /> 
														<br><em><small><?php _e('Select single or multiple categories you want to display. <strong>Empty will show all</strong>.','wpKnB')?></small></em>
													</td>
												</tr>
												<!-- <tr valign="top">
													<th scope="row"><label for="wpknb_formats"><?php _e('Post Formats', 'wpKnB');?></label></th>
													<td>
														<input type="text" name="wpknb[formats]" class="widefat" id="wpknb_formats" value="" /> 
														<br><em><small><?php //_e('Select Post Format you want to display. <strong>Empty will show all available post formats</strong>.','wpKnB');?></small></em>
													</td>
												</tr> -->
												<tr valign="top">
													<th scope="row"><label for="wpknb_orderby"><?php _e('Orderby', 'wpKnB');?></label></th>
													<td>
														<select name="wpknb[orderby]" class="wpknb_select2 widefat"> 
															<option value=""><?php _e('Default', 'wpKnB');?></option>
															<option value="title"><?php _e('Title', 'wpKnB');?></option>
															<option value="date"><?php _e('Date', 'wpKnB');?></option>
														</select>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_order"><?php _e('Order', 'wpKnB');?></label></th>
													<td>
														<select name="wpknb[order]" class="wpknb_select2 widefat"> 
															<option value=""><?php _e('Default', 'wpKnB');?></option>
															<option value="asc"><?php _e('Ascending', 'wpKnB');?></option>
															<option value="desc"><?php _e('Descending', 'wpKnB');?></option>
														</select>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_title"><?php _e('Hide Category Title','wpKnB')?>: </label></th>
													<td>
														<input type="checkbox" value="1" name="wpknb[title]" class="wpknb_check" id="wpknb_title" />
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_search"><?php _e('Hide Search Form','wpKnB')?>: </label></th>
													<td>
														<input type="checkbox" value="1" name="wpknb[search]" class="wpknb_check" id="wpknb_search" />
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_all"><?php _e('Hide View All Link','wpKnB')?>: </label></th>
													<td>
														<input type="checkbox" value="1" name="wpknb[all]" class="wpknb_check" id="wpknb_all" />
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_icons"><?php _e('Hide Item Icons','wpKnB')?>: </label></th>
													<td>
														<input type="checkbox" value="1" name="wpknb[icons]" class="wpknb_check" id="wpknb_icons" />
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div style="float:right; width:49%; min-width:350px;">
										<h3><?php _e('Layout', 'wpKnB');?></h3>
										<table class="form-table">
											<tbody>
												<tr valign="top">
													<th scope="row"><label for="wpknb_column"><?php _e('Columns', 'wpKnB');?></label></th>
													<td>
														<select name="wpknb[columns]" class="wpknb_field" id="wpknb_column">
															<option value="1">1</option>
															<option value="2">2</option>
															<option value="3">3</option>
															<option value="4">4</option>
														</select>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_align"><?php _e('Text Align', 'wpKnB')?></label></th>
													<td>
														<select name="wpknb[align]" class="wpknb_field" id="wpknb_align">
															<option value="left"><?php _e('Left', 'wpKnB');?></option>
															<option value="center"><?php _e('Center', 'wpKnB');?></option>
															<option value="right"><?php _e('Right', 'wpKnB');?></option>
														</select>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><label for="wpknb_items"><?php _e('List Items', 'wpKnB');?></label></th>
													<td>
														<input type="text" name="wpknb[items]" class="wpknb_field" size="4" id="wpknb_items" value="" />
														<br><em><small><?php _e('<strong>Default: 8</strong>','wpKnB')?></small></em>
													</td>
												</tr>
											</tbody>
										</table>
										<p class="wpp_save_changes_row">
										<input type="submit" value="Generate Shortcode" class="button-primary btn" name="Submit">
										 </p>
									</div>
									<div style="clear:both;"></div>
									</div>
							</div>
							</div>
						</div>
					</div>
					<br class="clear"/>					
				</div>
			</form>

		</div>
		<script>
		jQuery(document).ready(function(){
			jQuery("#wpknb_categories").select2({
			    createSearchChoice:function(term, data) { if (jQuery(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
			    multiple: true,
			    data: [<?php echo $cat;?>]
			});
		});
		</script>
		<?php
	}

	/*
		Settings On Load
	*/
	//will be executed if wordpress core detects this page has to be rendered
	function wpknb_on_load_settings() {
		//enqueue scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script('wp-color-picker');

		//load metaboxes
		add_meta_box('wpknb-display-metabox', __('Display and Labels','wpknb'), array(&$this, 'display_metabox'), $this->pagehook, 'normal', 'core');
		add_meta_box('wpknb-appearance-metabox', __('Appearance','wpknb'), array(&$this, 'appearance_metabox'), $this->pagehook, 'normal', 'core');
		add_meta_box('wpknb-advance-metabox', __('Advance Options','wpknb'), array(&$this, 'advance_metabox'), $this->pagehook, 'normal', 'core');
		add_meta_box('wpknb-css-metabox', __('Custom Css','wpknb'), array(&$this, 'css_metabox'), $this->pagehook, 'normal', 'core');
		// add_meta_box('wpknb-advance-metabox', __('Advance Options','wpknb'), array(&$this, 'wpknb_advance_metabox'), $this->pagehook, 'normal', 'core');
		
		if(isset($_POST['wpknb_action']) && $_POST['wpknb_action'] == 'save_wpknb_settings_page'){
			$postdata = serialize($_POST['wpknb']);
			// $formdata = unserialize($formdata);
			update_option('wpknb-settings', $postdata);
			$this->opts = unserialize(get_option('wpknb-settings'));
			$this->notices = '<div id="message" class="updated below-h2"><p>'. __('Settings Successfully Updated. Please <a href="'. str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) .'">click here</a> to refresh post type labels.','wpKnB') .'</p></div>';
		}	
	}

	/*
	Plugin Settings Page
	@since version 1.0
	*/
	public function settings(){
		?>
		<div id="howto-metaboxes-general" class="wrap">

			<?php screen_icon('options-general'); ?>
			<h2><?php _e('KnB - Settings','wpknb');?></h2><br /><br />
			<?php
			if(isset($_POST['wpknb_action']) && $_POST['wpknb_action'] == 'save_wpknb_settings_page'){
				echo $this->notices;
			}
			?>

			<form action="<?php _e( str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) ); ?>" method="post">
				<?php wp_nonce_field('wpknb-settings-page'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
				<input type="hidden" name="wpknb_action" value="save_wpknb_settings_page" />

				<div id="poststuff" class="metabox-holder">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->pagehook, 'side', $this->opts); ?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes($this->pagehook, 'normal', $this->opts); ?>
							<!-- <p class="wpp_save_changes_row">
							<input type="submit" value="Save Changes" class="button-primary btn" name="Submit">
							 </p> -->
						</div>
					</div>
					<br class="clear"/>					
				</div>
			</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
		<?php
	}

	/*
		Settings Display Metabox
		@since version 1.0
	*/
	public function display_metabox(){
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wpknb_all_text"><?php _e('View All Link Text', 'wpKnB');?></label></th>
					<td>
						<input type="text" name="wpknb[all_text]" class="wpknb_settings_field widefat" id="wpknb_all_text" value="<?php if(isset($this->opts['all_text']) && !empty($this->opts['all_text'])){ echo $this->opts['all_text']; }?>" />
						<br><em><small><?php _e('<strong>%s</strong> will be display as total article count per category.','wpKnB')?></small></em>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	/*
		Settings Appearance Metabox
		@since version 1.0
	*/
	public function appearance_metabox(){
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wpknb_category_color"><?php _e('Category Link Color','wpKnB')?></label></th>
					<td><input type="text" id="wpknb_category_color" name="wpknb[category_color]" class="wpknb_colorwell" value="<?php if(isset($this->opts['category_color']) && !empty($this->opts['category_color'])){ echo $this->opts['category_color']; }?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="wpknb_link_color"><?php _e('Text Link Color','wpKnB')?></label></th>
					<td><input type="text" id="wpknb_link_color" name="wpknb[link_color]" class="wpknb_colorwell" value="<?php if(isset($this->opts['link_color']) && !empty($this->opts['link_color'])){ echo $this->opts['link_color']; }?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="wpknb_all_color"><?php _e('View All Link Color','wpKnB')?></label></th>
					<td><input type="text" id="wpknb_all_color" name="wpknb[all_color]" class="wpknb_colorwell" value="<?php if(isset($this->opts['all_color']) && !empty($this->opts['all_color'])){ echo $this->opts['all_color']; }?>" /></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/*
		Settings Advance Metabox
		@since version 1.0
	*/
	public function advance_metabox(){
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wpknb_singular"><?php _e('Singular Name','wpknb');?></label></th>
					<td><input type="text" name="wpknb[singular]" id="wpknb_singular" value="<?php if(isset($this->opts['singular']) && !empty($this->opts['singular'])){ echo $this->opts['singular']; }?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="wpknb_plural"><?php _e('Plural Name','wpknb');?></label></th>
					<td><input type="text" name="wpknb[plural]" id="wpknb_plural" value="<?php if(isset($this->opts['plural']) && !empty($this->opts['plural'])){ echo $this->opts['plural']; }?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="wpknb_slug"><?php _e('Slug','wpknb');?></label></th>
					<td><input type="text" name="wpknb[slug]" id="wpknb_slug" value="<?php if(isset($this->opts['slug']) && !empty($this->opts['slug'])){ echo $this->opts['slug']; }?>"> 
						&nbsp;<em><?php _e("If you change this option, you might have to update/save the 'permalink' settings again", 'wpKnB');?></em></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="wpknb_category"><?php _e('Category Singular','wpknb');?></label></th>
					<td><input type="text" name="wpknb[category]" id="wpknb_category" value="<?php if(isset($this->opts['category']) && !empty($this->opts['category'])){ echo $this->opts['category']; }?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="wpknb_category_plural"><?php _e('Category Plural','wpknb');?></label></th>
					<td><input type="text" name="wpknb[category_plural]" id="wpknb_category_plural" value="<?php if(isset($this->opts['category_plural']) && !empty($this->opts['category_plural'])){ echo $this->opts['category_plural']; }?>"></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="wpknb_cat_slug"><?php _e('Category Slug','wpknb');?></label></th>
					<td><input type="text" name="wpknb[cat_slug]" id="wpknb_cat_slug" value="<?php if(isset($this->opts['cat_slug']) && !empty($this->opts['cat_slug'])){ echo $this->opts['cat_slug']; }?>"> 
						&nbsp;<em><?php _e("If you change this option, you might have to update/save the 'permalink' settings again", 'wpKnB');?></em></td>
				</tr>
				<tr valign="top">
					<th scope="row"><input type="submit" value="Save Changes" class="button-primary btn" name="Submit"></th>
					<td>
						&nbsp;
				</tr>
			</tbody>
		</table>
		<?php
	}

	/*
		Settings Custom Css Metabox
		@since version 1.0
	*/
	public function css_metabox(){
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wpknb_css"><?php _e('Add Custom Css Code','wpknb');?></label></th>
					<td>
						<textarea name="wpknb[css]" id="wpknb_css" class="widefat" rows="10"><?php if(isset($this->opts['css']) && !empty($this->opts['css'])){ echo stripcslashes($this->opts['css']); }?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><input type="submit" value="Save Changes" class="button-primary btn" name="Submit"></th>
					<td>
						&nbsp;
				</tr>
			</tbody>
		</table>
		<?php
	}
}
