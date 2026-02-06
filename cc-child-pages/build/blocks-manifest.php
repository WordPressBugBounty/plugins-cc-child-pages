<?php
// This file is generated. Do not modify it manually.
return array(
	'cc-child-pages' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'caterhamcomputing/cc-child-pages',
		'version' => '0.1.0',
		'title' => 'CC Child Pages',
		'category' => 'ccplugins',
		'icon' => 'admin-page',
		'description' => 'Display a list of child pages with flexible layouts, skins, and query options.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'padding' => true
			),
			'color' => array(
				'text' => true,
				'background' => true,
				'gradients' => true
			)
		),
		'attributes' => array(
			'id' => array(
				'type' => 'string',
				'default' => ''
			),
			'exclude' => array(
				'type' => 'string',
				'default' => ''
			),
			'pageIDs' => array(
				'type' => 'string',
				'default' => ''
			),
			'orderby' => array(
				'type' => 'string',
				'default' => 'menu_order'
			),
			'order' => array(
				'type' => 'string',
				'default' => 'ASC'
			),
			'siblings' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showCurrentPage' => array(
				'type' => 'boolean',
				'default' => false
			),
			'list' => array(
				'type' => 'string',
				'default' => 'false'
			),
			'selectSpecificPages' => array(
				'type' => 'boolean',
				'default' => false
			),
			'usePagination' => array(
				'type' => 'boolean',
				'default' => false
			),
			'postsPerPage' => array(
				'type' => 'integer',
				'default' => 3
			),
			'useLimit' => array(
				'type' => 'boolean',
				'default' => false
			),
			'ignoreStickyPosts' => array(
				'type' => 'boolean',
				'default' => true
			),
			'page' => array(
				'type' => 'integer'
			),
			'words' => array(
				'type' => 'integer',
				'default' => 55
			),
			'limit' => array(
				'type' => 'string',
				'default' => '3'
			),
			'cols' => array(
				'type' => 'string',
				'default' => '3'
			),
			'useCCSkin' => array(
				'type' => 'boolean',
				'default' => true
			),
			'skin' => array(
				'type' => 'string',
				'default' => 'simple'
			),
			'itemClass' => array(
				'type' => 'string',
				'default' => ''
			),
			'thumbs' => array(
				'type' => 'string',
				'default' => 'false'
			),
			'linkThumbs' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showPageContent' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showTitle' => array(
				'type' => 'boolean',
				'default' => true
			),
			'linkTitles' => array(
				'type' => 'boolean',
				'default' => false
			),
			'useCustomTitleClass' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customTitleClass' => array(
				'type' => 'string',
				'default' => ''
			),
			'showMore' => array(
				'type' => 'boolean',
				'default' => true
			),
			'more' => array(
				'type' => 'string',
				'default' => ''
			),
			'showWPMore' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showExcerpt' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAuthor' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showDateCreated' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showDateModified' => array(
				'type' => 'boolean',
				'default' => false
			),
			'truncateExcerpt' => array(
				'type' => 'boolean',
				'default' => false
			),
			'depth' => array(
				'type' => 'string',
				'default' => '1'
			),
			'subpageTitle' => array(
				'type' => 'string',
				'default' => ''
			),
			'offset' => array(
				'type' => 'integer',
				'default' => 0
			),
			'link' => array(
				'type' => 'string',
				'default' => ''
			),
			'linkTarget' => array(
				'type' => 'string',
				'default' => ''
			),
			'postStatus' => array(
				'type' => 'string',
				'default' => ''
			),
			'useCustomExcerpt' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customExcerptField' => array(
				'type' => 'string',
				'default' => ''
			),
			'useCustomTitle' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customTitleField' => array(
				'type' => 'string',
				'default' => ''
			),
			'useCustomMore' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customMoreField' => array(
				'type' => 'string',
				'default' => ''
			),
			'useCustomLink' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customLinkField' => array(
				'type' => 'string',
				'default' => 'cc_child_pages_link'
			),
			'useCustomLinkTarget' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customLinkTargetField' => array(
				'type' => 'string',
				'default' => 'cc_child_pages_link_target'
			),
			'useCustomThumbs' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customThumbsField' => array(
				'type' => 'string',
				'default' => ''
			),
			'useLegacyCSS' => array(
				'type' => 'boolean',
				'default' => false
			),
			'uiTab' => array(
				'type' => 'string',
				'default' => 'design'
			)
		),
		'textdomain' => 'cc-child-pages',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js'
	)
);
