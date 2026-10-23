=== AEGIS Pinterest for WooCommerce Conditions ===
Contributors: gambitmutant
Donate link: https://theaegisalliance.com
Tags: woocommerce, pinterest, products, catalog, ecommerce
Requires at least: 6.2
Tested up to: 7.1.3
Requires PHP: 7.4
Requires Plugins: woocommerce, pinterest-for-woocommerce
Stable tag: 1.3.3
License: AEGIS Source-Available License
License URI: https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions/blob/main/LICENSE
WC requires at least: 7.1
WC tested up to: 11.2

Set a product's Pinterest condition, or apply one condition to every variation, from the product editor.

== Description ==

The AEGIS Alliance is the original author. Original repository: https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions

Forks, copies, and reuse must credit The AEGIS Alliance, link to that repository, and keep the donation information below intact. The license is the AEGIS Source-Available License in LICENSE. This release is source-available. Earlier releases remain under GPL-2.0-or-later.

AEGIS Pinterest for WooCommerce Conditions is an add-on for the official Pinterest for WooCommerce plugin. It adds one control to the classic product editor so you can set the catalog condition Pinterest expects: New, Used, or Refurbished.

On a simple product it updates that product. On a variable product it applies the same condition to every variation. Choose "Do not change" and it leaves existing values alone.

It writes the meta key Pinterest for WooCommerce already uses, `_wc_pinterest_condition`. It does not replace Pinterest for WooCommerce, and it does not change your catalog feed, Pinterest tag, prices, or Google product category.

= Donations =

Donations support The AEGIS Alliance's project funding. A secondary purpose of donations is business operations funding.

* [AEGIS News](https://theaegisalliance.com)
* [AEGIS Shop](https://shop.theaegisalliance.com)
* PayPal, Venmo, and Zelle username: `aegisalliance`
* Cash App: `$aegisalliance`
* Crypto accepted at Coinbase:
* USDC: `0x39C7e115C4184f2E646D44E3681A87a22a93341B`
* BTC: `3PCJwFfVytUCW2mfgUgKbjWmqsYvTzgCH1`
* ETH: `0xcD8B620962e9ff4a098c4134f1C82fDf96236F87`
* DOGE: `DAo22guf8pmNcF1V7FvuqrXTgy2khuPysB`
* USDT: `0x4aF2eE05A313c8a8dd53EdA87DDA69F3E375c3B5`

= How it works =

1. You edit a product and open the General tab, in the same area as price and tax.
2. The plugin shows "Update Pinterest Condition", or "Bulk Update Variation Pinterest Condition" when the product is variable.
3. If every targeted item already shares one condition, that condition is shown. The dropdown itself always starts on "Do not change", so clicking Update does not overwrite anything unless you pick New, Used, or Refurbished.
4. When you update the product, WooCommerce runs `woocommerce_update_product`.
5. The plugin checks a nonce, checks that you are allowed to edit that product, and accepts only `new`, `used`, or `refurbished`.
6. A simple product gets `_wc_pinterest_condition` on itself. A variable product gets that meta on each variation, not on the parent. That is where Pinterest for WooCommerce reads it.
7. A success notice confirms how many items were updated. The notice is stored for your user only, for 30 seconds.
8. The next Pinterest catalog sync includes the condition. Nothing is sent to Pinterest by this plugin.

Deactivating or deleting this plugin does not remove condition meta. Those values stay with the products for Pinterest for WooCommerce.

= Requirements =

* WordPress 6.2 or newer. Tested up to WordPress 7.1.3.
* PHP 7.4 or newer, including PHP 8.4 and PHP 8.5. The code does not use PHP 8-only syntax, so PHP 7.4 through 8.3 keep working. Stored values are checked before they are treated as text, which avoids PHP 8 type errors if another tool wrote unexpected meta.
* WooCommerce 7.1 or newer. Tested up to WooCommerce 11.2 (11.2.1 is the current patch of that line).
* Pinterest for WooCommerce. Tested against 1.5.4, the current WordPress.org release. That release requires WordPress 7.0, WooCommerce 11.1, and PHP 7.4, and it is tested up to WooCommerce 11.2. This add-on follows the meta key and the condition values shipped in 1.5.4: `new`, `used`, and `refurbished`.

= Themes and other plugins =

This plugin prints one field on the WooCommerce product screen and saves one post meta key. It does not add public scripts or styles, template overrides, rewrite rules, database tables, or checkout changes, so it does not fight theme CSS, page caches, or optimization plugins.

It is written to sit beside common stacks:

* Themes: Storefront, Astra, Kadence, GeneratePress, OceanWP, Blocksy, Flatsome, Hello Elementor, Divi, Bricks, and the default WordPress themes.
* Product data added by other extensions stays in place. The field is a normal General-tab options group and does not remove other fields.
* SEO plugins such as Yoast, Rank Math, and SEOPress.
* Cache and performance plugins such as WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, and FlyingPress. You do not need to purge a cache for this plugin. Pinterest's own feed job picks up the meta later.
* Security plugins such as Wordfence, Solid Security, Sucuri, and Patchstack. Saves are limited by a nonce, the `edit_product` capability, and an allowlist. If a firewall blocks admin form posts, allow normal product saves on `post.php`.
* Multisite. Network-activated WooCommerce and Pinterest for WooCommerce are recognized, not only plugins activated per site.
* HPOS (custom order tables) and Cart and Checkout Blocks. This plugin does not read or write orders, carts, or checkout.

If a custom product screen never runs `woocommerce_product_options_general_product_data`, the dropdown will not appear. Open the product in the classic editor, or set `_wc_pinterest_condition` to `new`, `used`, or `refurbished` directly.

= Privacy =

This plugin does not phone home, does not add tracking, and does not send catalog data anywhere. Condition values stay in your WordPress database as product meta.

== Installation ==

1. Install and activate WordPress.
2. Install and activate WooCommerce.
3. Install and activate Pinterest for WooCommerce (1.5.4 or another release that stores `_wc_pinterest_condition`).
4. If "WooCommerce Pinterest Condition Bulk Updater" is already installed, deactivate and delete it before activating this plugin so the two tools are not both on the product screen.
5. Download `aegis-pinterest-for-woocommerce-conditions.zip` from the GitHub Releases page. Do not install the "Source code" zip. WordPress needs the packaged plugin zip.
6. In wp-admin go to Plugins, Add New Plugin, Upload Plugin, choose the zip, Install Now, then Activate.
7. To install by hand, unzip the package so this file exists: `wp-content/plugins/aegis-pinterest-for-woocommerce-conditions/aegis-pinterest-for-woocommerce-conditions.php`. Then activate it from Plugins.

On WordPress 6.5 and newer the plugin header also requires WooCommerce and Pinterest for WooCommerce, so WordPress will ask for those before activation.

== How to use ==

1. Go to Products, All Products, and edit a product with the classic product editor.
2. Open the General tab.
3. Find "Update Pinterest Condition", or "Bulk Update Variation Pinterest Condition" on a variable product.
4. Leave "Do not change" selected if you are only editing price or stock.
5. Choose New, Used, or Refurbished to set the condition.
6. Click Update, or Publish for a new product.
7. Read the success notice. It reports one product, or the number of variations.
8. Optional check: open the Pinterest tab on a simple product, or each variation on a variable product. The condition there should match. Pinterest includes it on the next catalog sync.

Variable parents do not store a condition. Pinterest keeps condition on each variation. This plugin follows that rule.

Other product types, such as a simple subscription, are updated on the product itself, the same way a simple product is. Pinterest's catalog may still ignore product types it does not sync. This plugin does not change those rules.

== Frequently Asked Questions ==

= Does this replace Pinterest for WooCommerce? =

No. Pinterest for WooCommerce connects the store, builds the catalog feed, and shows the condition field on its own Pinterest tab. This plugin only makes that condition faster to set, especially across every variation of one product.

= Will Update wipe the condition if I forget to pick one? =

No. The dropdown opens on "Do not change". An empty choice writes nothing.

= Can I set a different condition on each variation? =

Not with the bulk control. It applies one condition to all variations of that product. Set them one by one on each variation's Pinterest tab if they need to differ.

= Does uninstall delete conditions? =

No. Uninstall leaves `_wc_pinterest_condition` in place.

= Where do donations go? =

Donations support The AEGIS Alliance's project funding. A secondary purpose is business operations funding. Addresses and usernames are in the Description section.

== Changelog ==

= 1.3.3 =

* Added the AEGIS Source-Available License as LICENSE. TERMS.md is the same license.

= 1.3.2 =

* Added TERMS.md. Forks must credit The AEGIS Alliance, link to the original repository, and keep donation information intact.
* This release is source-available. Earlier releases remain under the GPL.

= 1.3.1 =

* Updated the Coinbase donation addresses. Bitcoin is `3PCJwFfVytUCW2mfgUgKbjWmqsYvTzgCH1`. Dogecoin is `DAo22guf8pmNcF1V7FvuqrXTgy2khuPysB`.

= 1.3.0 =

* Renamed the plugin to AEGIS Pinterest for WooCommerce Conditions.
* Author is now Gambit Mutant, with the plugin linked to the GitHub repository.
* Documented installation, daily use, how the save works, and donation information.
* Declared compatibility with WordPress 7.1.3, WooCommerce 11.2, and Pinterest for WooCommerce 1.5.4.
* Kept PHP 7.4 support and avoided PHP 8-only syntax, with guards so PHP 8.4 and 8.5 do not throw on unexpected meta.
* Confirmed the saved values still match Pinterest's condition meta: `_wc_pinterest_condition` set to `new`, `used`, or `refurbished`.
* Limited saves to those three values, and kept the nonce and `edit_product` checks.
* Recognized WooCommerce and Pinterest for WooCommerce when they are network-activated or moved by other plugins' load order.
* Declared WooCommerce HPOS and Cart and Checkout Blocks compatibility.
* Shows a notice if a required plugin is inactive.
* Does not remove product condition meta on uninstall.

= 1.2.3 =

* Earlier release, published as WooCommerce Pinterest Condition Bulk Updater by The AEGIS Alliance.

== Upgrade Notice ==

= 1.3.3 =
Adds the AEGIS Source-Available License file. Donation addresses and condition saving are unchanged.

= 1.3.2 =
Adds the repository terms. Donation addresses are unchanged. Existing condition meta is left as it is.

= 1.3.1 =
Donation address update only. Bitcoin and Dogecoin now use the Coinbase addresses in the readme. No change to how conditions are saved.

= 1.3.0 =
Rebrand, documentation, and compatibility update for WordPress 7.1.3, WooCommerce 11.2, Pinterest for WooCommerce 1.5.4, and PHP 7.4 through 8.5. Deactivate the old "WooCommerce Pinterest Condition Bulk Updater" plugin if it is still installed. Existing condition meta is left as it is.
