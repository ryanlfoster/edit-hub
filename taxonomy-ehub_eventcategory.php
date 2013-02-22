<?php get_header(); ?>

	<div id="main-container">

			<?php if (have_posts()) : ?>
	
				<?php /* If this is a custom taxonomy */ if ((is_tax()) && $wp_query->get_queried_object()) { ?>
					<h3 class="archive">Archive for &#8220;<?php $term = get_term_by('slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); echo $term->name; ?>&#8221; &rarr;</h3>
				
				<?php } ?>
	
				<?php while (have_posts()) : the_post(); ?>
				
					<div <?php post_class() ?>>
					
						<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
						
						<div class="meta">
							<img src="<?php echo get_template_directory_uri(); ?>/images/calendar-small.jpg" alt="calendar" class="do-not-print" style="vertical-align: text-bottom;" />
							<?php echo do_shortcode("[event-date-time]"); ?>
						</div>
						
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
	
				<?php endwhile; ?>
	
				<?php get_template_part('nav'); ?>
				
			<?php else : ?>
		
				<h2>Nothing found</h2>
		
			<?php endif; ?>
			
	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>
