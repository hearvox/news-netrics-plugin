<?php
/**
 * API call for domain data from BuiltWith.
 *
 * @since   0.1.1
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes/api/
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
 * @since   0.1.1
 *
 * @param  int   $post     WP Post object.
 * @return array $nn_site  Updated array of site data,
 */
function netrics_api_save_builtwith( $post, $api_key ) {
    $post_id = $post->ID;
    $bw_data   = netrics_api_builtwith( $post_id, $api_key );
    // We'll add data to existing post meta or start a new array.
    $nn_site = ( get_post_meta( $post_id, 'nn_site' ) ) ? get_post_meta( $post_id, 'nn_site', true ) : array();

    // Run BuiltWith, save data in post_meta.
    if ( $bw_data ) {
        $nn_site['builtwith'] = $bw_data;
        update_post_meta( $post_id, 'nn_site', $nn_site );
    }

    sleep( 2 ); // Works better with a pause.

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
 * @since   0.1.1
 *
 * @param  int  $post_id Post ID.
 * @return array  $bw_data  Data from BuiltWith.
 */
function netrics_api_builtwith( $post_id, $api_key, $api_type = 'free' ) {

    $accessKeyId     = $options['awis'];
    // BuitWith Free API url and API key.
    $api     = 'https://api.builtwith.com/free1/api.json';
    $domain  = get_post_meta( $post_id, 'nn_pub_site', true );
    $api_url = $api . '?KEY=' . $api_key . '&LOOKUP=' . $domain;
    $bw_json = $bw_data = false;
    // echo $api_url; // @todo Log to file.

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

BuiltWith (Free) data example, converted to array and stored in post meta:
[builtwith] => Array
(
    [widgets] => 8
    [ssl] => 1
    [hosting] => 5
    [analytics] => 15
    [feeds] => 3
    [javascript] => 14
    [mapping] => 2
    [framework] => 2
    [Server] => 1
    [Web Server] => 2
    [cdn] => 5
    [cms] => 2
    [ads] => 111
    [Web Master] => 0
    [cdns] => 1
    [media] => 1
    [CDN] => 1
    [css] => 5
    [mobile] => 3
    [ns] => 1
    [mx] => 2
    [payment] => 2
    [shop] => 0
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

