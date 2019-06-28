<?php
/**
 * Get data from APIs, performance tests, domian info, etc..
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/* ------------------------------------------------------------------------ *
 * Batch Processes (Uses Locamotive plugin)
 * ------------------------------------------------------------------------ */
function newsstats_batch_process() {

    register_batch_process( array(
        'name'     => 'Get Publication Articles',
        'type'     => 'post',
        'callback' => 'newsstats_get_articles_batch',
        'args'     => array(
            'post_type' => 'publication',
            'tax_query' => array(
                array(
                    'taxonomy' => 'flag',
                    'field'    => 'slug',
                    'terms'    => 'check',
                ),
            ),
            'posts_per_page' => 5,
        )
    ) );

    register_batch_process( array(
        'name'     => 'Do Google PageSpeed',
        'type'     => 'post',
        'callback' => 'newsstats_do_pagespeed_batch',
        'args'     => array(
            'post_type' => 'publication',
            'orderby'   => 'title',
            'order'     => 'ASC',
            'tax_query' => array(
                // 'relation'  => 'AND',
                array(
                    'taxonomy' => 'flag',
                    'field'    => 'slug',
                    'terms'    => '201905',
                ),
            ),
            'posts_per_page' => 1,
        )
    ) );

    /* */
    register_batch_process( array(
    'name'     => 'Set Feed Flag',
    'type'     => 'post',
    'callback' => 'newsstats_set_terms',
        'args'     => array(
            'post_type' => 'publication',
            'tax_query' => array(
                array(
                    'taxonomy' => 'flag',
                    'field'    => 'slug',
                    'terms'    => 'check',
                ),
            ),
            'posts_per_page' => 50,
        )
    ) );


}
add_action( 'locomotive_init', 'newsstats_batch_process' );

/**
 *
 *
 * Tax 'flag' terms (ID):
 * 'feed' (6170)
 *     'xml' (6171)
 *     json' (6172)
 *     'none' (6175)
 *     'fail' (6176)
 * 'articles' (6177)
 *     '201905' (6178)
 *     'check' (6179)
 *
 */
function newsstats_set_terms( $post ) {

    $post_id = $post->ID;
    $term_ids = '';
    $rss      = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file

    if ( has_term( '201905', 'flag', $post_id ) ) { // Check for: '201905'.

        // wp_remove_object_terms( $post_id, 'check', 'flag' ); // Remove: 'check'.

    } else {

        // wp_remove_object_terms( $post_id, 6170, 'flag' ); // Remove: 'feed'.
        // $term_ids = wp_set_post_terms( $post_id, array( 6175 ), 'flag', true ); // Add: 'none'.

    }

    return $term_ids;

}


/* ------------------------------------------------------------------------ *
 * cURL (get data from remote sites)
 * ------------------------------------------------------------------------ */
/**
 * Retrieves the response from the specified URL using one of PHP's outbound request facilities.
 *
 * @link https://tommcfarlin.com/wp_remote_get/ Code author
 *
 * @params  $url  The URL of the feed to retrieve.
 * @returns $response  The response from the URL; null if empty.
 */
function newsstats_request_data( $url, $timeout = 10 ) {

    $response = null;
    $args     = array(
        'sslverify' => false,
        'timeout'   => $timeout,
    );

    // First, we try to use wp_remote_get
    $response = wp_remote_get( $url, $args );
    if( is_wp_error( $response ) ) {

        // If that doesn't work, then we'll try file_get_contents
        $response = file_get_contents( $url );
        if( false == $response ) {

            // And if that doesn't work, then we'll try curl
            $response = newsstats_curl( $url );
            if( null == $response ) {
                $response = 0;
            } // end if/else

        } // end if

    } // end if

    // If the response is an array, it's coming from wp_remote_get,
    // so we just want to capture to the body index for json_decode.
    if( is_array( $response ) ) {
        $response = $response['body'];
    } // end if/else

    return $response;

} // end request_data

/**
 * Defines the function used to initial the cURL library.
 *
 * @link https://tommcfarlin.com/wp_remote_get/ Code author
 *
 * @param  string  $url        To URL to which the request is being made
 * @return string  $response   The response, if available; otherwise, null
 */
function newsstats_curl( $url ) {

    $curl = curl_init( $url );

    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_HEADER, 0 );
    curl_setopt( $curl, CURLOPT_USERAGENT, '' );
    curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

    $response = curl_exec( $curl );
    if( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
        $response = null;
    } // end if
    curl_close( $curl );

    return $response;

} // end curl

/* ------------------------------------------------------------------------ *
 * URLs (Get articles from Publication post meta)
 * ------------------------------------------------------------------------ */
/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_get_post_articles( $post_id ) {

    $articles = get_post_meta( $post_id, 'nn_articles_201905', true);

    return $articles;
}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_run_api( $url ) {

    $results = newsstats_request_data( $url, $timeout );

    return $results;
}


/* ------------------------------------------------------------------------ *
 * Google PageSpeed (with Lighthouse)
 * ------------------------------------------------------------------------ */
/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_do_pagespeed_batch( $post ) {

    $item     = 0;
    $strategy = 'mobile'; // mobile|desktop
    $term_id  = 6194; // gpg0m: 6194, gpg0d: 6195, gpg1m: 6192, gpg1d: 6193, gpg2m: 6196, gpg2d: 6197.
    $term_ids = 6;

    $post_id  = $post->ID;
    $articles = newsstats_get_post_articles( $post_id );
    $url      = ( isset( $articles[$item]['url'] ) ) ? $articles[$item]['url'] : false;

    if ( wp_http_validate_url( $url ) ) {
        // Run Pagespeed, save data in post_meta, then set term.
        $pagespeed = newsstats_pagespeed_run( $url, $strategy );
        if ( $pagespeed ) {
            $articles[$item]['pagespeed'] = $pagespeed; // Add data.
            update_post_meta( $post_id, 'nn_articles_201905', $articles );
            $term_ids = wp_set_post_terms( $post_id, array( $term_id ), 'flag', true ); // Record success.
        } else {
            $term_ids = wp_set_post_terms( $post_id, array( 6198 ), 'flag', true ); // Term: 'gpg-fail'.
        }

    } else { // Set term.
        $term_ids = wp_set_post_terms( $post_id, array( 6199 ), 'flag', true ); // Term: 'gpg-fail-url'.
    }

    sleep( 1 );
    return $term_ids;
}


/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_get_pagespeed_posts( $posts_per_page = 100, $offset = 0 ) {
    $query_args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'tax_query' => array(
            'relation'  => 'AND',
            array(
                'taxonomy' => 'flag',
                'field'    => 'slug',
                'terms'    => '201905',
            ),
            array(
                'taxonomy' => 'flag',
                'field'    => 'slug',
                'terms'    => 'gpg2m',
                'operator' => 'NOT IN',
            ),
        ),
        'posts_per_page' => $posts_per_page,
        'offset'         => $offset,
    );
    $query_posts = new WP_Query( $query_args );

    return $query_posts ;

}

/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_run_pagespeed_posts( $query_posts ) {

    if ( ! isset( $query_posts->posts ) ) {
        $query_posts = newsstats_get_pagespeed_posts();
    }

    foreach ( $query_posts->posts as $post ) {
        newsstats_do_pagespeed( $post );
    }

}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_do_pagespeed( $post ) {

    $item     = 2;
    $strategy = 'mobile'; // mobile|desktop
    $term_id  = 6196; // gpg0m: 6194, gpg0d: 6195, gpg1m: 6192, gpg1d: 6193, gpg2m: 6196, gpg2d: 6197.
    $term_ids = 6;

    $post_id  = $post->ID;
    $articles = newsstats_get_post_articles( $post_id );
    $url      = ( isset( $articles[$item]['url'] ) ) ? $articles[$item]['url'] : false;

    if ( wp_http_validate_url( $url ) ) {
        // Run Pagespeed, save data in post_meta, then set term.
        $pagespeed = newsstats_pagespeed_run( $url, $strategy );
        if ( $pagespeed ) {
            $articles[$item]['pagespeed'] = $pagespeed; // Add data.
            update_post_meta( $post_id, 'nn_articles_201905', $articles );
            $term_ids = wp_set_post_terms( $post_id, array( $term_id ), 'flag', true ); // Record success.
        } else {
            $term_ids = wp_set_post_terms( $post_id, array( 6198 ), 'flag', true ); // Term: 'gpg-fail'.
        }

    } else { // Set term.
        $term_ids = wp_set_post_terms( $post_id, array( 6199 ), 'flag', true ); // Term: 'gpg-fail-url'.
    }

    sleep( 1 );
    return $term_ids;
}

/*
- Batch: Get all Posts (WP_QUery)
- Get all articlels for each post (get post meta; params: $post_id; return: $articles )
- Extract valid each article url (foreach $articles as wp_http_validate_url( $article['url']; $articles )
- Get API test results (url with API param- switch)
- Save results (post_meta, term: params: $post_id, $arr[i] )
 */

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_pagespeed_run( $url, $strategy = 'mobile' ) {

    $json = '';
    $data = $pagespeed = array();
    // @todo Log in file.
    echo $url;

    // Construct API call.
    $api_url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    $fields  = 'analysisUTCTimestamp%2ClighthouseResult(audits%2Ccategories%2Fperformance%2Fscore)';
    $api_key = 'AIzaSyDFM3aDEdbfRIMMQQRhPDmF25A01dENS70';
    $api     = $api_url . '?strategy=' . $strategy . '&fields=' . $fields . '&key=' . $api_key . '&url=';

    // return $api . urlencode( $url );

    // Make API call.
    $json = newsstats_request_data( $api . urlencode( $url ), 60 );
    if ( $json ) {

        $data = json_decode( $json );
        if ( isset( $data->lighthouseResult ) ) {

            $audits = $data->lighthouseResult->audits;
            $data_arr = array(
                // 'date'     => date( 'Ym' ),
                'date'     => 201905,
                // 'bytes'    => $audits->diagnostics->details->items[0]->totalByteWeight,
                'dom'      => str_replace( ',', '', $audits->{"dom-size"}->details->items[0]->value ),
                'requests' => $audits->{"resource-summary"}->details->items[0]->requestCount,
                'size'     => $audits->{"resource-summary"}->details->items[0]->size,
                'speed'    => $audits->metrics->details->items[0]->speedIndex,
                'tti'      => $audits->metrics->details->items[0]->interactive,
                'score'    => $data->lighthouseResult->categories->performance->score,
                'time'     => $data->analysisUTCTimestamp,
                'error'    => 0,
            );

            $pagespeed = array();
            foreach ( $data_arr as $key => $value ) {
                $pagespeed[$key] = $value;
            }

        } else { // No Lighthouse data returned.
            $pagespeed['error'] = 1;
        }

    } else { // No JSON returned from remote request.
       $pagespeed['error'] = 2;
    }

    // @todo Log in file.
    print_r( $pagespeed );
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
function newsstats_pagespeed_results( $json ) {

    $post_id = $post->ID;
    $links = newsstats_feed_links( $post );
    if ( $links ) { // Add post_meta and set term.
        add_post_meta( $post_id, 'nn_articles_201905', $links, true);
        $term_ids = wp_set_post_terms( $post_id, 6178, 'flag', true ); // Term: 'fail'.

    } else { // Set term.
        $term_ids = wp_set_post_terms( $post_id, array( 6176 ), 'flag', true ); // Term: 'fail'.
    }

    return $term_ids;
}

/* ------------------------------------------------------------------------ *
 * Alexa Site Info (Amazon)
 * ------------------------------------------------------------------------ */

/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_run_alexa( $query_posts ) {

    if ( ! isset( $query_posts->posts ) ) {
        $query_posts = newsstats_get_pub_posts();
    }

    foreach ( $query_posts->posts as $post ) {
        newsstats_do_alexa( $post );
    }

}

/** Get all CPT posts (Publication)
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_get_pub_posts( $posts_per_page = 100, $offset = 0 ) {
    $query_args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'posts_per_page' => $posts_per_page,
        'offset'         => $offset,
    );
    $query_posts = new WP_Query( $query_args );

    return $query_posts ;

}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_do_alexa( $post ) {

    $post_id  = $post->ID;
    $alexa    = newsstats_get_alexa( $post_id );
    $nn_site  = array();

    // Run Alexa, save data in post_meta, then set term.
    if ( $alexa ) {
        $nn_site['alexa'] = $alexa;
        // update_post_meta( $post_id, 'nn_site', $nn_site );
    }
    sleep( 1 );
    return $nn_site;

}


/**
 * Get data from Alexa via API and domain name
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array $alexa  Data from Alexa.
 */
function newsstats_get_alexa( $post_id ) {

    // Alexa script (New Netrics customized) and API keys
    include_once( WP_PLUGIN_DIR . '/news-netrics/api/alexa.php' );
    $accessKeyId     = 'AKIAJBV4OWGCGHDIL3PQ';
    $secretAccessKey = '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';
    $site            = get_post_meta( $post_id, 'nn_pub_site', true );
    echo $site; // @todo Log

    if ( $site ) {

        // Class for Alexa API
        $urlInfo    = new UrlInfo( $accessKeyId, $secretAccessKey, get_post_meta( $post_id, 'nn_pub_site', true ) );
        $alexa_data = $urlInfo->getUrlInfo();

    }

    // Set return var to false if not an array.
    $alexa = ( is_array( $alexa_data ) ) ? $alexa_data : false;

    print_r ( $alexa ); // @todo Log

    return $alexa;

}
/*
Array
(
    [rank] => 74977
    [title] => Albuquerque Journal - ABQJournal
    [desc] => Provides New Mexico news and sports.
    [since] => 03-Feb-1996
    [links] => 2273
    [speed] => 2867
    [speed_pc] => 26
)
*/



