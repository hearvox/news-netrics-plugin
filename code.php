<?php
/*
WP-CLI file:
ssh wp_wugkzz@news.pubmedia.us.dream.website
t-2g7SJB
cd news.pubmedia.us
alias nn-file='wp eval-file /home/wp_wugkzz/news.pubmedia.us/wp-content/plugins/news-netrics/code.php'
nn-file

// Git and GitHub
/home/wp_wugkzz/news.pubmedia.us/wp-content/plugins/news-netrics
/home/wp_wugkzz/news.pubmedia.us/wp-content/themes/newsstats
nn-p
nn-t

git add .
git commit -m "New Pasges: Regions, Results (front), and Homepages"
git push -u origin master
islands plotters polled

settings:
git config -l


nn_articles['YYYY-MM'] = nn_articles_201905 and nn_articles_201905

https://help.dreamhost.com/hc/en-us/articles/216445317-How-do-I-set-up-a-Git-repository-
git log --oneline

https://help.dreamhost.com/hc/en-us/articles/115000676991-Pushing-your-DreamHost-Git-repository-to-GitHub
https://github.com/hearvox/news-netrics-theme
https://github.com/hearvox/news-netrics-theme.git
https://github.com/hearvox/news-netrics-plugin.git

// Make Alias example combining commands:
alias check-all='wp core check-update && wp plugin list --update=available && wp theme list --update=available'
Print all alias commands:
alias

// Run flag term:
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

// Run specific posts:
$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'posts_per_page' => 10,
    'offset'         => 0,
    'post__in'   => array( 4579, 4978 ),
);


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

$query = new WP_Query( $args );
newsstats_api_calls( $query, 'awis' );

Or:
foreach ( $query->posts as $post ) {
    newsstats_do_pagespeed( $post );
}

// Run Pagetest (set query NOT_IN flag, then articles item#, strategy, flag):
$query = newsstats_get_pagespeed_posts( 400 );
newsstats_run_pagespeed_posts( $query );

// Run Alexa Web Info Service (AWIS) API
$query = newsstats_get_pub_posts( 400, 800 );
newsstats_api_calls( $query, 'awis' );

// Run BuiltWith Free API
$query = newsstats_get_pub_posts( 500, 0 );
newsstats_api_calls( $query, 'bw' );

// Test BW JSON error:
$json = '{"Errors":[{"Lookup":null,"Message":"Domain not in our system. Paid for lookups would return something.","Code":-10}],"NextOffset":null,"Results":null}';
$arr = json_decode( $json );
print_r( $arr->Errors[0]->Message );

// Test specific posts:
$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'posts_per_page' => 500,
    'offset'         => 1000,
);
$query = new WP_Query( $args );

// Update post meta
foreach ($query->posts as $post) {

    $nn_articles = get_post_meta( $post->ID, 'nn_articles_201905', true );
    $i = 0;
    foreach ($nn_articles as $key => $article) {

        if ( isset( $article['pagespeed']['date'] ) ) {

            unset( $nn_articles[$key]['pagespeed']['date'] );
            $nn_articles[$key]['pagespeed']['date'] = '2019-05';
            $i++;

        }

    }


    if ( $i ) {
        update_post_meta( $post->ID, 'nn_articles_201905', $nn_articles );
    }

    print_r( get_post_meta( $post->ID, 'nn_articles_201905', true ) );

}



Error:
https://api.builtwith.com/free1/api.json?KEY=d0d0d4bd-044f-4a33-bd2f-c28e3a9a1415&LOOKUP=
4075 bangordailynews.com
4146 chicago.suntimes.com
4257 dallasnews.com
4717 ocregister.com
4719 ohio.com
Trashed:
4417 heraldnews.suntimes.com
4687 nj.comstarledger
Fixed:
4579 ssnewstelegram.com
4978 thecouriertimes.com

// Update specific post's meta:
$test = array( 4075, 4146, 4257, 4717, 4719  );
foreach ( $test as $id ) {
    $nn_site = get_post_meta( $id, 'nn_site', true );
    $nn_site['builtwith']['error'] = 1;
    update_post_meta( $id, 'nn_site', $nn_site );
    print_r( get_post_meta( $id, 'nn_site', true ) );
}


// Bot?
https://news.pubmedia.us/publication/americustimesrecorder-com/
Test (91):
https://www.americustimesrecorder.com/2019/06/08/joni-woolf-picnic-foods-for-summertime-events/

https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fwww.americustimesrecorder.com%2F2019%2F06%2F08%2Fjoni-woolf-picnic-foods-for-summertime-events%2F

$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'slug',
            'terms'    => 'redo1905',
        ),
    ),
    'posts_per_page' => 100,
    'offset'         => 2,
);
$query = new WP_Query( $args );
newsstats_run_pagespeed_posts( $query );

echo date( 'His' ) . '<hr>';

// Count pagespeed errors for each pub.
foreach ($query->posts as $key => $post) {

    $errors = 0;
    $links = newsstats_get_post_articles( $post->ID );
    foreach ( $links as $key => $link ) {

        if ( $link['pagespeed']['error'] ) {
            $errors = $errors + 1;
            $term_ids = wp_set_post_terms( $post->ID, 'redo' . $key, 'post_tag', true );
        }

    }

    echo $post->ID . ': ' . $errors . "\n";
    $term_ids = wp_set_post_terms( $post->ID, $errors . 'err', 'post_tag', true );

}




// Run flag term:
$args = array(
    'post_type' => 'publication',
    'p'   => 4937,
);
$query = new WP_Query( $args );

foreach ( $query->posts as $post ) {
    // print_r( get_post_meta( 4937, 'nn_articles_201905', true ) );
    // print_r( $post );
    newsstats_do_pagespeed( $post );
}

//--------------------
// Get Months's artciles from RSS Feed (make new flag and update term# below)
$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 500,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => array( 6221, 6175, 6172 ), // '201907', 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),

);
$query = new WP_Query( $args );

netrics_get_feeds( $query );

// $query = netrics_get_pubs_ids( 500, 900 );

//--------------------
// Get articles from JSON posts.
$json = array( 4045, 4308, 4339, 4392, 4521, 5057, 5083 );
foreach ( $json as $post_id ) {
    $items = netrics_parse_json_items( $post_id );
    print_r( $items );
    $run = netrics_save_feed_items( $post_id, $items  );
    print_r( $run );
}

// Change feed URLs for (2019-06):
https://news.pubmedia.us/publication/?p=4092
https://bentoncourier.com/rss.xml
(Was Drupal) new: https://www.bentoncourier.com/search/?f=rss&t=article&l=10

https://news.pubmedia.us/publication/?p=4146
https://chicago.suntimes.com/rss/index.xml
manual (Chorus): <entry>

https://news.pubmedia.us/publication/?p=4515
http://latrobebulletinnews.com/rss.xml
(Was Drupal) new WP: https://www.latrobebulletinnews.com/feed/
JSON: http://latrobebulletinnews.com/wp-json/wp/v2/posts/

https://news.pubmedia.us/publication/?p=4537
https://lmtribune.com/search/?f=rss&t=article&c=news&l=10
new: https://lmtribune.com/search/?f=rss&t=article&l=10

https://news.pubmedia.us/publication/?p=4579
https://www.ssnewstelegram.com/search/?f=rss&t=article&l=10 (404)
(Was BLOX) new Drupal: https://www.ssnewstelegram.com/rss.xml

https://news.pubmedia.us/publication/?p=4968
https://www.theadvocate.com/search/?f=rss&t=article&c=news&l=10
new: https://www.theadvocate.com/search/?f=rss&t=article&l=10

https://news.pubmedia.us/publication/?p=5105
https://www.tribuneledgernews.com/search/?f=rss&t=article&c=news&l=10
new: https://www.tribuneledgernews.com/search/?f=rss&t=article&l=10

// Update Circulation:
$circs_trans = get_transient( 'newsnetrics_circs' );
foreach ( $circs_trans as $id => $circ ) {
    $meta = update_post_meta( $id, 'nn_pub_circ_ep', $circ );
    echo "$id $circ $meta\n";
}

Score: 18 | Speed/TTI(s): 11.2/26.2 | Size: 4.0 MB | DOM: 1,650 | Requests: 319

// Last element:
print_r( $query->posts[ count( $query->posts ) - 1 ]  );
// or:
print_r( end( $query->posts )  );
reset( $query )

// Find file where functin defined:
$reflFunc = new ReflectionFunction('netrics_get_pubs_pagespeed_query');
print $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();

$query = netrics_get_pubs_ids( 300, 1150 );
// print_r( $query->posts[0]  );
netrics_get_pubs_pagespeed( $query );



//Check artilcles:
<pre><?php

$i = 0;
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
            // 'terms'    => array( 6221 ),
            'terms'    => array( 6175, 6172 ), // 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),
);
$query = new WP_Query( $args );

foreach( $query->posts as $post_id) {
    echo "$i\t$post_id\t";
    $meta = get_post_meta( $post_id, 'nn_articles_201907', true);
    if ( 3 === count( $meta ) && isset( $meta[0]['url'] ) ) {
        // print_r( get_post_meta( $post_id, 'nn_articles_201907', true) );
        echo "Success\n";
    } else {
        echo "ERROR\n";
    }
    $i++;
}

$args = array(
    'post_type'      => 'publication',
    // 'orderby'        => 'title',
    // 'order'          => 'DESC',
    'orderby'        => 'rand',
    'posts_per_page' => 500,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => array( 6221, 6175, 6172 ), // '201907', 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),

);
$query = new WP_Query( $args );

netrics_get_feeds( $query );

*/

$args = array(
    'post_type'      => 'publication',
    'orderby'        => 'rand',
    // 'order'          => 'DESC',
    'posts_per_page' => 500,
    'offset'         => 0,
    'fields'         => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'flag',
            'field'    => 'term_id',
            'terms'    => array( 6221, 6175, 6172 ), // '201907', 'none', 'json'
            'operator' => 'NOT IN',
        ),
    ),

);
$query = new WP_Query( $args );

netrics_get_feeds( $query );


















