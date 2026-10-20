# AEGIS Pinterest for WooCommerce Conditions

Add-on for [Pinterest for WooCommerce](https://wordpress.org/plugins/pinterest-for-woocommerce/) that sets a product's Pinterest condition from the classic product editor.

- **Simple products:** set New, Used, or Refurbished on that product.
- **Variable products:** apply one condition to every variation of that product.
- **Do not change:** leave existing values alone.

Author: [Gambit Mutant](https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions)

Version 1.3.1 · GPL-2.0-or-later

## Donations

Donations support **The AEGIS Alliance's project funding**. A secondary purpose of donations is **business operations funding**.

| | |
| --- | --- |
| AEGIS News | [theaegisalliance.com](https://theaegisalliance.com) |
| AEGIS Shop | [shop.theaegisalliance.com](https://shop.theaegisalliance.com) |
| PayPal | username `aegisalliance` |
| Venmo | username `aegisalliance` |
| Zelle | username `aegisalliance` |
| Cash App | `$aegisalliance` |
| Crypto accepted at Coinbase | |
| USDC | `0x39C7e115C4184f2E646D44E3681A87a22a93341B` |
| BTC | `3PCJwFfVytUCW2mfgUgKbjWmqsYvTzgCH1` |
| ETH | `0xcD8B620962e9ff4a098c4134f1C82fDf96236F87` |
| DOGE | `DAo22guf8pmNcF1V7FvuqrXTgy2khuPysB` |
| USDT | `0x4aF2eE05A313c8a8dd53EdA87DDA69F3E375c3B5` |

## Requirements

| Software | This plugin | Notes |
| --- | --- | --- |
| WordPress | 6.2 or newer, tested up to **7.1.3** | Pinterest for WooCommerce 1.5.4 itself requires WordPress 7.0. |
| PHP | **7.4 or newer**, including **8.4** and **8.5** | No PHP 8-only syntax. Older supported PHP versions keep working. PHP 8 string checks guard stored meta. |
| WooCommerce | 7.1 or newer, tested up to **11.2** | Current patch of that line is 11.2.1. Pinterest for WooCommerce 1.5.4 requires WooCommerce 11.1. |
| Pinterest for WooCommerce | Tested with **1.5.4** | Any release that stores `_wc_pinterest_condition` works the same way. |

WordPress 6.5 and newer also reads the plugin header `Requires Plugins: woocommerce, pinterest-for-woocommerce` and will ask for both before activation.

## Install

1. Install and activate WordPress, WooCommerce, and Pinterest for WooCommerce.
2. If **WooCommerce Pinterest Condition Bulk Updater** is still installed, deactivate and delete it first. Do not run both.
3. On the [Releases](https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions/releases) page, download `aegis-pinterest-for-woocommerce-conditions.zip`. Do not install the "Source code" zip.
4. In wp-admin open **Plugins → Add New Plugin → Upload Plugin**, choose the zip, install, and activate.

To install by hand, unzip the package so this path exists, then activate the plugin:

`wp-content/plugins/aegis-pinterest-for-woocommerce-conditions/aegis-pinterest-for-woocommerce-conditions.php`

## Use it

1. Go to **Products → All Products** and edit a product in the **classic** product editor.
2. Open the **General** tab (price and tax live here).
3. Find **Update Pinterest Condition**. On a variable product the heading is **Bulk Update Variation Pinterest Condition**.
4. The line under the heading shows the current condition when the product, or all of its variations, already share one. The dropdown still starts on **Do not change**.
5. Choose **New**, **Used**, or **Refurbished** only when you want to write a condition.
6. Click **Update** (or **Publish**).
7. A success notice tells you the product was updated, or how many variations were updated.
8. Optional: confirm it on the product's **Pinterest** tab (simple products) or on each variation (variable products). Pinterest picks the value up on its next catalog sync.

## How it works

Pinterest for WooCommerce stores condition in post meta:

| | |
| --- | --- |
| Meta key | `_wc_pinterest_condition` |
| Allowed values | `new`, `used`, `refurbished` |
| Simple product | meta is stored on that product |
| Variable product | meta is stored on **each variation**, not on the parent |

That key is what Pinterest for WooCommerce 1.5.4 builds in `AttributeManager`: the attribute id `condition` is prefixed with `_wc_pinterest_`. The official plugin applies condition to the `simple` and `variation` product types. This add-on writes the same key and does not change feed generation, the Pinterest tag, API calls, prices, or the Google product category.

On save, WooCommerce fires `woocommerce_update_product`. This plugin then:

1. Ignores autosaves and requests that did not include its field.
2. Verifies the `pinterest_condition_update` nonce.
3. Requires the `edit_product` capability for that product.
4. Accepts only `new`, `used`, or `refurbished`. Anything else, including **Do not change**, is ignored.
5. Writes the meta with `update_post_meta`.
6. Stores a plain-text success notice for the current user for 30 seconds.

Uninstall does **not** delete condition meta. Those values belong to the catalog.

## Themes and plugins

Nothing from this plugin loads on the public site. It does not add CSS, JavaScript, template overrides, rewrite rules, or custom tables, and it does not touch orders, cart, or checkout. Theme layout, page cache, and frontend optimization plugins have nothing of ours to conflict with.

It is built to sit beside common stores:

- **Themes:** Storefront, Astra, Kadence, GeneratePress, OceanWP, Blocksy, Flatsome, Hello Elementor, Divi, Bricks, and the default WordPress themes.
- **Other product fields:** the control is a normal General-tab `options_group`. It does not remove fields other extensions add.
- **SEO:** Yoast, Rank Math, SEOPress.
- **Cache:** WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, FlyingPress. No cache purge is required for this plugin. Pinterest's feed job reads the meta later.
- **Security plugins:** Wordfence, Solid Security, Sucuri, Patchstack. Saves are gated by a nonce, a capability check, and an allowlist. If a firewall blocks admin form posts, allow normal product updates on `post.php`.
- **Multisite:** network-activated WooCommerce and Pinterest for WooCommerce are detected, not only plugins turned on for one site.
- **WooCommerce features:** compatible with HPOS (custom order tables) and Cart and Checkout Blocks, because this plugin never reads or writes them.

A product screen that never fires `woocommerce_product_options_general_product_data` will not show the dropdown. Use the classic product editor, or set `_wc_pinterest_condition` yourself to `new`, `used`, or `refurbished`.

Product types other than `variable` are saved on the product itself, the same as a simple product. Pinterest may still skip types it does not put in the catalog. This plugin does not change that.

## Privacy

No phone-home, no tracking pixels, and no catalog data leaves the site because of this plugin. Condition values stay in the WordPress database.

## Security

See [SECURITY.md](SECURITY.md). Report a vulnerability to [gambit@theaegisalliance.com](mailto:gambit@theaegisalliance.com).

## Changelog

### 1.3.1

- Updated the Coinbase donation addresses. Bitcoin is `3PCJwFfVytUCW2mfgUgKbjWmqsYvTzgCH1`. Dogecoin is `DAo22guf8pmNcF1V7FvuqrXTgy2khuPysB`.

### 1.3.0

- Renamed to AEGIS Pinterest for WooCommerce Conditions.
- Author is Gambit Mutant. Plugin URI points at this repository.
- Added installation, usage, how-it-works, and donation documentation.
- Tested-up-to headers: WordPress 7.1.3, WooCommerce 11.2, alongside Pinterest for WooCommerce 1.5.4.
- PHP 7.4 through 8.5, without PHP 8-only syntax, plus guards around stored meta.
- Saves are limited to `new`, `used`, and `refurbished`. Nonce and `edit_product` checks stay in place.
- Dependency detection covers Multisite network activation and loaders that change the active-plugin list.
- Declares WooCommerce HPOS and Cart and Checkout Blocks compatibility.
- Notice when WooCommerce or Pinterest for WooCommerce is inactive.
- Uninstall still leaves condition meta untouched.

### 1.2.3

Earlier release, distributed as WooCommerce Pinterest Condition Bulk Updater by The AEGIS Alliance.

## License

[GPL-2.0-or-later](LICENSE). Copyright 2025–2026 Gambit Mutant and The AEGIS Alliance.
