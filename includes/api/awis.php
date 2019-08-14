<?php
/**
 * API call for domain data from Amazon's Alexa Web Information Sevice (AWIS).
 *
 * @since   0.1.1
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes/api/
 */

/**
 * Save AWIS data as post meta (array).
 *
 * @see https://docs.aws.amazon.com/awis/index.html
 *
 * @since   0.1.1
 *
 * @param  int   $post     WP Post object.
 * @return array $nn_site  Updated array of site data,
 */
function netrics_api_save_awis( $post ) {
    $post_id = $post->ID;
    // Returns NN-customized array.
    $alexa   = netrics_api_awis( $post_id );

    // Add data to existing post meta or start a new array.
    $nn_site = ( get_post_meta( $post_id, 'nn_site' ) )
        ? get_post_meta( $post_id, 'nn_site', true ) : array();

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
 * @since   0.1.1
 *
 * @todo Log data to file.
 *
 * @param  int  $post_id Post ID.
 * @return array  $alexa  Data from Alexa.
 */
function netrics_api_awis( $post_id ) {
    // Alexa script (New Netrics customized) and API keys
    require_once( plugin_dir_path( __FILE__ ) . 'awis-query.php' );

    $options         = netrics_get_options(); // Get API Keys.
    $accessKeyId     = $options['awis'];
    $secretAccessKey = $options['awissecret'];
    $domain          = get_post_meta( $post_id, 'nn_pub_site', true );

    if ( $domain ) {
        // Class for Alexa API
        $urlInfo    = new UrlInfo( $accessKeyId, $secretAccessKey, $domain );
        $alexa_data = $urlInfo->getUrlInfo();
    }

    // Set return var to false if not an array.
    $alexa = ( is_array( $alexa_data ) ) ? $alexa_data : false;

    // echo $domain; // @todo Log to file.
    // print_r ( $alexa ); // @todo Log to file.

    return $alexa;
}
/*
[alexa] => Array
(
    [rank] => 537082
    [title] => adirondackdailyenterprise.com/
    [desc] =>
    [since] =>
    [links] => 458
    [speed] => 5502
    [speed_pc] => 4
    [date] => 2019-06
)

date_parse_from_format ( 'd-M-Y' , $nn_site[0]['alexa']['since'] );
date_parse_from_format ( 'Y-m' , $nn_site[0]['alexa']['date'] );

*/
