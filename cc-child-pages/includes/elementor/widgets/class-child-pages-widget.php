<?php
/**
 * Elementor widget for CC Child Pages shortcode.
 *
 * @package cc-child-pages
 */

namespace CaterhamComputing\CCChildPages\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class Child_Pages_Widget extends Widget_Base {

	public function get_name() {
		return 'cc_child_pages';
	}

	public function get_title() {
		return __( 'CC Child Pages', 'cc-child-pages' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'ccplugins' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		/**
		 * Allow extensions (eg Pro) to register controls before the core widget controls.
		 *
		 * @param self $widget Elementor widget instance.
		 */
		do_action( 'cc_child_pages/elementor/before_register_controls', $this );

		/**
		 * Query
		 */
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'cc-child-pages' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'       => __( 'Pages to show', 'cc-child-pages' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current_children',
				'options'     => array(
					'current_children' => __( 'Children of current page', 'cc-child-pages' ),
					'parent_id'        => __( 'Children of a specific parent ID', 'cc-child-pages' ),
					'page_ids'         => __( 'Specific pages (comma-separated IDs)', 'cc-child-pages' ),
					'siblings'         => __( 'Siblings of current page', 'cc-child-pages' ),
				),
				'description' => __( 'When using templates, you may need to set a specific parent ID.', 'cc-child-pages' ),
			)
		);

		$this->add_control(
			'id',
			array(
				'label'       => __( 'Parent page ID', 'cc-child-pages' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'step'        => 1,
				'condition'   => array( 'source' => 'parent_id' ),
				'description' => __( 'Used when “Children of a specific parent ID” is selected.', 'cc-child-pages' ),
			)
		);

		$this->add_control(
			'page_ids',
			array(
				'label'       => __( 'Page IDs', 'cc-child-pages' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12,34,56',
				'condition'   => array( 'source' => 'page_ids' ),
				'description' => __( 'Comma-separated list of page or post IDs to include.', 'cc-child-pages' ),
			)
		);

		$this->add_control(
			'depth',
			array(
				'label'   => __( 'Depth', 'cc-child-pages' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'step'    => 1,
			)
		);

		$this->add_control(
			'show_current_page',
			array(
				'label'        => __( 'Include current page', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order by', 'cc-child-pages' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => array(
					'menu_order' => __( 'Menu order', 'cc-child-pages' ),
					'title'      => __( 'Title', 'cc-child-pages' ),
					'date'       => __( 'Date', 'cc-child-pages' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'cc-child-pages' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset', 'cc-child-pages' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'step'    => 1,
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'       => __( 'Limit', 'cc-child-pages' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
				'step'        => 1,
				'description' => __( '-1 means no limit.', 'cc-child-pages' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'       => __( 'Posts per page (pagination)', 'cc-child-pages' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
				'step'        => 1,
				'description' => __( '-1 disables pagination in most setups.', 'cc-child-pages' ),
			)
		);

		$this->end_controls_section();

		/**
		 * Layout
		 */
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'cc-child-pages' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'list',
			array(
				'label'        => __( 'Force list layout', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'cols',
			array(
				'label'       => __( 'Columns', 'cc-child-pages' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 6,
				'step'        => 1,
				'condition'   => array( 'list!' => 'true' ),
				'description' => __( 'Leave blank to use the skin default.', 'cc-child-pages' ),
			)
		);

		/**
		 * Skins: use skin_list() (filtered/extended) if available,
		 * fall back to default_skin_list() if not.
		 */
		$skin_options = array( 'simple' => __( 'Simple', 'cc-child-pages' ) );

		if ( class_exists( '\ccchildpages' ) ) {

			if ( is_callable( array( '\ccchildpages', 'skin_list' ) ) ) {
				$list = \ccchildpages::skin_list();
				if ( is_array( $list ) && ! empty( $list ) ) {
					$skin_options = $list;
				}
			} elseif ( is_callable( array( '\ccchildpages', 'default_skin_list' ) ) ) {
				$list = \ccchildpages::default_skin_list();
				if ( is_array( $list ) && ! empty( $list ) ) {
					$skin_options = $list;
				}
			}
		}

		$this->add_control(
			'skin',
			array(
				'label'     => __( 'Skin', 'cc-child-pages' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'simple',
				'options'   => $skin_options,
				'condition' => array( 'list!' => 'true' ),
			)
		);

		$this->add_control(
			'thumbs',
			array(
				'label'        => __( 'Show thumbnails', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
				'condition'    => array( 'list!' => 'true' ),
			)
		);

		$this->add_control(
			'link_thumbs',
			array(
				'label'        => __( 'Link thumbnails', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
				'condition'    => array(
					'list!'  => 'true',
					'thumbs' => 'true',
				),
			)
		);

		$this->add_control(
			'lazy_load',
			array(
				'label'        => __( 'Lazy load images', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
				'condition'    => array(
					'list!'  => 'true',
					'thumbs' => 'true',
				),
			)
		);

		$this->add_control(
			'link_titles',
			array(
				'label'        => __( 'Link titles', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
				'condition'    => array( 'list!' => 'true' ),
			)
		);

		// Optional: default legacy mode based on plugin mode.
		$legacy_default = '';
		if ( class_exists( '\ccchildpages' ) && is_callable( array( '\ccchildpages', 'css_version' ) ) ) {
			$legacy_default = ( 'ccflex' === \ccchildpages::css_version() ) ? '' : 'true';
		}

		$this->add_control(
			'use_legacy_css',
			array(
				'label'        => __( 'Use legacy CSS', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => $legacy_default,
			)
		);

		$this->add_control(
			'class',
			array(
				'label'       => __( 'Extra CSS class', 'cc-child-pages' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Added to the shortcode wrapper.', 'cc-child-pages' ),
			)
		);

		$this->end_controls_section();

		/**
		 * Content display
		 * Hidden entirely when list mode is enabled.
		 */
		$this->start_controls_section(
			'section_display',
			array(
				'label'     => __( 'Content display', 'cc-child-pages' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'list!' => 'true' ),
			)
		);

		$this->add_control(
			'hide_title',
			array(
				'label'        => __( 'Hide title', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_excerpt',
			array(
				'label'        => __( 'Hide excerpt', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_more',
			array(
				'label'        => __( 'Hide “Read more …” link', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'truncate_excerpt',
			array(
				'label'        => __( 'Truncate excerpt', 'cc-child-pages' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'words',
			array(
				'label'     => __( 'Excerpt words', 'cc-child-pages' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 55,
				'min'       => 1,
				'step'      => 1,
				'condition' => array( 'truncate_excerpt' => 'true' ),
			)
		);

		$this->add_control(
			'subpage_title',
			array(
				'label'       => __( 'Heading', 'cc-child-pages' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional heading shown above the grid or list.', 'cc-child-pages' ),
			)
		);

		$this->end_controls_section();

		/**
		 * Allow extensions (eg Pro) to register controls after the core widget controls.
		 *
		 * @param self $widget Elementor widget instance.
		 */
		do_action( 'cc_child_pages/elementor/after_register_controls', $this );
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = array();

		// Source mapping.
		$source = isset( $settings['source'] ) ? (string) $settings['source'] : 'current_children';

		if ( 'siblings' === $source ) {
			$atts['siblings'] = 'true';
		} elseif ( 'parent_id' === $source && ! empty( $settings['id'] ) ) {
			$atts['id'] = (string) absint( $settings['id'] );
		} elseif ( 'page_ids' === $source && ! empty( $settings['page_ids'] ) ) {
			$page_ids = preg_replace( '/[^0-9,]/', '', (string) $settings['page_ids'] );
			$page_ids = trim( $page_ids, ',' );
			if ( '' !== $page_ids ) {
				$atts['page_ids'] = $page_ids;
			}
		}

		// Common numeric/text atts.
		$atts['depth']          = (string) max( 1, absint( $settings['depth'] ?? 1 ) );
		$atts['orderby']        = in_array( $settings['orderby'] ?? 'menu_order', array( 'menu_order', 'title', 'date' ), true ) ? $settings['orderby'] : 'menu_order';
		$atts['order']          = ( 'DESC' === ( $settings['order'] ?? 'ASC' ) ) ? 'DESC' : 'ASC';
		$atts['offset']         = (string) max( 0, absint( $settings['offset'] ?? 0 ) );
		$atts['limit']          = (string) intval( $settings['limit'] ?? -1 );
		$atts['posts_per_page'] = (string) intval( $settings['posts_per_page'] ?? -1 );

		// Layout: cols.
		if ( ! empty( $settings['cols'] ) ) {
			$atts['cols'] = (string) max( 1, absint( $settings['cols'] ) );
		}

		/**
		 * Skins: validate against skin_list() (filtered/extended) if available,
		 * fall back to default_skin_list() if not.
		 */
		$allowed_skins = array( 'simple' => __( 'Simple', 'cc-child-pages' ) );

		if ( class_exists( '\ccchildpages' ) ) {

			if ( is_callable( array( '\ccchildpages', 'skin_list' ) ) ) {
				$list = \ccchildpages::skin_list();
				if ( is_array( $list ) && ! empty( $list ) ) {
					$allowed_skins = $list;
				}
			} elseif ( is_callable( array( '\ccchildpages', 'default_skin_list' ) ) ) {
				$list = \ccchildpages::default_skin_list();
				if ( is_array( $list ) && ! empty( $list ) ) {
					$allowed_skins = $list;
				}
			}
		}

		$skin         = isset( $settings['skin'] ) ? sanitize_key( (string) $settings['skin'] ) : 'simple';
		$atts['skin'] = isset( $allowed_skins[ $skin ] ) ? $skin : 'simple';

		// Boolean-y switches: Elementor returns 'true' or '' based on return_value.
		foreach ( array(
			'list',
			'thumbs',
			'link_thumbs',
			'lazy_load',
			'link_titles',
			'use_legacy_css',
			'hide_title',
			'hide_excerpt',
			'hide_more',
			'truncate_excerpt',
			'show_current_page',
		) as $bool_key ) {
			if ( ! empty( $settings[ $bool_key ] ) ) {
				$atts[ $bool_key ] = 'true';
			}
		}

		/**
		 * Enforce list mode behaviour.
		 * Even if Elementor has stored previous values, list mode should ignore grid/skin/thumb-related settings.
		 */
		if ( ! empty( $settings['list'] ) ) {
			$atts['list'] = 'true';
			unset( $atts['cols'], $atts['skin'], $atts['thumbs'], $atts['link_thumbs'], $atts['lazy_load'], $atts['link_titles'] );
		}

		if ( ! empty( $settings['words'] ) ) {
			$atts['words'] = (string) max( 1, absint( $settings['words'] ) );
		}

		if ( ! empty( $settings['class'] ) ) {
			// Allow multiple classes but sanitise each token.
			$raw    = (string) $settings['class'];
			$tokens = preg_split( '/\s+/', trim( $raw ) );
			$tokens = array_filter( array_map( 'sanitize_html_class', (array) $tokens ) );
			if ( ! empty( $tokens ) ) {
				$atts['class'] = implode( ' ', $tokens );
			}
		}

		if ( ! empty( $settings['subpage_title'] ) ) {
			$atts['subpage_title'] = sanitize_text_field( $settings['subpage_title'] );
		}

		/**
		 * Allow extensions (eg Pro) to modify the shortcode attributes produced by this widget.
		 *
		 * @param array $atts     Shortcode atts built by the widget.
		 * @param array $settings Elementor widget settings.
		 * @param self  $widget   Widget instance.
		 */
		$atts = apply_filters( 'cc_child_pages/elementor/shortcode_atts', $atts, $settings, $this );

		$shortcode = $this->build_shortcode( 'child_pages', $atts );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_shortcode( $shortcode );
	}

	/**
	 * Build a shortcode string from a tag and attribute array.
	 *
	 * @param string $tag  Shortcode tag.
	 * @param array  $atts Attributes.
	 * @return string
	 */
	private function build_shortcode( string $tag, array $atts ): string {
		$parts = array();

		foreach ( $atts as $key => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}
			$parts[] = sprintf(
				'%s="%s"',
				sanitize_key( $key ),
				esc_attr( (string) $value )
			);
		}

		return '[' . $tag . ( $parts ? ' ' . implode( ' ', $parts ) : '' ) . ']';
	}
}
