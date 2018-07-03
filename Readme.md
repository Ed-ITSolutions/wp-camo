# WP-Camo

![Logo](./logo.png)

_Proxies_ images via WordPress so that the origin is the sites domain.

Orignal use case was WordPress themes that used images from facebook which in schools was blocked. By _proxying_ the image through wp-camo images from Facebook will now load in school.

## Install

Grab the latest zip file from the [releases](https://github.com/Ed-ITSolutions/wp-camo/releases/latest) page and upload it to your WordPress site.

## Usage

WP-Camo provides a filter to hash/encrypt the url.

```php
apply_filters('wp_camo_hash_url', $url);
```

This will return an image source that can be passed to an image tag.

The image url will be encrypted with the sites nonce and then base64 encoded. When an image is requested it runs a base64 decode and then decrypts the url using the site nonce.

This ensures only your site can be used to generate valid WP-Camo urls.

### Error Handling

If WP-Camo encounters an error (404 on the image or bad nonce) it will return an image with the error in, which should prevent layouts from being broken by errors.

### Content Filters

Under Settings -> WP Camo there are two options that control the filters applied to `the_content`.

#### Prevent mixed content errors

When enabled any image source that starts with `http://` will be run through WP-Camo to prevent any mixed content warnings.

#### Apply WP-Camo to these domains

A list of domains that should be run through WP-Camo. Useful for sites like Facebook and Twitter that are frequently blocked by enterprise filtering systems but would normally meet content policies.