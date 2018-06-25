# WP-Camo

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

