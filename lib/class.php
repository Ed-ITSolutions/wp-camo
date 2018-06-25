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

    $decoded = base64_decode($hash);

    $url = openssl_decrypt($decoded, 'aes128', NONCE_KEY, 0, substr(NONCE_SALT, 0, 16));

    if($url === false){
      $this->errorImage(
        '403',
        __('URL not encrypted by this site.', 'wp-camo')
      );
      return;
    }

    $file = wp_remote_get($url);


    if($file->errors > 0){
      $this->errorImage(
        '404',
        __('Could not find Image.', 'wp-camo')
      );
      return;
    }

    header('Content-Type:' . $file['headers']['content-type']);

    echo($file['body']);
  }

  public function errorImage($error, $message){
    header("Content-type: image/png");
    $image = imagecreate(300, 100);
    $background = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);

    imagestring($image, 4, 30, 25, 'Error ' . $error, $textColor);
    imagestring($image, 4, 30, 65, $message, $textColor);


    imagepng($image);
  }
}