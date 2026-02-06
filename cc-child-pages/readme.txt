=== CC Child Pages ===
Contributors: caterhamcomputing
Donate link: https://ccplugins.co.uk/support/
Tags: child pages, subpages, shortcode, block, gutenberg
Requires at least: 6.3
Tested up to: 6.9
Stable tag: 2.0.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://ccplugins.co.uk/plugins/cc-child-pages/
Author: Caterham Computing
Author URI: https://caterhamcomputing.co.uk

Display child pages in a responsive grid or list via a shortcode or Gutenberg block. Includes modern CSS skins with optional legacy mode.

== Description ==

**CC Child Pages** displays child pages of any parent page in a responsive grid or list layout. You can use it in page content, widget areas, or templates using either:

* the `[child_pages]` **shortcode**, or
* the **CC Child Pages Gutenberg block**.

Both use a **modern CSS system** based on CSS variables and flexible grid layouts for improved theme compatibility. For older or heavily customised themes, a **Legacy CSS** mode is available - either by checking the **Use Legacy CSS** option in the block sidebar, or by setting `use_legacy_css="true"` in the shortcode.

This makes it ideal for building sub-navigation sections, page directories, or visual site maps.

A **Pro add-on** is available at [ccplugins.co.uk](https://ccplugins.co.uk) adding advanced skins, hover effects, taxonomy filters, and custom field support - while helping fund ongoing development of the free version.

### What's New in 2.0

* Added **Gutenberg block** with live preview and sidebar options.
* Reorganised shortcode attributes for clarity and ease of use.
* Introduced **modern CSS skins** using CSS variables.
* Added **Legacy CSS** toggle for backward compatibility.
* Retained the **legacy widget** for existing sites (see below).
* General code and security improvements.

### Legacy Widget Notice

The classic **CC Child Pages Widget** remains available to ensure existing sites continue to function.
However, it is now considered *legacy*. For new builds, please use either the **shortcode** or the **Gutenberg block** inside widget areas via the *Shortcode* or *Block* widget.

-

== How It Works ==

### 1. Gutenberg Block

Add a **CC Child Pages** block directly in the editor to display child pages of the current or chosen parent. Configure columns, thumbnails, excerpts, and "Read More" links visually from the sidebar.
The block uses **modern CSS-based skins**, designed for full theme compatibility. If your theme uses older styles or layout methods, you can toggle **Use Legacy CSS** in the sidebar to switch to the classic stylesheet, which resolves most compatibility issues automatically.

### 2. Shortcode

Insert `[child_pages]` into your content. The shortcode supports a comprehensive set of attributes for layout, display, and query control.
Like the block, it uses **modern CSS** by default and can be switched into legacy mode using `use_legacy_css="true"` if required.

-

== Shortcode Overview ==

**Basic usage:**
`[child_pages]` - Lists child pages of the current page.

**Display children of another page:**
`[child_pages id="42"]`

**Display in a grid:**
`[child_pages cols="3" skin="cards" thumbs="true"]`

### Full Attribute Reference (with defaults)

**Parent and hierarchy**

* `id` - Parent page ID (default: current page via `get_the_ID()`).
* `page_ids` - Comma-separated list of specific page IDs to include (default: empty).
* `depth` - Levels of hierarchy to include (default: `1`).
* `siblings` - Show sibling pages instead of children (default: `false`).
* `show_current_page` - Include the current page in results (default: `false`).

**Sorting and limits / pagination**

* `orderby` - Sort field (`menu_order`, `title`, `date`; default: `menu_order`).
* `order` - Sort direction (`ASC` or `DESC`; default: `ASC`).
* `offset` - Number of results to skip (default: `0`).
* `limit` - Limit total number of pages displayed (default: `-1` = no limit).
* `posts_per_page` - Items per page when paginating (default: `-1`).
* `page` - Force a specific page number for pagination (default: `-1` = auto).
* `ignore_sticky_posts` - Ignore sticky posts (default: `true`).
* `post_status` - Filter by status (default: empty = standard published pages).

**Layout and style**

* `cols` - Number of columns (default: empty; theme or skin decides).
* `skin` - Visual style template (default: `simple`).
* `list` - Force a list layout instead of a grid (default: `false`).
* `class` - Additional CSS class on the wrapper (default: empty).
* `use_legacy_css` - Use the classic stylesheet for compatibility (default: `false`).

**Content display**

* `hide_title` - Hide page title (default: `false`).
* `hide_excerpt` - Hide excerpt (default: `false`).
* `hide_more` - Hide "Read more ..." link (default: `false`).
* `hide_wp_more` - Ignore WP "more" tag (default: `false`).
* `show_page_content` - Show full page content (default: `false`).
* `truncate_excerpt` - Truncate long excerpts (default: `true`).
* `words` - Words to include in truncated excerpt (default: `55`).
* `thumbs` - Show featured image (default: `false`).
* `link_thumbs` - Make thumbnails clickable (default: `false`).
* `link_titles` - Make titles clickable (default: `false`).
* `title_link_class` - Class for linked titles (default: `ccpage_title_link`).
* `more` - Text for "Read more" link (default: `Read more ... `).
* `link_target` - Target for links (e.g. `_blank`; default: empty).
* `link` - Override the link URL (default: empty).
* `use_custom_excerpt` - Use custom excerpt field if present (default: empty).
* `use_custom_title` - Use custom title field if present (default: empty).
* `use_custom_more` - Use custom "more" text field if present (default: empty).
* `use_custom_thumbs` - Use custom thumbnail field if present (default: empty).
* `use_custom_link` - Meta key for custom link (default: `cc_child_pages_link`).
* `use_custom_link_target` - Meta key for custom target (default: `cc_child_pages_link_target`).
* `show_author` - Show page author (default: `false`).
* `show_date_created` - Show created date (default: `false`).
* `show_date_modified` - Show modified date (default: `false`).
* `subpage_title` - Custom heading above the list/grid (default: empty).

*Tip:* Most users get great results by combining `cols`, `skin`, and `thumbs`. Switch on **Legacy CSS** only if your theme needs it.

-

== Block Overview ==

The **CC Child Pages block** provides a simple, visual interface for configuring these same options, complete with a live preview.
It uses **modern CSS** for all skins and layouts. If your theme uses older styles or layout rules, enabling the **Use Legacy CSS** checkbox restores compatibility with the classic grid and list templates.

-

== Screenshots ==

1. CC Child Pages block in the Gutenberg editor.
2. Example grid layout using the "Sleek" skin.
3. List view.
4. Classic widget (legacy mode for existing installs).

-

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install via the WordPress Plugins screen.
2. Activate through the *Plugins* menu.
3. Add the shortcode `[child_pages]` to a page, or insert the "CC Child Pages" block in the editor.
4. Adjust display settings in the block sidebar or shortcode attributes as needed.
5. If your theme requires it, enable **Legacy CSS** (block setting or `use_legacy_css="true"`).

-

== Frequently Asked Questions ==

**Will the classic widget be removed?**
Not at this time. It remains available for compatibility, but future development will focus on the shortcode and block.

**Can I use it in widget areas?**
Yes. Either use the Shortcode widget and paste `[child_pages]`, or insert the block via the Block widget.

**Does it work with Full Site Editing themes?**
Yes. The block and shortcode use modern CSS and inherit typography and colours automatically.

**What if my theme's layout looks incorrect?**
Enable **Legacy CSS** from the block sidebar or set `use_legacy_css="true"` in the shortcode to restore the original styles. This resolves most compatibility issues.

**Is there a Pro version?**
Yes. [CC Child Pages Pro](https://ccplugins.co.uk/plugins/cc-child-pages-pro/) adds extra display skins, hover effects, taxonomy filters, and dynamic queries.

**Does it support multilingual sites?**
Yes. All text strings are translatable via standard `.po` and `.mo` files.

-

== Changelog ==

= 2.0.2 =
* Removed CSS intended for future optional title truncation which was accidentally included in modern CSS

= 2.0.1 =
* Security update.

= 2.0.0 =
* Added Gutenberg block with live preview.
* Updated shortcode attribute structure for clarity.
* Introduced modern CSS skins using CSS variables.
* Added Legacy CSS option for block and shortcode.
* Retained legacy widget for existing sites.
* General optimisations and translation updates.

= 1.45 =
* Maintenance release- minor bug fix to settings page.

= 1.44 =
* Maintenance release with security improvements to legacy widget.

= 1.43 =
* Maintenance release with security improvements.

= 1.42 =
* Bug fix for `hide_titles` function to stop it breaking custom templates.

= 1.41 =
* Added `hide_titles` option. Small bug fix for widget.

= 1.40 =
* Maintenance release. Added Exclude Tree option to widget

= 1.39 =
* Small fix to CC Child Pages Widget

= 1.38 =
* Small fix to CSS for page navigation links

= 1.37 =
* Small changes to try to prevent problems interacting with other plugins that may not return correct values in their filters

= 1.36 =
* Bug fix - by default private pages will be visible or hidden depending on whether the user is logged in
* Added `post_status` parameter to show child pages with specific statuses
* Added button to the settings page to disable loading of Skins CSS file if not being used (for performance)
* Added additional classes to child page elements to allow styling of elements with parent pages (and even specific parent pages)
* Added `use_custom_thumbs` parameter - specify the name of a custom field that will specify the ID or URL of an image to use as a thumbnail

= 1.35 =
* Bug fix - implemented code to remove `[child_pages]` shortcode from pages before generating excerpts to avoid getting stuck in an infinite loop under some circumstances
* Added `show_page_content` parameter to show complete page content
* Added `link_target` and `use_custom_link_target` parameters to allow control of how links open
* `depth` now works with the shortcode when not using `list` mode. Added `subpage_title` parameter to display a title for sub-pages when `depth` is greater than 1 and `list="true"` is NOT specified
* Added `show_author`, `show_date_created` and `show_date_modified` parameters to allow display of post information
* `order` can now be set to `rand` to show items in a random order

= 1.34 =
* Added `ignore_sticky_posts` parameter
* Added `limit` parameter to limit the number of pages displayed
* Added `offset` parameter to allow skipping a number of pages

= 1.33 =
* Added `posts_per_page` and `page` parameters for basic pagination
* Added `page_ids` parameter to allow display of specific pages
* Added `use_custom_link` parameter to allow the over-riding of the link target on a per page basis
* Added new CSS IDs to help make styling more flexible

= 1.32 =
* Bug fix - widget was displaying sibling pages instead of child pages under certain circumstances

= 1.31 =
* Added `siblings` option to the widget
* Added `show_current_page` option for use with the shortcode when `siblings` is set to `true`
* Added `hide_wp_more` to remove the standard "Continue reading... " message from excerpts
* Added `use_custom_excerpt`, `use_custom_title` and `use_custom_more` to the shortcode
* Added more filters and actions to widget and shortcode to allow extension of plugin

= 1.30 =
* Bug fix - internationalization now works correctly (translations need work though - currently only French, which is known to be poor)
* Added more filters to widget, list and shortcode to allow extension of plugin

= 1.29 =
* Bug fix - widget will now show on all pages/posts if "All Pages" or a specific parent page is selected
* Bug fix - shortcode now closes query correctly (was causing issues with some themes)
* The shortcode will now work with custom post types
* You can now specify multiple parent page IDs (when using `list="true"`, only a single parent ID can be specified)

= 1.28 =
* Further improvements to integration when used with Video Thumbnails plugin

= 1.27 =
* Added the `siblings` parameter to show siblings of current page
* Improved integration when used with Video Thumbnails plugin
* Minor bug fixes for CC Child Pages Widget

= 1.26 =
* The CSS for displaying child pages has been rewritten to allow for custom CSS to be more easily written - for example, specifying a border should no longer cause problems in the responsive layout. Fallbacks have been put in place for older versions of Internet Explorer.
* The handling of Custom CSS from the settings page has been improved.
* The loading of the plugin CSS has been returned to the default manner. While this means that CSS is loaded on pages where the shortcode is not used, it means that the CSS can be correctly minified by other plugins and ensures that valid HTML is generated.

= 1.25 =
* New option added to widget to show all top-level pages and their children. This can now be used as a complete replacement for the standard Pages widget
* New option added to the plugin's settings page allowing custom CSS code to be specified from within the plugin. This feature has been requested several times. This functionality will be expanded on in the future.

= 1.24 =
* Further enhancements to CSS when using both the `list` and `cols` parameters

= 1.23 =
* Minor fix for CSS when using both the `list` and `cols` parameters

= 1.22 =
* Changes to how excerpts are generated from content when no custom excerpt is specified.
* Changed how CSS is queued - the CSS file will now only be included in the page if the shortcode is specified, helping to keep page sizes to a minimum.

= 1.21 =
* Change to allow `cols` parameter to be used when `list` parameter is set to `true`.
* Changed `.ccpages_excerpt` container to `<div>` (was `<p>`) to avoid potentially invalid HTML when HTML excerpts are used.

= 1.20 =
* Change to improve efficiency when the plugin attempts to force thumbnail creation via Video Thumbnails plugin
* Minor change to avoid output of empty links when applying links to thumbnails and no thumbnail is present
* Minor change to escaping special characters in `more` parameter

= 1.19 =
* Small change to how the plugin works with thumbnails. It will now use thumbnails generated by the Video Thumbnails plugin if it is installed.
* Added `link_thumbs` parameter. If set to `"true"`, thumbnails will link to the child page.
* CSS is no longer minified, in order to make it easier to view existing CSS when defining your own custom styles. The CSS can be minified by other plugins if required.

= 1.18 =
* Added settings page to allow disabling of button in Visual Editor (TinyMCE)
* Added the `truncate_excerpt` parameter to the shortcode, defaults to `true` but setting to `false` stops custom excerpts from being shortened (where no custom excerpt exists, page content will still be truncated)

= 1.17 =
* Small change to how custom excerpts are handled for interoperability with Rich Text Excerpts plugin.

= 1.16 =
* Added the `hide_excerpt` parameter

= 1.15 =
* Added `hide_more` parameter to hide "Read more ..." links.
* Added `link_titles` parameter to make titles link to pages.
* Added `title_link_class` parameter for styling links in page titles.

= 1.14 =
* Bug fix: Corrected missing `<ul>` tags in widget
* Minor CSS changes to improve compatibility with certain themes

= 1.13 =
* Bug fix: Corrected problem with titles including special characters
* Added orderby and order parameters to control the display order of child pages

= 1.12 =
* Bug fix: Corrected problem when automatic excerpt returns value including a shortcode

= 1.11 =
* Bug fix: Corrected small bug introduced in version 1.10 when using `list="true"`

= 1.10 =
* Added `exclude` parameter
* Added `depth` parameter (only used if `list` is set to `"true"`)

= 1.9 =
* Added editor button
* Added custom excerpt capability to pages by default
* Refined generation of page excerpt where no custom excerpt exists
* Enhanced functionality of the `thumbs` option - you can now set this to the desired thumbnail size e.g. `thumbs="large"`, `thumbs="full"`, `thumbs="my-custom-size"`, etc.

= 1.8 =
* CC Child Pages widget enhanced to allow display of children of current page or a specific page
* CC Child Pages widget enhanced to allow depth to be specified

= 1.7 =
* Changed plugin author to show business name (Caterham Computing)
* Added CC Child Pages widget
* Added various new classes to help with custom CSS styling

= 1.6 =
* Added the `words` parameter. When set to a value greater than 0, the number of words in the excerpt will be trimmed if greater than the specified value.

= 1.5 =
* Added the `thumbs` parameter. If set to `"true"`, the featured image (if set) of a page will be shown.

= 1.4 =
* Added `more` parameter to override standard "Read more ..." text
* Internationalisation ...

= 1.3 =
* Corrected small error when using `list` parameter

= 1.2 =
* Added the `list` parameter

= 1.1 =
* Added the `skin` parameter
* Added the `class` parameter

= 1.0 =
* Initial Release
-

== Upgrade Notice ==

= 2.0.2 =
* Removed CSS intended for future optional title truncation which was accidentally included in modern CSS

= 2.0.1 =
* Security update.

= 2.0.0 =
* Added Gutenberg block with live preview.
* Updated shortcode attribute structure for clarity.
* Introduced modern CSS skins using CSS variables.
* Added Legacy CSS option for block and shortcode.
* Retained legacy widget for existing sites.
* General optimisations and translation updates.

= 1.45 =
* Maintenance release- minor bug fix to settings page.

= 1.44 =
* Maintenance release with security improvements to legacy widget.

= 1.43 =
* Maintenance release with security improvements.

= 1.42 =
* Bug fix for `hide_titles` function to stop it breaking custom templates.

= 1.41 =
* Added `hide_titles` option. Small bug fix for widget.

= 1.40 =
* Maintenance release. Added Exclude Tree option to widget

= 1.39 =
* Small fix to CC Child Pages Widget

= 1.38 =
* Small fix to CSS for page navigation links

= 1.37 =
* Small changes to try to prevent problems interacting with other plugins that may not return correct values in their filters

= 1.36 =
* Bug fix - by default private pages will be visible or hidden depending on whether the user is logged in
* Added `post_status` parameter to show child pages with specific statuses
* Added button to the settings page to disable loading of Skins CSS file if not being used (for performance)
* Added additional classes to child page elements to allow styling of elements with parent pages (and even specific parent pages)
* Added `use_custom_thumbs` parameter - specify the name of a custom field that will specify the ID or URL of an image to use as a thumbnail

= 1.35 =
* Bug fix - implemented code to remove `[child_pages]` shortcode from pages before generating excerpts to avoid getting stuck in an infinite loop under some circumstances
* Added `show_page_content` parameter to show complete page content
* Added `link_target` and `use_custom_link_target` parameters to allow control of how links open
* `depth` now works with the shortcode when not using `list` mode. Added `subpage_title` parameter to display a title for sub-pages when `depth` is greater than 1 and `list="true"` is NOT specified
* Added `show_author`, `show_date_created` and `show_date_modified` parameters to allow display of post information
* `order` can now be set to `rand` to show items in a random order

= 1.34 =
* Added `ignore_sticky_posts` parameter
* Added `limit` parameter to limit the number of pages displayed
* Added `offset` parameter to allow skipping a number of pages

= 1.33 =
* Added `posts_per_page` and `page` parameters for basic pagination
* Added `page_ids` parameter to allow display of specific pages
* Added `use_custom_link` parameter to allow the over-riding of the link target on a per page basis
* Added new CSS IDs to help make styling more flexible

= 1.32 =
* Bug fix - widget was displaying sibling pages instead of child pages under certain circumstances

= 1.31 =
* Added `siblings` option to the widget
* Added `show_current_page` option for use with the shortcode when `siblings` is set to `true`
* Added `hide_wp_more` to remove the standard "Continue reading... " message from excerpts
* Added `use_custom_excerpt`, `use_custom_title` and `use_custom_more` to the shortcode
* Added more filters and actions to widget and shortcode to allow extension of plugin

= 1.30 =
* Bug fix - internationalisation now works correctly (translations need work though - currently only French, which is known to be poor)
* Added more filters to widget, list and shortcode to allow extension of plugin

= 1.29 =
* Bug fix - widget will now show on all pages/posts if "All Pages" or a specific parent page is selected
* Bug fix - shortcode now closes query correctly (was causing issues with some themes)
* The shortcode will now work with custom post types
* You can now specify multiple parent page IDs (when using `list="true"`, only a single parent ID can be specified)

= 1.28 =
* Further improvements to integration when used with Video Thumbnails plugin

= 1.27 =
* Added the `siblings` parameter to show siblings of current page
* Improved integration when used with Video Thumbnails plugin
* Minor bug fixes for CC Child Pages Widget

= 1.26 =
* The CSS for displaying child pages has been rewritten to allow for custom CSS to be more easily written - for example, specifying a border should no longer cause problems in the responsive layout. Fallbacks have been put in place for older versions of Internet Explorer.
* The handling of Custom CSS from the settings page has been improved.
* The loading of the plugin CSS has been returned to the default manner. While this means that CSS is loaded on pages where the shortcode is not used, it means that the CSS can be correctly minified by other plugins and ensures that valid HTML is generated.

= 1.25 =
* New option added to widget to show all top-level pages and their children. This can now be used as a complete replacement for the standard Pages widget
* New option added to the plugin's settings page allowing custom CSS code to be specified from within the plugin. This feature has been requested several times. This functionality will be expanded on in the future.

= 1.24 =
* Further enhancements to CSS when using both the `list` and `cols` parameters

= 1.23 =
* Minor fix for CSS when using both the `list` and `cols` parameters

= 1.22 =
* Changes to how excerpts are generated from content when no custom excerpt is specified.
* Changed how CSS is queued - the CSS file will now only be included in the page if the shortcode is specified, helping to keep page sizes to a minimum.

= 1.21 =
* Change to allow `cols` parameter to be used when `list` parameter is set to `true`.
* Changed `.ccpages_excerpt` container to `<div>` (was `<p>`) to avoid potentially invalid HTML when HTML excerpts are used.

= 1.20 =
* Change to improve efficiency when the plugin attempts to force thumbnail creation via Video Thumbnails plugin
* Minor change to avoid output of empty links when applying links to thumbnails and no thumbnail is present
* Minor change to escaping special characters in `more` parameter

= 1.19 =
* Small change to how the plugin works with thumbnails. It will now use thumbnails generated by the Video Thumbnails plugin if it is installed.
* Added `link_thumbs` parameter. If set to `"true"`, thumbnails will link to the child page.
* CSS is no longer minified, in order to make it easier to view existing CSS when defining your own custom styles. The CSS can be minified by other plugins if required.

= 1.18 =
* Added settings page to allow disabling of button in Visual Editor (TinyMCE)
* Added the `truncate_excerpt` parameter to the shortcode, defaults to `true` but setting to `false` stops custom excerpts from being shortened (where no custom excerpt exists, page content will still be truncated)

= 1.17 =
* Small change to how custom excerpts are handled for interoperability with Rich Text Excerpts plugin.

= 1.16 =
* Added the `hide_excerpt` parameter

= 1.15 =
* Added `hide_more` parameter to hide "Read more ..." links.
* Added `link_titles` parameter to make titles link to pages.
* Added `title_link_class` parameter for styling links in page titles.

= 1.14 =
* Bug fix: Corrected missing `<ul>` tags in widget
* Minor CSS changes to improve compatibility with certain themes

= 1.13 =
* Bug fix: Corrected problem with titles including special characters
* Added orderby and order parameters to control the display order of child pages

= 1.12 =
* Bug fix: Corrected problem when automatic excerpt returns value including a shortcode

= 1.11 =
* Bug fix: Corrected small bug introduced in version 1.10 when using `list="true"`

= 1.10 =
* Added `exclude` parameter
* Added `depth` parameter (only used if `list` is set to `"true"`)

= 1.9 =
* Added editor button
* Added custom excerpt capability to pages by default
* Refined generation of page excerpt where no custom excerpt exists
* Enhanced functionality of the `thumbs` option - you can now set this to the desired thumbnail size e.g. `thumbs="large"`, `thumbs="full"`, `thumbs="my-custom-size"`, etc.

= 1.8 =
* CC Child Pages widget enhanced to allow display of children of current page or a specific page
* CC Child Pages widget enhanced to allow depth to be specified

= 1.7 =
* Changed plugin author to show business name (Caterham Computing)
* Added CC Child Pages widget
* Added various new classes to help with custom CSS styling

= 1.6 =
* Added the `words` parameter. When set to a value greater than 0, the number of words in the excerpt will be trimmed if greater than the specified value.

= 1.5 =
* Added the `thumbs` parameter. If set to `"true"`, the featured image (if set) of a page will be shown.

= 1.4 =
* Added `more` parameter to override standard "Read more ..." text
* Internationalisation ...

= 1.3 =
* Corrected small error when using `list` parameter

= 1.2 =
* Added the `list` parameter

= 1.1 =
* Added the `skin` parameter
* Added the `class` parameter

= 1.0 =
* Initial Release
-

== License ==

This plugin is free software; you can redistribute it and/or modify it under the terms of the **GNU General Public License v2 or later**.
See [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

© 2025 Caterham Computing.
