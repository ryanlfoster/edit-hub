<?php get_header(); ?>

	<div id="main-container">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	
				<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
	
				<?php get_template_part('meta'); ?>
	
				<div class="entry">
					<?php the_excerpt(); ?>
				</div>

				<?php
				if(taxonomy_exists('content_channel')) { ?>
					<div class="postmetadata">
						<p><?php echo get_the_term_list( get_the_ID(), 'content_channel', 'Posted to: ', ' ', '' ) ?></p>
					</div><?php
				}
				?>
	
			</div>
	
		<?php endwhile; ?>
	
		<?php get_template_part('nav'); ?>
	
		<?php else : ?>
	
			<h2>Not Found</h2>
	
		<?php endif; ?>
		
	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>
