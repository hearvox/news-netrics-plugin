<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

// Write time.
echo date( 'His' ) . '<hr>';


/* Time process */
$time_start = microtime(true);

// Sleep for a while
usleep(100);

$time_end = microtime(true);
$time = $time_end - $time_start;


echo time() . '<hr>';
$time_start = microtime(true);

// $pub_data  = newsstats_get_pub_data();

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "$time<hr>";


/* Check XML */
$rss      = get_post_meta( $post->ID, 'nn_pub_rss', true ); // RSS file
$links    = array();

libxml_use_internal_errors(true);
$xml = simplexml_load_file( $rss );

if ($xml === false) {
    echo "Failed loading XML: ";
    foreach(libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
} else {
    print_r($xml);
}

?>

<h2 class="widget-title"><?php esc_html_e( 'Regions with Dailies (state > county> city)', 'newsstats' ); ?></h2>
<ul style="margin-left: 0.5rem;">
<?php

/* Tax filters */
wp_list_categories( array(
    'taxonomy'   => 'region', // cms|owner|region
    'child_of'   => 0,
    'order'      => 'ASC', // ASC|DESC
    'orderby'    => 'name', // name|count(|slug|ID)
    'show_count' => 1,
    'title_li'   => '',
    'number'     => 4000, // 200|300|4000
    'parent'     => 0,
) );


$url = 'https://www.yumasun.com/search/?f=rss&t=article&c=news&l=50';

/* Screenshots (mShots) */
// @link https://github.com/Automattic/mShots
$url = 'https://s.wordpress.com/mshots/v1/http%3A%2F%2Fdailyadvocate.com/?w=140&h=105';

/* Get article URLs */
$articurls = newsstats_get_feed_items( $url );
print_r( $articurls );



/** @var array|WP_Error $response */


$xml  = simplexml_load_string( $body );

function newsstats_get_feed_items( $url ) {
    $xml = $item_count = '';
    $links = array();

    // Get external feed, if option value is an URL.
    $xml = simplexml_load_file( $url );

    $item_count = count( $xml->channel->item );

    // Insert external feed data into XML template.
    if ( isset( $xml->channel->item ) ) {
        // $item_count = count( $xml->channel->item );

        // Get latest article URLs.
        $i = 0;
        foreach ( $xml->channel->item as $item ) {
            $links[$i]['title'] .= $item->title;
            $links[$i]['url'] .= $item->link;
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



$url = 'https://www.dailyadvocate.com/feed/';

/** @var array|WP_Error $response */
$response = wp_remote_get( $url );

if ( is_array( $response ) && ! is_wp_error( $response ) ) {
    // $headers = $response['headers']; // array of http header lines
    // $body    = $response['body']; // use the content
    // $code = wp_remote_retrieve_response_code( $response );
    // $msg  = wp_remote_retrieve_response_message( $response );
    // $head = wp_remote_retrieve_headers( $response );

    // Return: The body of the response. Empty string if no body or incorrect parameter given.
    $body = wp_remote_retrieve_body( $response );
    $links = newsstats_get_feed_links( $body );
    print_r( $links );
} else {
    print_r( $response );
}

for ($i = 20; $i <= 25; $i++) {
    /** @var array|WP_Error $response */
    $response = wp_remote_get( $pub_data[$i]['pub_rss'] );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $body  = wp_remote_retrieve_body( $response );
        $links = newsstats_get_feed_links( $body );
        print_r( $links );
    } else {
        echo $pub_data[1]['pub_rss'] . '<br>';
        print_r( $response );
    }
    echo '<hr>';
}




$query_args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order' => 'ASC',
    // 'nopaging'  => true,
    'posts_per_page' => 10, // For tests.
    'offset'    => 200,
    // 'update_post_meta_cache' => false,
    // 'update_post_term_cache' => false,
    // 'fields' => 'ids',
);
$query_posts = new WP_Query( $query_args );

// print_r( $query_posts->posts );
$time_start = microtime(true);

foreach ( $query_posts->posts as $post ) {
    // API file
    $api = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';
    $rss = get_post_meta( $post->ID, 'nn_pub_rss', true ); // RSS file

    $response = wp_remote_get( $api . urldecode( $rss ) );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $body  = wp_remote_retrieve_body( $response );
        print_r( json_decode( $body ) );
    } else {
        echo ( $rss );
    }
}

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<hr>$time";

add_post_meta( $post_id, $meta_key, $meta_value, $unique = false )




$pub_data = newsstats_get_all_publications();
echo $pub_data[1]['pub_rss'] . '<hr>';
// $links = newsstats_get_feed_items( $pub_data[1]['pub_rss'] );
// print_r( $links );

// API file
// $pub_data = newsstats_get_all_publications();
$api = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';
$url = 'http://www.alliancetimes.com/search/?f=rss&t=article&l=10';

$response = wp_remote_get( $api . urldecode( $url ) );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $body  = wp_remote_retrieve_body( $response );
        print_r( json_decode( $body ) );
    } else {
        echo $pub_data[1]['pub_rss'] . '<br>';
        print_r( $response );
    }
    echo '<hr>';

    // API file
    $api = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';
    $rss = get_post_meta( $post->ID, 'nn_pub_rss', true ); // RSS file

    $response = wp_remote_get( $api . urldecode( $rss ) );
    $body  = wp_remote_retrieve_body( $response );

    if ( is_array( $response ) && ! is_wp_error( $response ) && $body ) {
        $data  = json_decode( $body, JSON_OBJECT_AS_ARRAY );
        print_r( $data );
    } else {
        echo ( $rss . "\n" );
    }


echo time() . '<hr>';
$query_args = array(
       'post_type' => 'publication',
        'orderby'   => 'title',
        'order' => 'ASC',
        // 'nopaging'  => true,
        'posts_per_page' => 10, // For tests.
        'offset'    => 200,
        // 'update_post_meta_cache' => false,
        // 'update_post_term_cache' => false,
        // 'fields' => 'ids',
);
$query_posts = new WP_Query( $query_args );

// print_r( $query_posts->posts );
$time_start = microtime(true);

foreach ( $query_posts->posts as $post ) {

    // API file
    $api = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';
    $rss = get_post_meta( $post->ID, 'nn_pub_rss', true ); // RSS file

    $response = wp_remote_get( $api . urldecode( $rss ) );
    $body  = wp_remote_retrieve_body( $response );

    if ( is_array( $response ) && ! is_wp_error( $response ) && $body ) {
        $data  = json_decode( $body, JSON_OBJECT_AS_ARRAY );
        print_r( $data );
    } else {
        echo ( $rss . "\n" );
    }
}

/* Get pub data */


$pub_data  = newsstats_get_pub_data();
$post_data = newsstats_get_pub( $post_id );
$pub_data  = newsstats_set_pub_data();







function newsstats_get_feed_links( $post ) {
    $rss      = get_post_meta( $post->ID, 'nn_pub_rss', true ); // RSS file
    $links    = array();
    $response = wp_remote_get( $rss );

    if ( is_wp_error( $response ) ) {
        print_r( $response );
        echo '<hr>';
        return;
    }

    $body = wp_remote_retrieve_body( $response );
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string( $body );

    if ( $xml === false ) {
        echo "Failed loading XML: $rss\n";
        foreach ( libxml_get_errors() as $error ) {
            echo "<br>", $error->message;
        } // foreach errors
        echo '<hr>';
        return;
    }

    if ( isset( $xml->channel->item ) ) {
        $item_count = count( $xml->channel->item );

        if ( $item_count ) {
            echo $item_count . "<hr>\n";
            return;
        } else {
            echo '$item_count false: ' . "$rss<hr>\n";
            return;
        }
    }

    echo "???<hr>\n";

}

// Total post -type posts:
echo wp_count_posts( 'publication')->publish );

// 'update_post_meta_cache' => false,
// 'update_post_term_cache' => false,
// 'fields' => 'ids',


echo time() . '<hr>';
$post_id = 4933;
$url = 'http://www.sunnysidesun.com/search/?f=rss&t=article&l=10';

$api = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/api/rss-url.php?api=links&site=';

$response = newsstats_curl( $api . $url );


echo $response;


echo time() . '<hr>';
$time_start = microtime( true );

$args = array(
    'post_type' => 'publication',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'slug',
            'terms'    => 'xml',
        ),
    ),
    'posts_per_page' => 25,
    'offset'         => 275,

);
$query = new WP_Query( $args );

foreach ( $query->posts as $post ) {
    print_r( newsstats_feed_links( $post ) );
}

$time_end = microtime( true );
$time = $time_end - $time_start;
echo round( $time, 2 );

/* JSON file */
$file = 'https://news.pubmedia.us/wp-content/plugins/news-netrics/includes/gpgspeed/gpgspeed-mobi.json';
$json = newsstats_request_data( $$file );

/* Headers only */
$head = wp_remote_head( $api . $url, array( 'timeout' => 60 ) );
print_r( $head );

echo date( 'His' ) . '<hr>';
$post_id = 5176;
$articles = newsstats_get_post_articles( $post_id );

$api = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?strategy=mobile&fields=analysisUTCTimestamp%2ClighthouseResult(audits%2Ccategories%2Fperformance%2Fscore)&key=AIzaSyDFM3aDEdbfRIMMQQRhPDmF25A01dENS70&url=';





print_r( $articles );

// Googl Pagespeed Insights plugin
if ( ! empty( $result ) ) {
            if ( isset( $result['responseCode'] ) && $result['responseCode'] >= 200 && $result['responseCode'] < 300 ) {
}


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
            'terms'    => 'gpg0m',
            'operator' => 'NOT IN',
        ),
    ),
    'posts_per_page' => 100,
    'offset'         => 0,
);
$query_posts = new WP_Query( $query_args );

foreach ( $query_posts->posts as $post ) {
    print_r( newsstats_feed_links( $post ) );
}


$accessKeyId     = 'AKIAJBV4OWGCGHDIL3PQ';
$secretAccessKey = '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';
$site = 'ocregister.com';

include_once( '/home/dh_a332ee/news.pubmedia.us/wp-content/plugins/news-netrics/api/awis-query-php/urlinfo.php' );

$urlInfo  = new UrlInfo($accessKeyId, $secretAccessKey, $site);
$response = $urlInfo->getUrlInfo();

print_r( $response );

function newsstats_do_pagespeed( $post, $item = 0, $strategy = 'mobile' )  {}

// https://developer.wordpress.org/reference/functions/add_query_arg/
// https://pippinsplugins.com/the-add_query_arg-helper-function/


// BuiltWith
$data = newsstats_call_bw_api( 4033 );
$bw_arr = array();

foreach ( $data->groups as $group ) {
    $bw_arr[$group->name] = $group->live;
}

var_dump( $bw_arr );


// Parse feeds.
echo date( 'His' ) . '<hr>';
$url      = 'https://www.amarillo.com//news?template=rss&mime=xml';
$content  = newsstats_request_data( $url );
$start    = '<item>';
$stop     = '</item>';
$articles = array();
$items    = explode( '<item>', $content );
foreach ($items as $key => $item) {
    $articles[$key]['title'] = newsstats_get_string_between( $items[1], '<title>', '</title>' );
    $articles[$key]['url']   = newsstats_get_string_between( $items[1], '<link>', '</link>' );
}

print_r( $articles );


$args = array(
    'post_type' => 'publication',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'slug',
            'terms'    => 'check',
        ),
    ),
    'posts_per_page' => 50,
);
$query = new WP_Query( $args );
foreach ( $query->posts as $post ) {
    $post->ID = $post_id;
    $url = get_post_meta( $post_id, 'pub_rss', true );
    echo $url . "\n";
}

$args = array(
    'post_type' => 'publication',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'slug',
            'terms'    => 'fail',
        ),
    ),
    'posts_per_page' => 50,
);
$query = new WP_Query( $args );
foreach ( $query->posts as $post ) {
    $post_id  = $post->ID;
    $url = get_post_meta( $post_id, 'nn_pub_rss', true );
    $articles = newsstats_parse_xml_items( $url );
    // print_r( $url );
    // print_r( $articles );
    update_post_meta( $post_id, 'nn_articles_201905', $articles );
    print_r( get_post_meta( $post_id, 'nn_articles_201905', true ) );
}




$args = array(
    'post_type' => 'publication',
    // 'post__in'  => array( 4045, 4308, 4392, 4521, 5057 ),
    'post__in'  => array( 4045 ),
    'posts_per_page' => 10,
);
$query = new WP_Query( $args );

$args = array(
    'post_type' => 'publication',
    'post__in'  => array( 4045, 4308, 4392, 4521, 5057 ),
    // 'post__in'  => array( 4045 ),
    'posts_per_page' => 10,
);
$query = new WP_Query( $args );

foreach ( $query->posts as $post ) {
    $post_id  = $post->ID;
    $url = get_post_meta( $post_id, 'nn_pub_rss', true );
    $body     = newsstats_request_data( $url );
    $items = explode( '"post",', $body );
    if ( is_array( $items ) && count( $items ) > 1 ) {

        array_shift( $items ); // Remove first element, before link/title.
        $articles = array();
        foreach ($items as $key => $item) {

            $link  = newsstats_get_string_between( '"link":"', '",', $item );
            $title = newsstats_get_string_between( '"title":{"rendered":"', '"},', $item );

            $articles[$key]['title'] = $title;
            $articles[$key]['url']   = stripslashes( $link );
            if ( 2 <= $key ) { // Need only 3 articles.
                break;
            }

        }

        update_post_meta( $post_id, 'nn_articles_201905', $articles );

    }

}

// <title><![CDATA[Quick Pics: Gone Fishin': Reeling in the evening catch]]></title>

// Update manually:
$post_id = 4146;
$items = array();
// Replace "" with &quot;, then:
$items[0]['title'] = "";
$items[0]['url']   = "";
$items[1]['title'] = "";
$items[1]['url']   = "";
$items[2]['title'] = "";
$items[2]['url']   = "";

$meta = netrics_save_feed_items( $post_id, $items );
print_r( $meta );
print_r( get_post_meta( $post_id, 'nn_articles_201907', true ) );

// Send/get errors.
$empty = 'empty';
$error = new WP_Error();
if ( empty( $xxx ) ) {
        $error->add( 'empty', 'X is ' . $empty, 2 );
}

if ( empty( $yyy ) ) {
        $error->add( 'empty', 'Y is empty.', 1 );
}

if ( ! empty( $error ) ) {
        $error->add( 'empty-not', 'Error is not empty.', 3 );
}

if ( ! empty( $error->get_error_message() ) ) {
    print_r( $error );
}

function echo_fn(){ echo __FUNCTION__; }
echo_fn();
/*
WP_Error Object
(
    [errors] => Array
        (
            [empty] => Array
                (
                    [0] => X is empty
                    [1] => Y is empty.
                )

            [empty-not] => Array
                (
                    [0] => Error is not empty.
                )

        )

    [error_data] => Array
        (
            [empty] => 1
            [empty-not] => 3
        )

)
*/

// Get articles 2019-06
$post_id = 4027;
$url = get_post_meta( $post_id, 'nn_pub_rss', true );
$xml = newsstats_request_data( $url );
$items = netrics_get_feed_items( $xml );
$terms = netrics_save_feed_items( $post_id, $items );

print_r( $terms );
print_r( $items );

// Run random test.
$rand_posts = get_transient( 'netrics_rand' );
foreach ( $rand_posts as $post ) {
    $post_id = $post->ID;
    $items = newsstats_get_post_articles( $post_id );
    $url   = ( isset( $articles[0]['url'] ) ) ? $articles[$item]['url'] : false;

    if ( wp_http_validate_url( $url ) ) {
        // Run Pagespeed, save data in post_meta, then set term.
        $pagespeed = newsstats_get_pagespeed( $url );
        if ( $pagespeed ) {
            $rand_test['pagespeed'] = $pagespeed; // Add data.
            // set_transient( 'netrics_test_sun_night', $articles );
        } else {
            // $rand_test['pagespeed']['error'] = 'No Pagespeed results.';
        }

    } else {
        // $rand_test['pagespeed']['error'] = 'Invalid URL';
    }

    $rand_test['pagespeed']['id'] = $post_id;

    sleep( 1 ); // Works better with a pause.

    return $pagespeed;

}

// AWIS
$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order' => 'ASC',
    'posts_per_page' => 2000, // For tests.
    'meta_key' => 'nn_site',
    // 'fields' => 'ids',
);
$query = new WP_Query( $args );
foreach ( $query->posts as $post ) {
    $meta = get_post_meta( $post->ID, 'nn_site', true );
    add_post_meta( $post->ID, 'nn_rank', $meta['alexa']['rank'], true );
    echo $post->ID . ' ' . get_post_meta( $post->ID, 'nn_rank', true ) . "\n";
}

// Plugins and themes: Replace Alexa rank with 'nn_rank'
// Plugins and themes: Replace 'nn_pub_circ_ep' with 'nn_circ'
delete_post_meta( $post->ID, 'nn_pub_circ_ep' );
delete_post_meta( $post->ID, 'nn_pub_circ' );



// Get/upload/set Featured Image
$post_id = 4027;
// $image_url = 'https://s.wordpress.com/mshots/v1/' . urlencode( get_post_meta( $post_id, 'nn_pub_url', true ) ) . '?w=800&h=600';

$image = file_get_contents('http://www.affiliatewindow.com/logos/1961/logo.gif');
file_put_contents('./myDir/myFile.png', $image);

$image_url = 'https://hearingvoices.com/images/radface/radface_black1.jpg';
$meta_id   = 0;

// Upload image.
// $media_id = media_sideload_image( $image_url, $post_id, get_post_meta( $post_id, 'nn_pub_site', true ) . ' homepage', 'id' ); // attachment ID
$media_id = media_sideload_image( $image_url, $post_id, ' homepage', 'id' );
print_r( $media_id );

// Set as Featured Image
if( ! empty( $media_id ) && ! is_wp_error( $media_id ) ) {
    $meta_id = set_post_thumbnail( $post_id, $media_id );
    print_r( $meta_id );
}

echo "Post: $post_id  /  FeatImg: $meta_id $image_url\n"; // Record success/fail.
?>




Here's the metircs I'm planning on grabbing from Google: Speed Index, Time to Interactive, Total Bytes, DOM elements, Performance Score. You want anything else? More info:
https://docs.google.com/document/d/1-YPJTi5t6c8yJJZg7Q9ERYlG1mrg4qLBUtFNgQF9o98/edit


https://www.googleapis.com/pagespeedonline/v5/runPagespeed?strategy=mobile&fields=analysisUTCTimestamp%2ClighthouseResult(audits%2Ccategories%2Fperformance%2Fscore)&key={YOUR_API_KEY}&url=

https://console.developers.google.com/apis/credentials?pli=1
https://console.developers.google.com/apis/credentials?pli=1&project=project-id-5526517909791426511&folder&organizationId

https://developers.google.com/speed/docs/insights/v5/about
https://developers.google.com/apis-explorer/#p/pagespeedonline/v5/

https://developers.google.com/chart/interactive/docs/gallery/gauge
https://developer.mozilla.org/en-US/docs/Learn/HTML/Howto/Use_data_attributes

Fail:
4839 https://rrdailyherald.com/

4146 https://chicago.suntimes.com/rss/index.xml
<entry> and <link href=""> (vs. <item<link>)
Manualy entered:




Ask:
* GitHub
* Mizzou server
* Pull WikipediaL circ, founded
* Places taxonomy
* Filmstrip (can't average)

@todo
* Cache screenshot
* Import Land area
* add score to map
* 5193 zanesville
* Fix: reg tax term (doesn't work with array/object)

done:
* Replace 201906 with '2019-06'


