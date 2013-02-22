<?php get_header(); ?>

	<div id="main-container">

			<?php if (have_posts()) : ?>
	
				<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	
				<?php /* If this is a custom taxonomy */ if ((is_tax()) && $wp_query->get_queried_object()) { ?>
					<h3 class="archive">Archive for &#8220;<?php $term = get_term_by('slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); echo $term->name; ?>&#8221; &rarr;</h3>
	
				<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
					<h3 class="archive">Archive for <?php the_time('F jS, Y'); ?> &rarr;</h3>
	
				<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
					<h3 class="archive">Archive for <?php the_time('F Y'); ?> &rarr;</h3>
	
				<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
					<h3 class="archive">Archive for &rarr;<?php the_time('Y'); ?></h3>
	
				<?php /* If this is an author archive */ } elseif (is_author()) { ?>
					<h3 class="archive">Author Archive &rarr;</h3>
	
				<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
					<h3 class="archive">Content Archives &rarr;</h3>
				
				<?php } ?>
	
				<?php while (have_posts()) : the_post(); ?>
				
					<div <?php post_class() ?>>
					
						<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
						
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
		
				<h2>Nothing found</h2>
		
			<?php endif; ?>
			
	</div><!-- end main-container -->

<?php get_sidebar(); ?>

<div style="clear: both;"></div>

<?php get_footer(); ?>
