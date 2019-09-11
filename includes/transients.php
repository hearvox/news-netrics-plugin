<?php
/**
 * Set and get transients.
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/*******************************
 =DATA (transients for Publications)
 ******************************/
/**
 * Get data for all CPT posts (Publication), with meta and tax terms.
 *
 * @todo Fn: for single pub, loop thru.
 * @todo Fn: Reset, delete_transient( string $transient );
 * @since   0.1.0
 *
 * @return array $pub_data Array of data for all CPT posts.
 */
function newsstats_get_all_publications() {

    $newsnetrics_pubs = get_transient( 'newsnetrics_pubs' );

    if ( $newsnetrics_pubs ) {
        return $newsnetrics_pubs;
    }

    $query_args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order' => 'ASC',
        'nopaging'  => true,
        // 'posts_per_page' => 10, // For tests.
        // 'update_post_meta_cache' => false,
        // 'update_post_term_cache' => false,
        // 'fields' => 'ids',
    );
    $query = new WP_Query( $query_args );
    $pub_data = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id   = $query->post->ID;
            $post_meta = get_post_meta( $post_id );

            $term_owner  = get_the_terms( $post_id, 'owner' );
            $pub_owner   = ( $term_owner && isset( $term_owner[0]->name ) ) ? $term_owner[0]->name : 'ERROR:owner';
            $term_city   = get_the_terms( $post_id, 'region' );
            $city        = ( $term_city && isset( $term_city[0]->name ) ) ? $term_city[0]->name : 'ERROR:city';
            $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
                ? get_term_meta( $term_city[0]->term_id ) : false;
            $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
                ? $city_meta['nn_region_pop'][0] : 0;
            $city_latlon = ( $city_meta && isset( $city_meta['nn_region_latlon'][0] ) )
                ? $city_meta['nn_region_latlon'][0] : '';
            $term_county = ( $term_city && isset( $term_city[0]->parent ) )
                ? get_term( $term_city[0]->parent ) : false;
            $county      = ( $term_county && isset( $term_county->name ) ) ? $term_county->name : 'ERROR:county';
            $term_state  = ( $term_county && isset( $term_county->parent ) )
                ? get_term( $term_county->parent ) : false;
            $state       = ( $term_state && isset( $term_state->name ) ) ? $term_state->name : 'ERROR:state';

            // newsstats_check_val(
            // $city_meta   = ( $term_city ) ? get_term_meta( $term_city[0]->term_id ) : 'no city';
            // $pub_city    =
            // $term_county = get_term( $term_city[0]->parent, 'region' );
            // $term_state  = get_term( $term_county->parent, 'region' );


            $pub_data[] = array(
                'pub_id'      => $post_id,
                'pub_title'   => $query->post->post_title,
                'pub_link'    => get_permalink( $post_id ),
                // 'pub_parent'  => $query->post->post_parent,
                'pub_domain'  => $post_meta['nn_pub_site'][0],
                'pub_name'    => $post_meta['nn_pub_name'][0],
                'pub_circ'    => $post_meta['nn_circ'][0],
                'pub_url'     => $post_meta['nn_pub_url'][0],
                'pub_rss'     => $post_meta['nn_pub_rss'][0],
                'pub_year'    => $post_meta['nn_pub_year'][0],
                'pub_owner'   => $pub_owner,
                'city'        => $city,
                'county'      => $county,
                'state'       => $state,
                'city_pop'    => $city_pop,
                'city_latlon' => $city_latlon,
            );
        }
    }  else {
        return false;
    }

    set_transient( 'newsnetrics_pubs', $pub_data, 30 * DAY_IN_SECONDS );

    return $pub_data;

    wp_reset_postdata();
}


/**
 * Get data for all CPT posts (Publication), with meta and tax terms.
 *
 * @todo Fn: for single pub, loop thru.
 * @todo Fn: Reset, delete_transient( string $transient );
 * @since   0.1.0
 *
 * @return array $pub_data Array of data for all CPT posts.
 */
function netrics_get_pub_data() {
    $post_id   = $query->post->ID;
    $post_meta = get_post_meta( $post_id );

    $site_data = maybe_unserialize( $post_meta['nn_site'][0] );
    // nn_articles_201905, nn_articles_201906

    $term_owner  = get_the_terms( $post_id, 'owner' );
    $pub_owner   = ( $term_owner && isset( $term_owner[0]->name ) ) ? $term_owner[0]->name : '';
    $term_city   = get_the_terms( $post_id, 'region' );
    $city        = ( $term_city && isset( $term_city[0]->name ) ) ? $term_city[0]->name : '';
    $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
        ? get_term_meta( $term_city[0]->term_id ) : false;
    $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
        ? $city_meta['nn_region_pop'][0] : 0;
    $city_latlon = ( $city_meta && isset( $city_meta['nn_region_latlon'][0] ) )
        ? $city_meta['nn_region_latlon'][0] : '';
    $term_county = ( $term_city && isset( $term_city[0]->parent ) )
        ? get_term( $term_city[0]->parent ) : false;
    $county      = ( $term_county && isset( $term_county->name ) ) ? $term_county->name : '';
    $term_state  = ( $term_county && isset( $term_county->parent ) )
        ? get_term( $term_county->parent ) : false;
    $state       = ( $term_state && isset( $term_state->name ) ) ? $term_state->name : '';

    // newsstats_check_val(
    // $city_meta   = ( $term_city ) ? get_term_meta( $term_city[0]->term_id ) : 'no city';
    // $pub_city    =
    // $term_county = get_term( $term_city[0]->parent, 'region' );
    // $term_state  = get_term( $term_county->parent, 'region' );


    $pub_data[] = array(
        'pub_id'      => $post_id,
        'pub_title'   => $query->post->post_title,
        'pub_link'    => get_permalink( $post_id ),
        // 'pub_parent'  => $query->post->post_parent,
        'pub_domain'  => $post_meta['nn_pub_site'][0],
        'pub_name'    => $post_meta['nn_pub_name'][0],
        'pub_circ'    => $post_meta['nn_pub_circ_ep'][0],
        'pub_url'     => $post_meta['nn_pub_url'][0],
        'pub_rss'     => $post_meta['nn_pub_rss'][0],
        'pub_year'    => $post_meta['nn_pub_year'][0],
        'pub_owner'   => $pub_owner,
        'city'        => $city,
        'county'      => $county,
        'state'       => $state,
        'city_pop'    => $city_pop,
        'city_latlon' => $city_latlon,
    );

    return $pub_data;

}


/*


Array
(
    [alexa] => Array
        (
            [rank] => 74977
            [title] => Albuquerque Journal - ABQJournal
            [desc] => Provides New Mexico news and sports.
            [since] => 03-Feb-1996
            [links] => 2273
            [speed] => 2867
            [speed_pc] => 26
            [date] => 2019-06
        )

    [builtwith] => Array
        (
            [javascript] => 25
            [analytics] => 39
            [widgets] => 20
            [hosting] => 4
            [cms] => 2
            [Web Server] => 8
            [framework] => 6
            [cdn] => 7
            [media] => 4
            [ssl] => 3
            [ads] => 155
            [Server] => 1
            [CDN] => 1
            [feeds] => 2
            [payment] => 2
            [Web Master] => 2
            [css] => 2
            [mx] => 4
            [mobile] => 6
            [mapping] => 3
            [shop] => 1
            [link] => 3
            [copyright] => 1
            [date] => 2019-06
            [error] => 0
        )

)

Array
(
    [0] => Array
        (
            [pub_id] => 4027
            [pub_title] => aberdeennews.com
            [pub_link] => https://news.pubmedia.us/publication/aberdeennews-com/
            [pub_domain] => aberdeennews.com
            [pub_name] => Aberdeen American News
            [pub_circ] => 11485
            [pub_url] => https://www.aberdeennews.com/
            [pub_rss] => http://www.aberdeennews.com/search/?f=rss&t=article&l=10
            [pub_year] => 1885
            [pub_owner] => GateHouse Media
            [city] => Aberdeen
            [county] => Brown County
            [state] => SD
            [city_pop] => 28264
            [city_latlon] => 45.4646|-98.4680
        )
}


*/

/**
 * Get data for a CPT post (Publication), with meta and tax terms.
 *
 * @todo Fn: Reset, delete_transient( string $transient );
 * @since   0.1.0
 *
 * @param  int   $post_id    Post ID.
 * @return array $post_data  Array of data for a CPT-post.
 */
function newsstats_get_pub( $post_id ) {
    if ( get_post_type( $post_id ) == 'publication' ) {
            $post_meta = get_post_meta( $post_id );

            $term_owner  = get_the_terms( $post_id, 'owner' );
            $pub_owner   = ( $term_owner && isset( $term_owner[0]->name ) ) ? $term_owner[0]->name : 'ERROR:owner';
            $term_city   = get_the_terms( $post_id, 'region' );
            $city        = ( $term_city && isset( $term_city[0]->name ) ) ? $term_city[0]->name : 'ERROR:city';
            $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
                ? get_term_meta( $term_city[0]->term_id ) : false;
            $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
                ? $city_meta['nn_region_pop'][0] : 0;
            $city_latlon = ( $city_meta && isset( $city_meta['nn_region_latlon'][0] ) )
                ? $city_meta['nn_region_latlon'][0] : '';
            $term_county = ( $term_city && isset( $term_city[0]->parent ) )
                ? get_term( $term_city[0]->parent ) : false;
            $county      = ( $term_county && isset( $term_county->name ) ) ? $term_county->name : 'ERROR:county';
            $term_state  = ( $term_county && isset( $term_county->parent ) )
                ? get_term( $term_county->parent ) : false;
            $state       = ( $term_state && isset( $term_state->name ) ) ? $term_state->name : 'ERROR:state';

            // newsstats_check_val(
            // $city_meta   = ( $term_city ) ? get_term_meta( $term_city[0]->term_id ) : 'no city';
            // $pub_city    =
            // $term_county = get_term( $term_city[0]->parent, 'region' );
            // $term_state  = get_term( $term_county->parent, 'region' );


            $pub_data[] = array(
                'pub_id'      => $post_id,
                'pub_title'   => get_the_title(),
                'pub_link'    => get_permalink( $post_id ),
                // 'pub_parent'  => $query->post->post_parent,
                'pub_domain'  => $post_meta['nn_pub_site'][0],
                'pub_name'    => $post_meta['nn_pub_name'][0],
                'pub_circ'    => $post_meta['nn_pub_circ_ep'][0],
                'pub_url'     => $post_meta['nn_pub_url'][0],
                'pub_rss'     => $post_meta['nn_pub_rss'][0],
                'pub_year'    => $post_meta['nn_pub_year'][0],
                'pub_owner'   => $pub_owner,
                'city'        => $city,
                'county'      => $county,
                'state'       => $state,
                'city_pop'    => $city_pop,
                'city_latlon' => $city_latlon,
            );
    }  else {
        return false;
    }

    return $post_data;
}

/**
 * Set transient with data for CPT posts (Publication)
 *
 * @since 0.1.0
 *
 *
 * @return bool $transient False if value was not set and true if value was set.
 */
function newsstats_set_pub_data() {
    $pub_data  = newsstats_get_all_publications();
    $transient = set_transient( 'newsnetrics_pubs', $pub_data, 30 * DAY_IN_SECONDS );

    return $transient;
}

/**
 * Get (or set) transient with data for all CPT posts (Publication)
 *
 * @since   0.1.0
 *
 * @return array $pub_data Array of data for all CPT posts.
 */
function newsstats_get_pub_data() {

     $pub_data = get_transient( 'newsnetrics_pubs' );

    // Check if transient exists.
    if ( $pub_data && is_array( $pub_data ) ) {

        return $pub_data;

    }  else {

        newsstats_set_pub_data(); // Set transient if none exists.
        $pub_data = get_transient( 'newsnetrics_pubs' );

    }

    return $pub_data;

}


/**
 * Increment artcile number when running PSI test.
 *
 * array( 'index' => 0, 'strategy- => 'mobile' )
 *
 * @since   0.1.0
 *
 * @return array $controls Array of PSI test settings.
 */
function newsnetrics_pagespeed_controls() {
    $controls = get_transient( 'newsnetrics_batch_controls' );

    if ( $controls ) { // Increment index, or, after 3 articles, reset index and change strategy.
        $index =  $controls['index'];
        if ( $index > 1 ) { // Reset index and switch strategy.

            $controls['index'] = 0;
            $controls['strategy'] = ( $controls['strategy'] == 'mobile' ) ? 'desktop': 'mobile';

        } else { // Increment index.
            $controls['index'] = $index + 1;
        }

    } else { // No transient so set one.
        $controls = array( 'index' => 0, 'strategy' => 'mobile' );
    }

    set_transient( 'newsnetrics_batch_controls', $controls, DAY_IN_SECONDS );

    return $controls;

}


/*******************************
 =STATISTICS (Site-wide data arrays for all Publications)
 ******************************/
/**
 * Get PSI data. (Not currently used.)
 *
 *
 * @since   0.1.0
 *
 * @return array $pubs_data Array of data for all CPT posts.
 */
function newsstats_get_pubs_pagespeed() {
    $pubs_data = get_transient( 'newsnetrics_pagespeed' );

    if ( $pubs_data ) {
        return $pubs_data;
    }

    $pubs_data = newsstats_set_pubs_pagespeed();

    return $pubs_data;
}

/**
 * Get PSI averages. (Not currently used.)
 *
 *
 * @since   0.1.0
 *
 * @return array $pubs_data Array of data for all CPT posts.
 */
function newsstats_set_pagespeed_avgs() {
    $pubs_data = array();
    $query     = newsstats_get_pub_posts( 2000 );
    $metrics   = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );

    foreach ( $query->posts as $post ) {
        $articles  = get_post_meta( $post->ID, 'nn_articles_201908', true);

        if ( $articles ) {
            $pagespeed = wp_list_pluck( $articles, 'pagespeed' );
            $errors    = wp_list_pluck( $pagespeed, 'error' );

            if ( in_array( 0, $errors) ) { // Has results.

                foreach ($metrics as $metric ) {
                    $results = wp_list_pluck( $pagespeed, $metric );
                    $pubs_data[$metric][] = nstats_mean( $results );
                }
            }
        }
    }

    $transient = set_transient( 'newsnetrics_pagespeed', $pubs_data, 30 * DAY_IN_SECONDS );

    return $pubs_data;
}

/**
 * Get PSI results. (Not currently used.)
 *
 *
 * @since   0.1.0
 *
 * @return array $pubs_data Array of data for all CPT posts.
 */
function newsstats_set_pubs_pagespeed() {
    $pubs_data = array();
    $query     = newsstats_get_pub_posts( 2000 );
    $metrics   = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );

    foreach ( $query->posts as $post ) {
        $articles = get_post_meta( $post->ID, 'nn_articles_201908', true);

        if ( $articles ) {

            foreach ($articles as $article ) {

                if ( isset( $article['pagespeed'] ) && ! $article['pagespeed']['error'] ) {
                    $pgspeed = $article['pagespeed'];

                    foreach ($metrics as $metric ) {
                        if ( $pgspeed[$metric] ) {
                            $pubs_data[$metric][] = floatval( $pgspeed[$metric] );
                        }
                    }
                }
            }

        }
    }

    // $transient = set_transient( 'newsnetrics_pagespeed', $pubs_data, 30 * DAY_IN_SECONDS );
    return $pubs_data;
}



/**
 * Get PSI results. (Not currently used.)
 *
 *
 * @since   0.1.0
 *
 * @return array $pubs_data Array of data for all CPT posts.
 */
function netrics_get_pubs_pagespeed_query( $query = array() ) {
    if ( ! isset( $query->posts ) ) {
        $query = newsstats_get_pub_posts( 2000 );
    }

    $pubs_data = array();
    $metrics   = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );

    foreach ( $query->posts as $post ) {
        $articles = get_post_meta( $post->ID, 'nn_articles_201908', true);

        if ( ! $articles ) {
            continue;
        }

        foreach ($articles as $article ) {
            if ( ! isset( $article['pagespeed'] ) || $article['pagespeed']['error'] ) {
                continue;
            }

            $pgspeed = $article['pagespeed'];

            foreach ($metrics as $metric ) {
                if ( $pgspeed[$metric] ) {
                    $pubs_data[$metric][] = floatval( $pgspeed[$metric] );
                }
            }
        }
    }

    return $pubs_data;
}

/**
 * Get query results for random publication for testing.
 *
 * get_transient( 'netrics_rand' );
 *
 * @since   0.1.0
 *
 * @return array $query_rand Array of random WP Post objects.
 */
function netrics_test_pubs() {
    $args = array(
        'post_type'      => 'publication',
        'orderby'        => 'rand',
        'posts_per_page' => 50,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => '0err',
            ),
        ),
    );
    $query_rand = new WP_Query( $args );

    // $query_rand = set_transient( 'netrics_rand', $query_rand, 30 * DAY_IN_SECONDS );
    // $rand_pubs  = get_transient( 'netrics_rand' );
    // return $rand_pubs;

    return $query_rand;
}

