<?php
class WPCamo{
  public function __construct(){
    add_filter('wp_camo_hash_url', array($this, 'hashUrl'));

    add_action('init', array($this, 'rewrites'));
    add_action('template_redirect', array($this, 'returnResource'), 1);
  }

  public function rewrites(){
    add_rewrite_tag('%wp_camo%', '([^&]+)');
    add_rewrite_rule('wp_camo/(.*)?', 'index.php?wp_camo=$matches[1]', 'top');
  }

  public function hashUrl($url){
    $hash = base64_encode(openssl_encrypt($url, 'aes128', NONCE_KEY, 0, substr(NONCE_SALT, 0, 16)));
    return home_url('/wp_camo/' . $hash);
  }

  public function returnResource(){
    global $wp_query, $wp;

    if(substr($wp->request, 0, 7) != 'wp_camo'){
      return;
    }

    $hash = $wp_query->get('wp_camo');
    $url = openssl_decrypt(base64_decode($hash), 'aes128', NONCE_KEY, 0, substr(NONCE_SALT, 0, 16));

    $file = wp_remote_get($url);

    header('Content-Type:' . $file['headers']['content-type']);

    echo($file['body']);
  }
}