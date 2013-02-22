<?php get_header(); 

/*

Template Name: Event Calendar

*/

?>

	<div id="main-container">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
	
				<div class="entry">
	
					<div id="calendar"></div>
	
				</div>
	
			</div>
	
			<?php endwhile; endif; ?>

	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>