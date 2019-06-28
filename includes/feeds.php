<?php
/**
 * Get articles from RSS feeds and JSON API.
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/* ------------------------------------------------------------------------ *
 * Articles (get articles links and titles from Publication feeds)
 * ------------------------------------------------------------------------ */
/** Get
 *
 * @since   0.1.0
 *
 * @param int $query   Array of post IDs.
 * @return string $url Post meta value
 */
function netrics_get_feeds( $query ) {
    if ( ! isset( $query->posts ) ) {
        $query = netrics_get_pubs_ids( 2000 );
    }

    $success = array();
    foreach ( $query->posts as $post_id ) {
        $url = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file
        if ( ! $url ) {
            netrics_error( $post_id, 'nn_get_feeds>url' );
            continue;
        }

        $xml = newsstats_request_data( $url );
        if ( ! $xml ) {
            netrics_error( $post_id, 'nn_get_feeds>xml' );
            continue;
        }

        echo $url;
        $items = netrics_get_feed_items( $xml, $url );
        if ( ! $items ) {
            netrics_error( $post_id, 'nn_get_feeds>items' );
            continue;
        }

        print_r( $items );
        if ( $items || ! isset( $items[0]['url'] ) ) {
            $terms = netrics_save_feed_items( $post_id, $items );
            $success[$post_id] = $terms;
        } else {
            netrics_error( $post_id, 'nn_get_feeds>terms' );
            continue;
        }

        echo "$post_id: $terms";

    }

    print_r( $success );
    return $success;
}

/**
 * Get feed URL (XML or JSON) from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function netrics_get_feed_url( $post_id ) {
    $url = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file

    if ( wp_http_validate_url( $url ) ) {
        return $url;
    } else {
        return new WP_Error( 'url_invalid', __( "URL does not validate." ) );
    }
}

/*
function doer_of_stuff() {
    return new WP_Error( 'broke', __( "I've fallen and can't get up", "my_textdomain" ) );
}

$return = doer_of_stuff();
if( is_wp_error( $return ) ) {
    echo $return->get_error_message();
}
*/



/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function netrics_get_feed_items( $xml, $url ) {
    $items   = array();
    $xml_obj = netrics_get_feed_object( $xml, $url ); // Make SimpleXML object.

    // If SimpleXML fails, try parsing XML as string.
    if ( ! $xml_obj ) {
        $items = netrics_parse_xml( $xml );
        return $items;
    }

    // Extract items from SimpleXML.
    if ( isset( $xml_obj->channel->item ) ) {
        // $item_count = count( $xml->channel->item );
        // Get latest article links, URLs and titles.
        $i = 0;
        foreach ( $xml_obj->channel->item as $item ) {
            $items[$i]['url']   = (string) $item->link;
            $items[$i]['title'] = (string) $item->title;
            $i++;
            if ( $i === 3 ) { // Need only 3 articles
                break;
            }
        }

        return $items;

    } else {
        return false;
    }
}

/** Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function netrics_get_feed_object( $xml ) {

    libxml_use_internal_errors( true );
    $xml_obj = simplexml_load_string( $xml, $url );
    if ( $xml_obj === false ) {

        // If it fails, try simplexml_load_file (works for some BLOX feeds).
        libxml_clear_errors();
        /* foreach ( libxml_get_errors() as $error ) { echo " | ", $error->message; } */
        libxml_use_internal_errors( true );
        $xml_obj = simplexml_load_file( $url ); // Work for some BLOX feeds.

    }

    return $xml_obj;

}

/**
 * Parse XML RSS feed for <items>s, <title>s, and <links>s.
 *
 * For when SimpleXML fails.
 *
 * @since   0.1.0
 *
 * @param string  $url       RSS feed url.
 * @return string $articles  First element of exploded array.
 */
function netrics_parse_xml( $xml ) {
    $xml_arr = explode( '<item>', $xml ); // Make array of XML items.

    if ( is_array( $xml_arr ) ) {

        $items = array();
        array_shift( $xml_arr ); // Remove first element (<channel>).
        foreach ($xml_arr as $key => $item) {

            $items[$key]['title'] = netrics_get_string_between( '<title>', '</title>', $item );
            $items[$key]['url']   = netrics_get_string_between( '<link>', '</link>', $item );
            if ( 2 <= $key ) { // Need only 3 articles.
                break;
            }

        }
        return $items;

    } else {
        return false;
    }
}

/**
 * Get string between two strings (e.g., <title>URL</title>).
 *
 * @since   0.1.0
 *
 * @param string  $content  Content to explode into array.
 * @param string  $start    Start of array element.
 * @param string  $stop     End of array element.
 * @return string $str      First element of exploded array.
 */
function netrics_get_string_between( $start, $stop, $content ) {
    $str = explode( $start, $content );

    if ( isset( $str[1] ) ) {
        $str_btwn = explode( $stop, $str[1] );
        return $str_btwn[0];
    } else {
            return false;
    }
}

/**
 * Get
 *
 * print_r( newsstats_save_post_articles( 1234 ) );
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function netrics_save_feed_items( $post_id, $items, $meta_key = 'nn_articles_201906', $term_id = 6216  ) {
    if ( $items && isset( $items[0]['url'] ) ) { // Add post_meta and set term.
        update_post_meta( $post_id, $meta_key, $items, true );
        $terms = wp_set_post_terms( $post_id, $term_id, 'flag', true ); // Term: '201906'.
    } else {
        return false;
    }

    return $terms;
}

/**
 * Parse JSON feed for <'title's, <title>s, and <links>s.
 *
 * For when json_decode() fails.
 *
 * @since   0.1.0
 *
 * @param string  $url       JSON feed url.
 * @return string $articles  First element of exploded array.
 */
function netrics_parse_json_items( $post_id ) {
    $url      = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file
    $json     = newsstats_request_data( $url );
    $posts = explode( '"post",', $posts );

    if ( is_array( $posts ) && count( $posts ) > 1 ) {

        array_shift( $posts ); // Remove first element, before link/title.
        $items = array();
        foreach ( $posts as $key => $post ) {

            $link  = netrics_get_string_between( '"link":"', '",', $post );
            $title = netrics_get_string_between( '"title":{"rendered":"', '"},', $post );

            $items[$key]['title'] = $title;
            $items[$key]['url']   = stripslashes( $link );
            if ( 2 <= $key ) { // Need only 3 articles.
                break;
            }

        }
    } else {
        // $links = array( 'error' => 'Error: fail newsstats_parse_json_items()' );
    }

    return $items;
}




/* ------------------------------------------------------------------------ *
 * OLD FUNCTIONS
 * ------------------------------------------------------------------------ */
/**
 * Get feed URL (XML or JSON) from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_get_feed_articles( $post ) {

    global $articles_meta, $articles_term;

    $post_id = $post->ID;
    $links = newsstats_feed_links( $post );
    if ( $links ) { // Add post_meta and set term.
        add_post_meta( $post_id, $nn_articles_meta, $links, true);
        $term_ids = wp_set_post_terms( $post_id, $articles_term, 'flag', true );

    } else { // Set term.
        $term_ids = wp_set_post_terms( $post_id, array( 6176 ), 'flag', true ); // Term: 'fail'.
    }

    return $term_ids;
}

/**
 * Get feed URL (XML or JSON) from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_feed_links( $post ) {

    $links   = array();
    $post_id = $post->ID;
    $url     = newsstats_get_rss_url( $post_id ); // RSS file

    $xml   = ( $url ) ? newsstats_get_xml( $url ) : false;
    $links = ( $xml ) ? newsstats_get_xml_links( $xml ) : false;

    if ( ! $links ) { // Try parsing with string-between function.
        $links = newsstats_parse_xml_items( $url );
    }

    return $links;
}

/**
 * Get feed URL (XML or JSON) from post meta.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return string $url Post meta value
 */
function newsstats_get_rss_url( $post_id ) {

    $url = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file

    return $url;

}

/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function newsstats_get_xml( $url ) {

    $body   = newsstats_request_data( $url );
    $xml    = '';

    libxml_use_internal_errors( true );
    $xml = simplexml_load_string( $body );
    if ( $xml === false ) {

        // if it fails, try simplexml_load_file (works for some BLOX feeds).
        libxml_clear_errors();
        /* foreach ( libxml_get_errors() as $error ) { echo " | ", $error->message; } */
        libxml_use_internal_errors( true );
        $xml = simplexml_load_file( $url ); // Work for some BLOX feeds.
        libxml_clear_errors();

        // if that fails, try parse function.
        // newsstats_parse_xml_items( $url )

    }

    return $xml;

}



/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function newsstats_get_xml_links( $xml ) {
    $links = array();

    // Insert external feed data into XML template.
    if ( isset( $xml->channel->item ) ) {
        // $item_count = count( $xml->channel->item );

        // Get latest article URLs.
        $i = 0;
        foreach ( $xml->channel->item as $item ) {

            $links[$i]['url']   = (string) $item->link;
            $links[$i]['title'] = (string) $item->title;
            $i++;
            if ( $i === 3 ) { // Need only 3 articles
                break;
            }

        }

        return $links;

    } else {

        return false;

    }

    return $links;
}

/**
 *
 */
function newsstats_get_feed_custom_bg( $url ) {

    $links = array();
    $api   = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';
    $row   = 0;
    if ( ( $handle = fopen( $api . $url, 'r' ) ) !== FALSE ) { // Read-only

        while ( ( $data = fgetcsv( $handle, 1000, '|' ) ) !== FALSE ) { // Get CSV data

            // Make array from CSV data
            $links[$row]['url']   = $data[0];
            $links[$row]['title'] = $data[1];
            $row++;

        }
        fclose( $handle );
    }

    return $links;

}


/**
 * Get
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function newsstats_save_json_articles( $post_id ) {

    global $nn_articles_meta;
    $links = array();
    $rss   = get_post_meta( $post_id, 'nn_pub_rss', true ); // RSS file
    $json  = file_get_contents( $rss );

    $articles = ( $json ) ? json_decode( $json ) : false;

    if ( $articles ) {
        // Get latest article URLs.
        $i = 0;
        foreach ( $articles as $article ) {

            $links[$i]['url']   = (string) $article->link;
            $links[$i]['title'] = (string) $article->title->rendered;
            $i++;
            if ( $i === 3 ) { // Need only 3 articles
                break;
            }

        }

    }

    // if that fails, try getting string between..


    if ( $links ) { // Add post_meta and set term.

        add_post_meta( $post_id, $nn_articles_meta, $links, true);
        $term_ids = wp_set_post_terms( $post_id, 6178, 'flag', true ); // Term: 'fail'.

    } else {
        $links = array( $post_id . ' / ' . date('H:i:s') );
    }

    return $links;

}

/**
 * For when above XML/JSON parsers fail.
 *
 * @since   0.1.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function newsstats_parse_feed( $url ) {

    $body     = newsstats_request_data( $url );
    $articles = array();
    $items = array( 'Error: failed newsstats_parse_feed()' );

    if ( $body ) {
        $items = newsstats_get_string_between( $body, '<item>', '</item>' );

        if ( $items ) {

            foreach ($items as $key => $item) {
                $articles[$key]['title'] = newsstats_get_string_between( $item, '<title>', '</title>' );
                $articles[$key]['url'] = newsstats_get_string_between( $item, '<link>', '</link>' );
                if ( 3 > $key ) {
                    break;
                }

            }

        }

    }

    return $items;

}

