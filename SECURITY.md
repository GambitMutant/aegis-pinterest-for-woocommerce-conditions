# Security

AEGIS Pinterest for WooCommerce Conditions only runs inside wp-admin product editing. It does not fetch remote code, does not write files, and does not run SQL of its own.

## What a save is allowed to do

A condition is written only when all of the following are true:

- The request is the WooCommerce product save (`woocommerce_update_product`), and it is not an autosave.
- The request includes this plugin's nonce (`pinterest_condition_update`), checked with `wp_verify_nonce()`.
- The current user passes `current_user_can( 'edit_product', $product_id )`.
- The submitted value is exactly `new`, `used`, or `refurbished` after `sanitize_text_field()` and `strtolower()`.
- The ID is an integer product WooCommerce can load.

The value is stored with `update_post_meta()` on that product, or on each variation when the product is variable. Output on the product screen is escaped (`esc_html`, `esc_attr`, `wp_kses_post`). The success notice is plain text created by the plugin, shown only to the user who saved, and removed after it is displayed.

Directory indexes are blocked with `ABSPATH` checks. Unknown or non-string meta is not passed to string functions, so PHP 8.4 and newer do not throw on bad data left by another tool.

The `Update URI` header points at this GitHub repository so a wordpress.org plugin cannot take over updates for this slug.

## Reporting

Email [gambit@theaegisalliance.com](mailto:gambit@theaegisalliance.com) with the plugin version, WordPress version, WooCommerce version, Pinterest for WooCommerce version, PHP version, and a description of the issue. Please give a reasonable time to ship a fix before any public write-up.

Do not open a public GitHub issue for an undisclosed vulnerability.
