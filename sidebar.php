<div id="sidebar" class="do-not-print">

	<div id="sidebar-nav">
	
		<div class="sidebar-block">
	
			<?php get_search_form(); ?>
			
			<div class="sidebar-button">
				<?php if ( is_user_logged_in() ) { ?>
					<a href="<?php echo site_url(); ?>/wp-admin/post-new.php?post_type=post">+ New Draft&nbsp;</a>
				<?php } ?>
			</div>
		
		</div>
		
		<div class="sidebar-block">
		
			<h2>Archives</h2>
			
				<div class="month-select">
					<select name="archive-dropdown" onChange='document.location.href=this.options[this.selectedIndex].value;'>
					<option value=""><?php echo esc_attr(__('Select Month')); ?></option>
					<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
				</div>
			
			<h2>Content Channels</h2>
				<?php echo '<ul>';
					$args_list = array(
						'taxonomy' => 'content_channel', // Registered tax name
						'show_count' => false,
						'depth' => '1',
						'title_li' => '',
						'echo' => '0',
					);	 
					echo wp_list_categories($args_list);
					echo '</ul>';
				?>
		
		</div>
		
		<div id="menutop">
			<?php if ( is_user_logged_in() ) { ?>
				<div class="sidebar-button"><a href="<?php echo wp_logout_url(); ?>">Log Out</a></div>
				<?php } else { ?>
				<div class="sidebar-button"><a href="<?php echo site_url(); ?>/wp-admin/">Log in</a></div>
			<?php } ?>
		</div>
				
	</div>

</div>