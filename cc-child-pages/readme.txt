=== CC Child Pages ===
Contributors: caterhamcomputing
Donate link: https://ccplugins.co.uk/donate/
Tags: child pages, subpages, page navigation, page list, elementor
Requires at least: 6.7
Tested up to: 6.9
Stable tag: 2.1.0
Requires PHP: 7.4
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display WordPress child pages in a responsive grid or list using a shortcode, Gutenberg block or Elementor widget.

== Description ==

CC Child Pages displays WordPress child pages of any parent page in a responsive grid or list layout, making it easy to display hierarchical page structures, subpages and navigation.

You can use it in page content, widget areas, or templates using:

* the `[child_pages]` shortcode,
* the **CC Child Pages** Gutenberg block, or
* the **CC Child Pages** Elementor widget (when Elementor is active).

All output methods use a modern CSS system based on CSS variables and flexible grid layouts for improved theme compatibility. For older or heavily customised themes, a Legacy CSS mode is available — either by checking the **Use Legacy CSS** option in the block or Elementor sidebar, or by setting `use_legacy_css="true"` in the shortcode.

This makes it ideal for building sub-navigation sections, page directories, or visual site maps.

Full documentation is available at [docs.ccplugins.co.uk](https://docs.ccplugins.co.uk/plugins/cc-child-pages/)

= Features =

* Display WordPress child pages in grid or list layout
* Supports shortcode, Gutenberg block and Elementor widget
* Flexible column layouts (1–6 columns)
* Modern CSS system based on CSS variables
* Optional Legacy CSS mode for older themes
* Pagination and sorting controls
* Custom field overrides
* Lazy loading support
* Fully translatable

A Pro add-on is also available at [ccplugins.co.uk](https://ccplugins.co.uk) which adds additional display skins, advanced layout controls, colour customisation options and Elementor enhancements.


== What’s New in 2.x ==

* Gutenberg block with live preview
* Elementor widget integration
* Modern CSS skins using CSS variables
* Legacy CSS compatibility toggle
* Reorganised shortcode structure
* Continued support for the classic widget (legacy)

== How It Works ==

= Gutenberg Block =

Insert the **CC Child Pages** block in the editor. Configure:

* Parent page
* Columns
* Thumbnails
* Excerpt display
* Sorting
* Legacy CSS toggle

The block provides a live preview while editing.

= Elementor Widget =

If **Elementor** is installed and active, a **CC Child Pages** widget becomes available.

The widget is located inside the **CC Plugins** category within the Elementor panel.

You can:

* Select the parent page
* Choose columns (1–6)
* Enable thumbnails and lazy loading
* Control excerpt display
* Configure sorting
* Enable Legacy CSS if required

All shortcode options are available via intuitive controls in the Elementor sidebar.

The widget renders the same output structure as the shortcode and block, ensuring consistent styling across editors.

= Shortcode =

Insert:

`[child_pages]`

By default, this displays the child pages of the current page.

To enable legacy styling:

`[child_pages use_legacy_css="true"]`

The shortcode supports detailed layout and query control, documented below.

== Complete Shortcode Guide ==

=== 1. Basic Usage ===

Display children of current page:

`[child_pages]`

Display children of a specific page:

`[child_pages id="42"]`

Display specific pages only:

`[child_pages page_ids="3,7,10"]`

Exclude specific pages:

`[child_pages exclude="5,9,12"]`

=== 2. Grid Layout Options ===

Choose number of columns:

`[child_pages cols="1"]`
`[child_pages cols="2"]`
`[child_pages cols="3"]`
`[child_pages cols="4"]`
`[child_pages cols="5"]`
`[child_pages cols="6"]`

Choose skin:

`[child_pages skin="simple"]`
`[child_pages skin="red"]`
`[child_pages skin="green"]`
`[child_pages skin="blue"]`
`[child_pages skin="sleek"]`
`[child_pages skin="bold"]`

Add custom wrapper class:

`[child_pages class="my-custom-grid"]`

=== 3. List Mode (Instead of Grid) ===

Display as unordered list:

`[child_pages list="true"]`

Control hierarchy depth:

`[child_pages list="true" depth="0"]`

Depth values:

* `0` – unlimited depth (nested list)
* `-1` – flat list
* `1` – top level only
* `2, 3, 4` – specific depth

In list mode, only these attributes apply:

`id`, `exclude`, `orderby`, `order`, `cols`, `class`, `depth`

=== 4. Thumbnails ===

Show featured images:

`[child_pages thumbs="true"]`

Specify size:

`[child_pages thumbs="large"]`
`[child_pages thumbs="full"]`
`[child_pages thumbs="my-custom-size"]`

Make thumbnails clickable:

`[child_pages thumbs="medium" link_thumbs="true"]`

Enable lazy loading:

`[child_pages thumbs="medium" lazy_load="true"]`
`[child_pages thumbs="medium" link_thumbs="true" lazy_load="true"]`

=== 5. Titles & Links ===

Make titles clickable:

`[child_pages link_titles="true"]`

Hide titles:

`[child_pages hide_title="true"]`

Hide “Read more” link:

`[child_pages hide_more="true"]`

Change “Read more” text:

`[child_pages more="View Details"]`

Open links in new tab:

`[child_pages link_target="_blank"]`

=== 6. Excerpt Control ===

Limit word count:

`[child_pages words="20"]`

Disable excerpt truncation:

`[child_pages truncate_excerpt="false"]`

Hide excerpt completely:

`[child_pages hide_excerpt="true"]`

Hide WordPress “Continue reading” text:

`[child_pages hide_wp_more="true"]`

Show full page content:

`[child_pages show_page_content="true"]`

=== 7. Sorting & Query Control ===

Order results:

`[child_pages orderby="title" order="ASC"]`

Supported `orderby` values:

* `menu_order` (default)
* `id`
* `title`
* `slug`
* `author`
* `date`
* `modified`
* `rand`

Limit number displayed:

`[child_pages limit="5"]`

Offset results:

`[child_pages offset="2"]`

Display sibling pages:

`[child_pages siblings="true"]`

Include current page with siblings:

`[child_pages siblings="true" show_current_page="true"]`

=== 8. Pagination ===

Enable pagination:

`[child_pages posts_per_page="6"]`

Force a specific page:

`[child_pages posts_per_page="6" page="2"]`

Pagination does not apply in list mode.

=== 9. Post Status Control ===

By default:

* Published pages are shown
* Private pages are shown to authorised users

Specify manually:

`[child_pages post_status="publish"]`
`[child_pages post_status="publish,private"]`

=== 10. Display Meta Information ===

Show author:

`[child_pages show_author="true"]`

Show creation date:

`[child_pages show_date_created="true"]`

Show modified date:

`[child_pages show_date_modified="true"]`

=== 11. Custom Fields (Advanced) ===

Override default values using meta fields.

`[child_pages use_custom_excerpt="custom_excerpt"]`
`[child_pages use_custom_title="custom_title"]`
`[child_pages use_custom_thumbs="custom_thumb"]`
`[child_pages use_custom_link="custom_link"]`
`[child_pages use_custom_link_target="custom_target"]`

Custom field overrides are ignored in list mode.

=== 12. Sticky Posts ===

Sticky posts are ignored by default.

To include them:

`[child_pages ignore_sticky_posts="false"]`

== Screenshots ==

1. CC Child Pages block in the Gutenberg editor.
2. Example grid layout using the "Sleek" skin.
3. List view.
4. Classic widget (legacy mode for existing installs).

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install via the WordPress Plugins screen.
2. Activate the plugin.
3. Insert the block, Elementor widget or add `[child_pages]` to your content.
4. Adjust settings as required.

== Frequently Asked Questions ==

= How do I display WordPress child pages? =

Insert the `[child_pages]` shortcode, use the Gutenberg block or add the Elementor widget.

= How do I list subpages in WordPress? =

Use the shortcode, block or Elementor widget to display child pages of any parent page in a grid or list.

= Can I show child pages in Elementor? =

Yes. When Elementor is active, a CC Child Pages widget appears in the CC Plugins category.

= Is Elementor required? =

No. Elementor integration is optional. The shortcode and block work independently.

= Will the classic widget be removed? =

No. The classic widget remains available for compatibility.

= Does it work with Full Site Editing themes? =

Yes. Modern CSS inherits typography and colours from the active theme.

= What if my layout looks incorrect? =

Enable Legacy CSS:

Block: Enable “Use Legacy CSS” in the sidebar.
Shortcode: `[child_pages use_legacy_css="true"]`
Elementor: Enable Legacy CSS in the widget settings.

= Is there a Pro version? =

Yes. A Pro version is available which adds additional skins, enhanced design controls and advanced layout options.

More information is available at [https://ccplugins.co.uk/plugins/cc-child-pages-pro/](https://ccplugins.co.uk/plugins/cc-child-pages-pro/)

= Does it support multilingual sites? =

Yes. All text strings are translatable via standard `.po` and `.mo` files.

== Upgrade Notice ==

= 2.1.0 =

Introduces Elementor widget integration and improved documentation.

= 2.0.0 =

Major update introducing Gutenberg block and modern CSS system.

== Changelog ==

= 2.1.0 =
* Added Elementor widget integration
* Added CC Plugins Elementor category
* Documentation updates

= 2.0.2 =
* Removed CSS intended for future optional title truncation accidentally included in modern CSS

= 2.0.1 =
* Security update

= 2.0.0 =
* Added Gutenberg block
* Introduced modern CSS system
* Added Legacy CSS compatibility mode
* Reorganised shortcode structure

== License ==

This plugin is free software; you may redistribute it and/or modify it under the terms of the GNU General Public License v2 or later.

See https://www.gnu.org/licenses/gpl-2.0.html
