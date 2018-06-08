<?php get_header() ?>

<?php while (have_posts()) : 
		the_post(); 
		pageBanner();
?>

	<div class="container container--narrow page-section">
		<div class="metabox metabox--position-up metabox--with-home-link">
			<p>
				<a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus');?>">
					<i class="fa fa-home" aria-hidden="true">
					</i> All Campuses
				</a>
				<span class="metabox__main"><?php echo the_title(); ?></span>
			</p>
		</div>
		
		<div class="generic-content">
			<?php 
			the_content(); 
			$mapLocation = get_field('map_location');
			?>

			<div class="acf-map">
				<div class="marker" data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
					<?php echo $mapLocation['address']; ?>
				</div>
			</div>

		</div>
		<!-- End Generic Content -->

		<?php 
			$programsQuery = new WP_Query(array(
	          'posts_per_page' => -1,
	          'post_type' => 'program',
	          'orderby' => 'title',
	          'order' => 'ASC',
	          'meta_query' => array(
	            array(
	              'key' => 'related_campus',
	              'compare' => 'LIKE',
	              'value' => '"' . get_the_ID() . '"'
	            )
	          )
	        ));

			if ( $programsQuery->have_posts() ) :
		?>

		<hr class="section-break">

		<h2 class="headline headline--medium">Programs Available At This Campus:</h2>

		<?php while ( $programsQuery->have_posts() ) : $programsQuery->the_post(); ?>
		
		<ul class="link-list min-list">
			<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
		</ul>

		<?php endwhile; endif; ?>

	</div>
	<!-- End container -->
<?php endwhile; ?>

<?php get_footer() ?>