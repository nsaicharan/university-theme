<?php 
    function university_register_search() {
        register_rest_route( 'unirest/v1', 'search', array(
            'methods' => WP_REST_SERVER::READABLE,
            'callback' => 'university_search_results'
        ) );
    }
    add_action( 'rest_api_init', 'university_register_search' );

    function university_search_results($data) {

        $search_query = new WP_Query( array(
            'post_type' => array( 'post', 'page', 'event', 'program', 'professor', 'campus' ),
            's' => sanitize_text_field( $data['term'] )
        ) );
        
        $search_results = array(
            'generalInfo' => array(),
            'events' => array(),
            'programs' => array(),
            'professors' => array(),
            'campuses' => array()
        );
        
        while ($search_query->have_posts()) {
            $search_query->the_post();

            switch (get_post_type()) {
                case 'event':
                    $eventDate = new DateTime( get_field('event_date') );
                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 );
                    array_push($search_results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'date' => $eventDate->format('d'),
                        'month' => $eventDate->format('M'),
                        'excerpt' => $excerpt
                    ));
                    break;
                case 'program':
                    array_push($search_results['programs'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'id' => get_the_id()
                    ));

                    $relatedCampuses = get_field('related_campus');
                    if ($relatedCampuses) {
                        foreach($relatedCampuses as $campus) {
                            array_push($search_results['campuses'], array(
                                'title' => get_the_title($campus),
                                'permalink' => get_the_permalink($campus)
                            ));
                        }
                    }
                    break;
                case 'professor':
                    array_push($search_results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                    break;
                case 'campus':
                    array_push($search_results['campuses'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
                default: // Posts and Pages
                    array_push($search_results['generalInfo'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'postType' => get_post_type(),
                        'authorName' => get_the_author()
                    ));
                    break;
            }
        }

        if ( $search_results['programs'] ) {
            $programsMetaQuery = array( 'relation' => 'OR' );

            foreach ($search_results['programs'] as $program) {
                array_push($programsMetaQuery, array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $program['id'] . '"'
                ));
            }

            $programsRelationshipQuery = new WP_Query(
                array(
                    'post_type' => array( 'professor', 'event' ),
                    'meta_query' => $programsMetaQuery
                )
            );
 
            while( $programsRelationshipQuery->have_posts() ) {
                $programsRelationshipQuery->the_post();
                
                if ( get_post_type() == 'professor' ) {
                    array_push($search_results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                }

                if ( get_post_type() == 'event' ) {
                    $eventDate = new DateTime( get_field('event_date') );
                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 );
                    
                    array_push($search_results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'date' => $eventDate->format('d'),
                        'month' => $eventDate->format('M'),
                        'excerpt' => $excerpt
                    ));
                }
            }

            $search_results['professors'] = array_values( array_unique($search_results['professors'], SORT_REGULAR) );

            $search_results['events'] = array_values( array_unique($search_results['events'], SORT_REGULAR) );
        }

        return $search_results;
    }
?>