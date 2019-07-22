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
/**
 * Get array of post meta keys (with registration values).
 *
 * Test API:
 * https://news.pubmedia.us/wp-json/wp/v2/publication
 *
 * @return array $pub_meta Array of post meta keys.
 */
function netrics_get_pub_meta() {
    $pub_meta = array(
        'nn_pub_name'      => array( 'News outlet name', 'string', 'sanitize_text_field' ),
        'nn_pub_site'      => array( 'News outlet domain name', 'string', 'sanitize_text_field' ),
        'nn_pub_url'       => array( 'News outlet URL', 'string', 'sanitize_text_field' ),
        'nn_circ'          => array( 'Circulation', 'number', 'absint' ),
        'nn_rank'          => array( 'AWIS Global Rank', 'number', 'absint' ),
        'nn_pub_year'      => array( 'Year founded', 'number', 'absint' ),
        // 'nn_pub_rss'       => array( 'RSS Feed URL', 'string', 'sanitize_text_field' ),
        // 'nn_site'          => array( 'Site data from AWIS, BuitWith', 'string', 'wp_json_encode' ),
    );

    return $pub_meta;
}

/**
 * Register term meta for taxonomies.
 *
 * Test API:
 * https://news.pubmedia.us/wp-json/wp/v2/posts
 *
 * @return void
 */
function newsstats_reg_pub_meta() {
    $pub_meta = netrics_get_pub_meta();

    foreach ( $pub_meta as $key => $value ) {
        $args = array(
            'description'       => $value[0],
            'type'              => $value[1], // string|boolean|integer|number
            'sanitize_callback' => $value[2],
            'single'            => true,
            'show_in_rest'      => true,
        );
        register_meta( 'post', sanitize_key( $key ), $args );
    }
}
add_action( 'init', 'newsstats_reg_pub_meta', 0 );


