<?php 
    function universityLikeRoutes() {
        register_rest_route( 'uni/v1', 'manageLike', array(
            'methods' => 'POST',
            'callback' => 'createLike'
        ) );

        register_rest_route( 'uni/v1', 'manageLike', array(
            'methods' => 'DELETE',
            'callback' => 'deleteLike'
        ) );
    }
    add_action( 'rest_api_init', 'universityLikeRoutes' );

    function createLike($data) {
        if ( is_user_logged_in() ) {
            $professor = sanitize_text_field( $data['professorID'] );
            
            $existQuery = new WP_Query( array(
                'post_type' => 'like',
                'author' => get_current_user_id(),
                'meta_query' => array(
                    array(
                        'key' => 'liked_professor_id',
                        'compare' => '=',
                        'value' => $professor
                    )
                )
            ) );

            if ($existQuery->found_posts === 0 && get_post_type( $professor ) == 'professor') {
                return wp_insert_post( array(
                    'post_type' => 'like',  
                    'post_status' => 'publish',
                    'post_title' => '2nd php test',
                    'author' => get_current_user_id(),
                    'meta_input' => array(
                        'liked_professor_id' => $professor
                    )
                ) );
            } else {
                die("Invalid professor id. You can only like a professor once.");
            }
        } else {
            die("Only logged in users can like.");
        }
    }

    function deleteLike($data) {
        $likeID = sanitize_text_field( $data['like'] );
        if ( get_current_user_id() == get_post_field( 'post_author', $likeID) && get_post_type($likeID) == 'like' ) {
            wp_delete_post($likeID, true);
            return 'Congrats, like deleted.';
        } else {
            die("You don't have permission to delete that.");
        }
    }
?>