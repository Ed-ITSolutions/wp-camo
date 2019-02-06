<?php
class WPCamo{
  public function __construct(){
    add_filter('wp_camo_hash_url', array($this, 'hashUrl'));
    add_filter('the_content', array($this, 'contentFilter'));

    add_action('admin_menu', array($this, 'adminPages'));
    add_action('wp_camo_clean', array($this, 'clean'));

    if(!wp_next_scheduled('wp_camo_clean')){
      wp_schedule_event(time(), 'daily', 'wp_camo_clean');
    }
  }

  public function hashUrl($url){
    $hash = base64_encode(openssl_encrypt($url, 'aes128', NONCE_KEY, 0, substr(NONCE_SALT, 0, 16)));

    $key = md5($url);

    $dir = wp_upload_dir()['basedir'] . '/wp-camo';

    if(!file_exists($dir)){
      mkdir($dir);
    }

    if(!get_transient('wp_camo_file_' . $key)){
      $file = wp_remote_get($url, array(
        'stream' => true,
        'filename' => $dir . '/' . $key
      ));

      set_transient('wp_camo_file_' . $key, true, 6 * HOUR_IN_SECONDS);
    }

    return wp_upload_dir()['baseurl'] . '/wp-camo/' . $key;
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

  private function imageCreateFromAny($filepath) {
    $type = exif_imagetype($filepath);
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
        break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
        break;
        case 3 :
            $im = imageCreateFromPng($filepath);
        break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
        break;
    }   
    return $im; 
  }

  public function clean(){
    $dir = wp_upload_dir()['basedir'] . '/wp-camo';

    $files = scandir($dir);

    foreach($files as $file){
      if($file != '.' && $file != '..'){
        $key = explode('.', $file)[0];
        
        if(!get_transient('wp_camo_file_' . $key)){
          unlink($dir . '/' . $file);
        }
      }
    }
  }
}