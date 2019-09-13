<?php
/*

Run code via WP-CLI

*/

$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 2000,
    'offset'         => 0,
    'fields'         => 'ids',
    // 'post__in'       => array( 4029 ),
);
$query = new WP_Query( $args );

foreach( $query->posts as $post_id ) {
    $articles_all = get_post_meta( $post_id, 'nn_articles', true );
    $articles_add = get_post_meta( $post_id, 'nn_articles_2908', true );

    $articles_all['2019-08'] = $articles_add;

    update_post_meta( $post_id, 'nn_articles', $articles_all );
}

/*

// Flags (taxonomy)
$month_feed = 6290; // '201909';
$month_psi  = 6291; // '1909done';


////////////////////////////////////////////
// Monthly: Get latest articles from feeds.
$month_feed = XXXX; // '2019XX'.
$flags = array( $month_feed, 6201, 6175, 6172 ); // Month with 'manual', 'none', 'json'.
$secs  = 30;
$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 2000,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => $flags,
            'operator' => 'NOT IN',
        ),
    ),

);
$query = new WP_Query( $args );
print_r( $query->posts );
$done = netrics_get_feeds( $query, $secs );
print_r( $done );

// Edit term and month:
// netrics_save_feed_items( $post_id, $items, $meta_key = 'nn_articles_201908', $term_id = 6284  )

////////////////////////////////////////////
// Monthly: Run PSI test for articles.
$month_psi = XXXX; // '2019XX'.
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
            'terms'    => $month_psi, // '2019XX'
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

foreach ( $query_ids->posts as $post_id ) {
    // print_r( get_post_meta( $post_id, 'nn_articles_201906', true ) );
}



//////// Escenic CMS only.
    'tax_query'      => array(
        'relation'  => 'AND',
        array(
            'taxonomy' => 'cms',
            'field'    => 'term_id',
            'terms'    => 5815, // 'Escenic'
        ),
    ),
 */
