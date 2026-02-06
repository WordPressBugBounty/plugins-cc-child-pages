<?php
/**
 * Server render for CC Child Pages block (shortcode string approach, sanitized).
 *
 * Notes:
 * - Booleans coming from the block can be true/false or "true"/"false". We normalize them
 *   and ALWAYS emit lowercase "true"/"false" in the shortcode.
 * - All interpolated attributes are escaped with esc_attr().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// 1) Start with empty arrays
$extra_classes = array();
$extra_styles  = array(); // associative map preferred: [ '--my-css-variable1' => '15px', '--cmy-css-variable2' => '1em' ]

// 2) Let extensions add to them
$extra_classes = apply_filters( 'ccchildpages_wrapper_classes', $extra_classes, $attributes );
$extra_styles  = apply_filters( 'ccchildpages_inline_vars_map', $extra_styles, $attributes );

// 3) Normalise classes (array → string)
$extra_classes = is_array( $extra_classes ) ? $extra_classes : array();
$extra_classes = array_map( 'sanitize_html_class', array_filter( array_map( 'strval', $extra_classes ) ) );
$extra_classes = array_values( array_unique( $extra_classes ) );
$class_str     = implode( ' ', $extra_classes );

// 4) Normalise styles (array → string; override duplicate props)
$decls = array();
if ( is_array( $extra_styles ) ) {
	foreach ( $extra_styles as $prop => $val ) {
		// Allow either ['--prop' => 'val'] or ['prop' => 'val'] (we'll add -- if missing)
		if ( is_int( $prop ) ) {
			// Also allow numeric entries like '--x:1em' or 'color:red'
			$pair = (string) $val;
			if ( strpos( $pair, ':' ) !== false ) {
				list( $p, $v ) = array_map( 'trim', explode( ':', $pair, 2 ) );
				$prop          = $p;
				$val           = $v;
			} else {
				continue;
			}
		}
		$prop = trim( (string) $prop );
		$val  = trim( (string) $val );
		if ( $prop === '' || $val === '' ) {
			continue;
		}
		// Prefer custom properties; add leading `--` if omitted
		if ( $prop[0] !== '-' ) {
			$prop = '--' . ltrim( $prop, '-' );
		}
		$decls[ strtolower( $prop ) ] = $val; // last one wins
	}
}

$style_str = '';
foreach ( $decls as $p => $v ) {
	$style_str .= $p . ':' . $v . ';';
}

// 5) Hand both to Core; it merges with existing wrapper attrs
$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $class_str,
		'style' => $style_str,
	)
);

/** Helpers */
if ( ! function_exists( 'cccp_bool_to_string' ) ) {
	/**
	 * Normalize various boolean-y inputs (bool, "true"/"false", "1"/"0") to "true" or "false" (lowercase).
	 */
	function cccp_bool_to_string( $val, $default = false ) {
		if ( is_bool( $val ) ) {
			return $val ? 'true' : 'false';
		}
		if ( is_string( $val ) ) {
			$lc = strtolower( trim( $val ) );
			if ( $lc === 'true' || $lc === '1' ) {
				return 'true';
			}
			if ( $lc === 'false' || $lc === '0' ) {
				return 'false';
			}
		}
		return $default ? 'true' : 'false';
	}
}
if ( ! function_exists( 'cccp_normalize_csv_ids' ) ) {
	function cccp_normalize_csv_ids( $value ) {
		$ids = is_array( $value ) ? $value : explode( ',', (string) $value );
		$ids = array_map( 'trim', $ids );
		$ids = array_filter( $ids, static fn( $v ) => $v !== '' );
		$ids = array_map( 'intval', $ids );
		$ids = array_filter( $ids, static fn( $v ) => $v > 0 );
		$ids = array_unique( $ids );
		return implode( ',', $ids );
	}
}
if ( ! function_exists( 'cccp_sanitize_orderby' ) ) {
	function cccp_sanitize_orderby( $val ) {
		$val     = (string) $val;
		$allowed = array(
			'none',
			'ID',
			'author',
			'title',
			'name',
			'type',
			'date',
			'modified',
			'parent',
			'rand',
			'comment_count',
			'menu_order',
			'post__in',
			'post_name__in',
			'post_parent__in',
			// common variants ccchildpages maps internally:
			'id',
			'post_id',
			'post_author',
			'post_date',
			'post_modified',
			'post_title',
			'post_name',
			'random',
			'RAND',
			'RANDOM',
		);
		return in_array( $val, $allowed, true ) ? $val : 'menu_order';
	}
}
if ( ! function_exists( 'cccp_sanitize_order' ) ) {
	function cccp_sanitize_order( $val ) {
		$val = strtoupper( (string) $val );
		return ( $val === 'ASC' || $val === 'DESC' ) ? $val : 'ASC';
	}
}
if ( ! function_exists( 'cccp_sanitize_link_target' ) ) {
	function cccp_sanitize_link_target( $raw ) {
		if ( $raw === '' || $raw === null ) {
			return '';
		}
		$raw     = (string) $raw;
		$allowed = array( '_self', '_blank', '_parent', '_top' );
		if ( in_array( $raw, $allowed, true ) ) {
			return $raw;
		}
		// allow named browsing contexts: letters, numbers, '_', '-', ':'
		return preg_match( '/^[A-Za-z0-9:_-]{1,50}$/', $raw ) ? $raw : '';
	}
}

if ( ! function_exists( 'cccp_sanitize_meta_key' ) ) {
	/**
	 * Allow common meta key chars: letters, numbers, underscore, colon, dash, dot.
	 * Do NOT force lowercase (meta keys can be case-sensitive).
	 * Returns '' if invalid.
	 */
	function cccp_sanitize_meta_key( $raw ) {
		if ( $raw === null ) {
			return '';
		}
		$k = trim( wp_unslash( (string) $raw ) );
		// keep it sane; 1-64 chars is generous and avoids abuse
		if ( $k === '' || strlen( $k ) > 64 ) {
			return '';
		}
		return preg_match( '/^[A-Za-z0-9_:\-\.]+$/', $k ) ? $k : '';
	}
}


/** Read + sanitize attributes */

// Integers
$depth  = isset( $attributes['depth'] ) ? max( 0, intval( $attributes['depth'] ) ) : 1;
$offset = isset( $attributes['offset'] ) ? max( 0, intval( $attributes['offset'] ) ) : 0;
$page   = ( array_key_exists( 'page', $attributes ) && $attributes['page'] !== null && $attributes['page'] !== '' ) ? max( 1, intval( $attributes['page'] ) ) : 0;
$words  = isset( $attributes['words'] ) ? max( 1, intval( $attributes['words'] ) ) : 55;

$posts_per_page = isset( $attributes['postsPerPage'] ) ? max( 1, intval( $attributes['postsPerPage'] ) ) : 0;
$limit          = isset( $attributes['limit'] ) ? max( 1, intval( $attributes['limit'] ) ) : 0;

// CSV IDs
$id_csv           = isset( $attributes['id'] ) ? cccp_normalize_csv_ids( $attributes['id'] ) : '';
$exclude_csv      = isset( $attributes['exclude'] ) ? cccp_normalize_csv_ids( $attributes['exclude'] ) : '';
$exclude_tree_csv = isset( $attributes['excludeTree'] ) ? cccp_normalize_csv_ids( $attributes['excludeTree'] ) : '';
$page_ids_csv     = isset( $attributes['pageIDs'] ) ? cccp_normalize_csv_ids( $attributes['pageIDs'] ) : '';

// Sorting
$orderby = isset( $attributes['orderby'] ) ? cccp_sanitize_orderby( $attributes['orderby'] ) : 'menu_order';
$order   = isset( $attributes['order'] ) ? cccp_sanitize_order( $attributes['order'] ) : 'ASC';

// Post Statuses
$post_status = isset( $attributes['postStatus'] ) ? $attributes['postStatus'] : '';

// Link
$link_url    = isset( $attributes['link'] ) ? esc_url_raw( $attributes['link'] ) : '';
$link_target = isset( $attributes['linkTarget'] ) ? cccp_sanitize_link_target( $attributes['linkTarget'] ) : '';

// Display / toggles (normalize to "true"/"false")
$list              = isset( $attributes['list'] ) ? cccp_bool_to_string( $attributes['list'] ) : 'false';
$show_page_content = isset( $attributes['showPageContent'] ) ? ( cccp_bool_to_string( $attributes['showPageContent'] ) === 'true' ) : false;

$show_title             = array_key_exists( 'showTitle', $attributes ) ? ( cccp_bool_to_string( $attributes['showTitle'], true ) === 'true' ) : true;
$link_titles            = array_key_exists( 'linkTitles', $attributes ) ? ( cccp_bool_to_string( $attributes['linkTitles'], true ) === 'true' ) : true;
$use_custom_title_class = array_key_exists( 'useCustomTitleClass', $attributes ) ? ( cccp_bool_to_string( $attributes['useCustomTitleClass'], true ) === 'true' ) : true;
$custom_title_class     = array_key_exists( 'customTitleClass', $attributes ) ? sanitize_html_class( $attributes['customTitleClass'] ) : '';
$show_excerpt           = array_key_exists( 'showExcerpt', $attributes ) ? ( cccp_bool_to_string( $attributes['showExcerpt'], true ) === 'true' ) : true;
$truncate_excerpt       = array_key_exists( 'truncateExcerpt', $attributes ) ? ( cccp_bool_to_string( $attributes['truncateExcerpt'], true ) === 'true' ) : true;
$show_wp_more           = isset( $attributes['showWPMore'] ) ? ( cccp_bool_to_string( $attributes['showWPMore'] ) === 'true' ) : false;
$show_more              = array_key_exists( 'showMore', $attributes ) ? ( cccp_bool_to_string( $attributes['showMore'], true ) === 'true' ) : true;
$show_author            = array_key_exists( 'showAuthor', $attributes ) ? ( cccp_bool_to_string( $attributes['showAuthor'], true ) === 'true' ) : true;
$show_date_created      = array_key_exists( 'showDateCreated', $attributes ) ? ( cccp_bool_to_string( $attributes['showDateCreated'], true ) === 'true' ) : true;
$show_date_modified     = array_key_exists( 'showDateModified', $attributes ) ? ( cccp_bool_to_string( $attributes['showDateModified'], true ) === 'true' ) : true;

$show_siblings     = isset( $attributes['siblings'] ) ? ( cccp_bool_to_string( $attributes['siblings'] ) === 'true' ) : false;
$show_current_page = isset( $attributes['showCurrentPage'] ) ? ( cccp_bool_to_string( $attributes['showCurrentPage'] ) === 'true' ) : false;

$use_pagination      = isset( $attributes['usePagination'] ) ? ( cccp_bool_to_string( $attributes['usePagination'] ) === 'true' ) : false;
$use_limit           = isset( $attributes['useLimit'] ) ? ( cccp_bool_to_string( $attributes['useLimit'] ) === 'true' ) : false;
$show_specific_pages = isset( $attributes['selectSpecificPages'] ) ? ( cccp_bool_to_string( $attributes['selectSpecificPages'] ) === 'true' ) : false;

$ignore_sticky_posts = isset( $attributes['ignoreStickyPosts'] ) ? ( cccp_bool_to_string( $attributes['ignoreStickyPosts'] ) === 'true' ) : 'false';

$use_cc_skin = isset( $attributes['useCCSkin'] ) ? ( cccp_bool_to_string( $attributes['useCCSkin'] ) === 'true' ) : false;

// Presentation
$skin           = isset( $attributes['skin'] ) ? sanitize_html_class( $attributes['skin'] ) : '';
$item_class     = isset( $attributes['itemClass'] ) ? sanitize_html_class( $attributes['itemClass'] ) : '';
$cols           = isset( $attributes['cols'] ) ? max( 1, intval( $attributes['cols'] ) ) : 0;
$thumbs         = isset( $attributes['thumbs'] ) ? sanitize_key( $attributes['thumbs'] ) : '';
$link_thumbs    = array_key_exists( 'linkThumbs', $attributes ) ? ( cccp_bool_to_string( $attributes['linkThumbs'], true ) === 'true' ) : true;
$more_text      = isset( $attributes['more'] ) ? $attributes['more'] : '';
$subpage_title  = isset( $attributes['subpageTitle'] ) ? $attributes['subpageTitle'] : '';
$use_legacy_css = isset( $attributes['useLegacyCSS'] ) ? ( cccp_bool_to_string( $attributes['useLegacyCSS'] ) === 'true' ) : false;

// Custom-field overrides
$use_custom_excerpt = isset( $attributes['useCustomExcerpt'] ) ? ( cccp_bool_to_string( $attributes['useCustomExcerpt'] ) === 'true' ) : false;
$custom_excerpt_key = isset( $attributes['customExcerptField'] ) ? cccp_sanitize_meta_key( $attributes['customExcerptField'] ) : '';

$use_custom_title = isset( $attributes['useCustomTitle'] ) ? ( cccp_bool_to_string( $attributes['useCustomTitle'] ) === 'true' ) : false;
$custom_title_key = isset( $attributes['customTitleField'] ) ? cccp_sanitize_meta_key( $attributes['customTitleField'] ) : '';

$use_custom_more = isset( $attributes['useCustomMore'] ) ? ( cccp_bool_to_string( $attributes['useCustomMore'] ) === 'true' ) : false;
$custom_more_key = isset( $attributes['customMoreField'] ) ? cccp_sanitize_meta_key( $attributes['customMoreField'] ) : '';

$use_custom_link = isset( $attributes['useCustomLink'] ) ? ( cccp_bool_to_string( $attributes['useCustomLink'] ) === 'true' ) : false;
$custom_link_key = isset( $attributes['customLinkField'] ) ? cccp_sanitize_meta_key( $attributes['customLinkField'] ) : '';

$use_custom_target = isset( $attributes['useCustomLinkTarget'] ) ? ( cccp_bool_to_string( $attributes['useCustomLinkTarget'] ) === 'true' ) : false;
$custom_target_key = isset( $attributes['customLinkTargetField'] ) ? cccp_sanitize_meta_key( $attributes['customLinkTargetField'] ) : '';

$use_custom_thumbs = isset( $attributes['useCustomThumbs'] ) ? ( cccp_bool_to_string( $attributes['useCustomThumbs'] ) === 'true' ) : false;
$custom_thumbs_key = isset( $attributes['customThumbsField'] ) ? cccp_sanitize_meta_key( $attributes['customThumbsField'] ) : '';
?>
<div <?php echo $block_wrapper_attributes; ?>>
	<?php
	$parts = array( '[child_pages' );

	// Selection logic
	if ( $show_siblings ) {
		$parts[] = 'siblings="true"';
		if ( $show_current_page ) {
			$parts[] = 'show_current_page="true"';
		}
	} elseif ( $show_specific_pages ) {
		if ( $page_ids_csv !== '' ) {
			$parts[] = 'page_ids="' . esc_attr( $page_ids_csv ) . '"';
		}
	} else {
		if ( $id_csv !== '' ) {
			$parts[] = 'id="' . esc_attr( $id_csv ) . '"';
		}
		if ( $exclude_csv !== '' ) {
			$parts[] = 'exclude="' . esc_attr( $exclude_csv ) . '"';
		}
	}

	// Pagination / page / limit
	if ( $use_pagination && $posts_per_page > 0 ) {
		$parts[] = 'posts_per_page="' . esc_attr( $posts_per_page ) . '"';
	}
	if ( $page > 0 ) {
		$parts[] = 'page="' . esc_attr( $page ) . '"';
	}
	if ( $use_limit && $limit > 0 ) {
		$parts[] = 'limit="' . esc_attr( $limit ) . '"';
	}

	// Ignore sticky posts
	$parts[] = 'ignore_sticky_posts="' . cccp_bool_to_string( $ignore_sticky_posts ) . '"';

	// Content flags
	if ( $show_page_content ) {
		$parts[] = 'show_page_content="true"';
	} else {
		if ( $show_title ) {
			if ( $link_titles ) {
				$parts[] = 'link_titles="true"';

				if ( $use_custom_title_class && $custom_title_class !== '' ) {
					$parts[] = 'title_link_class="' . esc_attr( $custom_title_class ) . '"';
				}
			}
		} else {
			$parts[] = 'hide_title="true"';
		}
		if ( ! $show_excerpt ) {
			$parts[] = 'hide_excerpt="true"';
		} else {
			if ( $truncate_excerpt ) {
				if ( $words > 0 ) {
					$parts[] = 'words="' . esc_attr( $words ) . '"';
				}
			} else {
				$parts[] = 'truncate_excerpt="true"';
			}
			if ( ! $show_wp_more ) {
				$parts[] = 'hide_wp_more="true"';
			}
		}
		if ( ! $show_more ) {
			$parts[] = 'hide_more="true"';
		} elseif ( $more_text != '' ) {
				$parts[] = 'more="' . esc_attr( $more_text ) . '"';
		}
	}
	if ( $show_author ) {
			$parts[] = 'show_author="true"';
	}
	if ( $show_date_created ) {
			$parts[] = 'show_date_created="true"';
	}
	if ( $show_date_modified ) {
			$parts[] = 'show_date_modified="true"';
	}

	// Link
	if ( $link_url !== '' ) {
		$parts[] = 'link="' . esc_attr( $link_url ) . '"';
	}
	if ( $link_target !== '' ) {
		$parts[] = 'link_target="' . esc_attr( $link_target ) . '"';
	}

	// Sorting & layout
	$parts[] = 'list="' . esc_attr( $list ) . '"';
	$parts[] = 'orderby="' . esc_attr( $orderby ) . '"';
	$parts[] = 'order="' . esc_attr( $order ) . '"';
	if ( $cols > 0 ) {
		$parts[] = 'cols="' . esc_attr( $cols ) . '"';
	}
	if ( $thumbs !== '' ) {
		$parts[] = 'thumbs="' . esc_attr( $thumbs ) . '"';

		if ( $link_thumbs ) {
			$parts[] = 'link_thumbs="true"';
		}
	}
	if ( $use_cc_skin ) {
		if ( $skin !== '' ) {
			$parts[] = 'skin="' . esc_attr( $skin ) . '"';
		}
	} elseif ( $item_class !== '' ) {
			$parts[] = 'class="' . esc_attr( $item_class ) . '"';
	}
	if ( $depth > 0 ) {
		$parts[] = 'depth="' . esc_attr( $depth ) . '"';

		if ( $list !== 'true' && $subpage_title !== '' ) {
			$parts[] = 'subpage_title="' . esc_attr( $subpage_title ) . '"';
		}
	}
	if ( $offset > 0 ) {
		$parts[] = 'offset="' . esc_attr( $offset ) . '"';
	}

	if ( $post_status !== '' ) {
		$parts[] = 'post_status ="' . esc_attr( $post_status ) . '"';
	}

	// Custom-field overrides → only pass when enabled AND key present
	if ( $use_custom_excerpt && $custom_excerpt_key !== '' ) {
		$parts[] = 'use_custom_excerpt="' . esc_attr( $custom_excerpt_key ) . '"';
	}
	if ( $use_custom_title && $custom_title_key !== '' ) {
		$parts[] = 'use_custom_title="' . esc_attr( $custom_title_key ) . '"';
	}
	if ( $use_custom_more && $custom_more_key !== '' ) {
		$parts[] = 'use_custom_more="' . esc_attr( $custom_more_key ) . '"';
	}
	if ( $use_custom_link && $custom_link_key !== '' ) {
		$parts[] = 'use_custom_link="' . esc_attr( $custom_link_key ) . '"';
	}
	if ( $use_custom_target && $custom_target_key !== '' ) {
		$parts[] = 'use_custom_link_target="' . esc_attr( $custom_target_key ) . '"';
	}
	if ( $use_custom_thumbs && $custom_thumbs_key !== '' ) {
		$parts[] = 'use_custom_thumbs="' . esc_attr( $custom_thumbs_key ) . '"';
	}

	if ( $use_legacy_css ) {
		$parts[] = 'use_legacy_css="true"';
	}

	$parts[]      = ']';
	$cc_shortcode = implode( ' ', $parts );

	echo do_shortcode( $cc_shortcode );
	?>
</div>
