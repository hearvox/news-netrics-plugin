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
 * Get all CPT posts (Publication).
 *
 * @since   0.1.0
 *
 * @param  int   $per_page  The number of post to return.
 * @param  int   $offset    The starting point in array.
 * @return array $query     Array of WP Post objects.
 */
function netrics_get_pub_posts( $per_page = 3000, $offset = 0 ) {
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
 * Add an dated error message to post meta, with name of function that threw error.
 *
 * @since   0.1.0
 *
 * @param  string $post_id  Post ID.
 * @return string $url      Post meta value
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
 * @link https://tommcfarlin.com/wp_remote_get/
 *
 * @param  string $url       The URL of the feed to retrieve.
 * @param  int    $timeout   Number of seconds to try before closing connection.
 * @return array  $response  The response from the URL; null if empty.
 */
function netrics_request_data( $url, $timeout = 10 ) {

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
            $response = netrics_curl( $url );
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
 * @param  string $url       To URL to which the request is being made
 * @return string $response  The response, if available; otherwise, null
 */
function netrics_curl( $url ) {

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
 * Get article items from post meta.
 *
 * @since   0.1.0
 *
 * @param  int    $post_id  Post ID.
 * @param  string $key      Post meta key with article URLs..
 * @return string $items    Article URLs (and titles).
 */
function netrics_get_pub_items( $post_id, $key = '' ) {
    $meta_key = ( $key ) ? $key : 'nn_articles_new';

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
 * @param  int   $per_page  The number of post to return.
 * @param  int   $offset    The starting point in array.
 * @return array $query     Array of WP Post objects.
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

/* ------------------------------------------------------------------------ *
 * API Calls
 * ------------------------------------------------------------------------ */
/**
 * Set month to use for API calls.
 *
 * @since   0.1.1
 *
 * @return string Month in format YYYYMM.
 */
function netrics_get_data_month() {
    return date( 'Y-m' );
    // return '201908';
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
 * @param  array  $query_ids  Array of Post IDs.
 * @param  string $strategy   PSI test: 'mobile' or 'desktop'.
 * @return array  $psi_data   Array of (selected) PSI test results.
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
 * @param  sting $url       URL of site to PSI test.
 * @return array $psi_data  Array of (selected) PSI test results.
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
 * For newly added publications or yearly AWIS data updates.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int   $post_id  Post ID.
 * @return array $awis     Array of (selected) AWIS site data.
 */
function netrics_api_call_awis( $post_id ) {
    // Alexa script (New Netrics customized) and API keys
    require_once( plugin_dir_path( __FILE__ ) . 'api/awis.php' );

    $awis = netrics_api_awis( $post_id );

    return $awis;
}

/**
 * Get publication's Alexa Web Info Service data.
 *
 * AWIS Global Rank, description, year online, etc..
 *
 * @since   0.1.1
 *
 * @param  int   $post_id  Default Post ID of Page for post meta.
 * @return array $awis     Array of (selected) AWIS site data.
 */
function netrics_get_awis_meta( $post_id ) {
    $nn_site = get_post_meta( $post_id , 'nn_site' ); // Returns unserialized array.

    if ( ! $nn_site ) {
        return;
    }

    $awis = array();
    $awis['desc']  = ( isset( $nn_site[0]['alexa']['desc']  ) && $nn_site[0]['alexa']['desc'] )
        ? '&mdash; ' . $nn_site[0]['alexa']['desc'] : '';
    $awis['rank']  = ( isset( $nn_site[0]['alexa']['rank'] ) && $nn_site[0]['alexa']['rank'] )
        ? number_format( floatval($nn_site[0]['alexa']['rank'] ) ) : '--';
    $awis['since'] = ( isset( $nn_site[0]['alexa']['since']  ) && $nn_site[0]['alexa']['since'] )
        ? date_parse_from_format( 'd-M-Y', $nn_site[0]['alexa']['since'] ) : false;
    $awis['year']  = ( $awis['since'] ) ? absint( $awis['since']['year'] ) : '--';
    $awis['links'] = ( isset( $nn_site[0]['alexa']['links']  ) && $nn_site[0]['alexa']['links'] )
        ? number_format( (int) $nn_site[0]['alexa']['links'] ) : '--';


    return $awis;
}


/* ------------------------------------------------------------------------ *
 * BuiltWith: Free API
 * ------------------------------------------------------------------------ */
/**
 * Get BuiltWith data for domain via API call.
 *
 * For newly added publications or yearly BuiltWith data updates.
 *
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int   $post_id  Post ID.
 * @return array $bw_data  Array of (selected) BuiltWith site data..
 */
function netrics_api_call_builtwith( $post_id, $api_key ) {
    // Alexa script (New Netrics customized) and API keys
    require_once( plugin_dir_path( __FILE__ ) . 'api/builtwith.php' );

    $bw_data = netrics_api_builtwith( $post_id, $api_key );

    return $bw_data;
}
