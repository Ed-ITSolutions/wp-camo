<?php
class WPCamo{
  public function __construct(){
    add_filter('wp_camo_hash_url', array($this, 'hashUrl'));
    add_filter('the_content', array($this, 'contentFilter'));

    add_action('init', array($this, 'rewrites'));
    add_action('admin_menu', array($this, 'adminPages'));
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

  public function contentFilter($content){
    $pattern = '/<img ?.* src="(.*?)"/m';
    $httpCamo = get_option('wp_camo_http', 1);

    if($httpCamo == 1){
      $content = preg_replace_callback($pattern, function($matches){
        if(substr($matches[1], 0, 5) == "http:"){
          return str_replace($matches[1], apply_filters('wp_camo_hash_url', $matches[1]), $matches[0]);
        }else{
          return $matches[0];
        }
      }, $content);
    }

    $content = preg_replace_callback($pattern, function($matches){
      $str = $matches[0];

      $domains = get_option('wp_camo_domains', array());

      foreach($domains as $domain){
        if(strpos($matches[1], $domain) !== false){
          $str =  str_replace($matches[1], apply_filters('wp_camo_hash_url', $matches[1]), $matches[0]);
        }
      }

      return $str;
    }, $content);

    return $content;
  }

  public function returnResource(){
    global $wp_query, $wp;

    if(substr($wp->request, 0, 7) != 'wp_camo'){
      return;
    }

    $hash = $wp_query->get('wp_camo');

    $transientKey = 'wp_camo_' . md5($hash);

    if(false === ($fileData = get_transient($transientKey))){
      $decoded = base64_decode($hash);

      $url = openssl_decrypt($decoded, 'aes128', NONCE_KEY, 0, substr(NONCE_SALT, 0, 16));

      if($url === false){
        $this->errorImage(
          '403',
          __('URL not encrypted by this site.', 'wp-camo')
        );
        return;
      }

      $url = str_replace("&amp;", "&", $url);

      $file = wp_remote_get($url);

      if($file->errors > 0){
        $this->errorImage(
          '404',
          __('Could not find Image.', 'wp-camo')
        );
        return;
      }

      $fileData = array(
        'header' => $file['headers']['content-type'],
        'body' => base64_encode($file['body'])
      );

      set_transient($transientKey, $fileData, 6 * HOUR_IN_SECONDS);
    }

    header('Content-Type:' . $fileData['header']);

    $body = base64_decode($fileData['body']);
    echo($body);
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

  public function adminPages(){
    add_options_page(
      __('WP-Camo', 'wp-camo'),
      __('WP-Camo', 'wp-camo'),
      'manage_options',
      'wp-camo',
      array($this, 'settingsPage')
    );
  }

  public function settingsPage(){
    require_once('pages/settings.php');
  }
}