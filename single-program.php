<?php get_header() ?>

<?php while (have_posts()) : 
		the_post(); 
		pageBanner();
?>

	<div class="container container--narrow page-section">
		<div class="metabox metabox--position-up metabox--with-home-link">
			<p>
				<a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program');?>">
					<i class="fa fa-home" aria-hidden="true">
					</i> All Programs
				</a>
				<span class="metabox__main"><?php echo the_title(); ?></span>
			</p>
		</div>
		
		<div class="generic-content">
			<?php the_field('program_body_content'); //Used custom field instead of the_content() to issues in live search ?>
		</div>

		<!-- Program's Professors -->
		<?php 
			$professorsQuery = new WP_Query( array(
				'post_type' => 'professor',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'related_programs',
						'compare' => 'LIKE',
						'value' => '"' . get_the_ID(). '"'
					)
				)
			) );

			if ( $professorsQuery->have_posts() ) :

		?>
		<hr class="section-break">
		<h2 class="headline headline--medium"><?php the_title(); ?> Professor(s)</h2>
		
		<ul class="professor-cards">
		<?php while ( $professorsQuery->have_posts() ) : $professorsQuery->the_post(); ?>
			<li class="professor-card__list-item">
				<a class="professor-card"  href="<?php the_permalink(); ?>">
					<img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
					<span class="professor-card__name"><?php the_title(); ?></span>	
				</a>
			</li>
		<?php endwhile; wp_reset_postdata(); ?>	
		</ul>

		<?php endif; ?>

		<!-- Upcoming events -->
		<?php 
			$today = date('Ymd');
			$eventsQuery = new WP_Query(array(
				'posts_per_page' => 2,
				'post_type' => 'event',
				'meta_key' => 'event_date',
				'orderby' => 'meta_value_num',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'event_date',
						'compare' => '>=',
						'value' => $today,
						'type' => 'numeric'
					),
					array(
						'key' => 'related_programs',
						'compare' => 'LIKE',
						'value' => '"' . get_the_ID() . '"' 
					)
				)
			));

			if ( $eventsQuery->have_posts() ) :

		?>

		<hr class="section-break">
		<h2 class="headline headline--medium">Upcoming <?php the_title(); ?> Events</h2>

		<?php 
			while( $eventsQuery->have_posts() ) : 
			
			$eventsQuery->the_post(); 
			
			get_template_part( 'template-parts/content', 'event' );
		?>

		<?php endwhile; endif; wp_reset_postdata(); ?>
		
		<?php 
			$relatedCampuses = get_field('related_campus');
			if ( $relatedCampuses ) :
		?>
		<hr class="section-break">
		<h2 class="headline headline--medium"><?php the_title(); ?> is available at these campuses:</h2>

		<ul class="link-list min-list">
		<?php foreach ( $relatedCampuses as $campus ) : ?>
			<li><a href="<?php echo get_the_permalink( $campus ); ?>"><?php echo get_the_title( $campus ); ?></a></li>
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>

	</div>
	<!-- container -->
<?php endwhile; ?>

<?php get_footer() ?>