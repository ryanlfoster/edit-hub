<?php get_header(); 

/*

Template Name: Full Archive

*/

?>

	<div id="main-container">

		<h2>Content Archives</h2>

		<div id="archives-wrapper">
	 
	 		<div class="archive-type-block">
	 
				<h3>Monthly Archives</h3>
				
					<div class="month-select">
						<select name="archive-dropdown" onChange='document.location.href=this.options[this.selectedIndex].value;'>
						<option value=""><?php echo esc_attr(__('Select Month')); ?></option>
						<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
					</div>
					
			</div>
			
			<div class="archive-type-block">
								
				<h3>Archives by Content Channel</h3>
					<?php echo '<ul>';
						$args_list = array(
							'taxonomy' => 'content_channel', // Registered tax name
							'show_count' => true,
							'hierarchical' => true,
							'title_li' => '',
							'echo' => '0',
						);	 
						echo wp_list_categories($args_list);
						echo '</ul>';
					?>
					
			</div>
		 
		 	<div class="archive-type-block">
		 
				<h3>Archives by Author</h3>
					<ul>
						<?php wp_list_authors('exclude_admin=0&optioncount=1&show_fullname=1&hide_empty=1'); ?>
					</ul>
			
			</div>
			
			<div class="archive-type-block">
		 	
				<h3>Archives by Audience</h3>
					<?php echo '<ul>';
						$args_list = array(
							'taxonomy' => 'audience', // Registered tax name
							'show_count' => true,
							'hierarchical' => true,
							'title_li' => '',
							'echo' => '0',
						);	 
						echo wp_list_categories($args_list);
						echo '</ul>';
					?>

			</div>
			
			<div class="archive-type-block">
				
				<h3>Search EDIT HUB</h3>
					<div id="in-archive-search">
						<?php get_search_form(); ?>
					</div>	 
		 	</div>
			
			<div class="archive-type-block" id="ie-last-fix">
			
				<h3>Keyword Cloud</h3>
				 	
			 	<?php wp_tag_cloud('number=0&smallest=1&largest=1&format=list&unit=em&taxonomy=keyword'); ?>
		 	
		 	</div>
		 
		</div>
	
	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>