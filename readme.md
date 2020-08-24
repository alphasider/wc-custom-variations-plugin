# What does this plugin do?

Plugin changes variable product view (dropdown) in single product page

## Installation

1. Upload `parachute` plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Done!

## Usage

The plugin assumes that each product has 3 attributes:
1. bed_size (Single / Double / King / Super King)
2. frequency (1 / 2 / 3 / 4)
3. delivery (Day / Month / Morning-Evening)

## Attention
The plugin uses/modifies WooCommerce core functions `(woocommerce/include/wc-template-functions.php)` and templates `woocommerce/single-product/add-to-cart/variable.php` and may include a lot of unnecessary code :)