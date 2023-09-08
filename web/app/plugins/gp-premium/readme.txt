=== GP Premium ===
Contributors: edge22
Donate link: https://generatepress.com
Tags: generatepress
Requires at least: 5.2
Tested up to: 6.3
Requires PHP: 5.6
Stable tag: 2.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Take GeneratePress to the next level.

== Description ==

The entire collection of GeneratePress premium modules. Once activated, each module extends certain aspects of GeneratePress, giving you more options to build your website.

= Documentation =

Check out our [documentation](https://docs.generatepress.com) for more information on each module and how to use them.

== Installation ==

To learn how to install GP Premium, check out our documentation [here](https://docs.generatepress.com/article/installing-gp-premium/).

In most cases, #1 will work fine and is way easier.

== Changelog ==

= 2.3.2 =
* Tweak: Remove deprecated wp_get_loading_attr_default function

= 2.3.1 =
* Fix: SelectSearch component infinite loop
* Fix: Block widths inside Block Elements

= 2.3.0 =
* Feature: Add Search Modal Element type
* Fix: Inline post meta feature in GenerateBlocks 1.7
* Fix: Close "Choose Element Type" modal with ESC key
* Fix: Replace WooCommerce secondary image attachment size
* Fix: WP Filesystem error missing credentials
* Fix: Undefined array keys in dynamic Container URL
* Fix: Author avatar in Header/Block Element titles
* Fix: Infinite loop error when autosaving with dynamic content block
* Fix: Add aria-label to off-canvas panel button
* Fix: WooCommerce button dynamic typography
* Fix: Empty WooCommerce quantity fields
* Fix: PHP 8.1 notice using disable elements in Customizer
* Tweak: Improve license key area
* Tweak: Improve off-canvas transitions
* Tweak: Check for WooCommerce functions
* Tweak: Open off-canvas using space bar
* Tweak: Use image ID in mobile header/sticky nav logos
* Tweak: Improve Elements hook selection dropdown UI
* Tweak: Add site library check for min GenerateBlocks version
* Tweak: Add Loop Template to Custom Post Type dropdown filters

= 2.2.2 =
* Fix: Off Canvas anchor links not working

= 2.2.1 =
* Fix: Add value to off-canvas aria-hidden attribute
* Tweak: Remove/add aria-hidden to off-canvas on toggle

= 2.2.0 =
* Feature: Allow block element autosave
* Feature: Add revisions to block elements
* Feature: Add Loop Template block element
* Feature: Dont display the raw license key in the Dashboard
* Feature: Add "Paginated Results" to Element Display Rules
* Fix: Element post navigation template PHP warning when not using GB Pro
* Fix: Remove unnecessary zoom CSS from featured images
* Fix: Font icon CSS order
* Fix: Load more button showing in product tax archives
* Fix: Prevent tabbing in hidden off-canvas panel
* Fix: Hide hidden off-canvas panel from screen readers
* Fix: menu-toggle aria-controls when using off-canvas panel
* Fix: Focus first focusable element when opening off-canvas panel
* Fix: Focus slideout toggle when closing off-canvas panel
* Fix: Off-Canvas Panel sub-menu a11y
* Fix: Prevent secondary nav legacy typography CSS
* Fix: Fix dynamic term meta link
* Fix: Block margins in the block element editor
* Fix: Embeds in Block Elements
* Fix: Apply display rules to editor with no ID
* Fix: Navigation background image applying to secondary nav
* Fix: WooCommerce order received page float issue
* Fix: Block element editor error in GenerateBlocks 1.7
* Tweak: Remove jquery-migrate from sticky script
* Tweak: Add message in Blog section about Loop Template

= 2.1.2 =
* Elements: Fix custom field value in dynamic container links
* Elements: Fix block widths in the editor
* General: Fix double slashes in dashboard file request
* General: Fix missing Customizer translations

= 2.1.1 =
* Menu Plus: Fix mobile header sticky auto hide
* Site Library: Fix broken CSS variables on import

= 2.1.0 =
* Blog: Fix masonry JS error if no archive pagination exists
* Blog: Fix full width featured blog column
* Colors: Deprecate module if using GP 3.1.0
* Elements: Add custom class option to dynamic image block
* Elements: Add support for post_type array in display rules
* Elements: Fix container link option when targeting next/previous posts
* Menu Plus: Integrate off-canvas panel with new dynamic typography system
* Menu Plus: Integrate off-canvas panel with new color system
* Menu Plus: Integrate mobile header HTML attributes with new HTML attribute system
* Menu Plus: Integrate mobile header and off-canvas panel with new generate_has_active_menu filter
* Menu Plus: Fix broken inline CSS when using floated sticky navigation
* Menu Plus: Add logo dimensions to navigation logo
* Secondary Navigation: Integrate with new color system
* Secondary Navigation: Integrate with new generate_has_active_menu filter
* Secondary Navigation: Integrate with new dynamic typography system
* Secondary Navigation: Reduce box-shadow to match main navigation
* Secondary Navigation: Change direction of sub-menu box-shadow when opening left
* Secondary Navigation: Fix sub-menu overlap when using dropdown click/mobile
* Secondary Navigation: Replace box-shadow with border-bottom when sub-menu opens down
* Site Library: Add site author attribution
* Typography: Deprecate module if using dynamic typography in GP 3.1.0
* WooCommerce: Integrate with new dynamic typography system
* WooCommerce: Integrate with new colors system
* WooCommerce: Remove category title/description if using page hero with title disabled
* WooCommerce: Remove "speak" CSS properties
* WooCommerce: Fix empty continue shopping link on mobile
* WooCommerce: Fix persistent sticky add to cart panel
* General: Integrate with new GP 3.1.0 Dashboard
* General: Remove featured-image-active body class if featured image is disabled
* General: Change date format in exported JSON filename
* General: Fix PHP error when license key activation returns 403

= 2.0.3 =
* Elements: Use block_categories_all filter in WP 5.8
* Elements: Remove wp-editor dependency from new widget editor

= 2.0.2 =
* Elements: Use blog page title for dynamic title if set
* Spacing: Fix reset button bug in Customizer controls
* WooCommerce: Fix infinite scroll applying to product taxonomy pages

= 2.0.1 =
* Blog: Fix infinite scroll bug on product archives
* Elements: Fix error in WP 4.9.x versions
* Menu Plus: Prevent 0x0 logo dimension attributes if no dimensions exist
* Site Library: Add vertical scroll ability to the Site Library control area

= 2.0.0 =
* Blog: Rewrite infinite scroll using vanilla javascript
* Blog: Rewrite masonry using vanilla javascript
* Blog: Add separate infinite scroll path element to footer
* Blog: Fix missing single/page featured image options when archive image disabled
* Blog: Add aria-label instead of screen-reader-text to read more button
* Colors: Fix back to top Customizer color preview
* Elements: New Content Template Element
* Elements: New Post Meta Template Element
* Elements: New Post Navigation Template Element
* Elements: New Page Hero Block Element
* Elements: New Archive Navigation Template Element
* Elements: New Editor Width option in Block Elements
* Elements: Move Block Elements options into editor sidebar
* Elements: Show Site Header options by default in Header Element
* Elements: Fix Classic Editor issue in Block Elements
* Elements: Add a list of active Elements to page editor
* Elements: Add a list of active Elements to the admin bar
* Elements: Add notices to Customizer if Elements may be overwriting options
* Elements: Disable mobile header menu if menu is disabled via Layout Element
* Elements: Improve Display Rule loading performance
* Elements: Add block type filter to Elements dashboard
* Elements: Add generate_element_display filter
* Elements: Add No Results as a Display Rule condition
* Menu Plus: Hide slideout toggle at set mobile menu breakpoint value
* Menu Plus: Fix sticky menu height when using navigation as header
* Menu Plus: Add dimensions to mobile/sticky logos
* Menu Plus: Make Off-Canvas menu take up full width of canvas
* Menu Plus: Fix menu bar item sticky transition
* Menu Plus: Prevent sticky sidebar nav if mobile header is set to sticky
* Secondary Nav: Fix centered secondary navigation items using flexbox
* Secondary Nav: Fix conflict with Nav as Header option
* Secondary Nav: Fix missing menu cart items when using click dropdowns
* Sections: Officially deprecate module
* Site Library: Completely rebuild Site Library using React
* WooCommerce: Move full width single product CSS to inline CSS
* WooCommerce: Use wc_get_product() instead of new WC_Product()
* WooCommerce: Add more checks for WC() class to prevent error logs
* WooCommerce: Add generate_wc_cart_panel_checkout_button_output filter
* WooCommerce: Add generate_wc_sticky_add_to_cart_action filter
* WooCommerce: Add generate_wc_show_sticky_add_to_cart filter
* WooCommerce: Re-write quantity button javascript to be more performant/extendable
* General: Update theme install link
* General: Update alpha color picker script
* General: Use correct URL scheme in external stylesheet URLs
* General: Check if FS_CHMOD_FILE is defined in external stylesheet generation
* General: Use inline CSS when using AMP plugin
* General: Update EDD_SL_Plugin_Updater class to 1.8.0
* General: Clean up javascript throughout plugin
* General: Replace deprecated jQuery functions

= 1.12.3 =
* WooCommerce: Fix quantity buttons in WP 5.6

= 1.12.2 =
* Blog: Load columns CSS when using filter to enable it anywhere
* Menu Plus: Fix sticky mobile header jump when using inline mobile toggle
* Menu Plus: Add margin to sticky nav branding when set to full width
* Menu Plus: Disable sticky nav container text align padding
* Menu Plus: Make mobile menu absolute only when smooth scroll is enabled
* Menu Plus: Improve nav as header/mobile header when using flexbox
* Menu Plus: Prevent off-canvas panel close button from flashing visible when closing
* Spacing: Fix custom nav search height

= 1.12.1 =
* Typography: Fix missing heading font size controls in the Customizer

= 1.12.0 =
* Blog: Fix column margin on mobile with some caching plugins
* Blog: Add post-load trigger to infinite scroll for better plugin compatibility
* Blog: Take generate_blog_columns filter into account when loading columns CSS
* Colors: Add search menu-bar-item color live preview to Customizer
* Elements: Fix broken custom hook field in Block Elements
* Elements: Integrate page hero with text container alignment in GP 3.0
* Elements: Better integrate Header Element colors with GP 3.0
* Elements: Only disable content title when {{post_title}} is present on single pages
* Elements: Fix </body> tag in metabox
* Elements: Add generate_elements_metabox_ajax_allow_editors filter
* Elements: Add new 3.0 hooks to hook selector
* Elements: Re-add generate_elements_custom_args filter
* Menu Plus: Better prepare navigation as header for GP 3.0
* Menu Plus: Better prepare Off-Canvas Panel for GP 3.0
* Menu Plus: Better prepare sticky navigation for GP 3.0
* Menu Plus: Add has-menu-bar-items class to mobile header if needed
* Menu Plus: Add is-logo-image class to all site logos
* Menu Plus: Fix mobile header alignment when it has menu bar items
* Secondary Nav: Better prepare sticky navigation for GP 3.0
* Secondary Nav: Fix hidden navigation widget in top bar when merged with secondary nav
* Sites: Improve the option-only import feature
* Sites: Improve the refresh sites button functionality
* Sites: Improve the undo site import functionality
* Sites: Fix Elementor site import issues
* Sites: Re-add Elementor sites to library
* Spacing: Better prepare Customizer live preview for GP 3.0
* Spacing: Fix blog column spacing Customizer live preview
* Spacing: Stop handling mobile header, widget and footer widget features added in GP 3.0
* Typography: Add mobile font size control for H3 (when using GP 3.0)
* Typography: Add mobile font size control for H4 (when using GP 3.0)
* Typography: Add mobile font size control for H5 (when using GP 3.0)
* Typography: Allow empty tablet and mobile site title font size values
* Typography: Make menu toggle CSS selector specific to primary navigation
* WooCommerce: Use CSS for secondary product image functionality instead of JS
* WooCommerce: Only load .js file if needed
* WooCommerce: Fix quantity box functionality when multiple quantity boxes exist on the page
* General: Improve alpha color picker script for better 5.5 compatibility
* General: Move child theme stylesheet after dynamic stylesheet if enabled
* General: Update gp-premium-de_DE.mo
* General: Update gp-premium-es_ES.mo
* General: Update gp-premium-fi.mo
* General: Update gp-premium-pt_PT.mo
* General: Update gp-premium-sv_SE.mo

= 1.11.3 =
* Blog: Set widths to grid-sizer element
* Elements: Fix legacy Page Header/Hooks buttons in the Elements dashboard
* Page Header: Replace .load() with .on('load')
* Page Header: Fix color picker error in WP 5.5

= 1.11.2 =
* Blog: Remove negative featured image top margin from columns when using one container
* Blog: Fix infinite scroll items loading above viewport when using columns
* Blog: Fix infinite scroll featured images not displaying in Safari
* Elements: Prevent error in editor when generate_get_option() function doesn't exist
* General: Load inline CSS in previews when using external CSS option
* General: Update gp-premium-es_ES.mo
* General: Update gp-premium-pt_PT.mo

= 1.11.1 =
* Elements: Remove stray quote character in Layout Element metabox
* Sections: Fix color picker JS error in WP 5.5
* General: Fix external CSS option not removing inline CSS in some cases

= 1.11.0 =
* New: Block Elements
* New: Apply Layout Element options to the block editor if set
* New: Generate dynamic CSS in an external file
* Blog: Separate CSS and load only when needed
* Blog: Add column width classes to stylesheet
* Blog: Disable featured image itemprop if microdata is disabled
* Blog: Add generate_blog_masonry_init filter
* Blog: Add generate_blog_infinite_scroll_init filter
* Blog: Fix archive page header overlap when using no featured image padding/one container
* Blog: Replace screen reader text with aria-label in read more buttons
* Disable Elements: Add option to disable the Mobile Header
* Disable Elements: Disable top bar disables it even when combined with Secondary Nav
* Disable Elements: Use generate_show_post_navigation filter to disable single post navigation
* Elements: Use full hook name with generate_ prefix in dropdown
* Elements: Rebuild how Element types are chosen
* Elements: Add chosen hook under type column in edit.php
* Menu Plus: Add generate_after_mobile_header_menu_button filter
* Menu Plus: Add sticky placeholder only when nav becomes sticky
* Menu Plus: Add class to sticky nav when scrolling up
* Menu Plus: Fix navigation branding/mobile header layout when using RTL languages
* Page Header: Prevent PHP notices
* Secondary Nav: Clean up CSS
* Secondary Nav: Add generate_after_secondary_navigation hook
* Secondary Nav: Add generate_before_secondary_navigation hook
* Secondary Nav: Integrate with future flexbox option
* Secondary Nav: Add has-top-bar class if needed
* Secondary Nav: Add screen reader text to mobile menu toggle if no text exists
* Secondary Nav: Remove microdata if disabled
* Secondary Nav: Add generate_secondary_menu_bar_items hook
* Spacing: Set sidebar width in Customizer for future flexbox option
* WooCommerce: Add generate_woocommerce_show_add_to_cart_panel filter
* WooCommerce: Integrate with future flexbox option
* WooCommerce: Ensure WC()->cart is set
* WooCommerce: Remove left margin from SVG menu cart icon
* WooCommerce: Show sticky add to cart panel on sold individually products
* WooCommerce: Remove bottom margin from related/upsell products
* WooCommerce: Fix cart menu item spacing in RTL languages
* WooCommerce: Fix menu item cart dropdown design in RTL languages
* General: Update selectWoo
* General: Update select2
* General: Run all CSS through PostCSS
* General: Fix various text domains
* General: Fix JS error when toggling nav as header option without Colors/Typography modules
* General: Update all translations over 90% complete
* General: PHP cleanup/coding standards
* General: Add off_canvas_desktop_toggle_label to wpml-config.xml

= 1.10.0 =
* Blog: Remove existing on-the-fly featured image resizer (Image Processing Queue)
* Blog: Choose from existing image sizes for featured images
* Blog: Use CSS to further resize featured images if necessary
* Blog: Fix edge case persistent transient bug with old image resizer
* Elements: Fix broken closing element in metabox
* General: Change scroll variable to gpscroll in smooth scroll script to avoid conflicts
* General: Update responsive widths in Customizer
* General: Fix responsive Customizer views when using RTL
* Menu Plus: Don't output sticky nav branding if sticky nav is disabled
* Menu Plus: Fix focus when off canvas overlay is opened (a11y)
* Menu Plus: Fix sticky navigation jump when navigation branding is in use
* Sections: Fix visible block editor when Sections are enabled
* WooCommerce: Use minmax in grid template definitions to fix overflow issue
* WooCommerce: Prevent add to cart panel interfering with back to top button on mobile
* WooCommerce: WooCommerce: Fix secondary image position if HTML isn't ordered correctly
* General: Add/update all translations over 50% complete. Big thanks to all contributors!
* Translation: Added Arabic - thank you anass!
* Translation: Added Bengali - thank you gtmroy!
* Translation: Added Spanish (Spain) - thank you davidperez (closemarketing.es)!
* Translation: Added Spanish (Argentina) - thank you bratorr!
* Translation: Added Finnish - thank you Stedi!
* Translation: Add Dutch - thank you Robin!
* Translation: Added Ukrainian - thank you EUROMEDIA!
* Translation: Vietnamese added - thank you themevi!

= 1.9.1 =
* Blog: Fix "null" in infinite scroll load more button text
* WooCommerce: Fix hidden added to cart panel on mobile when sticky nav active
* WooCommerce: Fix missing SVG icon in mobile added to cart panel

= 1.9.0 =
* Blog: Support SVG icon feature
* Colors: Add navigation search color options
* Disable Elements: Disable mobile menu in Mobile Header if nav is disabled
* Elements: Add wp_body_open hook
* Elements: Allow 0 mobile padding in Elements
* Elements: Add generate_elements_admin_menu_capability filter
* Elements: Add generate_page_hero_css_output filter
* Elements: Prevent error in Header Element if taxonomy doesn't exist
* Elements: Fix double logo when Header Element has logo + using nav as header
* Elements: Fix mobile header logo not replacing if merge is disabled
* Elements: Fix missing arrow in Choose Element Type select in WP 5.3
* Elements: Add generate_inside_site_container hook option
* Elements: Add generate_after_entry_content hook option
* Menu Plus: Add off canvas desktop toggle label option
* Menu Plus: Add generate_off_canvas_toggle_output filter
* Menu Plus: Support SVG icon feature
* Menu Plus: Fix sticky navigation overlapping BB controls
* Menu Plus: Add align-items: center to nav as header, mobile header and sticky nav with branding
* Sections: Fix text/visual switch bug in Firefox
* Sites: Add option to revert site import
* Sites: Increase site library limit to 100
* Spacing: Add live preview to group container padding
* Typography: Add tablet site title/navigation font size options
* Typography: Add archive post title weight, transform, font size and line height
* Typography: Add single content title weight, transform, font size and line height
* Typography: Only call all google fonts once in the Customizer
* Typography: Get Google fonts from readable JSON list
* Typography: Make sure font settings aren't lost if list is changed
* Typography: Only call generate_get_all_google_fonts if needed
* WooCommerce: Add columns gap options (desktop, tablet, mobile)
* WooCommerce: Add tablet column options
* WooCommerce: Add related/upsell tablet column options
* WooCommerce: Support SVG icon feature
* WooCommerce: Prevent empty added to cart panel on single products
* WooCommerce: Fix woocommerce-ordering arrow in old FF versions
* WooCommerce: Make item/items string translatable
* General: Better customizer device widths
* General: Use generate_premium_get_media_query throughout modules
* General: Improve Customizer control styling

= 1.8.3 =
* Menu Plus: Use flexbox for center aligned nav with nav branding
* Menu Plus: Center overlay off canvas exit button on mobile
* Menu Plus: Add alt tag to sticky nav logo
* Menu Plus: Set generate_not_mobile_menu_media_query filter based on mobile menu breakpoint
* Sections: Remember when text tab is active
* Sections: Disable visual editor if turned off in profile
* Typography: Add generate_google_font_display filter
* WooCommerce: Fix single product sidebar layout metabox option
* WooCommerce: Reduce carousel thumbnail max-width to 100px to match new thumbnail sizes

= 1.8.2 =
* Elements: Use Page Hero site title color for mobile header site title
* Menu Plus: Give mobile header site title more left spacing
* Menu Plus: Fix nav search icon in sticky navigation when using nav branding in Firefox
* Site Library: Show Site Library tab even if no sites exist
* Site Library: Show an error message in Site Library if no sites exist
* Typography: Remove reference to generate_get_navigation_location() function
* WooCommerce: Remove quantity field arrows when using quantity buttons in Firefox
* WooCommerce: Remove extra border when loading quantity buttons
* WooCommerce: Use get_price_html() is sticky add to cart panel

= 1.8.1 =
* Menu Plus: Revert sticky nav duplicate ID fix due to Cyrillic script bug

= 1.8 =
* Blog: Apply columns filter to masonry grid sizer
* Colors: Merge Footer Widgets and Footer controls in Color panel
* Colors: Remove edit_theme_options capability to Customizer controls (set by default)
* Disable Elements: Make sure mobile header is disabled when primary navigation is disabled
* Elements: Add content width option in Layout Element
* Elements: Fix mobile header logo when mobile menu toggled
* Elements: Add generate_page_hero_location filter
* Elements: Add generate_elements_show_object_ids filter to show IDs in Display Rule values
* Elements: Prevent merged header wrap from conflicting with Elementor controls
* Elements: Change Container tab name to Content
* Elements: Add woocommerce_share option to Hooks
* Elements: Improve WPML compatibility
* Elements: Improve Polylang compatibility
* Elements: Prevent PHP notices when adding taxonomy locations to non-existent archives
* Elements: Add generate_mobile_cart_items hook to hook list
* Elements: Add generate_element_post_id filter
* Elements: Escape HTML elements inside Element textarea
* Elements: Add Beaver Builder templates to the Display Rules
* Menu Plus: Add mobile header breakpoint option
* Menu Plus: Add off canvas overlay option
* Menu Plus: Add navigation as header option
* Menu Plus: Remove navigation logo option if navigation as header set
* Menu Plus: Add sticky navigation logo option
* Menu Plus: Allow site title in mobile header instead of logo
* Menu Plus: Add option to move exit button inside the off canvas panel
* Menu Plus: Change Slideout Navigation name to Off Canvas Panel
* Menu Plus: Only re-focus after slideout close on escape key
* Menu Plus: Give close slideout event a name so it can be removed
* Menu Plus: Remove invalid transition-delay
* Menu Plus: Improve slideout overlay transition
* Menu Plus: Add mobile open/close icons to GPP font
* Menu Plus: Allow dynamic widget classes in off canvas panel (fixes WC range slider widget issue)
* Menu Plus: Basic compatibility with future SVG icons
* Menu Plus: Prevent duplicate IDs when sticky navigation is cloned
* Secondary Nav: Add dropdown direction option
* Secondary Nav: Basic compatibility with future SVG icons
* Sections: Fix section editor issues in WP 5.0
* Sections: Show Better Font Awesome icon in editor
* Sites: Re-design UI
* Sites: Add option to activate as a module like all the other modules
* Sites: Don't show backup options button if no options exist
* Sites: Make JS action classes more specific to the site library
* Sites: Set mime types of content.xml and widgets.wie
* Spacing: Add header padding option for mobile
* Spacing: Add widget padding option for mobile
* Spacing: Add footer widgets padding option for mobile
* Spacing: Add content separator option
* Spacing: Apply mobile menu item width to mobile bar only
* WooCommerce: Add option for mini cart in the menu
* WooCommerce: Add option to open off overlay panel on add to cart
* WooCommerce: Add option to open sticky add to cart panel on single products
* WooCommerce: Add option to add +/- buttons to the quantity fields
* WooCommerce: Add option to show number of items in cart menu item
* WooCommerce: Add option to choose single product image area width
* WooCommerce: Add color options for price slider widget
* WooCommerce: Use CSS grid for the product archives
* WooCommerce: Horizontally align add to cart buttons
* WooCommerce: Re-design the cart widget
* WooCommerce: Tighten up product info spacing
* WooCommerce: Improve product tab design to look more like tabs
* WooCommerce: Simplify single product image display
* WooCommerce: Use flexbox for quantity/add to cart alignment
* WooCommerce: Improve rating star styles
* WooCommerce: Use product alignment setting for related/upsell products
* WooCommerce: Remove bottom margin from product image
* WooCommerce: Organize colors in the Customizer
* WooCommerce: Remove title attribute from menu cart item
* WooCommerce: Improve coupon field design
* WooCommerce: Improve result count/ordering styling
* WooCommerce: Add gap around WC single product images
* WooCommerce: Remove arrow from checkout button
* WooCommerce: Hide view cart link on add to cart click
* WooCommerce: Organize CSS
* Introduce in-Customizer shortcuts
* Add generate_disable_customizer_shortcuts filter
