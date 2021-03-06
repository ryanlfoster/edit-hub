<?php get_header(); 

/*

Template Name: Event Listing

*/

?>

	<div id="main-container">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
	
				<h2><?php the_title(); ?></h2>
	
				<div class="entry">
	
					<?php echo do_shortcode("[ehub-events-full limit='25']"); ?>
	
					<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
					
				</div>
	
			</div>
	
			<?php endwhile; endif; ?>

	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>