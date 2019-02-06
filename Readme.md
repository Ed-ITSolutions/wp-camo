# WP-Camo

[![CircleCI](https://circleci.com/gh/Ed-ITSolutions/wp-camo/tree/master.svg?style=svg)](https://circleci.com/gh/Ed-ITSolutions/wp-camo/tree/master)

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

The image url will be a location in `/wp-content/uploads/wp-camo/`. The image will be downloaded and cached on the server to reduce load.

### Error Handling

If WP-Camo encounters an error (404 on the image or bad image file) it will return an image with the error in, which should prevent layouts from being broken by errors.

### Change paths

There are 2 filters for changing the path wp-camo uploads images to.

 - `wp_camo_disk_path` which sets the on disk path of the wp-camo directory.
 - `wp_camo_public_path` which sets the public url of the wp-camo directory.

### Content Filters

Under Settings -> WP Camo there are two options that control the filters applied to `the_content`.

#### Prevent mixed content errors

When enabled any image source that starts with `http://` will be run through WP-Camo to prevent any mixed content warnings.

#### Apply WP-Camo to these domains

A list of domains that should be run through WP-Camo. Useful for sites like Facebook and Twitter that are frequently blocked by enterprise filtering systems but would normally meet content policies.