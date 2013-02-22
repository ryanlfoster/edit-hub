<?php get_header(); ?>

	<div id="main-container">

		<?php if (have_posts()) : ?>
	
			<h3 class="archive"><?php printf( __( 'Search Results for &#8220;%s&#8221; &rarr;' ), '<span>' . get_search_query() . '</span>'); ?></h3>
	
			<?php while (have_posts()) : the_post(); ?>
	
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	
					<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
	
					<?php get_template_part('meta'); ?>
	
					<div class="entry">
						<?php the_excerpt(); ?>
					</div>

				</div>
	
			<?php endwhile; ?>
	
			<?php get_template_part('nav'); ?>
	
		<?php else : ?>
	
			<h2>No posts found.</h2>
	
		<?php endif; ?>
		
		</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>
