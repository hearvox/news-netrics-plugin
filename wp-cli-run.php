<?php
/*
Run code via WP-CLI
*/

netrics_add_month_psi_avgs_all();

/*

// Flags (taxonomy)
$month_feed = 6290; // '201909';
$month_done  = 6291; // '1909done';
////////////////////////////////////////////
// Monthly: Get latest articles from feeds:
// 1. Delete month's data ('nn_articles_new' post meta; 'articles' and 'psi' flags)
// 2. Get articles from JSON feeds (set 'articles' flag).
// 3. Get articles from  XML feeds (set 'articles' flag).

netrics_clear_month_data();

// delete_post_meta( $post_id, 'nn_articles_new' );
// wp_remove_object_terms( $post_id, array( 1234, 5678 ), 'flag' ); // Monthly flags: feed and PSI.

/////////////////////
// Get articles from JSON posts.
$json = array( 4045, 4308, 4339, 4392, 4521, 5057, 5083 );
foreach ( $json as $post_id ) {
    $items = netrics_parse_json_items( $post_id );
    print_r( $items );
    $run = netrics_save_feed_items( $post_id, $items  );
    print_r( $run );
}

/////////////////////
// Get articles from RSS posts.
$month_feed = XXXX; // '2019XX'.
$flags = array( $month_feed, 6201, 6175 ); // Month with 'manual', 'none'.
$flags = array( $month_feed, 6175 ); // Month with 'none', 'json'.

$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 2000,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => $flags,
            'operator' => 'NOT IN',
        ),
    ),
);
$query_ids = new WP_Query( $args );
print_r( $query_ids->posts );
$done = netrics_get_feeds( $query_ids );
print_r( $done );

// Edit term and month:
// netrics_save_feed_items( $post_id, $items, $meta_key = 'nn_articles_201908', $term_id = 6284  );


////////////////////////////////////////////
// Monthly: Run PSI test for articles.
$flags      = array( 6286, 6175, 6172 ), // '1908done' = 6286, 'none', 'json'
$month_feed = XXXX; // '2019XX'.
$month_done = XXXX; // '19XXdone'.

$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 200,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query'      => array(
        'relation'  => 'AND',
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => $month_feed,
        ),
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => $month_done,
            'operator' => 'NOT IN',
        ),
    ),
);
$query_ids = new WP_Query( $args );
print_r( $query_ids->posts );
$done = netrics_api_call_pagespeed( $query_ids );
print_r( $done );

// Misc.
$query_ids = netrics_get_pubs_ids();
foreach( $query_ids->posts as $post_id ) {
    print_r( get_post_meta( $post_id, 'nn_articles_new', true) );
}
// 'post__in'       => array( 4209, 4273, 4795, 5076 ),

// results.php
netrics_add_month_psi();
netrics_add_month_psi_avgs();
netrics_add_month_psi_avgs_all();
*/



