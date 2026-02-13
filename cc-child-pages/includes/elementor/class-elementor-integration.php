<?php
/**
 * Elementor integration bootstrap for CC Child Pages.
 *
 * @package cc-child-pages
 */

namespace CaterhamComputing\CCChildPages\Elementor;

defined( 'ABSPATH' ) || exit;

final class Elementor_Integration {

	/**
	 * Register hooks.
	 */
	public function init(): void {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
	}

	/**
	 * Register CC Plugins category in Elementor.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 */
	public function register_category( $elements_manager ): void {
		if ( ! is_object( $elements_manager ) || ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$elements_manager->add_category(
			'ccplugins',
			array(
				'title' => __( 'CC Plugins', 'cc-child-pages' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ): void {
		if ( ! is_object( $widgets_manager ) || ! method_exists( $widgets_manager, 'register' ) ) {
			return;
		}

		require_once __DIR__ . '/widgets/class-child-pages-widget.php';

		$widgets_manager->register(
			new \CaterhamComputing\CCChildPages\Elementor\Widgets\Child_Pages_Widget()
		);
	}
}
