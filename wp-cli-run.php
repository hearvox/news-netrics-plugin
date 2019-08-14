<?php
/*

Run code via WP-CLI

*/

$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 500,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => array( 6284, 6175, 6172 ), // '201908', 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),

);
$query = new WP_Query( $args );

netrics_get_feeds( $query );
