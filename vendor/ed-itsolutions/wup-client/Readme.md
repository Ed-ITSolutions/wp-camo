# WUP Client

Client side code for [WP Update Provider](https://github.com/Ed-ITSolutions/wp-update-provider).

## Install

Using Composer:

```
composer require ed-itsolutions/wup-client
```

## Usage

Require composer as normal.

Add `wup_client` to your `functions.php` or plugin class.

```php
// For a plugin
wup_client('plugin', 'plugin-slug', 'http://your.site.com/wup/plugin-slug');

// For a theme
wup_client('theme', 'theme-slug', 'http://your.site.com/wup/theme-slug');
//                                 ^^ URL of your WUP install.
```

That's it!

wup-client will now talk to WUP and offer updates when needed.

## CI

wup-client also provides the `wup_build` function which can be used to have your CI build and release a new version of your plugin automatically.

```php
wup_build(
  'theme-slug', // The themes (or plugins) slug.
  '/some/path', // The root path of the theme/plugin. Use dirname(__FILE__) to make this generic.
  'deployKey', // The deploy key for the server. DON'T ACTUALLY PUT THIS IN VCS. ProTip. getenv('WUP_DEPLOY_KEY')
  'http://yoursite.com/wp-admin/admin-post.php' // The admin-post.php url for your site.
);
```