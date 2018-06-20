<?php 
    function university_search_results($data) {

        $search_query = new WP_Query( array(
            'post_type' => array( 'post', 'page', 'event', 'program', 'professor', 'campus' ),
            's' => sanitize_text_field( $data['term'] )
        ) );
        
        $search_results = array(
            'general_info' => array(),
            'events' => array(),
            'programs' => array(),
            'professors' => array(),
            'campuses' => array()
        );
        
        while ($search_query->have_posts()) {
            $search_query->the_post();

            switch (get_post_type()) {
                case 'event':
                    array_push($search_results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
                case 'program':
                    array_push($search_results['programs'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
                case 'professor':
                    array_push($search_results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
                case 'campus':
                    array_push($search_results['campuses'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
                default:
                    array_push($search_results['general_info'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink()
                    ));
                    break;
            }
        }

        return $search_results;
    }

    function university_register_search() {
        register_rest_route( 'unirest/v1', 'search', array(
            'methods' => WP_REST_SERVER::READABLE,
            'callback' => 'university_search_results'
        ) );
    }

    add_action( 'rest_api_init', 'university_register_search' );
?>