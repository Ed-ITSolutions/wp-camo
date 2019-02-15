=== WP-Camo ===
Contributors: arcath
Donate link: https://www.ed-itsolutions.com
Tags: proxy,
Requires at least: 4.6
Tested up to: 5.0.3
Stable tag: trunk
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Passes images through the WordPress site to prevent mixed content errors and bypass local filtering.

== Description ==

Supplies filters

Orignal use case was WordPress themes that used images from facebook which in schools was blocked. By _proxying_ the image through wp-camo images from Facebook will now load in school.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-camo` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->WP-Camo screen to configure the plugin

There are two settings that can be configured

 * Prevent Mixed Content Errors, which applies a filter to `the_content` which finds all `http://` images and changes them to wp-camo images.
 * Apply WP-Camo to these domains, which will pass images on these domains through wp-camo regardless of if its hosted on https.

In your theme/plugin you can use the filter `wp_camo_hash_url` to get the location of the image through WP-Camo.

```php
apply_filters('wp_camo_hash_url', $url);
```

The image's url will be a location in `/wp-content/uploads/wp-camo/`. The image will be downloaded and cached on the server to reduce load.

There are 2 filters for changing the path wp-camo uploads images to.

 - `wp_camo_disk_path` which sets the on disk path of the wp-camo directory.
 - `wp_camo_public_path` which sets the public url of the wp-camo directory.

== Frequently Asked Questions ==

= Images don't appear = 

This could be for a few reasons, the most common are:

 * WP-Camo could not write to the disk.
 * Your web server could not request the image.

== Changelog ==

= 1.0.0 =

 * Cache the images on the server instead of fetching them anew each request.

== Upgrade Notice ==

= 1.0.0 =
Improves performance by caching images on the server.

== Screenshots ==

1. logo.png