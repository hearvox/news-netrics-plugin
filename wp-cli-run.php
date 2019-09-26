<?php
/*

Run code via WP-CLI

*/

////////////////////////////////////////////
// Monthly: Run PSI test for articles.
$month_feed = 6290; // '201909'.
$month_done = 6291; // '1909done'.
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

/*

// Flags (taxonomy)
$month_feed = 6290; // '201909';
$month_done  = 6291; // '1909done';

////////////////////////////////////////////
// Monthly: Get latest articles from feeds.
$month_feed = XXXX; // '2019XX'.
$flags = array( $month_feed, 6201, 6175, 6172 ); // Month with 'manual', 'none', 'json'.
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
// netrics_save_feed_items( $post_id, $items, $meta_key = 'nn_articles_201908', $term_id = 6284  )

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
    // 'post__in'       => array( 4209, 4273, 4795, 5076 ),
    // 'post__in'       => array( 4045, 4308, 4339, 4392 , 4397, 4521, 4630, 5057, 5083, 5186 ),
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
netrics_api_call_pagespeed( $query_ids );

