<?php
/**
 * Custom taxonomy: Regions
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */


/*******************************
 =REGION (taxonomy: State, County, City terms, each with term meta)
 ******************************/
/**
 * Get Post's Region terms and term meta.
 *
 * City with country/state parents, and meta: population, latlon, fips, etc..
 *
 * @since   0.1.1
 *
 * @param int $post_id  Default Post ID of Page for post meta.
 *
 * @return array $city  Array of data for all post regions.
 */
function netrics_get_city_meta( $post_id ) {
    $city_term = get_the_terms( $post_id, 'region' );

    if ( ! $city_term ) {
        return;
    }

    $city = array();
    // Add city term object and meta to array.
    $city['city_term'] = $city_term[0];
    $city['city_meta'] = get_term_meta( $city_term[0]->term_id, '', true );

    // Get county and state term IDs.
    $ancestors = get_ancestors( $city_term[0]->term_id, 'region', 'taxonomy' );

    // If county and state, add term and meta object to array.
    if ( 2 === count( $ancestors ) ) { // If county and state.
        $city['county_term'] = get_term( $ancestors[0], 'region' );
        $city['county_meta'] = get_term_meta( $ancestors[0], '', true );
        $city['state_term']  = get_term( $ancestors[1], 'region' );
        $city['state_meta']  = get_term_meta( $ancestors[1], '', true );
    }

    return $city;
}


function netrics_get_region_term_meta( $term_id, $tax = 'region' ) {
    $term = get_term( $term_id, $tax );

    if ( ! $term ) {
        return;
    }

    $region = array();
    // Add term object and meta to array.
    $region['term'] = $term;
    $region['meta'] = get_term_meta( $term_id, $tax, 'taxonomy' );

    return $region;
}

function netrics_get_pub_meta( $post_id ) {
    $cf = get_post_custom( $post_id );
    // $post_id       = get_the_ID();

    $rss_url  = ( isset( $cf['nn_pub_rss'][0] ) ) ? $cf['nn_pub_rss'][0] : false;
    $rss_link = ( $rss_url ) ? ' | <a href="' . esc_url( $rss_url ) . '">RSS feed</a>' : '';

    $site_url  = ( isset( $cf['nn_pub_url'][0] ) ) ? $cf['nn_pub_url'][0] : false;
    $site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

    $pub_name    = ( isset( $cf['nn_pub_name'][0] ) ) ? $cf['nn_pub_name'][0] : '';
    $pub_year    = ( isset( $cf['nn_pub_year'][0] ) && $cf['nn_pub_year'][0] )
        ? absint( $cf['nn_pub_year'][0] ) : '--';
    $pub_circ    = ( isset( $cf['nn_circ'][0] ) && $cf['nn_circ'][0] )
        ? number_format( absint( $cf['nn_circ'][0] ) ) : '--';
    $pub_rank    = ( isset( $cf['nn_rank'][0] ) && $cf['nn_rank'][0] )
        ? number_format( absint( $cf['nn_rank'][0] ) ) : '--';

    return $meta;
}

/**
 * Copy of Region code from theme templates
 */
if ( 0 ) {

/////////////////////
// content-single-publication
$custom_fields = get_post_custom();
$post_id       = get_the_ID();

$rss_url  = ( isset( $custom_fields['nn_pub_rss'][0] ) ) ? $custom_fields['nn_pub_rss'][0] : false;
$rss_link = ( $rss_url ) ? ' | <a href="' . esc_url( $rss_url ) . '">RSS feed</a>' : '';

$site_url  = ( isset( $custom_fields['nn_pub_url'][0] ) ) ? $custom_fields['nn_pub_url'][0] : false;
$site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

$pub_name    = ( isset( $custom_fields['nn_pub_name'][0] ) ) ? $custom_fields['nn_pub_name'][0] : '';
$pub_year    = ( isset( $custom_fields['nn_pub_year'][0] ) && $custom_fields['nn_pub_year'][0] )
    ? absint( $custom_fields['nn_pub_year'][0] ) : '--';
$pub_circ    = ( isset( $custom_fields['nn_circ'][0] ) && $custom_fields['nn_circ'][0] )
    ? number_format( absint( $custom_fields['nn_circ'][0] ) ) : '--';
$pub_rank    = ( isset( $custom_fields['nn_rank'][0] ) && $custom_fields['nn_rank'][0] )
    ? number_format( absint( $custom_fields['nn_rank'][0] ) ) : '--';

$term_region = get_the_terms( $post_id, 'region' );
$term_id     = ($term_region) ? $term_region[0]->term_id : false;

$term_pop  = ( get_term_meta( $term_id, 'nn_region_pop', true ) )
    ? get_term_meta( $term_id, 'nn_region_pop', true ) : false;
$term_pop  = ( $term_pop ) ? number_format( floatval( $term_pop ) ) : '';

$args_region = array(
    'format'    => 'id',
    'separator' => ' &gt; ',
);
$regions = get_term_parents_list( $term_id, 'region', $args_region ) ;

$city       = $term_region[0]->name;
$terms_reg  = get_ancestors( $term_id, 'region', 'taxonomy' );
$state_id   = end( $terms_reg );
$state_arr  = get_term_by( 'id', absint( $state_id ), 'region' );
$state      = $state_arr->name;
$latlon     = get_term_meta( $term_id, 'nn_region_latlon', true );

$map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
$map_loc = '&q=' . urlencode( $city ) . '+' . $state;
$map_ctr = '&amp;center=' . str_replace( '|', ',', $latlon);
$map_src = $map_api . $map_loc . $map_ctr;

// Alexa Web Info Service
// Use get_post_meta to unserialize, which above get_post_custom() doesn't.
$nn_site    = get_post_meta( $post_id , 'nn_site' );
$awis_desc  = ( isset( $nn_site[0]['alexa']['desc']  ) && $nn_site[0]['alexa']['desc'] )
    ? '&mdash; ' . $nn_site[0]['alexa']['desc'] : '';
$awis_rank  = ( isset( $nn_site[0]['alexa']['rank'] ) && $nn_site[0]['alexa']['rank'] )
    ? number_format( floatval($nn_site[0]['alexa']['rank'] ) ) : '--';
$awis_since = ( isset( $nn_site[0]['alexa']['since']  ) && $nn_site[0]['alexa']['since'] )
    ? date_parse_from_format( 'd-M-Y', $nn_site[0]['alexa']['since'] ) : false;
$awis_year  = ( $awis_since ) ? absint( $awis_since['year'] ) : '--';
$awis_links = ( isset( $nn_site[0]['alexa']['links']  ) && $nn_site[0]['alexa']['links'] )
    ? number_format( (int) $nn_site[0]['alexa']['links'] ) : '--';

/////////////////////
// content-archive
$custom_fields = get_post_custom();
$post_id       = get_the_ID();

$site_url  = ( isset( $custom_fields['nn_pub_url'][0] ) ) ? $custom_fields['nn_pub_url'][0] : false;
$site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

$pub_name  = ( isset( $custom_fields['nn_pub_name'][0] ) ) ? $custom_fields['nn_pub_name'][0] : '';
$pub_year  = ( isset( $custom_fields['nn_pub_year'][0] ) ) ? $custom_fields['nn_pub_year'][0] : '';
$pub_circ  = ( isset( $custom_fields['nn_circ'][0] ) )
    ? number_format( absint( $custom_fields['nn_circ'][0] ) ) : '--';
$pub_rank  = ( isset( $custom_fields['nn_rank'][0] ) )
    ? number_format( absint( $custom_fields['nn_rank'][0] ) ) : '--';

$term_region = get_the_terms( $post_id, 'region' );
$term_id     = ($term_region) ? $term_region[0]->term_id : false;

$term_pop  = ( get_term_meta( $term_id, 'nn_region_pop', true ) )
    ? get_term_meta( $term_id, 'nn_region_pop', true ) : false;
$city_pop  = ( $term_pop ) ? ' (<em>Pop.:</em> ' . number_format( floatval( $term_pop ) ) . ')' : '';

$args_region = array(
    'format'    => 'id',
    'separator' => ' / ',
);
$regions = get_term_parents_list( $term_id, 'region', $args_region ) ;

$site_ps = netrics_site_pagespeed( $post_id );
$pgspeed = '';

if ( $site_ps  && isset( $site_ps['score'] ) ) {
    $pgspeed .=  '<em>Score:</em> ' . round( $site_ps['score'] * 100, 1 );
    $pgspeed .=  ' | <em>Speed/TTI:</em> ' . round( $site_ps['speed'] / 1000, 1 ) . 's';
    $pgspeed .=  ' / ' . round( $site_ps['tti']  / 1000, 1 ) . 's';
    $pgspeed .=  ' | <em>Size</em>: ' . size_format( $site_ps['size'], 1 );
} else {
    $pgspeed = '<em>Score:</em> [No Pagespeed results.]';
}



/////////////////////
// taxonomy-owner
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

/////////////////////
// page-data-table-newspapers, page-data-table
// Tax terms (and parents for Region: county, state).
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

/////////////////////
// page-news-table
// Get Region values: city, county and state (tax terms), and city population (term meta).
$term_city   = get_the_terms( $post_id, 'region' );
$city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
    ? get_term_meta( $term_city[0]->term_id ) : false;
$city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
    ? $city_meta['nn_region_pop'][0] : 0;
$term_county = ( $term_city && isset( $term_city[0]->parent ) )
    ? get_term( $term_city[0]->parent ) : false;
$term_state  = ( $term_county && isset( $term_county->parent ) )
    ? get_term( $term_county->parent ) : false;

/////////////////////
// page-regions-city, page-regions-county, page-regions
$city_data = get_post_meta( 7594, 'nn_cities', true );
$counties_data = get_post_meta( 7594, 'nn_counties', true );
$states_data = get_post_meta( 7594, 'nn_states', true );

} // if (0)
