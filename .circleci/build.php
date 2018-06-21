<?php
require(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

wup_build(
  'wp-camo',
  dirname(dirname(__FILE__)),
  getenv('WUP_DEPLOY_KEY'),
  'http://local.ed-it.solutions/wp-admin/admin-post.php'
);