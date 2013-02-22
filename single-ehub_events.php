<?php get_header(); ?>

	<div id="main-container">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				
				<div class="export-area">
				
					<h2><?php the_title(); ?></h2>
					
					<div class="meta">
						<img src="<?php echo get_template_directory_uri(); ?>/images/calendar-small.jpg" alt="calendar" class="do-not-print" style="vertical-align: text-bottom;" />
						<?php echo do_shortcode("[event-date-time]"); ?>
					</div>
					
					<div class="entry">
						
						<?php the_content(); ?>
						
						<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
						
						<div class="postmetadata">
						
							<p><?php $eventcat = get_the_term_list( $post->ID, 'ehub_eventcategory', 'Event Category: ', ', ', '' );
								//- check if there are any categories -
								if ($eventcat) { ?>
								<?php echo $eventcat; ?>
								<?php } // - no categories assigned -
								else { ?>
								Event Category: <span class="sans-serif">Not Assigned</span>
							<?php } ?></p>
						
						</div>
							
					</div>
				
				</div>
				
				<div id="content-actions">
					<div class="action"><?php if (is_user_logged_in() ) { ?><img src="<?php echo get_template_directory_uri(); ?>/images/pencil.jpg" alt="Edit" /><?php } ?><?php edit_post_link('Edit Content'); ?></div>
					<div class="action"><img src="<?php echo get_template_directory_uri(); ?>/images/print.jpg" alt="Print" /><a href="javascript:window.print()">Print</a></div>
					<div class="action"><img src="<?php echo get_template_directory_uri(); ?>/images/email.jpg" alt="Email" /><?php echo direct_email('Email') ?></div>
					<div class="action"><img src="<?php echo get_template_directory_uri(); ?>/images/text-export.jpg" alt="Text" /><a href="#" id="download-text">Export Text</a></div>
					<div class="action"><img src="<?php echo get_template_directory_uri(); ?>/images/html-export.jpg" alt="HTML" /><a href="#" id="download-html">Export HTML</a></div>
				</div>
					
			</div>
	
		<?php endwhile; endif; ?>

	</div><!-- end main-container -->
	
<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>