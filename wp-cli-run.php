<?php
/*

Run code via WP-CLI

*/

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
            'taxonomy' => 'cms',
            'field'    => 'term_id',
            'terms'    => 5815, // 'Escenic'
        ),
    ),
    /*
    'tax_query'      => array(
        'relation'  => 'AND',
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => 6284, // '201908'
        ),
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => array( 6286, 6175, 6172 ), // '1908done' = 6286, 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),
    */
);
$query_ids = new WP_Query( $args );
print_r( $query_ids->posts );

foreach ( $query_ids->posts as $post_id ) {
    // print_r( get_post_meta( $post_id, 'nn_articles_201906', true ) );
}

// $done = netrics_get_feeds( $query_ids, 10 );
// print_r( $done );
// $results = netrics_api_call_pagespeed( $query_ids );
// print_r( $results );
