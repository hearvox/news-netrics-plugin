<?php
// https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=feed|gnews|links|count&site={domain}

/*
if ( isset( $_GET['site'] ) || isset( $_GET['api'] ) ) {
    $site = $_GET['site'];
    switch ( $_GET['api'] ) {
        case 'test':
            $xml  = 'yo2';
            break;
        case 'feed':
            $url  = 'http://' . $site;
            $xml  = get_xml_feed( $url );
            if ( ! $xml ) {
                $url = 'https://' . $site;
                $xml = get_xml_feed( $url );
            }
            break;
        case 'gnews':
            $url  = 'https://news.google.com/rss/search?hl=en-US&gl=US&ceid=US%3Aen&num=5&q=site%3A' . $site;
            $xml  = get_google_news_feed( $url );
            break;
        case 'links':
            $xml  = get_feed_links( $site );
            break;
        case 'count':
            $xml  = get_link_count( $site );
            break;
        default:
            break;
        }
} else {
    return false;
}

echo $xml;
*/

function get_xml_feed( $url ) {
    // libxml_use_internal_errors( true ); // Suppress warnings (invalid code in HTML).
    $request = file_get_contents( $url );

    if( $html = @DOMDocument::loadHTML( file_get_contents( $url ) ) ) {

        $xpath = new DOMXPath( $html );
        $feeds = $xpath->query( "//head/link[@href][@type='application/rss+xml']/@href" );

        $results = array();

        foreach($feeds as $feed) {
            // $results[] = $feed->nodeValue; // Array of RSS Urls.
        }

        return $feeds[0]->nodeValue;

    } else {
        return false;
    }
}

function get_google_news_feed( $url ) {
    $xml = $feed = $item_count = '';
    // Get external feed, if option value is an URL.
    $feed = simplexml_load_file( $url );

    // Insert external feed data into XML template.
    if ( isset( $feed->channel->item ) ) {
        // $item_count = count( $xml->channel->item );

        // Get items in XMl.
        foreach ( $feed->channel->item as $item ) {
            $links[] = $item->link;
        }

        return count( $links );
    } else {
        return false;
    }
}

function get_feed_links( $url ) {
    $xml = $item_count = '';
    $links = '';

    // Get external feed, if option value is an URL.
    $xml = simplexml_load_file( $url );
    // Insert external feed data into XML template.
    if ( isset( $xml->channel->item ) ) {
        // $item_count = count( $xml->channel->item );

        // Get latest article URLs.
        $i = 0;
        foreach ( $xml->channel->item as $item ) {
            $links .= (string) $item->link . '|' . (string) $item->title . "\n";
            $i++;
            if ( $i === 3 ) {
                break;
            }
        }

        return $links;

    } else {
        return false;
    }

}

function get_link_count( $url ) {
    $xml = $item_count = '';
    $links = array();

    // Get external feed, if option value is an URL.
    $xml = simplexml_load_file( $url );

    // Insert external feed data into XML template.
    if ( isset( $xml->channel->item ) ) {
        // $item_count = count( $xml->channel->item );

        // Get latest article URLs.
        foreach ( $xml->channel->item as $item ) {
            $links[] .= $item->link;
        }

        return count( $links );
    } else {
        return false;
    }
}
