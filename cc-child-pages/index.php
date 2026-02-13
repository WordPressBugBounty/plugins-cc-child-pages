<?php
/**
 * Plugin Name: CC Child Pages
 * Plugin URI: https://ccplugins.co.uk/plugins/cc-child-pages/
 * Description: Display WordPress child pages in a responsive grid or list using a shortcode, Gutenberg block or Elementor widget.
 * Version:           2.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author: Caterham Computing
 * Author URI: https://caterhamcomputing.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cc-child-pages
 * Domain Path:       /languages
 *
 * @package Caterhamcomputing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set up constants used within the plugin
 */
define( 'CC_CHILD_PAGES_VERSION', '2.1.0' );


/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function caterhamcomputing_cc_child_pages_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'caterhamcomputing_cc_child_pages_block_init' );


/**
 * CC Child Pages shortcode and widget
 */
require_once 'includes/ccchildpages.php';
// in your main plugin bootstrap
// require_once __DIR__ . '/blocks/cc-child-pages/index.php';


add_shortcode( 'child_pages', 'ccchildpages::show_child_pages' );
add_action( 'wp_enqueue_scripts', 'ccchildpages::enqueue_styles' );
add_action( 'plugins_loaded', 'ccchildpages::load_plugin_textdomain' );

require_once 'includes/ccchildpages_widget.php';
// register widget
function register_ccchildpages_widget() {
	register_widget( 'ccchildpages_widget' );
}
add_action( 'widgets_init', 'register_ccchildpages_widget' );

// Dashboard feed
add_action( 'wp_dashboard_setup', 'ccchildpages::dashboard_widgets' );

// Dismiss handler.
add_action( 'admin_post_ccchildpages_dismiss_widget', array( 'ccchildpages', 'dashboard_handle_dismiss' ) );

// TinyMCE Buttons
add_action( 'init', 'ccchildpages::tinymce_buttons' );

// Show excerpt for pages ...
add_action( 'init', 'ccchildpages::show_page_excerpt' );

// Add action links for plugin
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ccchildpages::plugin_action_links' );

// Add links to plugin meta
add_filter( 'plugin_row_meta', 'ccchildpages::plugin_row_meta', 10, 4 );

// Set default option values
register_activation_hook( __FILE__, 'ccchildpages::options_activation' );

// Regsiter settings
add_action( 'admin_init', 'ccchildpages::register_options' );

// Add options page
add_action( 'admin_menu', 'ccchildpages::options_menu' );

// Add custom query variables for paging
add_filter( 'query_vars', 'ccchildpages::add_query_strings' );

// Add action to handle offset correction for pagination ...
add_action( 'pre_get_posts', 'ccchildpages::query_offset', 1 );

// Exempt our new shortcode from texturising ...
add_filter( 'no_texturize_shortcodes', 'ccchildpages::exempt_from_wptexturize' );

add_action(
	'enqueue_block_editor_assets',
	'ccchildpages::enqueue_block_editor_assets'
);

/**
 * Insert "CC Plugins" category immediately after "Media" (with legacy fallback).
 */
function ccplugins_insert_category_after_media( $cats, $post ) {
	$slug  = 'ccplugins';
	$title = __( 'CC Plugins', 'cc-child-pages' );
	$new   = array(
		'slug'  => $slug,
		'title' => $title,
	);

	$cats      = array_values( $cats );
	$cat_slugs = wp_list_pluck( $cats, 'slug' );

	// Remove existing to re-insert in desired spot.
	if ( false !== ( $i = array_search( $slug, $cat_slugs, true ) ) ) {
		array_splice( $cats, $i, 1 );
		$cat_slugs = wp_list_pluck( $cats, 'slug' );
	}

	$after  = array( 'media', 'widgets', 'embed' );
	$insert = false;
	foreach ( $after as $candidate ) {
		$idx = array_search( $candidate, $cat_slugs, true );
		if ( false !== $idx ) {
			$insert = $idx;
			break; }
	}

	if ( false !== $insert ) {
		array_splice( $cats, $insert + 1, 0, array( $new ) );
	} else {
		$cats[] = $new;
	}
	return $cats;
}

// Add only the correct hook for the running WP version.
if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
	add_filter( 'block_categories_all', 'ccplugins_insert_category_after_media', 10, 2 );
} else {
	add_filter( 'block_categories', 'ccplugins_insert_category_after_media', 10, 2 );
}


/**
 * Elementor integration.
 */
add_action(
	'plugins_loaded',
	function () {

		// Elementor not active or not yet loaded.
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Ensure our core class exists (skin list comes from here).
		if ( ! class_exists( '\ccchildpages' ) ) {
			return;
		}

		require_once __DIR__ . '/includes/elementor/class-elementor-integration.php';

		$integration = new \CaterhamComputing\CCChildPages\Elementor\Elementor_Integration();
		$integration->init();
	}
);
