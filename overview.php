<?php get_header(); 

/*

Template Name: Edit Hub Home

*/

?>

	<div id="main-container">
	
		<h2>Content &amp; Calendar Overview</h2>
		
			<div class="overview-block">

				<h3>Recently Posted<img src="<?php echo get_template_directory_uri(); ?>/images/recent-post.jpg" alt="Recent" style="margin-left: 5px;" /></h3>
				
				<ul id="recently-posted">
					<?php
						global $post;
						$args = array( 'numberposts' => 5 );
						$myposts = get_posts( $args );
						foreach( $myposts as $post ) :	setup_postdata($post);
					?>
						
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><span class="recent-meta"><img src="<?php echo get_template_directory_uri(); ?>/images/grey-bullet.png" alt="" style="margin-right: 5px;" />Completed by <?php the_author_meta('first_name') ?>&nbsp;<?php the_author_meta('last_name') ?>  on <?php the_time('F j, Y') ?></span></li>
					
					<?php endforeach; ?>
					
				</ul>
			
			</div>
			
			<div class="overview-block">
		
				<h3>In Development<img src="<?php echo get_template_directory_uri(); ?>/images/in-dev.jpg" alt="Draft" style="margin-left: 8px;" /></h3>
			
					<?php query_posts('posts_per_page=3&post_status=draft&order=ASC'); ?>
						
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<p><?php the_title(); ?>&nbsp;<span class="read-more"><?php edit_post_link('&#8594; Continue editing'); ?></span></p>
					
					<?php endwhile;	else: ?>
					
						<p>No content currently in draft status.</p>
					
					<?php endif; ?>
					
					<?php wp_reset_query(); ?>

			</div>
		
		<h3>Upcoming Events &amp; Deadlines<img src="<?php echo get_template_directory_uri(); ?>/images/calendar.jpg" alt="Calendar" style="margin-left: 8px;" /></h3>
		
		<?php echo do_shortcode("[ehub-events-brief limit='5']"); ?>
	
	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>