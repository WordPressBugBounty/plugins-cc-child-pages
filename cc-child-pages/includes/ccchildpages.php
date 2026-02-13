<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ccchildpages
 */

class ccchildpages {


	// Used to uniquely identify this plugin's menu page in the WP manager
	const admin_menu_slug = 'ccchildpages';

	// Plugin name
	const plugin_name = 'CC Child Pages';

	// Plugin version
	const plugin_version = CC_CHILD_PAGES_VERSION;

	// ID Count
	protected static $cc_id_counter;

	// Initialise class
	public static function init( $value = 0 ) {
		self::$cc_id_counter = $value;
	}

	// Get unique ID
	public static function get_unique_id() {
		++self::$cc_id_counter;
		return self::$cc_id_counter;
	}

	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'cc-child-pages', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	public static function show_child_pages( $atts ) {
		// Get unique id for this instance of CC Child Pages
		$cc_uid = self::get_unique_id();

		// Store current page ID
		$cc_current_page_id = get_the_ID();

		// Store image size details in case we need to output Video Thumbnails, etc. which may be external files
		$img_sizes   = get_intermediate_image_sizes();
		$img_sizes[] = 'full'; // allow "virtual" image size ...

		$default_atts = apply_filters(
			'ccchildpages_defaults',
			array(
				'id'                     => get_the_ID(),
				'page_ids'               => '',
				'posts_per_page'         => -1,
				'limit'                  => -1,
				'page'                   => -1,
				'cols'                   => '',
				'depth'                  => '1',
				'exclude'                => '',
				'exclude_tree'           => '',
				'skin'                   => 'simple',
				'class'                  => '',
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'link_titles'            => 'false',
				'title_link_class'       => 'ccpage_title_link',
				'hide_title'             => 'false',
				'hide_more'              => 'false',
				'hide_excerpt'           => 'false',
				'show_page_content'      => 'false',
				'truncate_excerpt'       => 'true',
				'list'                   => 'false',
				'link_thumbs'            => 'false',
				'thumbs'                 => 'false',
				'lazy_load'              => 'false',
				'more'                   => __( 'Read more ...', 'cc-child-pages' ),
				'link'                   => '',
				'siblings'               => 'false',
				'show_current_page'      => 'false',
				'hide_wp_more'           => 'false',
				'use_custom_excerpt'     => '',
				'use_custom_title'       => '',
				'use_custom_more'        => '',
				'use_custom_link'        => 'cc_child_pages_link',
				'use_custom_link_target' => 'cc_child_pages_link_target',
				'use_custom_thumbs'      => '',
				'use_legacy_css'         => 'false',
				'ignore_sticky_posts'    => 'true',
				'offset'                 => 0,
				'words'                  => 55,
				'subpage_title'          => '',
				'link_target'            => '',
				'show_author'            => 'false',
				'show_date_created'      => 'false',
				'show_date_modified'     => 'false',
				'post_status'            => '',
			)
		);

		$a = shortcode_atts( $default_atts, $atts );

		$a = apply_filters( 'ccchildpages_attributes', $a );

		// Boolean values for checking lazy loading for images.
		$lazy_load = apply_filters( 'cc_child_pages_lazy_load', strtolower( $a['lazy_load'] ) === 'true' );

		// If we are displaying siblings, set starting point to page parent and add current page to exclude list
		if ( strtolower( trim( $a['siblings'] ) ) == 'true' ) {
			$a['id'] = wp_get_post_parent_id( get_the_ID() ) ? wp_get_post_parent_id( get_the_ID() ) : 0;

			if ( strtolower( trim( $a['show_current_page'] ) ) != 'true' ) {
				if ( $a['exclude'] != '' ) {
					$a['exclude'] .= ',';
				}
				$a['exclude'] .= get_the_ID();
			}
		}

		$depth = intval( $a['depth'] );

		if ( strtolower( trim( $a['list'] ) ) != 'true' && $a['cols'] == '' ) {
			$a['cols'] = '3';
		}

		switch ( $a['cols'] ) {
			case '6':
				$class = 'sixcol';
				$cols  = 6;
				break;
			case '5':
				$class = 'fivecol';
				$cols  = 5;
				break;
			case '4':
				$class = 'fourcol';
				$cols  = 4;
				break;
			case '3':
				$class = 'threecol';
				$cols  = 3;
				break;
			case '2':
				$class = 'twocol';
				$cols  = 2;
				break;
			case '1':
				$class = 'onecol';
				$cols  = 1;
				break;
			default:
				$class = '';
				$cols  = 1;
		}

		$default_skins   = self::default_skin_list();
		$available_skins = self::skin_list();

		$skin_param = trim( strtolower( $a['skin'] ) );

		if ( array_key_exists( $skin_param, $available_skins ) ) {
			$skin = ( $skin_param !== 'simple' && array_key_exists( $skin_param, $default_skins ) ) ? 'cc' . $skin_param : $skin_param;
		} else {
			$skin = 'simple';
		}

		if ( strtolower( trim( $a['list'] ) ) == 'true' ) {
			$list = true;
		} else {
			$list = false;
		}

		if ( strtolower( trim( $a['truncate_excerpt'] ) ) == 'true' ) {
			$truncate_excerpt = true;
		} else {
			$truncate_excerpt = false;
		}

		if ( strtolower( trim( $a['link_titles'] ) ) == 'true' ) {
			$link_titles      = true;
			$title_link_class = self::sanitize_class_list( $a['title_link_class'] );
		} else {
			$link_titles = false;
		}

		if ( strtolower( trim( $a['hide_more'] ) ) == 'true' ) {
			$hide_more = true;
		} else {
			$hide_more = false;
		}

		if ( strtolower( trim( $a['hide_title'] ) ) == 'true' ) {
			$hide_title = true;
		} else {
			$hide_title = false;
		}

		if ( strtolower( trim( $a['hide_wp_more'] ) ) == 'true' ) {
			$hide_wp_more = true;
		} else {
			$hide_wp_more = false;
		}

		if ( strtolower( trim( $a['hide_excerpt'] ) ) == 'true' ) {
			$hide_excerpt = true;
		} else {
			$hide_excerpt = false;
		}

		if ( strtolower( trim( $a['show_page_content'] ) ) == 'true' ) {
			$show_page_content = true;
		} else {
			$show_page_content = false;
		}

		if ( strtolower( trim( $a['ignore_sticky_posts'] ) ) == 'true' ) {
			$ignore_sticky_posts = true;
		} else {
			$ignore_sticky_posts = false;
		}

		if ( strtolower( trim( $a['show_author'] ) ) == 'true' ) {
			$show_author = true;
		} else {
			$show_author = false;
		}

		if ( strtolower( trim( $a['show_date_created'] ) ) == 'true' ) {
			$show_date_created = true;
		} else {
			$show_date_created = false;
		}

		if ( strtolower( trim( $a['show_date_modified'] ) ) == 'true' ) {
			$show_date_modified = true;
		} else {
			$show_date_modified = false;
		}

		$offset = ( intval( $a['offset'] ) > 0 ) ? intval( $a['offset'] ) : 0;

		if ( $a['order'] == 'ASC' ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}

		switch ( $a['orderby'] ) {
			case 'post_id':
			case 'id':
			case 'ID':
				$orderby = 'ID';
				break;
			case 'post_author':
			case 'author':
				if ( $list ) {
					$orderby = 'post_author';
				} else {
					$orderby = 'author';
				}
				break;
			case 'random':
			case 'rand':
			case 'RANDOM':
			case 'RAND':
				$orderby = 'rand';
				break;
			case 'post_date':
			case 'date':
				if ( $list ) {
					$orderby = 'post_date';
				} else {
					$orderby = 'date';
				}
				break;
			case 'post_modified':
			case 'modified':
				if ( $list ) {
					$orderby = 'post_modified';
				} else {
					$orderby = 'modified';
				}
				break;
			case 'post_title':
			case 'title':
				if ( $list ) {
					$orderby = 'post_title';
				} else {
					$orderby = 'title';
				}
				break;
			case 'post_name':
			case 'name':
			case 'slug':
				if ( $list ) {
					$orderby = 'post_name';
				} else {
					$orderby = 'name';
				}
				break;
			default:
				$orderby = 'menu_order';
		}

		if ( $a['post_status'] == '' ) {
			$post_status = '';
		} else {
			$post_status = explode( ',', $a['post_status'] );
		}

		if ( strtolower( trim( $a['link_thumbs'] ) ) == 'true' ) {
			$link_thumbs = true;
		} else {
			$link_thumbs = false;
		}

		if ( strtolower( trim( $a['thumbs'] ) ) == 'true' ) {
			$thumbs = 'medium';
		} elseif ( strtolower( trim( $a['thumbs'] ) ) == 'false' ) {
			$thumbs = false;
		} else {
			$thumbs = strtolower( trim( $a['thumbs'] ) );

			if ( ! in_array( $thumbs, $img_sizes ) ) {
				$thumbs = 'medium';
			}
		}

		$more = esc_html( trim( $a['more'] ) ); // default

		// if class is specified, substitue value for skin class
		if ( $a['class'] != '' ) {
			$skin = self::sanitize_class_list( $a['class'] );
		}

		$css_version_class = self::css_version();
		$use_legacy_css    = trim( strtolower( $a['use_legacy_css'] ) );

		if ( $use_legacy_css === 'true' ) {
			$css_version_class = 'cclegacy';
		}

		$outer_template = str_replace( '{{class}}', $class, apply_filters( 'ccchildpages_outer_template', '<div id="ccchildpages-' . $cc_uid . '" class="ccchildpages ' . apply_filters( 'ccchildpages_css_version', $css_version_class, $a ) . ' {{class}} {{skin}} ccclearfix">{{ccchildpages}}</div>', $a ) );
		$outer_template = str_replace( '{{skin}}', $skin, $outer_template );

		$inner_template = apply_filters( 'ccchildpages_inner_template', '<div class="ccchildpage {{page_class}}">{{title_tag}}{{meta}}{{thumbnail}}{{excerpt}}{{more}}</div>', $a );

		$title_template = apply_filters(
			'ccchildpages_title_template',
			'<h3{{title_class}}>{{title}}</h3>',
			$a
		);

		$meta_template = apply_filters( 'ccchildpages_meta_template', '<p class="small cc-meta-info">{{meta}}</p>', $a );

		// $return_html = '<div class="ccchildpages ' . $class .' ' . $skin . ' ccclearfix">';

		$page_id = $a['id'];

		if ( $list ) {
			$args = array(
				'title_li'    => '',
				'child_of'    => $page_id,
				'echo'        => 0,
				'depth'       => $depth,
				'exclude'     => $a['exclude'],
				'sort_order'  => $order,
				'sort_column' => $orderby,
			);

			if ( is_array( $post_status ) || $post_status != '' ) {
				$args['post_status'] = $post_status;
			}

			$post_type         = get_post_type( $page_id );
			$args['post_type'] = $post_type;

			$args = apply_filters( 'ccchildpages_list_pages_args', $args, $a );

			$page_count = 0;

			$return_html = '<ul class="ccchildpages_list ccclearfix">';

			$page_list = trim( wp_list_pages( $args ) );

			if ( $page_list == '' ) {
				return '';
			}

			$return_html .= $page_list;

			$return_html .= '</ul>';
		} else {
			$return_html = '';

			$posts_array = explode( ',', $page_id ); // Allow for comma separated lists of IDs
			$post_count  = count( $posts_array );

			$posts_per_page = intval( $a['posts_per_page'] );
			$page           = intval( $a['page'] );

			$ccpage_var = ( is_front_page() ? 'page' : 'ccpage' . $cc_uid );

			$ccpaged = ( get_query_var( $ccpage_var ) ) ? absint( get_query_var( $ccpage_var ) ) : 1;

			$args = array(
				// 'post_type'      => 'page',
				// 'post_type'      => $post_type,
				'posts_per_page'      => $posts_per_page,
				// 'post_parent'    => $page_id,
				'order'               => $order,
				'orderby'             => $orderby,
				// 'post__not_in'   => explode(',', $a['exclude']),
				'ignore_sticky_posts' => $ignore_sticky_posts,
			);

			if ( is_array( $post_status ) || $post_status != '' ) {
				$args['post_status'] = $post_status;
			}

			if ( trim( $a['exclude'] ) != '' ) {
				$args['post__not_in'] = explode( ',', $a['exclude'] );
			}

			if ( $posts_per_page > 0 ) {
				$args['paged'] = $ccpaged;

				// If page has been set manually, override any pagination
				if ( $page > 0 ) {
					$args['paged'] = $page;
				}
			} elseif ( intval( $a['limit'] ) > 0 ) {
					// If limit is specified, show only that number of pages
					$args['posts_per_page'] = intval( $a['limit'] );
					$args['paged']          = 1;
			}

			if ( $offset > 0 ) {
				$args['offset'] = $offset;
			}

			if ( $post_count > 1 ) {
				// Multiple IDs specified, so set the post_parent__in parameter
				$args['post_parent__in'] = $posts_array;

				/*
				// get post_type for each post specified ...
				$post_type_array = array();

				foreach ( $posts_array as $post_id ) {
					// Get post_type
					$post_type = get_post_type( $post_id );

					if ( ! in_array($post_type, $post_type_array) ) $post_type_array[] = $post_type;
				}

				$args['post_type'] = $post_type_array; */

				$args['post_type'] = 'any';
			} else {
				// Single ID specified, so set the post_parent parameter
				$args['post_parent'] = $page_id;
				$args['post_type']   = get_post_type( $page_id );
			}

			if ( $a['page_ids'] != '' ) {
				// Multiple specific posts specified, so unset unwanted values in $args then build the lists of IDs
				unset( $args['post_parent'] );
				unset( $args['post_parent__in'] );

				$posts_array = explode( ',', $a['page_ids'] );

				$args['post__in'] = $posts_array;
				$args['orderby']  = 'post__in';

				/*
				// get post_type for each post specified ...
				$post_type_array = array();

				foreach ( $posts_array as $post_id ) {
					// Get post_type
					$post_type = get_post_type( $post_id );

					if ( ! in_array($post_type, $post_type_array) ) $post_type_array[] = $post_type;
				}

				$args['post_type'] = $post_type_array; */

				$args['post_type'] = 'any';
			}

			$args['ccchildpages'] = 'true';

			$args = apply_filters( 'ccchildpages_query_args', $args, $a );

			$parent = new WP_Query( $args );

			if ( ! $parent->have_posts() ) {
				return '';
			}

			$page_count = 0;

			while ( $parent->have_posts() ) {

				$tmp_html = $inner_template;

				$parent->the_post();

				$id = get_the_ID();

				++$page_count;

				if ( $page_count % $cols == 0 && $cols > 1 ) {
					$page_class = ' cclast';
				} elseif ( $page_count % $cols == 1 && $cols > 1 ) {
					$page_class = ' ccfirst';
				} else {
					$page_class = '';
				}

				if ( $page_count % 2 == 0 ) {
					$page_class .= ' cceven';
				} else {
					$page_class .= ' ccodd';
				}

				$page_class .= ' ccpage-count-' . $page_count;
				$page_class .= ' ccpage-id-' . $id;
				$page_class .= ' ccpage-' . self::the_slug( $id );

				// Check to see if this page has a parent, and if it has add classes for the id and slug
				if ( $page_parent_id = wp_get_post_parent_id( $id ) ) {
					$page_class .= ' ccpage-has-parent';
					$page_class .= ' ccpage-pid-' . $page_parent_id;
					$page_class .= ' ccpage-parent-' . self::the_slug( $page_parent_id );
				} else {
					$page_class .= ' ccpage-top-level';
					$page_class .= ' ccpage-pid-0';
				}

				/* Check to see if link has been specified */
				if ( $a['link'] == '' ) {
					$link = get_permalink( $id );
				} else {
					$link = esc_url( $a['link'] );
				}

				/* Check to see if custom link has been specified */
				$use_custom_link = sanitize_key( $a['use_custom_link'] );

				if ( ! empty( $use_custom_link ) ) {

					// Retrieve raw meta value (could be anything)
					$raw_meta_link = get_post_meta( $id, $use_custom_link, true );

					if ( ! empty( $raw_meta_link ) ) {

						// Trim whitespace
						$raw_meta_link = trim( $raw_meta_link );

						// Sanitize URL safely for storage/use
						$sanitized_link = esc_url_raw( $raw_meta_link );

						// Only update $link if we have a valid-looking URL
						if ( ! empty( $sanitized_link ) ) {
							$link = $sanitized_link;
						}
					}
				}

				/* Check to see if target has been specified */
				if ( $a['link_target'] == '' ) {
					$link_target = '';
				} else {
					$link_target = sanitize_html_class( $a['link_target'] );
				}

				/* Check to see if custom target has been specified */
				$use_custom_link_target = sanitize_key( $a['use_custom_link_target'] );

				if ( ! empty( $use_custom_link_target ) ) {

					$raw_meta_link_target = get_post_meta( $id, $use_custom_link_target, true );

					if ( is_string( $raw_meta_link_target ) ) {
						$raw_meta_link_target = trim( $raw_meta_link_target );
					} else {
						$raw_meta_link_target = '';
					}

					if ( $raw_meta_link_target !== '' ) {

						// Base sanitisation for text
						$sanitized_target = sanitize_text_field( $raw_meta_link_target );

						// Option A: allow only the standard targets (strict & safest)
						$allowed_targets = array( '_self', '_blank', '_parent', '_top' );

						if ( in_array( $sanitized_target, $allowed_targets, true ) ) {
							$link_target = $sanitized_target;

						} else {
							// Option B: in order to support custom named targets (frames),
							// restrict to a safe character set so it can't break HTML.
							$custom_target = preg_replace( '/[^A-Za-z0-9_\-]/', '', $sanitized_target );

							if ( $custom_target !== '' ) {
								$link_target = $custom_target;
							}
							// If it ends up empty, we silently keep the existing $link_target
						}
					}
				}

				if ( $id == $cc_current_page_id ) {
					$page_class .= ' active current-page cccurrent';
				}

				$tmp_html = str_replace( '{{page_class}}', $page_class, $tmp_html );

				$title_value = get_the_title(); // default

				$use_custom_title = trim( $a['use_custom_title'] );
				$meta_title       = ''; // default - no meta_title

				// If meta title field specified, get the value
				if ( $use_custom_title != '' ) {
					// Get value of custom field to be used as title
					$meta_title = trim( get_post_meta( $id, $use_custom_title, true ) );
					// If value from custom field is set, use that - otherwise use page title
					if ( $meta_title != '' ) {
						$title_value = esc_html( trim( $meta_title ) );
					}
				}

				if ( ! $link_titles ) {
					$title_html  = $title_value;
					$title_class = ' class="ccpage_title" title="' . esc_attr( $title_value ) . '"';
				} else {
					$title_html = '<a class="' . esc_attr( $title_link_class ) . '" href="' . esc_url( $link ) . '"';

					if ( $link_target != '' ) {
						$title_html .= ' target="' . esc_attr( $link_target ) . '"';
					}

					$title_html .= ' title="' . $title_value . '">' . $title_value . '</a>';
					$title_class = ' class="ccpage_title ccpage_linked_title" title="' . esc_attr( $title_value ) . '"';
				}

				$tmp_title = ( $hide_title ) ? '' : $title_template; // if hide_title is true, our tmp title is empty
				$tmp_html  = str_replace( '{{title_tag}}', $tmp_title, $tmp_html ); // inject our tmp_title tag into the template

				$tmp_html = str_replace( '{{title}}', $title_html, $tmp_html );
				$tmp_html = str_replace( '{{title_class}}', $title_class, $tmp_html );

				$meta = array();

				if ( $show_author ) {
					$meta[] = '<span class="cc-meta-data">' . __( 'Author: ', 'cc-child-pages' ) . get_the_author_link() . '</span>';
				}

				if ( $show_date_created ) {
					$meta[] = '<span class="cc-meta-data">' . __( 'Created: ', 'cc-child-pages' ) . get_the_date() . '</span>';
				}

				if ( $show_date_modified ) {
					$meta[] = '<span class="cc-meta-data">' . __( 'Modified: ', 'cc-child-pages' ) . get_the_date() . '</span>';
				}

				if ( count( $meta ) > 0 ) {
					$tmp_meta  = implode( ', ', $meta );
					$meta_html = str_replace( '{{meta}}', $tmp_meta, $meta_template );
				} else {
					$meta_html = '';
				}

				$tmp_html = str_replace( '{{meta}}', $meta_html, $tmp_html );

				$thumb_url   = '';
				$thumbs_html = '';

				if ( $thumbs != false ) {

					$thumbnail = '';

					$thumb_attr = array(
						'class' => 'cc-child-pages-thumb',
						'alt'   => $title_value,
						'title' => $title_value,
					);

					if ( $lazy_load ) {
						$thumb_attr['loading'] = 'lazy';
					}

					/* Check to see if custom thumbnails has been specified */
					$use_custom_thumbs = ! empty( $a['use_custom_thumbs'] )
					? sanitize_key( $a['use_custom_thumbs'] )
					: '';

					if ( ! empty( $use_custom_thumbs ) ) {

						$custom_thumb = get_post_meta( $id, $use_custom_thumbs, true );

						if ( ! empty( $custom_thumb ) ) {

							// Attachment ID stored in meta
							if ( is_numeric( $custom_thumb ) ) {

								$thumbnail = wp_get_attachment_image(
									(int) $custom_thumb,
									$thumbs,
									false,
									$thumb_attr
								);

							} else {

								// URL stored in meta

								// Trim and sanitise the raw URL
								$raw_thumb_url = trim( (string) $custom_thumb );
								$sanitised_url = esc_url_raw( $raw_thumb_url );

								if ( ! empty( $sanitised_url ) ) {

									// Make sure $title_value is plain text before we escape it
									$title_text = sanitize_text_field( $title_value );

									// Build the <img> tag with proper escaping
									$thumbnail = sprintf(
										'<img src="%1$s" alt="%2$s" title="%2$s" class="cc-child-pages-thumb" />',
										esc_url( $sanitised_url ),
										esc_attr( $title_text )
									);
								}
							}
						}
					}

					// Get the thumbnail code ...
					if ( $thumbnail == '' ) {
						$thumbnail = get_the_post_thumbnail( $id, $thumbs, $thumb_attr );
					}

					if ( $thumbnail != '' ) {
						// Thumbnail found, so set thumb_url to actual URL of thumbnail
						$tmp_thumb_id        = get_post_thumbnail_id( $id );
						$tmp_thumb_url_array = wp_get_attachment_image_src( $tmp_thumb_id, 'thumbnail-size', true );
						$thumb_url           = $tmp_thumb_url_array[0];
					}

					// If no thumbnail found, request a "Video Thumbnail" (if plugin installed)
					// to try and force generation of thumbnail
					if ( $thumbnail == '' ) {
						// Check whether Video Thumbnail plugin is installed.
						// If so, call get_video_thumbnail() to make sure that thumnail is generated.
						if ( class_exists( 'Video_Thumbnails' ) && function_exists( 'get_video_thumbnail' ) ) {
							// Call get_video_thumbnail to generate video thumbnail
							$video_img = get_video_thumbnail( $id );

							// If we got a result, display the image
							if ( $video_img != '' ) {

								// First, try to pick up the thumbnail in case it has been regenerated (may be the case if automatic featured image is turned on)
								$thumbnail = get_the_post_thumbnail( $id, $thumbs, $thumb_attr );

								// If thumbnail hasn't been regenerated, use Video Thumbnail (may be the full size image)
								if ( $thumbnail == '' ) {

									// First, try and find the attachment ID from the URL
									$attachment_id = self::get_attachment_id( $video_img );

									$thumb_url = $video_img;

									if ( $attachment_id != false ) {
										// Attachment found, get thumbnail
										$thumbnail = wp_get_attachment_image( $attachment_id, $thumbs, false, $thumb_attr );
									} else {
										$thumbnail .= '<img src="' . $video_img . '" alt="' . $title_value . '" title="' . $title_value . '" class="cc-child-pages-thumb"';
										if ( $lazy_load ) {
											$thumbnail .= ' loading="lazy"';
										}
										$thumbnail .= ' />';
									}
								}
							}
						}
					}

					// If thumbnail is found, display it.

					if ( ! empty( $thumbnail ) ) {

						if ( $link_thumbs && ! empty( $link ) ) {

							// Make sure title text is clean before using in an attribute.
							$title_text = sanitize_text_field( $title_value );

							$thumbs_html = '<a class="ccpage_linked_thumb" href="' . esc_url( $link ) . '"';

							if ( ! empty( $link_target ) ) {
								$thumbs_html .= ' target="' . esc_attr( $link_target ) . '"';

								// Hardening for _blank links:
								if ( '_blank' === $link_target ) {
									$thumbs_html .= ' rel="noopener noreferrer"';
								}
							}

							$thumbs_html .= ' title="' . esc_attr( $title_text ) . '">' . $thumbnail . '</a>';

						} else {
							$thumbs_html = $thumbnail;
						}
					}
				}

				$tmp_html = str_replace( '{{thumbnail}}', $thumbs_html, $tmp_html );
				$tmp_html = str_replace( '{{thumbnail_url}}', $thumb_url, $tmp_html );

				$page_excerpt = '';

				$excerpt_template = apply_filters( 'ccchildpages_excerpt_template', '<div class="ccpages_excerpt">{{page_excerpt}}</div>', $a );

				if ( $show_page_content ) {
					if ( $hide_wp_more ) {
						$page_excerpt = get_the_content( '' );
					} else {
						$hide_more    = true;
						$page_excerpt = get_the_content();
					}

					// Remove any [child_pages] shortcodes to avoid creating an infinite loop
					$page_excerpt = self::strip_shortcode( $page_excerpt );

					$page_excerpt = do_shortcode( $page_excerpt );

					$page_excerpt = apply_filters( 'the_content', $page_excerpt );

					$page_excerpt = str_replace( '{{page_excerpt}}', $page_excerpt, $excerpt_template );
				} elseif ( ! $hide_excerpt ) {
					$words = ( intval( $a['words'] ) > 0 ? intval( $a['words'] ) : 55 );

					// Shortcode attribute: which meta key to use for the excerpt.
					$use_custom_excerpt = ! empty( $a['use_custom_excerpt'] )
					? sanitize_key( $a['use_custom_excerpt'] )
					: '';

					$meta_excerpt = ''; // default - no meta_excerpt

					// If a meta excerpt field is specified, get and sanitise the value.
					if ( ! empty( $use_custom_excerpt ) ) {

						$raw_meta_excerpt = get_post_meta( $id, $use_custom_excerpt, true );

						// Make sure we’re dealing with a string.
						if ( is_string( $raw_meta_excerpt ) ) {
							$raw_meta_excerpt = trim( $raw_meta_excerpt );
						} else {
							$raw_meta_excerpt = '';
						}

						if ( $raw_meta_excerpt !== '' ) {
							// Allow safe HTML similar to normal post content.
							// Allows things like <strong>, <em>, <a>, <p>, etc.
							$meta_excerpt = wp_kses_post( $raw_meta_excerpt );
						}
					}

					// If value from custom field is set, use that - otherwise use page content
					if ( $meta_excerpt != '' ) {
						$page_excerpt = trim( $meta_excerpt );
					} elseif ( has_excerpt() ) {
						$page_excerpt = get_the_excerpt();
						if ( str_word_count( strip_tags( $page_excerpt ) ) > $words && $truncate_excerpt ) {
							$page_excerpt = wp_trim_words( $page_excerpt, $words, '...' );
						}
					} else {
						if ( $hide_wp_more ) {
							$page_excerpt = get_the_content( '' ); // get full page content without continue link
						} else {
							$page_excerpt = get_the_content(); // get full page content including continue link
						}

						// Remove any [child_pages] shortcodes to avoid creating an infinite loop
						$page_excerpt = self::strip_shortcode( $page_excerpt );
						$page_excerpt = do_shortcode( $page_excerpt );

						if ( str_word_count( wp_trim_words( $page_excerpt, $words + 10, '' ) ) > $words ) {
							// If page content is longer than allowed words,
							$trunc = '...';
						} else {
							// If page content is within allowed word count, do not add anything to the end of it
							$trunc = '';
						}
						$page_excerpt = wp_trim_words( $page_excerpt, $words, $trunc );
					}

					$page_excerpt = str_replace( '{{page_excerpt}}', $page_excerpt, $excerpt_template );
				}

				$child_list_html = '';

				if ( $depth != 1 ) {
					$child_depth = ( $depth > 1 ) ? $depth - 1 : $depth;

					$child_args = array(
						'depth'    => $child_depth,
						'child_of' => $id,
						'echo'     => false,
						'title_li' => '',
					);

					$child_list_title = apply_filters( 'ccchildpages_child_list_title', '<h4 class="ccsubpages_title">{{subpage_title}}</h4>', $a );

					$child_list_title = ( trim( $a['subpage_title'] ) == '' ) ? '' : str_replace( '{{subpage_title}}', esc_html( trim( $a['subpage_title'] ) ), $child_list_title );

					$child_list_template = apply_filters( 'ccchildpages_child_list_template', '<div class="ccsubpages">{{child_list_title}}<ul>{{child_list}}</ul></div>', $a );

					$child_list = wp_list_pages( $child_args );

					if ( trim( $child_list ) != '' ) {
						$child_list_html = str_replace( '{{child_list_title}}', $child_list_title, $child_list_template );

						$child_list_html = str_replace( '{{child_list}}', $child_list, $child_list_html );

					}
				}

				$tmp_html = str_replace( '{{excerpt}}', $page_excerpt, $tmp_html );

				$more_html = '';

				$use_custom_more = trim( $a['use_custom_more'] );
				$more_text       = $more; // default

				// If meta more field specified, get the value
				if ( $use_custom_more != '' ) {
					// Get value of custom field to be used as excerpt
					$meta_more = trim( get_post_meta( $id, $use_custom_more, true ) );
					// If value from custom field is set, use that - otherwise use page title
					if ( $meta_more != '' ) {
						$more_text = esc_html( trim( $meta_more ) );
					}
				}

				if ( ! $hide_more ) {
					$more_html = str_replace( '{{more}}', $more_text, apply_filters( 'ccchildpages_more_template', '<p class="ccpages_more"><a href="{{link}}" {{link_target}} title="{{more}}">{{more}}</a></p>', $a ) );
				}

				$more_html .= $child_list_html;

				$tmp_html = str_replace( '{{more}}', $more_html, $tmp_html );
				$tmp_html = str_replace( '{{link}}', esc_url( $link ), $tmp_html );

				if ( $link_target != '' ) {
					$link_target = 'target="' . $link_target . '"';
				}

				$tmp_html = str_replace( '{{link_target}}', $link_target, $tmp_html );

				$return_html .= $tmp_html;
			}

			if ( $posts_per_page > 0 && $page < 1 ) {

				$cc_link_format = '?' . $ccpage_var . '=%#%';

				$cc_num_results = $parent->found_posts;

				$cc_num_pages = intval( ( $cc_num_results - $offset ) / $posts_per_page );

				if ( ( $cc_num_results - $offset ) % $posts_per_page > 0 ) {
					++$cc_num_pages;
				}

				$return_html .= '<div id="ccpages_nav-' . $cc_uid . '" class="ccpages_nav">' . paginate_links(
					array(
						'format'  => $cc_link_format,
						'current' => $ccpaged,
						'total'   => $cc_num_pages,
					)
				) . '</div>';
			}

			// Reset global post query
			wp_reset_postdata();
		}

		$return_html = str_replace( '{{ccchildpages}}', $return_html, $outer_template );

		$return_html = apply_filters( 'ccchildpages_before_shortcode', '', $a ) . $return_html . apply_filters( 'ccchildpages_after_shortcode', '', $a );

		// wp_reset_query(); // Should not be required

		return $return_html;
	}

	public static function enqueue_styles() {
		$css_file      = plugins_url( 'css/styles.css', __FILE__ );
		$css_skin_file = plugins_url( 'css/skins.css', __FILE__ );
		if ( $options = get_option( 'cc_child_pages' ) ) {
			if ( empty( $options['link_skins'] ) ) {
				// undefined - so set to true for backward compatibility
				$link_skins = true;
			} elseif ( $options['link_skins'] == 'true' ) {
				$link_skins = true;
			} else {
				$link_skins = false;
			}
		} else {
			$link_skins = true;
		}

		if ( ! is_admin() ) {
			// Load main styles
			wp_register_style(
				'ccchildpagescss',
				$css_file,
				false,
				self::plugin_version
			);
			wp_enqueue_style( 'ccchildpagescss' );

			// Load skins
			if ( $link_skins ) {
				wp_register_style(
					'ccchildpagesskincss',
					$css_skin_file,
					false,
					self::plugin_version
				);
				wp_enqueue_style( 'ccchildpagesskincss' );
			}

			// Load custom CSS
			$custom_css = self::custom_css();

			if ( $custom_css != '' ) {
				wp_add_inline_style( 'ccchildpagesskincss', $custom_css );
			}
		}
	}

	public static function admin_upgrade_link_js() {
		?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {

		const upgradeSpan = document.querySelector(
			'#toplevel_page_cc-child-pages .ccchildpages-upgrade-menu'
		);

		if (upgradeSpan) {
			const link = upgradeSpan.closest('a');
			if (link) {
				link.setAttribute('target', '_blank');
				link.setAttribute('rel', 'noopener');
			}
		}

	});
	</script>
		<?php
	}

	public static function admin_upgrade_link_css() {
		?>
	<style>
	/* Upgrade menu styling */
	#toplevel_page_cc-child-pages .ccchildpages-upgrade-menu {
		font-weight: bold;
		color: #ffffff !important;
		background-color: #7A27FF !important;
		padding: .3rem .6rem .3rem .3rem;
		border-radius: .3rem
	}

	#toplevel_page_cc-child-pages .ccchildpages-upgrade-menu .dashicons {
		font-size: 16px;
		margin-right: 2px;
		color: #ff9900;
	}
	</style>
		<?php
	}

	private static function the_slug( $id ) {
		$post_data = get_post( $id, ARRAY_A );
		$slug      = $post_data['post_name'];
		return $slug;
	}

	/** */
	public static function dashboard_widgets() {
		if ( current_user_can( 'update_plugins' ) ) {
			if ( get_user_meta( get_current_user_id(), 'ccchildpages_hide_dash_widget', true ) ) {
				return;
			}

			wp_add_dashboard_widget( 'cc-child-pages-dashboard', 'CC Child Pages', 'ccchildpages::dashboard_widget_feed' );
		}
	}

	public static function dashboard_widget_feed() {
		// Compute/cached stats for speed.
		$stats = self::dashboard_get_stats();

		// Dismiss URL (admin-post handler below).
		$dismiss_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=ccchildpages_dismiss_widget' ),
			'ccchildpages_dismiss_widget'
		);

		// Settings / Docs links.
		$settings_url = admin_url( 'admin.php?page=cc-child-pages' );
		$docs_url     = 'https://docs.ccplugins.co.uk/plugins/cc-child-pages/';
		$examples_url = 'https://ccplugins.co.uk/examples/cc-child-pages/';

		// Optional: filter to allow owners to change the upgrade URL.
		$upgrade_url = apply_filters( 'ccchildpages_upgrade_url', 'https://ccplugins.co.uk/wordpress-plugins/cc-child-pages-pro/' );

		echo '<div class="cccp-widget" style="line-height:1.5">';
		// a) Quick usage summary
		echo '<p><strong>' . esc_html__( 'Usage summary', 'cc-child-pages' ) . '</strong></p>';
		echo '<ul style="margin:0 0 12px 18px">';
		echo '<li>' . esc_html__( 'Published pages:', 'cc-child-pages' ) . ' ' . esc_html( number_format_i18n( $stats['total_pages'] ) ) . '</li>';
		echo '<li>' . esc_html__( 'Pages with child pages:', 'cc-child-pages' ) . ' ' . esc_html( number_format_i18n( $stats['parents_with_children'] ) ) . '</li>';
		echo '<li>' . esc_html__( 'Pages using CC Child Pages (block or shortcode):', 'cc-child-pages' ) . ' ' . esc_html( number_format_i18n( $stats['pages_using_plugin'] ) ) . '</li>';
		echo '</ul>';

		// b) Recently updated parent pages (with children)
		if ( ! empty( $stats['recent_parents'] ) ) {
			echo '<p><strong>' . esc_html__( 'Recently updated parent pages', 'cc-child-pages' ) . '</strong></p>';
			echo '<ol style="margin:0 0 12px 18px">';
			foreach ( $stats['recent_parents'] as $p ) {
				$title = get_the_title( $p );
				$link  = get_edit_post_link( $p );
				echo '<li><a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a></li>';
			}
			echo '</ol>';
		}

		// c) Quick links
		echo '<p style="margin-top:12px">';
		echo '<a class="button" href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'cc-child-pages' ) . '</a> ';
		echo '<a class="button" href="' . esc_url( $docs_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Documentation', 'cc-child-pages' ) . '</a> ';
		echo '<a class="button" href="' . esc_url( $examples_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Examples', 'cc-child-pages' ) . '</a>';
		echo '</p>';

		// d) Footer with discreet upgrade + dismiss
		if ( ! self::show_upgrade_notice() ) {
			echo '<p style="margin-top:8px; font-size:12px; opacity:.85">';
			echo esc_html__( 'Need more skins and customisation options?', 'cc-child-pages' ) . ' ';
			echo '<a href="' . esc_url( $upgrade_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Upgrade to Pro', 'cc-child-pages' ) . '</a></p>';
		}

		echo '<p style="margin-top:8px; font-size:12px; opacity:.85"><a href="' . esc_url( $dismiss_url ) . '">' . esc_html__( 'Hide this widget', 'cc-child-pages' ) . '</a>';
			echo '</p>';

		echo '</div>';
	}

	/**
	 * Compute and cache lightweight stats for the widget.
	 *
	 * @return array {
	 *   @type int   total_pages
	 *   @type int   parents_with_children
	 *   @type int   pages_using_plugin
	 *   @type int[] recent_parents  Post IDs
	 * }
	 */
	private static function dashboard_get_stats() {
		$cache_key = 'ccchildpages_dash_stats';
		$cached    = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		// 1) Total published pages
		$total_pages = (int) ( wp_count_posts( 'page' )->publish ?? 0 );

		// 2) Pages that have children (parents)
		global $wpdb;
		$parents_with_children = (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT p.post_parent)
         FROM {$wpdb->posts} p
         WHERE p.post_type = 'page'
           AND p.post_status = 'publish'
           AND p.post_parent > 0"
		);

		// 3) Pages using the plugin (either shortcode or block)
		// Allow site owners (or your Pro add-on) to declare exact block names used.
		$block_slugs = apply_filters(
			'ccchildpages_block_slugs',
			array(
				// If your registered block name differs, filter this.
				'caterhamcomputing/cc-child-pages',
				'cc/child-pages',
			)
		);

		// Build a LIKE set for block comments: <!-- wp:block/name -->
		$like_blocks = array();
		foreach ( $block_slugs as $slug ) {
			$like_blocks[] = $wpdb->prepare( 'post_content LIKE %s', '%<!-- wp:' . $wpdb->esc_like( $slug ) . ' %' );
		}
		// Shortcode usage
		$like_shortcode = $wpdb->prepare( 'post_content LIKE %s', '%[child_pages%' );

		$where_fragments = array_merge( array( $like_shortcode ), $like_blocks );
		$where_sql       = implode( ' OR ', $where_fragments );

		$pages_using_plugin = (int) $wpdb->get_var(
			"SELECT COUNT(ID)
           FROM {$wpdb->posts}
          WHERE post_type = 'page'
            AND post_status = 'publish'
            AND ( {$where_sql} )"
		);

		// 4) Recently updated parent pages (IDs, max 5)
		// Filterable args so power users can tweak.
		$recent_args = apply_filters(
			'ccchildpages_dashboard_parent_query_args',
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 5,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		// Select parents that actually have at least one child.
		$recent_parents = get_posts( $recent_args );
		if ( ! empty( $recent_parents ) ) {
			$recent_parents = array_values(
				array_filter(
					$recent_parents,
					static function ( $pid ) use ( $wpdb ) {
						$count = (int) $wpdb->get_var(
							$wpdb->prepare(
								"SELECT COUNT(1) FROM {$wpdb->posts}
                  WHERE post_type = 'page'
                    AND post_status = 'publish'
                    AND post_parent = %d",
								$pid
							)
						);
						return $count > 0;
					}
				)
			);
			// Keep top 5 just in case filter expanded list.
			$recent_parents = array_slice( $recent_parents, 0, 5 );
		}

		$stats = array(
			'total_pages'           => $total_pages,
			'parents_with_children' => $parents_with_children,
			'pages_using_plugin'    => $pages_using_plugin,
			'recent_parents'        => $recent_parents,
		);

		// Cache for 12 hours.
		set_transient( $cache_key, $stats, 12 * HOUR_IN_SECONDS );

		return $stats;
	}

	/**
	 * Handle per-user dismissal of the widget.
	 */
	public static function dashboard_handle_dismiss() {
		// Capability for dismiss (can be stricter than view).
		$cap = apply_filters( 'ccchildpages_dashboard_dismiss_capability', 'edit_pages' );
		if ( ! current_user_can( $cap ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'cc-child-pages' ) );
		}
		check_admin_referer( 'ccchildpages_dismiss_widget' );

		update_user_meta( get_current_user_id(), 'ccchildpages_hide_dash_widget', 1 );
		wp_safe_redirect( admin_url() );
		exit;
	}

	/**
	 * Helper to clear cached stats (e.g. after settings save).
	 * Call self::dashboard_clear_cache() when you know content changed drastically.
	 */
	public static function dashboard_clear_cache() {
		delete_transient( 'ccchildpages_dash_stats' );
	}

	public static function tinymce_buttons() {
		if ( $options = get_option( 'cc_child_pages' ) ) {
			if ( empty( $options['show_button'] ) ) {
				// undefined - so set to true for backward compatibility
				$show_button = true;
			} elseif ( $options['show_button'] == 'true' ) {
				$show_button = true;
			} else {
				$show_button = false;
			}
		} else {
			$show_button = true;
		}

		if ( $show_button ) {
			add_filter( 'mce_external_plugins', 'ccchildpages::add_childpages_buttons' );
			add_filter( 'mce_buttons', 'ccchildpages::register_childpages_buttons' );
		}
	}

	public static function add_childpages_buttons( $plugin_array ) {
		$plugin_array['ccchildpages'] = plugins_url( 'js/ccchildpages-plugin.js', __FILE__ );
		return $plugin_array;
	}

	public static function register_childpages_buttons( $buttons ) {
		array_push( $buttons, 'ccchildpages' );
		return $buttons;
	}

	/*
	 * Add options page ...
	 */

	// Set default values on activation ...
	public static function options_activation() {
		$options                = array();
		$options['show_button'] = 'true';
		$options['link_skins']  = 'true';
		$options['customcss']   = '';

		$options = apply_filters( 'ccchildpages_options', $options );

		add_option( 'cc_child_pages', $options, '', 'yes' );
	}

	// Register settings ...
	public static function register_options() {
		register_setting( 'cc_child_pages', 'cc_child_pages' );
	}

	// Add submenu
	public static function options_menu() {
		$page_title = apply_filters( 'ccchildpages_menu_page_title', __( 'CC Child Pages', 'cc-child-pages' ) );
		$menu_title = apply_filters( 'ccchildpages_menu_title', __( 'CC Child Pages', 'cc-child-pages' ) );

		$function   = apply_filters( 'ccchildpages_menu_function', array( 'ccchildpages', 'options_page' ) );
		$capability = apply_filters( 'ccchildpages_menu_capability', 'manage_options' );
		$menu_slug  = apply_filters( 'ccchildpages_menu_slug', 'cc-child-pages' );
		$icon       = apply_filters( 'ccchildpages_menu_icon', 'dashicons-screenoptions' );
		$position   = apply_filters( 'ccchildpages_menu_position', null );

		// Top-level menu.
		add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$icon,
			$position
		);

		// “Settings” submenu (same page for now).
		add_submenu_page(
			$menu_slug,
			__( 'Settings', 'cc-child-pages' ),
			__( 'Settings', 'cc-child-pages' ),
			$capability,
			$menu_slug,
			$function
		);

		// External "Upgrade" submenu item.
		if ( ! self::show_upgrade_notice() ) {
			add_submenu_page(
				$menu_slug,
				__( 'Upgrade to Pro', 'cc-child-pages' ),
				'<span class="ccchildpages-upgrade-menu"><span class="dashicons dashicons-star-filled"></span> ' . __( 'Upgrade', 'cc-child-pages' ) . '</span>',
				$capability,
				'https://ccplugins.co.uk/wordpress-plugins-by-cc-plugins/cc-child-pages-pro/',
				null
			);}
	}

	// Display options page
	public static function options_page() {
		// Get options with sane defaults (keeps existing behaviour)
		$options = get_option( 'cc_child_pages', array() );
		$options = wp_parse_args(
			$options,
			array(
				'show_button' => 'true', // default true (back-compat)
				'link_skins'  => 'true', // default true (back-compat)
				'customcss'   => '',
			)
		);

		$show_button = ( 'true' === $options['show_button'] );
		$link_skins  = ( 'true' === $options['link_skins'] );
		$customcss   = (string) $options['customcss'];
		?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'CC Child Pages options', 'cc-child-pages' ); ?></h1>
		<hr class="wp-header-end" />

		<?php
			do_action( 'ccchildpages_options_before_form', $options ); // allow extension of settings form
		?>

		<form method="post" id="cc_child_page_form" action="options.php">
			<?php settings_fields( 'cc_child_pages' ); ?>

			<table class="form-table" role="presentation">
				<tbody>
				<?php
					do_action( 'ccchildpages_options_form_top', $options ); // allow extension of settings form
				?>
					<tr>
						<th scope="row">
							<label for="cccp-show-button"><?php esc_html_e( 'Add button to the visual editor (Classic Editor)', 'cc-child-pages' ); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Add button to the visual editor (Classic Editor)', 'cc-child-pages' ); ?></legend>

								<label>
									<input
										type="radio"
										id="cccp-show-button"
										name="cc_child_pages[show_button]"
										value="true"
										<?php checked( true, $show_button ); ?>
									/>
									<?php esc_html_e( 'Yes', 'cc-child-pages' ); ?>
								</label>
								<br />
								<label>
									<input
										type="radio"
										name="cc_child_pages[show_button]"
										value="false"
										<?php checked( false, $show_button ); ?>
									/>
									<?php esc_html_e( 'No', 'cc-child-pages' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cccp-link-skins"><?php esc_html_e( 'Enqueue Skins CSS', 'cc-child-pages' ); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Enqueue Skins CSS', 'cc-child-pages' ); ?></legend>

								<label>
									<input
										type="radio"
										id="cccp-link-skins"
										name="cc_child_pages[link_skins]"
										value="true"
										<?php checked( true, $link_skins ); ?>
										aria-describedby="cccp-link-skins-desc"
									/>
									<?php esc_html_e( 'Yes', 'cc-child-pages' ); ?>
								</label>
								<br />
								<label>
									<input
										type="radio"
										name="cc_child_pages[link_skins]"
										value="false"
										<?php checked( false, $link_skins ); ?>
										aria-describedby="cccp-link-skins-desc"
									/>
									<?php esc_html_e( 'No', 'cc-child-pages' ); ?>
								</label>

								<p class="description" id="cccp-link-skins-desc">
									<?php esc_html_e( 'If you are providing your own CSS for the styling of the child pages, you can opt to not load the CSS for the Skins.', 'cc-child-pages' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<?php if ( '' !== $customcss ) : ?>
						<tr>
							<th scope="row">
								<label for="cccp-customcss"><?php esc_html_e( 'Custom CSS', 'cc-child-pages' ); ?></label>
							</th>
							<td>
								<div class="notice notice-warning inline">
									<p>
										<span class="dashicons dashicons-warning" aria-hidden="true" style="vertical-align: text-bottom;"></span>
										<strong><?php esc_html_e( 'Deprecated:', 'cc-child-pages' ); ?></strong>
										<?php esc_html_e( 'This function is deprecated and may be removed in future releases. It is strongly recommended that all custom CSS is moved to the theme customiser.', 'cc-child-pages' ); ?>
									</p>
								</div>

								<textarea
									name="cc_child_pages[customcss]"
									id="cccp-customcss"
									class="large-text code"
									rows="10"
								><?php echo esc_textarea( $customcss ); ?></textarea>

								<?php
								/**
								 * Preserve original behaviour: only fire this hook when custom CSS exists.
								 * Add-ons that print extra fields can hook here and render their own markup.
								 */
								?>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					do_action( 'ccchildpages_options_form', $options ); // allow extension of settings form
					?>

				</tbody>
			</table>

				<?php submit_button( __( 'Save Changes', 'cc-child-pages' ) ); ?>
		</form>
	</div>
		<?php
	}

	/*
	 * CSS Version
	 */
	public static function css_version() {
		$legacy_css = '';
		if ( $options = get_option( 'cc_child_pages' ) ) {
			if ( ! empty( $options['legacy_css'] ) ) {
				if ( trim( $options['legacy_css'] ) != '' ) {
					$legacy_css = trim( $options['legacy_css'] );
				}
			}
		}

		$css_version_class = ( $legacy_css === 'true' ) ? 'cclegacy' : 'ccflex';

		return $css_version_class;
	}

	/*
	 * Output Custom CSS
	 */
	public static function custom_css() {
		$custom_css = '';
		if ( $options = get_option( 'cc_child_pages' ) ) {
			if ( ! empty( $options['customcss'] ) ) {
				if ( trim( $options['customcss'] ) != '' ) {
					$custom_css = trim( $options['customcss'] );
				}
			}
		}
		return self::sanitize_css( $custom_css );
	}

	/*
	 * Sanitize CSS output
	 */
	public static function sanitize_css( $css ) {
		$search = array(
			'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@',         // Strip multi-line comments
		);

		$output = preg_replace( $search, '', $css );
		return $output;
	}

	/*
	 * Show Excerpt for Pages ...
	 */
	public static function show_page_excerpt() {
		add_post_type_support( 'page', 'excerpt' );
	}

	/*
	 * Get Attachment ID from URL
	 */
	public static function get_attachment_id( $url ) {
		$dir = wp_upload_dir();

		// baseurl never has a trailing slash
		if ( false === strpos( $url, $dir['baseurl'] . '/' ) ) {
			// URL points to a place outside of upload directory
			return false;
		}

		$file  = basename( $url );
		$query = array(
			'post_type'  => 'attachment',
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
				),
			),
		);

		$query['meta_query'][0]['key'] = '_wp_attached_file';

		// query attachments
		$ids = get_posts( $query );

		if ( ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				// first entry of returned array is the URL
				$tmp_url = wp_get_attachment_image_src( $id, 'full' );
				if ( $url === array_shift( $tmp_url ) ) {
					return $id;
				}
			}
		}

		return false;
	}

	/*
	 * Get size information for thumbnail by size
	 */
	private static function get_image_dimensions( $thumbs ) {
		global $_wp_additional_image_sizes;

		$dimensions = array();

		// If a default image size, use get options method
		if ( in_array( $thumbs, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$dimensions['height'] = get_option( $thumbs . '_size_h' );
			$dimensions['width']  = get_option( $thumbs . '_size_w' );
		} elseif ( isset( $_wp_additional_image_sizes[ $thumbs ] ) ) {
			$dimensions['height'] = $_wp_additional_image_sizes[ $thumbs ]['height'];
			$dimensions['width']  = $_wp_additional_image_sizes[ $thumbs ]['width'];
		}

		return $dimensions;
	}

	/*
	 * Show plugin links
	 */
	public static function plugin_action_links( $links ) {
		$cc_links = array( '<a href="https://docs.ccplugins.co.uk/plugins/cc-child-pages/" target="_blank">' . __( 'Documentation', 'cc-child-pages' ) . '</a>' );

		$links = array_merge( (array) $links, $cc_links );

		return $links;
	}

	public static function plugin_row_meta( $links, $file ) {
		$current_plugin = basename( dirname( $file ) );

		$cc_links = array();

		if ( $current_plugin == 'cc-child-pages' ) {
			$cc_links[] = '<a href="admin.php?page=cc-child-pages">' . __( 'Settings...', 'cc-child-pages' ) . '</a>';
			$cc_links[] = '<a href="https://wordpress.org/support/view/plugin-reviews/cc-child-pages" target="_blank"><strong>' . __( 'Rate this plugin...', 'cc-child-pages' ) . '</strong></a>';
		}

		$links = array_merge( (array) $links, $cc_links );

		return $links;
	}

	public static function add_query_strings( $vars ) {
		// Register query strings for paging ...
		for ( $i = 1; $i < 25; $i++ ) {
			$vars[] = 'ccpage' . $i;
		}
		$vars[] = 'ccchildpages';
		return $vars;
	}

	public static function query_offset( &$query ) {
		// Check that query was called from CC Child Pages
		if ( ! isset( $query->query_vars['ccchildpages'] ) ) {
			return;
		}
		if ( $query->query_vars['ccchildpages'] != 'true' ) {
			return;
		}

		// Check whether offset has been specified
		$offset = ( isset( $query->query_vars['offset'] ) ) ? intval( $query->query_vars['offset'] ) : -1;
		if ( $offset < 1 ) {
			return;
		}

		// If we made it this far, the query is from CC Child Pages and an Offset has been specified!
		$posts_per_page = ( isset( $query->query_vars['posts_per_page'] ) ) ? intval( $query->query_vars['posts_per_page'] ) : -1;

		if ( $query->is_paged ) {
			$paged = intval( $query->query_vars['paged'] );

			if ( $paged > 0 ) {
				$page_offset = $offset + ( ( $paged - 1 ) * $posts_per_page );
			} else {
				$page_offset = $offset;
			}
		} else {
			$page_offset = $offset;
		}
		$query->set( 'offset', $page_offset );

		// By default, if posts_per_page is set to -1 offset is ignored.
		// To get around this, if posts_per_page is set to -1 we will set it to something large
		if ( $posts_per_page < 1 ) {
			$query->set( 'posts_per_page', 1000000 );
		}
	}

	public static function exempt_from_wptexturize( $shortcodes ) {
		$shortcodes[] = 'child_pages';
		return $shortcodes;
	}

	// public static function strip_shortcode( $page_excerpt ) {
	// Remove any [child_pages] shortcodes to avoid the possibility creating a loop,
	// and also to avoid the mess that may be created by having nested child pages displayed
	// $page_excerpt = str_replace( '[child_pages]', '', $page_excerpt ); // remove basic shortcode
	// $page_excerpt = preg_replace( '~(?:\[child_pages/?)[^/\]]+/?\]~s', '', $page_excerpt ); // remove shortcode with parameters
	// return $page_excerpt;
	// }

	public static function strip_shortcode( $content ) {
		// 1) Remove [child_pages] shortcode in any form using WP's official pattern
		if ( function_exists( 'get_shortcode_regex' ) ) {
			// Limit to just this shortcode so we don't strip other shortcodes the user may want.
			$pattern = get_shortcode_regex( array( 'child_pages' ) );
			$content = preg_replace( '/' . $pattern . '/s', '', $content );
		} else {
			// Fallback if get_shortcode_regex is unavailable for some reason.
			// Matches [child_pages ...] and optional closing [/child_pages]
			$content = preg_replace(
				'/\[\s*child_pages\b[^\]]*\](?:.*?\[\/\s*child_pages\s*\])?/si',
				'',
				$content
			);
		}

		// 2) Remove the Gutenberg block form (self-closing and paired)
		// Self-closing block: <!-- wp:caterhamcomputing/cc-child-pages {...} /-->
		$content = preg_replace(
			'/<!--\s*wp:caterhamcomputing\/cc-child-pages\b[^>]*?\/\s*-->/is',
			'',
			$content
		);

		// Paired block:
		// <!-- wp:caterhamcomputing/cc-child-pages {...} -->
		// ... inner HTML ...
		// <!-- /wp:caterhamcomputing/cc-child-pages -->
		$content = preg_replace(
			'/<!--\s*wp:caterhamcomputing\/cc-child-pages\b[^>]*?-->(.*?)<!--\s*\/wp:caterhamcomputing\/cc-child-pages\s*-->/is',
			'',
			$content
		);

		// Optional: also catch the bare "[child_pages]" without attrs (covered by regex, but harmless to leave)
		// $content = str_replace( '[child_pages]', '', $content );

		return $content;
	}

	public static function sanitize_class_list( $classes ) {
		$class_array = explode( ' ', $classes );
		$class_list  = '';

		foreach ( $class_array as $class ) {
			$clean_class = trim( sanitize_html_class( $class ) );

			if ( $clean_class != '' ) {
				if ( $class_list != '' ) {
					$class_list .= ' ';
				}
				$class_list .= $clean_class;
			}
		}

		return $class_list;
	}

	public static function default_skin_list() {
		$css_version_class = self::css_version();

		if ( $css_version_class == 'ccflex' ) {
			// Default skins
			return array(
				'simple' => __( 'Simple', 'cc-child-pages' ),
				'red'    => __( 'Red', 'cc-child-pages' ),
				'green'  => __( 'Green', 'cc-child-pages' ),
				'blue'   => __( 'Blue', 'cc-child-pages' ),
				'sleek'  => __( 'Sleek', 'cc-child-pages' ),
				'bold'   => __( 'Bold', 'cc-child-pages' ),
			);} else { // Legacy skins
			return array(
				'simple' => __( 'Simple', 'cc-child-pages' ),
				'red'    => __( 'Red', 'cc-child-pages' ),
				'green'  => __( 'Green', 'cc-child-pages' ),
				'blue'   => __( 'Blue', 'cc-child-pages' ),
			);}
	}

	public static function skin_list() {
		$default_skins = self::default_skin_list();

		return apply_filters( 'cc_child_pages_editor_skins', $default_skins );
	}

	public static function enqueue_block_editor_assets() {
		// Your block name exactly as in block.json
		$block_name = 'caterhamcomputing/cc-child-pages';

		// Get the block type to see which handles WP registered for it
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
		if ( ! $block_type ) {
			return; // not registered yet (shouldn't happen if you register on init)
		}

		// Collect the relevant script handles that might be loaded in the editor
		$handles = array();

		// editor_script is the one we want for the block editor UI
		if ( ! empty( $block_type->editor_script ) ) {
			$handles = array_merge( $handles, (array) $block_type->editor_script );
		}

		if ( empty( $handles ) ) {
			return;
		}

		$data = array(
			'skins'      => self::skin_list(), // your single source of truth, filterable
			'proPresent' => self::show_upgrade_notice(),
			'docsUrl'    => 'https://docs.ccplugins.co.uk/plugins/cc-child-pages/',
			'proUrl'     => 'https://ccplugins.co.uk/plugins/cc-child-pages-pro/',
		);

		foreach ( array_unique( $handles ) as $handle ) {
			// Make sure the script is at least registered before localizing
			if ( wp_script_is( $handle, 'registered' ) ) {
				wp_localize_script( $handle, 'CCChildPagesEditorData', $data );

				if ( function_exists( 'wp_set_script_translations' ) ) {
					wp_set_script_translations(
						$handle,
						'cc-child-pages',
						dirname( __DIR__ ) . '/languages'
					);
				}
			}
		}
	}

	public static function show_upgrade_notice() {
		$pro_present = defined( 'CCCP_PRO_VER' )
		|| class_exists( '\CaterhamComputing\CCChildPagesPro\Plugin' )
		|| function_exists( 'ccpro_css_version' )
		|| apply_filters( 'ccchildpages/pro_present', false );

		return $pro_present;
	}
}

/*EOF*/
