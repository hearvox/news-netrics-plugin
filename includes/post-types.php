<?php
/**
 * Post meta and custom post types.
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/*******************************
 =CUSTOM POST TYPES
 ******************************/
/**
 * Register custom post types.
 *
 * @see get_post_type_labels() for label keys.
 */
function newsstats_custom_post_types() {
    $labels = array(
        'name'               => _x( 'Publications', 'newsnetrics' ),
        'singular_name'      => _x( 'Publication', 'newsnetrics' ),
        'add_new'            => __( 'Add Publication', 'newsnetrics' ),
        'add_new_item'       => __( 'Add New Publication', 'newsnetrics' ),
        'new_item'           => __( 'New Publication', 'newsnetrics' ),
        'edit_item'          => __( 'Edit Publication', 'newsnetrics' ),
        'view_item'          => __( 'View Publication', 'newsnetrics' ),
        'view_items'         => __( 'View Publication', 'newsnetrics' ),
        'search_items'       => __( 'Search Publication', 'newsnetrics' ),
        'not_found'          => __( 'No Publication found.', 'newsnetrics' ),
        'not_found_in_trash' => __( 'No Publication found in Trash.', 'newsnetrics' ),
        'parent_item_colon'  => __( 'Parent Publication"', 'newsnetrics' ),
        'all_items'          => __( 'All Publications', 'newsnetrics' ),
        'archives'           => __( 'Publication Archives', 'newsnetrics' ),
        'attributes'         => __( 'Publication Attributes', 'newsnetrics' ),
        'filter_items_list'  => _x( 'Filter Publication', 'newsnetrics' ),
    );

    $args = array(
        'label'        => 'Publications',
        'labels'       => $labels,
        'description'  => 'News outlet websites.',
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-media-document',
        'supports'     => array( 'title', 'editor', 'revisions', 'excerpt', 'thumbnail', 'custom-fields' ),
        'taxonomies'   => array( 'cms', 'region', 'owner', 'server', 'post_tag' ),
        'has_archive'  => true,
    );

    register_post_type( 'publication', $args );
}
add_action( 'init', 'newsstats_custom_post_types' );

 /**
 * Display CPT posts on front page
 *
 */
function newsstats_home_cpt_posts( $query ) {

    if( $query->is_main_query() && $query->is_home() ) {
        $query->set( 'post_type', array( 'publication') );
    }
}
// add_action( 'pre_get_posts', 'newsstats_home_cpt_posts' );


/*******************************
 =POST META
 ******************************/

