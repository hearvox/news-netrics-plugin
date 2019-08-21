<?php
/**
 * Get data from APIs, performance tests, domain info, etc..
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
function netrics_get_pubs_tax_ids( $per_page = 100, $offset = 0, $fields = 'ids', $flag = '201908' ) {
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
 * Publication (CPT)
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
function netrics_get_pub_posts( $per_page = 100, $offset = 0 ) {
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

/**
 * Get article items from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_pub_items( $post_id, $key = '' ) {
    $meta_key = ( $key ) ? $key : 'nn_articles_' . netrics_get_data_month();

    $items = get_post_meta( $post_id, $meta_key, true ); // Articles.

    if ( is_array( $items ) && isset( $items[0]['url'] ) ) {
        return $items;
    } else {
        return new WP_Error( 'items_none', __( "Post does not have articles." ), $post_id );
    }
}

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

/**
 * Check URL syntax.
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

/* ------------------------------------------------------------------------ *
 * API Calls
 * ------------------------------------------------------------------------ */
/**
 * Make API calls for domain data; store in post meta.
 *
 * @since   0.1.0
 *
 * @param int    $post_id  Post ID (required).
 * @param string $api      API service (AWIS or BuiltWith).
 * @param string $api_key  API key required for BuiltWith.
 *
 * @return void
 */
function netrics_api_calls( $query, $api = 'awis', $api_key = '' ) {
    if ( ! isset( $query->posts ) ) {
        $query = newsstats_get_pub_posts();
    }

    switch ( $api ) {
        case 'awis': // Alexa Web Information Service (Amazon).
            require_once( plugin_dir_path( __FILE__ ) . 'api/awis.php' );
            foreach ( $query->posts as $post ) {
                netrics_api_save_awis( $post );
            }
            break;
        case 'bw': // BuiltWith
            require_once( plugin_dir_path( __FILE__ ) . 'api/builtwith.php' );
            foreach ( $query->posts as $post ) {
                netrics_api_save_builtwith( $post, $api_key );
            }
            break;
        default:
            break;
    }
}

/**
 * Set month to use for API calls.
 *
 * @since   0.1.1
 *
 * @return
 */
function netrics_get_data_month() {
    return date( 'Ym' );
}

/* ------------------------------------------------------------------------ *
 * PageSpeed Insights (with Lighthouse)
 * ------------------------------------------------------------------------ */
 /**
 * Get PSI performance results for Publication articles via API call.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_api_call_pagespeed( $query_ids, $strategy = 'mobile' ) {
    // PSI file with API call.
    require_once( plugin_dir_path( __FILE__ ) . 'api/pagespeed.php' );

    // $query_ids = new WP_Query( array( 'post_type' => 'publication', 'p' => 4030, 'fields' => 'ids' ) );

    $psi_data = netrics_get_pubs_pagespeed( $query_ids, $strategy  );

    return $psi_data;
}

/**
 * Get PSI performance results for one URL via API call.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_api_call_pagespeed_url( $url, $strategy = 'mobile' ) {
    // PSI file with API call.
    require_once( plugin_dir_path( __FILE__ ) . 'api/pagespeed.php' );

    $psi_data = netrics_get_pagespeed( $url, $strategy );

    return $psi_data;
}

/* ------------------------------------------------------------------------ *
 * Alexa Web Information Service (Amazon) API
 * ------------------------------------------------------------------------ */
/**
 * Get AWIS data for domain via API call.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_api_call_awis( $post_id ) {
    // Alexa script (New Netrics customized) and API keys
    require_once( plugin_dir_path( __FILE__ ) . 'api/awis.php' );

    $awis = netrics_api_awis( $post_id );

    return $awis;
}


/* ------------------------------------------------------------------------ *
 * BuiltWith: Free API
 * ------------------------------------------------------------------------ */
/**
 * Get BuiltWith data for domain via API call.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_api_call_builtwith( $post_id, $api_key ) {
    // Alexa script (New Netrics customized) and API keys
    require_once( plugin_dir_path( __FILE__ ) . 'api/builtwith.php' );

    $bw_data = netrics_api_builtwith( $post_id, $api_key );

    return $bw_data;
}


/* ------------------------------------------------------------------------ *
 * Veracity.ai
 * ------------------------------------------------------------------------ */
/**
 * Get ID from Veracity search then store as post meta.
 *
 * @see https://dashboard.veracity.ai/dashboard/docs/
 *
 * @since   0.1.0
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_veracity_id( $post_id, $set = 1 ) {
    // https://APIKEY:SECRET@dashboard.veracity.ai/api/v1/auth_test/;

    $pk = 0; // Var for Veracity ID.
    // Build Veracity API url.
    $api_key    = 'CB92E85AB1384E0E9B580C4D395C4103';
    $api_secret = 'D288870706D4448BAC0814CE1E32C3EF';
    $api        = '@dashboard.veracity.ai/api/v1/domain/search/?q=';
    $domain  = get_post_meta( $post_id, 'nn_pub_site', true );
    $api_url    = 'https://' . $api_key . ':' . $api_secret . $api . $domain;

    $json = newsstats_request_data( $api_url );
    $data = json_decode( $json );
    $pk   = $data->response[0]->pk ?? 0;
    if ( $set ) {
        $meta = add_post_meta( $post_id, 'nn_veracity', absint( $pk ), true );
    }

    return $pk;
}

/**
 * Get articles of domains from Veracity crawls then store as post meta.
 *
 * @see https://dashboard.veracity.ai/dashboard/docs/
 *
 * @since   0.1.0
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Veracity.
 */
function netrics_get_veracity_articles( $post_id, $set = 1 ) {
    // https://APIKEY:SECRET@dashboard.veracity.ai/api/v1/auth_test/;

    $pk = 0; // Var for Veracity ID.
    // Build Veracity API url.
    $api_key    = 'CB92E85AB1384E0E9B580C4D395C4103';
    $api_secret = 'D288870706D4448BAC0814CE1E32C3EF';
    $api        = '@dashboard.veracity.ai/api/v1/domain/articles/?page=1&per_page=25&pk=';
    $pk         = get_post_meta( $post_id, 'nn_veracity', true );
    $api_url    = 'https://' . $api_key . ':' . $api_secret . $api . $pk;

    $json = newsstats_request_data( $api_url );
    $data = json_decode( $json );
    // $pk   = $data->response[0]->pk ?? 0;
    if ( $set ) {
        // $meta = add_post_meta( $post_id, 'nn_veracity', absint( $pk ), true );
    }

    return $data;
}

/*
// Get offset for next post after $post_id.
// $offset = array_search( 4364, $query_ids->posts ) + 1;

// To set all Veracity Ids.
$query_ids = netrics_get_pubs_ids( 2000, 341 ); // posts_per_page, offset.

foreach ( $query_ids->posts as $post_id ) {
    // $pk   = netrics_veracity_id( $post_id );
    $meta = get_post_meta( $post_id, 'nn_veracity', true );

    echo "$post_id\t$pk\t$meta\n";
}
*/
