<?php
/**
 * API call for site-performance results from Google's PageSpeed Insights (PSI).
 *
 * https://developers.google.com/speed/pagespeed/insights/
 *
 * @since   0.1.1
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes/api/
 */

/** Run PageSpeed tests (with Lighthouse)
 *
 * @since   0.1.0
 *
 * @param int $query_ids  Array of Post IDs.
 * @return string         Post meta value
 */
function netrics_get_pubs_pagespeed( $query_ids, $strategy = 'mobile'  ) {
    if ( ! isset( $query_ids->posts ) ) {
        $query_ids = netrics_get_pubs_ids();
    }

    foreach ( $query_ids->posts as $post_id ) {
        echo "ID: $post_id\n";
        $terms = '';
        $items = netrics_get_pub_items( $post_id ); // Get articles.

        if ( is_wp_error( $items ) ) { // No articles.
            continue;
        }

        foreach ( $items as $key => $item ) { // Get PageSpeed results for articles.
            // Skip if URL already successfully retrieved PSI results ('error' > 0).
            if ( isset( $item['error'] ) && $item['error'] ) {
                continue;
            }

            // Run PSI.
            if ( isset( $item['url'] ) && wp_http_validate_url( $item['url'] ) ) {
                echo "URL: {$item['url']}\n";
                $pagespeed = netrics_get_pagespeed( $item['url'], $strategy ); // Run PageSpeed test.
                print_r( $pagespeed );
            }

            // Store PSI results in post meta.
            if ( $pagespeed ) { // Successful remote request.
                $terms = netrics_save_pagespeed( $post_id, $pagespeed, $key );
            } else {
                return new WP_Error( 'pagespeed', __( "No PageSpeed results." ), $post_id );
            }

            sleep( 1 ); // Works better with a pause.
        }
    }

    return "$post_id $terms[0]\n";
}

/**
 * Run Pagespeed Insights test for URL then return results.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_pagespeed( $url, $strategy = 'mobile' ) {
    $json = '';
    $data = $pagespeed = array();

    // Construct API call URL.
    $api_url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    $fields  = 'analysisUTCTimestamp%2ClighthouseResult(audits%2Ccategories%2Fperformance%2Fscore)';
    $options = netrics_get_options(); // Get API Keys.
    $api_key = $options['psi'];
    $api     = $api_url . '?strategy=' . $strategy . '&fields=' . $fields . '&key=' . $api_key . '&url=';

    // Make API call to run PageSpeed test.
   $json = netrics_request_data( $api . urlencode( $url ), 300 );
    if ( $json ) {
        $data = json_decode( $json );

        // Make array of test results from JSON.
        if ( isset( $data->lighthouseResult ) ) {
            $audits    = $data->lighthouseResult->audits;
            $pagespeed = array(
                // 'bytes'    => $audits->diagnostics->details->items[0]->totalByteWeight,
                'date'     => date( 'Y-m' ),
                'dom'      => str_replace( ',', '', $audits->{"dom-size"}->details->items[0]->value ),
                'requests' => $audits->{"resource-summary"}->details->items[0]->requestCount,
                'size'     => $audits->{"resource-summary"}->details->items[0]->size,
                'speed'    => $audits->metrics->details->items[0]->speedIndex,
                'tti'      => $audits->metrics->details->items[0]->interactive,
                'score'    => $data->lighthouseResult->categories->performance->score,
                'time'     => $data->analysisUTCTimestamp,
                'error'    => 0,
            );

        } else { // No Lighthouse data returned.
            $pagespeed['error'] = 1;
        }

    } else { // No JSON returned from remote request.
       $pagespeed['error'] = 2;
    }

    return $pagespeed;
}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_save_pagespeed( $post_id, $pagespeed, $key, $term_id = 6295 ) { // '1910done'
    if ( $pagespeed ) {
        $meta_key = 'nn_articles_new';
        $items    = get_post_meta( $post_id, $meta_key, true );
        $items[$key]['pagespeed'] = $pagespeed; // Save results as array in post_meta.
        update_post_meta( $post_id, $meta_key, $items );

        // $articles = array_merge( get_post_meta( $post_id, 'nn_articles', true ), $items );
        // update_post_meta( $post_id, 'nn_articles', $articles );

        $terms = wp_set_post_terms( $post_id, array( $term_id ), 'flag', true ); // Flag success.
    } else {
        $terms = wp_set_post_terms( $post_id, $key . 'redo' . date( 'Ym' ), 'post_tag', true ); // Tag error.
    }

    return $terms;
}



/*
Array
(
    [0] => Array
        (
            [url] => https://www.abqjournal.com/1320964/tornados-leave-trail-of-destruction-across-ohio-indiana.html
            [title] => Tornadoes carve a path through Ohio and Indiana; 1 killed - Albuquerque Journal
            [pagespeed] => Array
                (
                    [bytes] => 2426022
                    [dom] => 872
                    [requests] => 196
                    [size] => 2426022
                    [speed] => 25402
                    [tti] => 19438
                    [score] => 0.26
                    [time] => 2019-06-02T22:27:49.942Z
                    [error] => 0
                    [date] => 2019-05
                )

        )

    [1] => Array
        (
            [url] => https://www.abqjournal.com/1320967/knife-wielding-man-attacks-schoolgirls-in-japan-killing-2.html
            [title] => Knife-wielding man attacks schoolgirls in Japan, killing 2 - Albuquerque Journal
            [pagespeed] => Array
                (
                    [error] => 2
                )

        )

    [2] => Array
        (
            [url] => https://www.abqjournal.com/1320869/trump-ending-japan-trip-after-memorial-day-speech-to-troops.html
            [title] => Trump wishes ‘happy Memorial Day’ to US, Japanese troops - Albuquerque Journal
            [pagespeed] => Array
                (
                    [error] => 1
                )

        )

)

*/

/* ------------------------------------------------------------------------ *
 * Set tax terms
 * ------------------------------------------------------------------------ */
function newsstats_set_terms( $post ) {
    $post_id = $post->ID;
    $term_ids = '';
    $rss      = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file

    if ( has_term( '201906', 'flag', $post_id ) ) { // Check for: '201905'.

        // wp_remove_object_terms( $post_id, 'check', 'flag' ); // Remove: 'check'.

    } else {

        // wp_remove_object_terms( $post_id, 6170, 'flag' ); // Remove: 'feed'.
        // $term_ids = wp_set_post_terms( $post_id, array( 6175 ), 'flag', true ); // Add: 'none'.

    }

    return $term_ids;
}

/**
 * Tax 'flag' terms (ID):
 * 'feed' (6170)
 *     'xml' (6171)
 *     json' (6172)
 *     'none' (6175)
 *     'fail' (6176)
 * 'articles' (6177)
 *     '201905' (6178)
 *     'check' (6179)
 */

