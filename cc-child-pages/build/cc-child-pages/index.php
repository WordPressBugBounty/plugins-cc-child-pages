<?php
// blocks/cc-child-pages/index.php
namespace CaterhamComputing\CCChildPagesBlock;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', __NAMESPACE__ . '\\register_block' );

function register_block() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => __NAMESPACE__ . '\\render_block',
		)
	);
}

function render_block( $attributes = array(), $content = '', $block = null ) {
	// Ensure ccchildpages is initialised once per request
	if ( method_exists( 'ccchildpages', 'init' ) ) {
		static $did_init = false;
		if ( ! $did_init ) {
			\ccchildpages::init();
			$did_init = true; }
	}

	// Default to current post for id, keep only the 2 attrs we expose for now
	$defaults = array(
		'id'     => ( is_object( $block ) && isset( $block->context['postId'] ) ) ? (int) $block->context['postId'] : get_the_ID(),
		'skin'   => 'simple',          // string
		'thumbs' => 'false',           // string: 'false' | 'true' | size name
	);

	$atts = array_merge( $defaults, array_intersect_key( (array) $attributes, $defaults ) );

	// Render using your existing method
	$html = \ccchildpages::show_child_pages( $atts );

	return $html ?: '';
}

/**
 * Load your front-end styles in the editor so the preview matches.
 */
add_action(
	'enqueue_block_editor_assets',
	function () {
		// Main styles
		wp_register_style(
			'ccchildpagescss',
			plugins_url( '../../css/styles.css', __FILE__ ),
			array(),
			\ccchildpages::plugin_version
		);
		wp_enqueue_style( 'ccchildpagescss' );

		// Skins (respect plugin option)
		$link_skins = true;
		if ( $options = get_option( 'cc_child_pages' ) ) {
			$link_skins = empty( $options['link_skins'] ) || $options['link_skins'] === 'true';
		}
		if ( $link_skins ) {
			wp_register_style(
				'ccchildpagesskincss',
				plugins_url( '../../includes/css/skins.css', __FILE__ ),
				array(),
				\ccchildpages::plugin_version
			);
			wp_enqueue_style( 'ccchildpagesskincss' );
		}

		// Optional: custom CSS from plugin settings
		if ( method_exists( 'ccchildpages', 'custom_css' ) ) {
			if ( $css = \ccchildpages::custom_css() ) {
				wp_add_inline_style( 'ccchildpagescss', $css );
			}
		}
	}
);
