<?php
/**
 * Get data from APIs, performance tests, domian info, etc..
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/**
 * Get CPT (Publication) post IDs.
 *
 * @since   0.1.0
 *
 * @param  int    $per_page  The number of post to return.
 * @param  int    $offset    The starting point in array.
 * @param  string $fields    The Post object fields to return.
 * @return array  $query     Array of WP Post objects.
 */
function netrics_get_pubs_ids( $per_page = 100, $offset = 0, $fields = 'ids' ) {
    $args = array(
        'post_type'      => 'publication',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => $fields,
        'posts_per_page' => $per_page,
        'offset'         => $offset,
    );
    $query = new WP_Query( $args );

    return $query;
}

/**
 * Get CPT posts (Publication)
 *
 * @since   0.1.0
 *
 * @param  int    $per_page  The number of post to return.
 * @param  int    $offset    The starting point in array.
 * @return array  $query     Array of WP Post objects.
 */
function netrics_get_pubs_tax_ids( $per_page = 100, $offset = 0, $fields = 'ids', $flag = '201906' ) {
    $args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'tax_query' => array(
            // 'relation'  => 'AND',
            array(
                'taxonomy' => 'flag',
                'field'    => 'slug',
                'terms'    => $flag,
            ),
        ),
        'fields'         => $fields,
        'posts_per_page' => $per_page,
        'offset'         => $offset,
    );
    $query = new WP_Query( $args );

    return $query ;
}

/**
 * Add an dated error message to post meta, with name of function that threw error.
 *
 * @since   0.1.0
 *
 * @param string $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_error( $post_id, $error ) {
    if ( ! $post_id || ! $error ) {
        // return new WP_Error( 'broke', __( "I've fallen and can't get up", "my_textdomain" ) );
        return false;
    }

    add_post_meta( $post_id, 'nn_error', $error . date( '-Y.m' ), false );
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
    $articles = get_post_meta( $post_id, 'nn_articles_201906', true);

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
/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_pagespeeds( $query ) {
    if ( ! isset( $query->posts ) ) {
        $query = netrics_get_pubs_ids();
    }

    foreach ( $query->posts as $post_id ) {
        newsstats_do_pagespeed( $post_id );
    }
}



/**
 * Get article items from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_pub_items( $post_id, $meta_key = 'nn_artciles_201906' ) {
    $items = get_post_meta( $post_id, $meta_key, true ); // Articles.

    if ( is_array( $items ) && isset( $items[0]['url'] ) ) {
        return $items;
    } else {
        return new WP_Error( 'items_none', __( "Post does not have articles." ) );
    }
}

/**
 * Get article item URLs.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_validate_url( $url ) {
    if ( wp_http_validate_url( $url ) ) {
        return $url;
    } else {
        return new WP_Error( 'url_invalid', __( "URL does not validate." ) );
    }
}

/**
 * Get
 *
 * @since   0.1.0
 *
 * $strategy = mobile|desktop
 * $item = 0|1|2
 * $term_id = 6196 ('201906'>'1906done')
 *
 * @param  int    $post_id  Post ID.
 * @return string $url      Post meta value
 */
function netrics_get_pagespeed( $post_id, $strategy = 'mobile', $term_id = 6196 ) {
    $items = newsstats_get_post_articles( $post_id );
    $url   = ( isset( $articles[$item]['url'] ) ) ? $articles[$item]['url'] : false;
    $terms = null;

    if ( wp_http_validate_url( $url ) ) {
        // Three articles for each publication.
        foreach ( $items as $key => $item ) {
            $pagespeed = netrics_get_pagespeed( $url, $strategy ); // Run Pagespeed test.
        }

    } else { // Set term. // Throw error.
        // $term_ids = wp_set_post_terms( $post_id, array( 6199 ), 'flag', true ); // Term: 'gpg-fail-url'.
    }

    sleep( 1 ); // Works better with a pause.

    return $terms;
}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_pagespeed_results( $url, $strategy = 'mobile' ) {
    $json = '';
    $data = $pagespeed = array();

    // Construct API call URL.
    $api_url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    $fields  = 'analysisUTCTimestamp%2ClighthouseResult(audits%2Ccategories%2Fperformance%2Fscore)';
    $api_key = 'AIzaSyDFM3aDEdbfRIMMQQRhPDmF25A01dENS70';
    $api     = $api_url . '?strategy=' . $strategy . '&fields=' . $fields . '&key=' . $api_key . '&url=';

    // Make API call to run Pagespeed test.
    $json = newsstats_request_data( $api . urlencode( $url ), 60 );
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
function netrics_save_pagespeed_results( $post_id, $pagespeed, $item = 0, $term_id = 6198 ) {
    if ( $pagespeed ) {
        $articles[$item]['pagespeed'] = $pagespeed; // Save results as array in post_meta.
        update_post_meta( $post_id, 'nn_articles_201906', $articles );
        $terms = wp_set_post_terms( $post_id, array( $term_id ), 'flag', true ); // Flag success.
    } else {
        $terms = wp_set_post_terms( $post_id, '1906redo' . $key, 'post_tag', true ); // Flag error.
    }

    return $terms;
}


/* ------------------------------------------------------------------------ *
 * OLD: Google PageSpeed (with Lighthouse)
 * ------------------------------------------------------------------------ */
/**
 * Get CPT posts (Publication)
 *
 * @since   0.1.0
 *
 * @param  int    $per_page  The number of post to return.
 * @param  int    $offset    The starting point in array.
 * @return array  $query     Array of WP Post objects.
 */
function newsstats_get_pagespeed_posts( $per_page = 100, $offset = 0 ) {
    $args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'tax_query' => array(
            'relation'  => 'AND',
            array(
                'taxonomy' => 'flag',
                'field'    => 'slug',
                'terms'    => '2019-06',
            ),
            array(
                'taxonomy' => 'flag',
                'field'    => 'slug',
                'terms'    => 'gpg2m',
                'operator' => 'NOT IN',
            ),
        ),
        'posts_per_page' => $per_page,
        'offset'         => $offset,
    );
    $query = new WP_Query( $args );

    return $query ;
}

/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_run_pagespeed_posts( $query ) {
    if ( ! isset( $query->posts ) ) {
        $query = newsstats_get_pagespeed_posts();
    }

    foreach ( $query->posts as $post ) {
        newsstats_do_pagespeed( $post );
    }
}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param  int    $post_id  Post ID.
 * @return string $url      Post meta value
 */
function newsstats_do_pagespeed( $post ) {

    $item     = 0; // Set URL to ren, either 0, 1, or 2.
    $strategy = 'mobile'; // mobile|desktop
    $term_id  = 6196; // gpg0m: 6194, gpg0d: 6195, gpg1m: 6192, gpg1d: 6193, gpg2m: 6196, gpg2d: 6197.
    $term_ids = 6;

    $post_id  = $post->ID;
    $articles = newsstats_get_post_articles( $post_id );
    $url      = ( isset( $articles[$item]['url'] ) ) ? $articles[$item]['url'] : false;

    if ( wp_http_validate_url( $url ) ) {
        // Run Pagespeed, save data in post_meta, then set term.
        $pagespeed = newsstats_get_pagespeed( $url, $strategy );
        if ( $pagespeed ) {
            $articles[$item]['pagespeed'] = $pagespeed; // Add data.
            update_post_meta( $post_id, 'nn_articles_201906', $articles );
            // $term_ids = wp_set_post_terms( $post_id, array( $term_id ), 'flag', true ); // Record success.
        } else {
            // $term_ids = wp_set_post_terms( $post_id, array( 6198 ), 'flag', true ); // Term: 'gpg-fail'.
        }

    } else { // Set term. // Throw error.
        // $term_ids = wp_set_post_terms( $post_id, array( 6199 ), 'flag', true ); // Term: 'gpg-fail-url'.
    }

    sleep( 1 ); // Works better with a pause.

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
function newsstats_get_pagespeed( $url, $strategy = 'mobile' ) {

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
        add_post_meta( $post_id, 'nn_articles_201906', $links, true);
        $term_ids = wp_set_post_terms( $post_id, 6178, 'flag', true ); // Term: 'fail'.

    } else { // Set term.
        $term_ids = wp_set_post_terms( $post_id, array( 6176 ), 'flag', true ); // Term: 'fail'.
    }

    return $term_ids;
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

/* ------------------------------------------------------------------------ *
 * Alexa Web Information Service (Amazon)
 * ------------------------------------------------------------------------ */
/**
 * Get all CPT posts (Publication).
 *
 * @since   0.1.0
 *
 * @param  int    $per_page  The number of post to return.
 * @param  int    $offset    The starting point in array.
 * @return array  $query     Array of WP Post objects.
 */
function newsstats_get_pub_posts( $per_page = 100, $offset = 0 ) {
    $args = array(
        'post_type' => 'publication',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'posts_per_page' => $per_page,
        'offset'         => $offset,
    );
    $query = new WP_Query( $args );

    return $query ;

}

/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_api_calls( $query, $api = 'awis' ) {
    if ( ! isset( $query->posts ) ) {
        $query = newsstats_get_pub_posts();
    }

    switch ( $api ) {
        case 'awis': // Alexa Web Information Service (Amazon)
            foreach ( $query->posts as $post ) {
                newsstats_save_alexa_data( $post );
            }
            break;
        case 'bw': // BuiltWith
            foreach ( $query->posts as $post ) {
                newsstats_save_bw_data( $post );
            }
            break;
        default:
            break;
    }

}

/* ------------------------------------------------------------------------ *
 * Alexa Web Information Service (Amazon) API
 * ------------------------------------------------------------------------ */
/**
 * Save AWIS data as post meta (array).
 *
 * @see https://docs.aws.amazon.com/awis/index.html
 *
 * @since   0.1.0
 *
 * @param  int   $post     WP Post object.
 * @return array $nn_site  Updated array of site data,
 */
function newsstats_save_alexa_data( $post ) {

    $post_id = $post->ID;
    // Returns NN-customized array.
    $alexa   = newsstats_call_alexa_api( $post_id );
    // We'll add data to existing post meta or start a new array.
    $nn_site = ( get_post_meta( $post_id, 'nn_site' ) ) ? get_post_meta( $post_id, 'nn_site', true ) : array();

    // Run Alexa, save data in post_meta, then set term.
    if ( $alexa ) {

        $nn_site['alexa'] = $alexa;
        // @todo Don't replace with empty fields, get keys and check via foreach.
        update_post_meta( $post_id, 'nn_site', $nn_site );

    }

    sleep( 2 ); // Works better with a pause.

    return $nn_site;

}


/**
 * Get data from AWIS via API for domain name.
 *
 * @since   0.1.0
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function newsstats_call_alexa_api( $post_id ) {

    // Alexa script (New Netrics customized) and API keys
    include_once( WP_PLUGIN_DIR . '/news-netrics/api/alexa.php' );
    $accessKeyId     = 'AKIAJBV4OWGCGHDIL3PQ';
    $secretAccessKey = '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';
    $domain          = get_post_meta( $post_id, 'nn_pub_site', true );
    echo $domain; // @todo Log to file.

    if ( $domain ) {

        // Class for Alexa API
        $urlInfo    = new UrlInfo( $accessKeyId, $secretAccessKey, $domain );
        $alexa_data = $urlInfo->getUrlInfo();

    }

    // Set return var to false if not an array.
    $alexa = ( is_array( $alexa_data ) ) ? $alexa_data : false;

    print_r ( $alexa ); // @todo Log to file.

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
    [date] => 201906
)

date_parse_from_format ( 'Y-m' , $nn_site[0]['alexa']['date'] );
date_parse_from_format ( 'd-M-Y' , $nn_site[0]['alexa']['since'] );
*/

/* ------------------------------------------------------------------------ *
 * BuiltWith: Free API
 * ------------------------------------------------------------------------ */
/**
 * Save AWIS data as post meta (array).
 *
 * Domain API (web tech names and catorgories)
 * @see https://api.builtwith.com/domain-api
 *
 * @since   0.1.0
 *
 * @param  int   $post     WP Post object.
 * @return array $nn_site  Updated array of site data,
 */
function newsstats_save_bw_data( $post ) {

    $post_id = $post->ID;
    $bw_data   = newsstats_call_bw_api( $post_id );
    // We'll add data to existing post meta or start a new array.
    $nn_site = ( get_post_meta( $post_id, 'nn_site' ) ) ? get_post_meta( $post_id, 'nn_site', true ) : array();

    // Run Alexa, save data in post_meta, then set term.
    if ( $bw_data ) {

        $nn_site['builtwith'] = $bw_data;
        update_post_meta( $post_id, 'nn_site', $nn_site );

    }

    sleep( 2 ); // Works better with a pause.

    print_r ( $nn_site );

    return $nn_site;

}


/**
 * Get data from AWIS via API for domain name.
 *
 * Domain API (web tech names and categories):
 * https://api.builtwith.com/v12/api.[xml|json]?KEY=[YOUR KEY]&LOOKUP=[DOMAIN]
 * @see https://api.builtwith.com/domain-api
 *
 * Free API (category count only):
 * https://api.builtwith.com/free1/api.json?KEY=[YOUR KEY]&LOOKUP=builtwith.com
 * @see https://api.builtwith.com/free-api
 *
 * @since   0.1.0
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function newsstats_call_bw_api( $post_id , $api_type = 'free') {

    // BuitWith Free API url and API key.
    $api     = 'https://api.builtwith.com/free1/api.json';
    $api_key = 'd0d0d4bd-044f-4a33-bd2f-c28e3a9a1415';
    $domain  = get_post_meta( $post_id, 'nn_pub_site', true );
    $api_url = $api . '?KEY=' . $api_key . '&LOOKUP=' . $domain;
    $bw_json = $bw_data = false;
    echo $api_url; // @todo Log to file.
    if ( $domain ) {

        // Make API call and return data with tech-category names and counts.
        $bw_json = newsstats_request_data( $api_url );
        $bw_arr  = json_decode( $bw_json );
        $bw_data  = array();
        if ( isset( $bw_arr->groups ) ) {

            // Make tech-cat and count into array.
            foreach ( $bw_arr->groups as $group ) {
                $bw_data[$group->name] = $group->live;
            }
            $bw_data['date']  = date( 'Y-m' );
            $bw_data['error'] = 0;

        } else { // No BW results.

            $bw_data['error']  = 4001;
            if ( isset( $bw_arr->Errors[0]->Message ) ) {

                $bw_data['err_bw_msg']  = $bw_arr->Errors[0]->Message;
                $bw_data['err_bw_code'] = ( isset( $bw_arr->Errors[0]->Code ) ) ? $bw_arr->Errors[0]->Code : null;

            }

        }

    } else { // No domain name.

        $bw_data['error'] = 4002;
        $bw_data['err_nn'] = 'No domain name';

    }

    return $bw_data;

}
/*

BuiltWith data example, coverted to array and stored in post meta:
Array
(
    [ads] => 74
    [analytics] => 34
    [CDN] => 1
    [cdn] => 15
    [cdns] => 3
    [cms] => 5
    [copyright] => 0
    [css] => 3
    [feeds] => 1
    [framework] => 12
    [hosting] => 9
    [javascript] => 48
    [language] => 1
    [link] => 0
    [mapping] => 1
    [media] => 3
    [mobile] => 6
    [mx] => 6
    [ns] => 0
    [payment] => 2
    [Server] => 2
    [shop] => 1
    [ssl] => 3
    [Web Master] => 3
    [Web Server] => 11
    [widgets] => 13
    [date] => 2019-06
    [error] => 0
)

$bw_cats = array(
    'ads'         => 'Advertising',
    'analytics'   => 'Analytics and Tracking',
    'CDN'         => 'Content Delivery Network',
    'cdn'         => 'Content Delivery Network', // ignore
    'cdns'        => 'Verified CDN',
    'cms'         => 'Content Management System',
    'copyright'   => 'Copyright',
    'css'         => 'CSS Media Queries',
    'docinfo'     => 'Document Standards',
    'encoding'    => 'Document Encoding',
    'feeds'       => 'Syndication Techniques',
    'framework'   => 'Frameworks',
    'hosting'     => 'Web Hosting Providers',
    'javascript'  => 'JavaScript Libraries and Functions',
    'language'    => 'Language',
    'link'        => 'Verified Link',
    'mapping'     => 'Mapping',
    'media'       => 'Audio / Video Media',
    'mobile'      => 'Mobile',
    'mx'          => 'Email Hosting Providers',
    'ns'          => 'Name Server',
    'payment'     => 'Payment',
    'seo_headers' => 'SEO Header Tag',
    'seo_meta'    => 'SEO Meta Tag',
    'seo_title'   => 'SEO Title Tag',
    'shipping'    => 'Shipping Providers',
    'Server'      => 'Operating Systems and Servers',
    'shop'        => 'eCommerce',
    'ssl'         => 'SSL Certificates',
    'Web Master'  => 'Web Master Registration',
    'Web Server'  => 'Web Servers',
    'widgets'     => 'Widgets'
);



BuiltWith error example, raw JSON:
'{"Errors":[{"Lookup":null,"Message":"Domain not in our system. Paid for lookups would return something.","Code":-10}],"NextOffset":null,"Results":null}';

*/

