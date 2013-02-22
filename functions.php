<?php
	
	// GENERAL HOUSEKEEPING...
	
	// * Add RSS links to <head> section *
	
	add_theme_support( 'automatic-feed-links' );
	
	// * Load jQuery *
	  
	function load_google_scripts() {  
		if (!is_admin()) {  
			wp_deregister_script( 'jquery' );  
			wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');  
			wp_enqueue_script('jquery');  
		}  
	}  
	add_action('wp_enqueue_scripts', 'load_google_scripts');  
	
	// * Clean up the <head> *
	
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
    // * Set max content width *
    
    if ( ! isset( $content_width ) ) $content_width = 720;
    
    // * Include notifier file *
    
    include("inc/updatenotifier.php");
 
 	// * Add support for WP menu *
 
	function register_ehub_menu() {
			register_nav_menu('main-nav-menu', __('Main Navigation Menu'));
	}
 	add_action('init', 'register_ehub_menu');
 
    // DASHBOARD AND OTHER MENU MODS...
    
	// * Add Edit Hub logos to WP login page and dashboard *
	
	// - login page logo -
	function custom_login_logo() {
		echo '<style type="text/css">h1 a { background: url('.get_template_directory_uri().'/images/edithub-login-logo.png) 50% 50% no-repeat !important; }</style>';
	}
	add_action('login_head', 'custom_login_logo');
	
	// - hook the administrative header output -
	function custom_db_logo() {
		echo '<style type="text/css">#header-logo { background-image: url('.get_template_directory_uri().'/images/edithub-db-logo.png) !important; }</style>';
	}
		add_action('admin_head', 'custom_db_logo');

	// * Clean up WP Dashboard area *
	
	function remove_dashboard_widgets(){
	  global$wp_meta_boxes;
	  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	}
	
	add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
	
	// * Add custom welcome Dashboard widget *
	
	// - create the function to output the contents of the welcome Dashboard widget -
		function welcome_dashboard_widget_function() {
			// - output -
			echo '<p>Welcome to Edit Hub! You are currently using version 1.0.</p>';
			echo '<p>For usage tips or access to documentation on how to make the most of your Edit Hub installation, please visit the <a href="http://mandiwise.com/edithub/" target="_blank"> developer\'s website</a> .</p>';
		}
		 
		// - create the function used in the action hook -
		function welcome_add_dashboard_widgets() {
			wp_add_dashboard_widget('welcome_dashboard_widget', 'Edit Hub', 'welcome_dashboard_widget_function');
		}
		
		// - hook into the 'wp_dashboard_setup' action to register our other functions -
		add_action('wp_dashboard_setup', 'welcome_add_dashboard_widgets' );
	
	// * Add Recent Content Posts Dashboard Widget *

	// - hook into wp_dashboard_setup and add the widget -
	add_action('wp_dashboard_setup', 'recent_posts_widget');
	
	// - create the function that adds the widget -
	function recent_posts_widget(){
		// Add our Posts Query widget
		wp_add_dashboard_widget( 'recent-posts', 'My Recently Published Content', 'recent_posts_output', 'recent_posts_control');
	}
	
	// - the function that outputs the posts query -
	function recent_posts_output(){
		
		$widgets = get_option( 'dashboard_widget_options' );
		$widget_id = 'recent-posts'; // - this must be the same ID we set in wp_add_dashboard_widget -
		
		// - check whether the post count is set through the controls... if not, set the default to 3 -
		 
		$total_items = 	isset( $widgets[$widget_id] ) && isset( $widgets[$widget_id]['items'] )
						? absint( $widgets[$widget_id]['items'] ) : 3;
		
		// - set up the query -
		$posts_query = new WP_Query( array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'author' => $GLOBALS['current_user']->ID,
			'posts_per_page' => $total_items, // - sets the number of posts to the option collected above -
			'orderby' => 'date',
			'order' => 'DESC'
		) );
		$posts =& $posts_query->posts;
	
		if ( $posts && is_array( $posts ) ) {
			$list = array();
			foreach ( $posts as $post ) { 
				$url = get_edit_post_link( $post->ID ); // - the URL to the "Edit" post page -
				$title = get_the_title( $post->ID ); // - the title of the post -
				$chars = 30; // -the character limit -
				
				// - create the title of the post and link it to the "Edit" page -
				$item = "<h4><a href='$url' title='" . sprintf( __( 'Edit &#8220;%s&#8221;' ), esc_attr( $title ) ) . "'>" . esc_html($title) . "</a> <abbr title='" . get_the_time(__('Y/m/d g:i:s A'), $post) . "'>" . get_the_time( get_option( 'date_format' ), $post ) . '</abbr></h4>';
				
				// - create the post content -
				if ( $the_content = preg_split( '#\s#', strip_tags( $post->post_content ), $chars+1 , PREG_SPLIT_NO_EMPTY ) )
					$item .= '<p>' . join( ' ', array_slice( $the_content, 0, $chars ) ) . ( $chars < count( $the_content ) ? '&hellip;' : '' ) . '</p>';
				$list[] = $item;
			}
		
		// - output the posts... -
	?>
		<ul>
			<li><?php echo join( "</li>\n<li>", $list ); ?></li>
		</ul>
	<?php
		} else { 
			_e('You haven\'t published any posts yet.'); //
		}

	} // - #output -
	
	// - the function that enables controls -
	function recent_posts_control(){
		
		$widget_id = 'recent-posts'; // - this must be the same ID we set in wp_add_dashboard_widget -
		$form_id = 'recent-posts-control'; // - can be set to whatever you want -
		
		// - checks whether there are already dashboard widget options in the database -
		if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
			$widget_options = array(); // - if not, we create a new array -
		
		// - check whether there's information available for this form -
		if ( !isset($widget_options[$widget_id]) )
			$widget_options[$widget_id] = array(); // If not, create a new array
		
		// - check whether the form was just submitted -
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$form_id]) ) {
			/*
			 * Get the value. In this case ['items'] is from the input 
			 * field with the name of '.$form_id.'[items]
			 */
			$number = absint( $_POST[$form_id]['items'] );
			
			$widget_options[$widget_id]['items'] = $number; // - set the number of items -
			update_option( 'dashboard_widget_options', $widget_options ); // - update our dashboard widget options -
		}
		
		// - check if the number of posts was previously set... if not, then just set it as empty -
		
		// - this value is used when we create the input field -
		$number = isset( $widget_options[$widget_id]['items'] ) ? (int) $widget_options[$widget_id]['items'] : '';
		
		// - create our form fields -
		echo '<p><label for="recent-posts-number">' . __('Number of posts to show:') . '</label>';
		echo '<input id="recent-posts-number" name="'.$form_id.'[items]" type="text" value="' . $number . '" size="3" /></p>';
	}
	
	// * Add Upcoming Events Dashboard Widget *

	// - create the function to output the upcoming events (brief) shortcode -
		function upcoming_events_widget() {
			// - output -
			echo do_shortcode("[ehub-events-brief limit='5']");
		}
		 
		// - create the function used in the action hook -
		function events_add_dashboard_widget() {
			wp_add_dashboard_widget('events-dashboard-widget', 'Upcoming Events and Deadlines', 'upcoming_events_widget');
		}
		// - hook into the 'wp_dashboard_setup' action to register other functions -
	
	add_action('wp_dashboard_setup', 'events_add_dashboard_widget' );
	
	// * Change the name of Posts to Content *
	
	function change_post_menu_label() {
		global $menu;
		global $submenu;
		$menu[5][0] = 'Content';
		$submenu['edit.php'][5][0] = 'Content';
		$submenu['edit.php'][10][0] = 'Add Content Post';
		echo '';
	}
	
	function change_post_object_label() {
		global $wp_post_types;
		$labels = &$wp_post_types['post']->labels;
		$labels->name = 'Content';
		$labels->singular_name = 'Content';
		$labels->add_new = 'Add Content Post';
		$labels->add_new_item = 'Add Content Post';
		$labels->edit_item = 'Edit Content';
		$labels->new_item = 'Content';
		$labels->view_item = 'View Content';
		$labels->search_items = 'Search Content';
		$labels->not_found = 'No Content found';
		$labels->not_found_in_trash = 'No Content found in Trash';
		}
	
	add_action( 'init', 'change_post_object_label' );
	add_action( 'admin_menu', 'change_post_menu_label' );
	
	// * Remove Comments, Links & Tools submenus from admin menu *
	
	function remove_menu_items() {
		global $menu;
		$restricted = array(__('Links'), __('Comments'));
		end ($menu);
		while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
		  unset($menu[key($menu)]);}
		}
	}
		
	add_action('admin_menu', 'remove_menu_items');
	
	// * Remove unneeded submenus from admin menu *
	
	function remove_submenu_profile() {
		remove_submenu_page('themes.php','widgets.php');
		remove_submenu_page('tools.php','tools.php');
		remove_submenu_page('tools.php','import.php');
		remove_submenu_page('options-general.php','options-writing.php');
		remove_submenu_page('options-general.php','options-discussion.php');
		remove_submenu_page('options-general.php','options-media.php');
	}
	
	add_action( 'admin_menu', 'remove_submenu_profile' );

	function remove_theme_editor() {
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
	}
	
	add_action('admin_init', 'remove_theme_editor', 102);
		
	// * Customize User Profile contact information inputs *
	
	function new_contactmethods( $contactmethods ) {
		$contactmethods['twitter'] = 'Twitter'; // Add Twitter
		$contactmethods['facebook'] = 'Facebook'; // Add Facebook
		unset($contactmethods['yim']); // Remove Yahoo IM
		unset($contactmethods['aim']); // Remove AIM
		unset($contactmethods['jabber']); // Remove Jabber
	
	return $contactmethods;
	}
	
	add_filter('user_contactmethods','new_contactmethods',10,1);

	// * Customizes the admin footer *
	
	function modify_footer_admin () {
	  echo 'Powered by <a href="http://wordphtress.org">WordPress</a> &#8226; Created by <a href="http://mandiwise.com">Mandi Wise</a> &#8226; Edit Hub <a href="http://mandiwise.com/edithub/">Info &amp; Help</a>';
	}
	
	add_filter('admin_footer_text', 'modify_footer_admin');
	
	function change_footer_version() { return '&nbsp;'; }
	
	add_filter( 'update_footer', 'change_footer_version', 9999);
	
	// * Move the author drop-down to the Publish metabox *
	
	add_action( 'admin_menu', 'remove_author_metabox' );
	add_action( 'post_submitbox_misc_actions', 'move_author_to_publish_metabox' );
	
	function remove_author_metabox() {
		remove_meta_box( 'authordiv', 'post', 'normal' );
		remove_meta_box( 'authordiv', 'page', 'normal' );
	}
	
	function move_author_to_publish_metabox() {
		global $post_ID;
		$post = get_post( $post_ID );
		echo '<div id="author" class="misc-pub-section" style="border-top: 1px solid #dfdfdf; border-bottom: 0;">Author: ';
		post_author_meta_box( $post );
		echo '</div>';
	}
	
	// * Remove unnecessary metaboxes from post and page editors *
	
	function remove_extra_meta_boxes() {
		remove_meta_box( 'postcustom' , 'post' , 'normal' ); // Removes custom fields for posts
		remove_meta_box( 'postcustom' , 'page' , 'normal' ); // Removes custom fields for pages
		remove_meta_box( 'postexcerpt' , 'post' , 'normal' ); // Removes post excerpts
		remove_meta_box( 'postexcerpt' , 'page' , 'normal' ); // Removes page excerpts
		remove_meta_box( 'commentsdiv' , 'post' , 'normal' ); // Removes recent comments for posts
		remove_meta_box( 'commentsdiv' , 'page' , 'normal' ); // Removes recent comments for pages
		remove_meta_box( 'categorydiv' , 'post' , 'side' ); // Removes post categories 
		remove_meta_box( 'tagsdiv-post_tag' , 'post' , 'side' ); // Removes post tags
		remove_meta_box( 'tagsdiv-post_tag' , 'page' , 'side' ); // Removes page tags
		remove_meta_box( 'trackbacksdiv' , 'post' , 'normal' ); // Removes post trackbacks
		remove_meta_box( 'trackbacksdiv' , 'page' , 'normal' ); // Removes page trackbacks
		remove_meta_box( 'commentstatusdiv' , 'post' , 'normal' ); // Removes allow comments for posts
		remove_meta_box( 'commentstatusdiv' , 'page' , 'normal' ); // Removes allow comments for pages
		remove_meta_box('slugdiv','post','normal'); // Removes post slug
		remove_meta_box('slugdiv','page','normal'); // Removes page slug
	}
	
	add_action( 'admin_menu' , 'remove_extra_meta_boxes' );
	
	// * Disable Edit Flow on pages *
	
	function disable_editflow_on_pages () {
		remove_post_type_support('page', 'ef_custom_statuses');
			remove_post_type_support('page', 'ef_notifications');
			remove_post_type_support('page', 'ef_editorial_comments');
			remove_post_type_support('page', 'ef_calendar');
			remove_post_type_support('page', 'ef_editorial_metadata');
		}
		
	add_action( 'init', 'disable_editflow_on_pages' );
	
	// * Remove default tag and category options from posts
	
	function remove_submenus() {
		global $submenu;
		unset($submenu['edit.php'][15]); // Removes 'Categories'
		unset($submenu['edit.php'][16]); // Removes 'Tags'
		}
		
	add_action('admin_menu', 'remove_submenus');
	
	//  * Customize column view for all Posts grid * 
	
	function add_new_post_columns($posts_columns) {
		// - delete an existing column -
		$posts_columns = array(
			"cb" => "",
			"title" => "Title",
			"author" => "Author",
			"date" => "Date",
		);
		return $posts_columns;
	}
	
	function custom_post_columns($defaults) {
	  unset($defaults['categories']);
	  unset($defaults['tags']);
	  return $defaults;
	}
	
	add_filter('manage_posts_columns', 'add_new_post_columns');
	add_filter('manage_posts_columns', 'custom_post_columns');
	
	function add_new_columns($defaults) {
		$defaults['pub_cats'] = __('Content Channel');
		return $defaults;
	}
	function add_column_data( $column_name, $post_id ) {
		if( $column_name == 'pub_cats' ) {
			$_taxonomy = 'content_channel';
			$terms = get_the_terms( $post_id, $_taxonomy );
			if ( !empty( $terms ) ) {
				$out = array();
				foreach ( $terms as $c )
					$out[] = "<a href='edit-tags.php?action=edit&taxonomy=$_taxonomy&post_type=book&tag_ID={$c->term_id}'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
				echo join( ', ', $out );
			}
			else {
				_e('Not Assigned');
			}
		}
	}
	
	add_filter( 'manage_posts_columns', 'add_new_columns' );
	add_action( 'manage_posts_custom_column', 'add_column_data', 10, 2 );
	
	// STYLE FIXES
	
	// * Various Dashboard style fixes *  
	
	function various_admin_style_fixes() {
		?>
			<style type="text/css">
				#recent-posts h4 abbr { font-weight: normal; font-family: sans-serif; font-size: 12px; color: #999; margin-left: 3px; }
				#recent-posts p { font-size: 12px; margin-top: 6px; }
				#events-dashboard-widget .eventtext { margin: 6px 0 12px; }
				#events-dashboard-widget .eventtext img { display: none; }
				#footer p#footer-left.alignleft a { color: #333; }
				#contextual-help-link-wrap { display: none; } /* hide the Help tab... for now */
				#ui-datepicker-div { display: none; } /* hide phantom datepicker div */
			</style>
		<?php
	}
					
	add_action('admin_enqueue_scripts', 'various_admin_style_fixes');		
	
	// * Remove Quick Edit and default Category select dropdown from manage posts admin AND remove Category box on nav menu page
	
	function remove_default_category_selectors($hook) {
		if( 'edit.php' != $hook )
			return;
		?>
			<style type="text/css">
				#the-list .hide-if-no-js,a.editinline { display: none; }
				form#posts-filter select#cat.postform { display: none; }
			</style>
		<?php
	}
		
	function remove_category_box_nav_menus($hook) {
		if( 'nav-menus.php' != $hook )
			return;
		?>
			<style type="text/css">
				form#nav-menu-meta #add-category { display: none !important; }
			</style>
		<?php
	}
	
	add_action( 'admin_enqueue_scripts', 'remove_default_category_selectors' );
	add_action( 'admin_enqueue_scripts', 'remove_category_box_nav_menus' );
	
	// * Various style fixes for when the Edit Flow plugin is installed *
	
	function default_category_fix_for_story_budget() {
	
		if (is_plugin_active('edit-flow/edit_flow.php')) {
			?>
				<style type="text/css">
					select#cat.postform { display:none; } /* target category dropdowns if Edit Flow is installed */
					#ef-story-budget-wrap h3.hndle span { visibility:hidden; }
				</style>
			<?php
		}
	}
	
	add_action('admin_enqueue_scripts', 'default_category_fix_for_story_budget');		

	// * Hide empty theme options for Edit Hub on Manage Themes
	
	function hide_theme_option_ehub_appearance($hook) {
		if( 'themes.php' != $hook )
			return;
		?>
			<style type="text/css">
				#current-theme .theme-options { display:none; }
			</style>
		<?php
	}
	add_action( 'admin_enqueue_scripts', 'hide_theme_option_ehub_appearance' );
	
	// * Customize the Admin bar option *
	
	function edithub_admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('edit');
		$wp_admin_bar->remove_menu('new-content');
		$wp_admin_bar->remove_menu('comments');
		$wp_admin_bar->remove_menu('appearance');
		$wp_admin_bar->remove_menu('updates');
	}
	
	add_action( 'wp_before_admin_bar_render', 'edithub_admin_bar_render' );
	
	// CREATE CUSTOM TAXONOMIES
	
	// * Create Publication Category taxonomy *
	
	function create_channel_taxonomy() {
		 $labels = array(
			'name' => _x( 'Content Channels', 'taxonomy general name' ),
			'singular_name' => _x( 'Content Channel', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Content Channels' ),
			'all_items' => __( 'All Content Channels' ),
			'parent_item' => __( 'Parent Content Channel' ),
			'parent_item_colon' => __( 'Parent Content Channel:' ),
			'edit_item' => __( 'Edit Content Channel' ),
			'update_item' => __( 'Update Content Channel' ),
			'add_new_item' => __( 'Add New Content Channel' ),
			'new_item_name' => __( 'New Content Channel Name' ),
		  ); 	
		
		  register_taxonomy( 'content_channel', 'post', array(
			'label' => __('Content Channel'),
			'labels' => $labels,
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'content-channel' ),
		  ));
	}
	
	add_action( 'init', 'create_channel_taxonomy' );
	
	// * Create Audience taxonomy *
	
		function create_audience_taxonomy() {
		 $labels = array(
			'name' => _x( 'Audiences', 'taxonomy general name' ),
			'singular_name' => _x( 'Audience', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Audiences' ),
			'all_items' => __( 'All Audiences' ),
			'parent_item' => __( 'Parent Audience' ),
			'parent_item_colon' => __( 'Parent Audience:' ),
			'edit_item' => __( 'Edit Audience' ),
			'update_item' => __( 'Update Audience' ),
			'add_new_item' => __( 'Add New Audience' ),
			'new_item_name' => __( 'New Audience Name' ),
		  ); 	
		
		  register_taxonomy( 'audience', 'post', array(
			'label' => __('Audience'),
			'labels' => $labels,
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'audience' ),
		  ));
	}
	
	add_action( 'init', 'create_audience_taxonomy' );
	
	// * Create Keywords taxonomy *

	function create_keyword_taxonomy() {
		 $labels = array(
			'name' => _x( 'Keywords', 'taxonomy general name' ),
			'singular_name' => _x( 'Keyword', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Keywords' ),
			'all_items' => __( 'All Keywords' ),
			'parent_item' => __( 'Parent Keyword' ),
			'parent_item_colon' => __( 'Parent Keyword:' ),
			'edit_item' => __( 'Edit Keyword' ),
			'update_item' => __( 'Update Keyword' ),
			'add_new_item' => __( 'Add New Keyword' ),
			'new_item_name' => __( 'New Keyword Name' ),
		  ); 	
		
		  register_taxonomy( 'keyword', 'post', array(
			'label' => __('Keyword'),
			'labels' => $labels,
			'hierarchical' => false,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'keyword' ),
		  ));
	}
	
	add_action( 'init', 'create_keyword_taxonomy' );
	
	// * Fix the category hierarchy of the Content Channel custom taxonomy
 
	add_action( 'admin_head', 'remove_default_contentchannel_box' );
	add_action('admin_menu', 'add_custom_contentchannel_box');
	 
	function remove_default_contentchannel_box() {
		remove_meta_box('content_channeldiv', 'post', 'side');
	}
	function add_custom_contentchannel_box() {
		add_meta_box('mycontent_channeldiv', 'Content Channels', 'post_contentchannel_meta_box', 'post', 'side', 'default', array( 'taxonomy' => 'content_channel' ));
	}
	 
	function post_contentchannel_meta_box( $post, $box ) {
		$defaults = array('taxonomy' => 'content_channel');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax = get_taxonomy($taxonomy);
	 
		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
			</ul>
	 
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>
	 
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<?php
				$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
	<?php
	
		// - checked_ontop set to FALSE maintains the parent-child hierarchy - 
	
	wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => FALSE ) )
	?>
				</ul>
			</div>
		<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
				<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
					<h4>
						<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
							<?php
								/* translators: %s: add new taxonomy label */
								printf( __( '+ %s' ), $tax->labels->add_new_item );
							?>
						</a>
					</h4>
					<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
						<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
							<?php echo $tax->labels->parent_item_colon; ?>
						</label>
						<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
						<input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-sumbit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
						<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
						<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
	
	// * Fix the category hierarchy of the Audience custom taxonomy
 
	add_action( 'admin_head', 'remove_default_audience_box' );
	add_action('admin_menu', 'add_custom_audience_box');
	 
	function remove_default_audience_box() {
		remove_meta_box('audiencediv', 'post', 'side');
	}
	function add_custom_audience_box() {
		add_meta_box('myaudiencediv', 'Audiences', 'post_audience_meta_box', 'post', 'side', 'default', array( 'taxonomy' => 'audience' ));
	}
	 
	function post_audience_meta_box( $post, $box ) {
		$defaults = array('taxonomy' => 'audience');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax = get_taxonomy($taxonomy);
	 
		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
			</ul>
	 
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>
	 
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<?php
				$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
	<?php
	
		// - checked_ontop set to FALSE maintains the parent-child hierarchy - 
	
	wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => FALSE ) )
	?>
				</ul>
			</div>
		<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
				<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
					<h4>
						<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
							<?php
								/* translators: %s: add new taxonomy label */
								printf( __( '+ %s' ), $tax->labels->add_new_item );
							?>
						</a>
					</h4>
					<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
						<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
							<?php echo $tax->labels->parent_item_colon; ?>
						</label>
						<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
						<input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-sumbit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
						<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
						<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
	
	// BEGINNING OF EVENTS CALENDAR CODE...

	// * Registers custom post type for events calendar *
	
	add_action( 'init', 'create_event_postype' );
	
	function create_event_postype() {
	
	$labels = array(
		'name' => _x('Event Calendar', 'post type general name'),
		'singular_name' => _x('Event Calendar', 'post type singular name'),
		'add_new' => _x('Add New Event', 'events'),
		'add_new_item' => __('Add New Calendar Event'),
		'edit_item' => __('Edit Calendar Event'),
		'new_item' => __('New Calendar Event'),
		'view_item' => __('View Calendar Event'),
		'search_items' => __('Search Calendar Events'),
		'not_found' =>  __('No events found'),
		'not_found_in_trash' => __('No events found in Trash'),
		'parent_item_colon' => '',
	);
	
	$args = array(
		'label' => __('Calendar Events'),
		'labels' => $labels,
		'public' => true,
		'can_export' => true,
		'show_ui' => true,
		'_builtin' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array( "slug" => "calendar" ),
		'supports'=> array('title', 'editor') ,
		'show_in_nav_menus' => true,
		'taxonomies' => array( 'ehub_eventcategory' )
	);
	
	register_post_type( 'ehub_events', $args );
	
	}

	// * Registers custom taxonomy for events calendar *
	
	function create_eventcategory_taxonomy() {

		$labels = array(
			'name' => _x( 'Event Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Event Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Event Categories' ),
			'popular_items' => __( 'Popular Event Categories' ),
			'all_items' => __( 'All Event Categories' ),
			'parent_item' => __( 'Parent Event Category' ),
			'parent_item_colon' => __( 'Parent Event Category:' ),
			'edit_item' => __( 'Edit Event Category' ),
			'update_item' => __( 'Update Event Category' ),
			'add_new_item' => __( 'Add New Event Category' ),
			'new_item_name' => __( 'New Event Category Name' ),
		);
		
		register_taxonomy('ehub_eventcategory','ehub_events', array(
			'label' => __('Event Category'),
			'labels' => $labels,
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'event-category' ),
		));
	}
	
	add_action( 'init', 'create_eventcategory_taxonomy', 0 );
	
	// * Fix the category hierarchy of the Event Category custom taxonomy
 
	add_action( 'admin_head', 'remove_default_eventcategory_box' );
	add_action('admin_menu', 'add_custom_eventcategory_box');
	 
	function remove_default_eventcategory_box() {
		remove_meta_box('ehub_eventcategorydiv', 'ehub_events', 'side');
	}
	function add_custom_eventcategory_box() {
		add_meta_box('mycontent_channeldiv', 'Event Categories', 'ehub_events_eventcategory_meta_box', 'ehub_events', 'side', 'default', array( 'taxonomy' => 'ehub_eventcategory' ));
	}
	 
	function ehub_events_eventcategory_meta_box( $post, $box ) {
		$defaults = array('taxonomy' => 'ehub_eventcategory');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax = get_taxonomy($taxonomy);
	 
		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
			</ul>
	 
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>
	 
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<?php
				$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
	<?php
	
		// - checked_ontop set to FALSE maintains the parent-child hierarchy - 
	
	wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => FALSE ) )
	?>
				</ul>
			</div>
		<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
				<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
					<h4>
						<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
							<?php
								/* translators: %s: add new taxonomy label */
								printf( __( '+ %s' ), $tax->labels->add_new_item );
							?>
						</a>
					</h4>
					<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
						<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
							<?php echo $tax->labels->parent_item_colon; ?>
						</label>
						<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
						<input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-sumbit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
						<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
						<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	// * Customizes column view for events calendar in admin area *
	
	add_filter ("manage_edit-ehub_events_columns", "ehub_events_edit_columns");
	add_action ("manage_posts_custom_column", "ehub_events_custom_columns");
	
	function ehub_events_edit_columns($columns) {
	
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Event Name",
			"ehub_col_ev_cat" => "Event Category",
			"ehub_col_ev_date" => "Dates",
			"ehub_col_ev_times" => "Times",
			);
		return $columns;
		}
		
		function ehub_events_custom_columns($column)
		{
		global $post;
		$custom = get_post_custom();
		switch ($column)
		{
		case "ehub_col_ev_cat":
			// - show taxonomy terms -
			$eventcats = get_the_terms($post->ID, "ehub_eventcategory");
			$eventcats_html = array();
			if ($eventcats) {
			foreach ($eventcats as $eventcat)
			array_push($eventcats_html, $eventcat->name);
			echo implode($eventcats_html, ", ");
			} else {
			_e('None', 'edithub');;
			}
		break;
		case "ehub_col_ev_date":
			// - show dates -
			$startd = $custom["ehub_events_startdate"][0];
			$endd = $custom["ehub_events_enddate"][0];
			$startdate = date("F j, Y", $startd);
			$enddate = date("F j, Y", $endd);
			echo $startdate . '<br /><em>' . $enddate . '</em>';
		break;
		case "ehub_col_ev_times":
			// - show times -
			$startt = $custom["ehub_events_startdate"][0];
			$endt = $custom["ehub_events_enddate"][0];
			$time_format = get_option('time_format');
			$starttime = date($time_format, $startt);
			$endtime = date($time_format, $endt);
			echo $starttime . ' - ' .$endtime;
		break;
		
		}
	}

	// * Add meta box for event date and time *
	
	add_action( 'admin_init', 'ehub_events_create' );
	
	function ehub_events_create() {
		add_meta_box('ehub_events_meta', 'Add the Event Date and Time', 'ehub_events_meta', 'ehub_events', 'normal', 'high');
	}
	
	function ehub_events_meta () {
	
		// - grab data -
		
		global $post;
		$custom = get_post_custom($post->ID);
		$meta_sd = $custom["ehub_events_startdate"][0];
		$meta_ed = $custom["ehub_events_enddate"][0];
		$meta_st = $meta_sd;
		$meta_et = $meta_ed;
		
		// - grab wp time format -
		
		$date_format = get_option('date_format'); // Not required
		$time_format = get_option('time_format');
		
		// - populate today if empty, 00:00 for time -
		
		if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}
		
		// - format date and time consistently -
		
		$clean_sd = date("D, M d, Y", $meta_sd);
		$clean_ed = date("D, M d, Y", $meta_ed);
		$clean_st = date($time_format, $meta_st);
		$clean_et = date($time_format, $meta_et);
		
		// - security -
		
		echo '<input type="hidden" name="ehub-events-nonce" id="ehub-events-nonce" value="' . wp_create_nonce( 'ehub-events-nonce' ) . '" />';
		
		// - output -
		
		?>
		<table class="ehub-cal-meta">
			<tbody>
				<tr height="35px"><td width="80px"><label>Start Date</label></td><td><input name="ehub_events_startdate" class="ehubdate" value="<?php echo $clean_sd; ?>" /></td></tr>
				<tr height="35px"><td width="80px"><label>Start Time</label></td><td><input name="ehub_events_starttime" value="<?php echo $clean_st; ?>" style="margin-right: 5px" /></td></tr>
				<tr height="35px"><td width="80px"><label>End Date</label></td><td><input name="ehub_events_enddate" class="ehubdate" value="<?php echo $clean_ed; ?>" /></td></tr>
				<tr height="35px"><td width="80px"><label>End Time</label></td><td><input name="ehub_events_endtime" value="<?php echo $clean_et; ?>" style="margin-right: 5px" /></td></tr>
			</tbody>
		</table>
		<?php
		}

	// * Save event date and time metabox data *
	
	add_action ('save_post', 'save_ehub_events');
	
	function save_ehub_events(){
	
		global $post;
		
		// - still require nonce -
		
		if ( isset( $_POST['ehub-events-nonce'] ) && ! wp_verify_nonce( $_POST['ehub-events-nonce'], 'ehub-events-nonce' ) ) {
			return $post->ID;
		}
		
		if ( !current_user_can( 'edit_posts' ))
			return $post->ID;
		
		// - convert back to unix & update post -
		
		if(!isset($_POST["ehub_events_startdate"])):
		return $post;
		endif;
		$updatestartd = strtotime ( $_POST["ehub_events_startdate"] . $_POST["ehub_events_starttime"] );
		update_post_meta($post->ID, "ehub_events_startdate", $updatestartd );
		
		if(!isset($_POST["ehub_events_enddate"])):
		return $post;
		endif;
		$updateendd = strtotime ( $_POST["ehub_events_enddate"] . $_POST["ehub_events_endtime"]);
		update_post_meta($post->ID, "ehub_events_enddate", $updateendd );
	
	}

	// * Customizes JS Datepicker UI *
	
	function events_styles() {
		global $post_type;
		if( 'ehub_events' != $post_type )
			return;
		wp_enqueue_style('ui-datepicker', get_template_directory_uri() . '/js/smoothness/jquery-ui-1.8.16.custom.css');
	}
	
	function events_scripts() {
		global $post_type;
		if( 'ehub_events' != $post_type )
			return;
		wp_deregister_script( 'jquery-ui-datepicker' );
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('ui-datepicker', get_template_directory_uri() . '/js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core'));
		wp_enqueue_script('custom_script', get_template_directory_uri() . '/js/custom-datepick.js', array('jquery'));
	}
	
	add_action( 'admin_enqueue_scripts', 'events_styles', 1000 );
	add_action( 'admin_enqueue_scripts', 'events_scripts', 1000 );
	
	// * Add shortcode for displaying events on full event listing page *
	
	function ehub_events_full ( $atts ) {
		
		// - define arguments -
		
		extract(shortcode_atts(array(
			'limit' => '10', // # of events to show
		 ), $atts));
		
		// - output function - 
		
		ob_start();
		
		// - loop: full events section -
		
			// - hide events that are older than 6am today -
			
			$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
			
			// - query -
			
			global $wpdb;
			$querystr = "
				SELECT *
				FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
				WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
				AND (metaend.meta_key = 'ehub_events_enddate' AND metaend.meta_value > $today6am )
				AND metastart.meta_key = 'ehub_events_enddate'
				AND wposts.post_type = 'ehub_events'
				AND wposts.post_status = 'publish'
				ORDER BY metastart.meta_value ASC LIMIT $limit
			 ";
			
			$events = $wpdb->get_results($querystr, OBJECT);
			
			if ($events):
			global $post;
			foreach ($events as $post):
			setup_postdata($post);
			
			// - custom variables -
			
			$custom = get_post_custom(get_the_ID());
			$sd = $custom["ehub_events_startdate"][0];
			$ed = $custom["ehub_events_enddate"][0];
			
			// - determine if it's a new day -
			
			$startdate = date("F j, Y", $sd);
			$enddate = date("F j, Y", $ed);

			// - local time format -
			
			$time_format = get_option('time_format');
			$stime = date($time_format, $sd);
			$etime = date($time_format, $ed);
			
			// - output - ?>
			
			<div class="full-events">
				<?php { if ($startdate < $enddate) { echo '<h3 class="full-events">' . $startdate . ' &#8211; ' . $enddate . '</h3>'; } else { echo '<h3 class="full-events">' . $startdate . '</h3>'; } } ?>
				<div class="text">
					<div class="title">
						<div class="eventtext"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></div>
						<div class="time"><img src="<?php echo get_template_directory_uri(); ?>/images/grey-bullet.png" alt="" style="margin-right: 5px;" /><?php echo ' Starts at ' . $stime . ' / Ends at ' . $etime; ?></div>
					</div>
				</div>
				 <div class="event-cat-list"><?php echo get_the_term_list( $post->ID, 'ehub_eventcategory', 'Event Category: ', ', ', '' ); ?></div>
			</div>
			<?php
			
			endforeach;
			else : ?><p style="margin-top: 10px;"><em>There are no upcoming events or deadlines right now.</em></p><?php
			endif;
		
		// - return: full events section -
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
		}
		
	add_shortcode('ehub-events-full', 'ehub_events_full'); // Resulting shortcode e.g. [ehub-events-full limit='20']

	// * Add shortcode for displaying event info snippets in a list on home page *
	
	function ehub_events_brief ( $atts ) {
		
		// - define arguments -
		
		extract(shortcode_atts(array(
			'limit' => '10', // # of events to show
		 ), $atts));
		
		// - output function - 
		
		ob_start();
		
		// - loop: full events section -
		
			// - hide events that are older than 6am today -
			
			$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
			
			// - query -
			
			global $wpdb;
			$querystr = "
				SELECT *
				FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
				WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
				AND (metaend.meta_key = 'ehub_events_enddate' AND metaend.meta_value > $today6am )
				AND metastart.meta_key = 'ehub_events_enddate'
				AND wposts.post_type = 'ehub_events'
				AND wposts.post_status = 'publish'
				ORDER BY metastart.meta_value ASC LIMIT $limit
			 ";
			
			$events = $wpdb->get_results($querystr, OBJECT);
			
			if ($events):
			global $post;
			foreach ($events as $post):
			setup_postdata($post);
			
			// - custom variables -
			
			$custom = get_post_custom(get_the_ID());
			$sd = $custom["ehub_events_startdate"][0];
			$ed = $custom["ehub_events_enddate"][0];
			
			// - determine if it's a new day -
			
			$startdate = date("F j, Y", $sd);
			$enddate = date("F j, Y", $ed);

			// - local time format -
			
			$time_format = get_option('time_format');
			$stime = date($time_format, $sd);
			$etime = date($time_format, $ed);
			
			// - output - ?>
			
			<div class="brief-event-listing">
				<?php { if ($startdate < $enddate) { echo '<h4 class="brief-event-listing">' . $startdate . ' &#8211; ' . $enddate . '</h4>'; } else { echo '<h4 class="brief-event-listing">' . $startdate . '</h4>'; } } ?>
				<div class="text">
					<div class="title">
						<div class="eventtext"><img src="<?php echo get_template_directory_uri(); ?>/images/grey-bullet.png" alt="" style="margin-right: 5px;" /><?php the_title(); ?></div>
					</div>
				</div>
			</div>
			<?php
			
			endforeach;
			else : ?><p>There are no upcoming events or deadlines right now</p><?php
			endif;
		
		// - return: full events section -
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
		}
		
	add_shortcode('ehub-events-brief', 'ehub_events_brief'); // Resulting shortcode e.g. [ehub-events-brief limit='20']

	// * Add shortcode for displaying date/time of a given event *
	
	function event_date_time() {
	
			global $post;
			$custom = get_post_custom($post->ID);
			$meta_sd = $custom["ehub_events_startdate"][0];
			$meta_ed = $custom["ehub_events_enddate"][0];
			$meta_st = $meta_sd;
			$meta_et = $meta_ed;
			$date_format = get_option('date_format'); // Not required
			$time_format = get_option('time_format');
			$clean_sd = date("F j, Y", $meta_sd);
			$clean_ed = date("F j, Y", $meta_ed);
			$clean_st = date($time_format, $meta_st);
			$clean_et = date($time_format, $meta_et);
			
			return 'Starts ' . $clean_sd . ' at ' . $clean_st . ' / Ends ' . $clean_ed .  ' at ' . $clean_et;
			
	}
	
	add_shortcode('event-date-time', 'event_date_time');

	// * Load javascript for full calendar *
	
	function load_ehubcal_js() {
	
		// - fullcalendar -
		
		wp_enqueue_script('fullcalendar', (get_template_directory_uri()) . '/js/fullcalendar.js', array('jquery'));
		wp_enqueue_script('gcal', (get_template_directory_uri()) . '/js/gcal.js', array('jquery'));
		
		// - set path to json feed -
		
		$jsonevents = (get_template_directory_uri()) . '/inc/json-feed.php';
		
		// - tell JS to use this variable instead of a static value -
		
		wp_localize_script( 'fullcalendar', 'ehubeventcal', array(
			'events' => $jsonevents,
			));
		}
	
	add_action('wp_print_scripts', 'load_ehubcal_js');

	// ...END OF EVENTS CALENDAR CODE
	
	// MISC MODS...
	
	// * Replace elipsis with text on excerpt *
	
	function new_excerpt_more($more) {
		global $post;
		return '&nbsp;<a href="'. get_permalink($post->ID) . '" class="read-more">[...]</a>';
	}
	add_filter('excerpt_more', 'new_excerpt_more');

	// * Allow users to email posts *
	
	function direct_email($text="Send by email"){
		global $post;
		$title = htmlspecialchars($post->post_title);
		$subject = 'From '.htmlspecialchars(get_bloginfo('name')).': '.$title;
		$body = 'I\'m forwarding you a link to content posted on Edit Hub titled "'.$title.'." Read it here: '.get_permalink($post->ID);
		$link = '<a rel="nofollow" href="mailto:?subject='.rawurlencode($subject).'&amp;body='.rawurlencode($body).'" title="'.$text.' : '.$title.'">'.$text.'</a>';
		return $link;
	}
	
	// * Create attachment list on single post pages *
	
	add_filter( 'the_content', 'ehub_the_content_filter' );
	
	function ehub_the_content_filter( $content ) {
		global $post;
	
		if ( is_single() && $post->post_type == 'post' && $post->post_status == 'publish' ) {
			$attachments = get_posts( array(
				'post_type' => 'attachment',
				'posts_per_page' => 0,
				'post_parent' => $post->ID
			) );
	
			if ( $attachments ) {
				$content .= '<h3>Attachments</h3>';
				$content .= '<ul class="post-attachments">';
				foreach ( $attachments as $attachment ) {
					$class = "post-attachment mime-" . sanitize_title( $attachment->post_mime_type );
					$title = wp_get_attachment_link( $attachment->ID, false );
					$content .= '<li class="' . $class . '">' . $title . '</li>';
				}
				$content .= '</ul>';
			}
		}
	
		return $content;
	}
	
	// * Load and localize javascript to download an HTML file of post content *
	
	function load_download_js() {
		if ( is_single() ) {
			wp_enqueue_script('generatefile', (get_template_directory_uri()) . '/js/jquery.generateFile.js', array('jquery'));
			wp_enqueue_script('downloadscript', (get_template_directory_uri()) . '/js/download-script.js', array('jquery'));
			
			$filepath = (get_template_directory_uri()) . '/inc/download.php';
			wp_localize_script('downloadscript', 'templateDir', array( 'downloadfile' => $filepath,));
		}
	}
	
	add_action('wp_print_scripts', 'load_download_js');

?>