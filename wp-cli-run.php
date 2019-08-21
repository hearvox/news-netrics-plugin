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
    // 'post__in'       => array( 5186 ),
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
);
$query_ids = new WP_Query( $args );
print_r( $query_ids->posts );
$results = netrics_api_call_pagespeed( $query_ids );
print_r( $results );
