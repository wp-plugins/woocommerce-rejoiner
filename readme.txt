=== WooCommerce Rejoiner ===
Contributors: madjax
Tags: woocommerce, rejoiner, abandoned cart, remarketing, ecommerce, cart abandonment email
Requires at least: 3.8
Tested up to: 4.1.1
Stable tag: 1.2.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Rejoiner enables any WooCommerce merchant to send automated, segmented email campaigns like: cart abandonment, post-purchase, welcome series, win back & more. The Rejoiner add-on is simple to install and takes just a few minutes to configure. Their cart abandonment campaigns alone can convert up to 15% of abandoners into paying customers. They provide full service set up & support and will even design your first campaign for free.  Better yet, their 14-day free trial doesn't start until you've generated real revenue. To get started, [sign up for an account today](https://app.rejoiner.com/signup).

* Reliable, authenticated email delivery = no messing with PHP sendmail
* Flexible scheduling & real-time delivery = no cron jobs to set up or manage
* Segment campaigns based on items in cart, cart value, customer purchase history & more 
* Regenerate user sessions across all devices
* Free custom templates for every new client

== Installation ==

1. Upload, activate WooCommerce Rejoiner plugin.

2. [Signup](https://app.rejoiner.com/signup) for a free account at [Rejoiner.com](https://app.rejoiner.com/signup)

3. Visit WooCommerce Settings and look for the Integration tab, click on the Rejoiner link. Enter your account details there as found on the implementation tab when logged in to Rejoiner.com.

== Frequently Asked Questions ==

= Why do I need this? =
Recent studies have shown that roughly two out of every three shopping carts are abandoned.

= How does it work? =
This plugin inserts an asynchronous tracking code on your checkout page, and conversion code on your receipt page. When a customer visits the checkout page, Rejoiner captures their email as soon as it's entered. When they abandon the cart, your remarketing email campaign begins at your predefined intervals.

== Screenshots ==

== Changelog ==
= 1.2.5 =
* Add new filters: wc_rejoiner_cart_item_name, wc_rejoiner_cart_item_variant, wc_rejoiner_thumb_size - see included sample-functions.php file
* When user is logged in, set 'email' parameter as part of the setCartData call on cart and checkout, with the customer's email address

= 1.2.4 =
* Undeclared variable bug fix

= 1.2.3 =
* Product name escaping bug fix

= 1.2.2 =
* Remove description from Rejoiner JS
* Better number formatting
* Prevent display of tracking code on thank you page

= 1.2.1 =
* Display tracking only on cart and checkout

= 1.2 =
* Validate image URLs
* Use excerpt for description
* Better description sanitization

= 1.1 =
* Initial public release