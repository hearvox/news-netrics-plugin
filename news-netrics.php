<?php
/*
 * Plugin Name: News Netrics
 * Plugin URI: https://news.pubmedia.us/
 * Description: Net Netrics customizations and post types.
 * Author:  Barrett Golding
 * Version: 0.1.0
 * Author URI: https://hearingvoices.com/
 * License: GPL2+
 * Text Domain: newsnetrics
 * Domain Path: /languages/
 * Plugin Prefix: newsnetrics
*/

/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

/* ------------------------------------------------------------------------ *
 * Plugin init and uninstall text change
 * ------------------------------------------------------------------------ */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( defined( 'NEWSNETRICS_VERSION' ) ) {
    return;
}

/* ------------------------------------------------------------------------ *
 * Constants: plugin version, name, and the path and URL to directory.
 *
 * NEWSNETRICS_BASENAME news-netrics-master/news-netrics.php
 * NEWSNETRICS_DIR      /path/to/wp-content/plugins/news-netrics-master/
 * NEWSNETRICS_URL      https://example.com/wp-content/plugins/news-netrics-master/
 * ------------------------------------------------------------------------ */
define( 'NEWSNETRICS_VERSION', '0.1.0' );
define( 'NEWSNETRICS_BASENAME', plugin_basename( __FILE__ ) );
define( 'NEWSNETRICS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'NEWSNETRICS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Adds "Settings" link on plugin page (next to "Activate").
 */
//
function newsnetrics_plugin_settings_link( $links ) {
  $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=newsnetrics' ) ) . '">' . __( 'Settings', 'newsnetrics' ) . '</a>';
  array_unshift( $links, $settings_link );
  return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'NEWSNETRICS_plugin_settings_link' );

/**
 * Redirect to Settings screen upon plugin activation.
 *
 * @param  string $plugin Plugin basename (e.g., "my-plugin/my-plugin.php")
 * @return void
 */
function newsnetrics_activation_redirect( $plugin ) {
    if ( $plugin === NEWSNETRICS_BASENAME ) {
        $redirect_uri = add_query_arg(
            array(
                'page' => 'newsnetrics' ),
                admin_url( 'options-general.php' )
            );
        wp_safe_redirect( $redirect_uri );
        exit;
    }
}
add_action( 'activated_plugin', 'NEWSNETRICS_activation_redirect' );

/**
 * Load the plugin text domain for translation.
 *
 * @since   0.1.0
 */
function newsnetrics_load_textdomain() {
    load_plugin_textdomain( 'newsnetrics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'newsnetrics_load_textdomain' );

/**
 * Sets default settings option upon activation, if options doesn't exist.
 *
 * @uses NEWSNETRICS_get_options()   Safely get site option, check plugin version.
 */
function newsnetrics_activate() {
    newsnetrics_get_options();
}
register_activation_hook( __FILE__, 'newsnetrics_activate' );

/**
 * The code that runs during plugin deactivation (not currently used).
 */
/*
function NEWSNETRICS_deactivate() {
}
register_deactivation_hook( __FILE__, 'NEWSNETRICS_deactivate' );
*/

/* ------------------------------------------------------------------------ *
 * Required Plugin Files
 * ------------------------------------------------------------------------ */
include_once( dirname( __FILE__ ) . '/includes/admin-options.php' );
include_once( dirname( __FILE__ ) . '/includes/apis.php' );
include_once( dirname( __FILE__ ) . '/includes/feeds.php' );
include_once( dirname( __FILE__ ) . '/includes/functions.php' );
include_once( dirname( __FILE__ ) . '/includes/post-types.php' );
include_once( dirname( __FILE__ ) . '/includes/results.php' );
include_once( dirname( __FILE__ ) . '/includes/taxonomies.php' );
include_once( dirname( __FILE__ ) . '/includes/transients.php' );

