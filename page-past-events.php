<?php 

  get_header(); 
  
  pageBanner( array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of your past events.'
  ) );

?>

<div class="container container--narrow page-section">
  <?php 

    $today = date('Ymd');

    $pastEventsQuery = new WP_Query ( array(
      'paged' => get_query_var('paged', 1),
      'post_type' => 'event',
      'meta_key' => 'event_date',
      'orderby' => 'meta_value_num',
      'order' => 'ASC',
      'meta_query' => array(
        'key' => 'event_date',
        'compare' => '<',
        'value' => $today,
        'type' => 'numeric'
      )
    ) );

    while ( $pastEventsQuery->have_posts() ) : 
      $pastEventsQuery->the_post(); 
      
      $eventDate = new DateTime(get_field('event_date'));
  ?>
      
    <div class="event-summary">
      <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
       <span class="event-summary__month"><?php echo $eventDate->format('M'); ?></span>
       <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>  
      </a>
      <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <p><?php echo wp_trim_words( get_the_content(), 18 );?>
        <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
      </div>
    </div>

    <?php 
      echo paginate_links(array(
        'total' => $pastEventsQuery->max_num_pages
      )); 
    ?>

  <?php endwhile; ?>

  <hr class="section-break">

  <p><a href="<?php echo site_url('/events'); ?>"><i class="fa fa-angle-double-left"></i> Back to upcoming events</a> </p>
</div>

<?php get_footer(); ?>