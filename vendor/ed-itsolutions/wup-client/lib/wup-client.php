<?php
class WUPClient{
  public $url;
  public $type;
  public $slug;
  public $settingName;

  public function __construct($type, $slug, $url){
    $this->type = $type;
    $this->url = $url;
    $this->slug = $slug;

    $this->settingName = 'wup_client_' . $type . '_' . $slug;

    $this->hooks();
  }

  public function hooks(){
    // When WordPress checks for update.
    add_filter('pre_set_site_transient_update_' . $this->type . 's', array($this, 'onTransientUpdate'));

    // Inform WordPress of the monitored update.
		add_filter('site_transient_update_' . $this->type . 's', array($this,'injectUpdate'));

		//Delete our update info when WP deletes its own.
    //This usually happens when a theme is installed, removed or upgraded.
    // Delete update data when WordPress clears its data
		add_action('delete_site_transient_update_' . $this->type . 's', array($this, 'deleteStoredData'));
  }

  public function deleteStoredData(){
    delete_option($this->settingName);
  }

  public function onTransientUpdate($value){
    $this->checkForUpdates();

    return $value;
  }

  public function injectUpdate($updates){
    $state = get_option($this->settingName);

    if(
      !empty($state)
      &&
      isset($state->wupVersion)
      &&
      !empty($state->wupVersion)
      &&
      Composer\Semver\Comparator::greaterThan($state->wupVersion, $state->localVersion)
    ){
			$updates->response[$this->updateResponseKey()] = $this->updateResponse($state);
    }

    return $updates;
  }

  public function updateResponseKey(){
    if($this->type == 'theme'){
      return $this->slug;
    }else{
      return $this->slug . '/' . $this->slug . '.php';
    }
  }

  public function updateResponse($state){
    if($this->type == 'theme'){
      return array(
        'new_version' => $state->wupVersion,
        'url' => $state->detailsUrl,
        'package' => $state->downloadUrl,
        'theme' => $this->slug
      );
    }else{
      $update = new StdClass;
      $update->slug = $this->slug;
      $update->plugin = $this->updateResponseKey();
      $update->new_version = $state->wupVersion;
      $update->package = $state->downloadUrl;
      $update->url = $state->detailsUrl;

      return $update;
    }
  }

  public function checkForUpdates(){
    $state = get_option($this->settingName);

    if(!empty($state)){
      $state = new StdClass;
      $state->lastCheck = 0;
      $state->localVersion = '';
      $state->wupVersion = null;
      $state->detailsUrl = '';
      $state->downloadUrl = '';
    }

    $state->lastCheck = time();
    
    if($this->type == 'plugin'){
      $state->localVersion = $this->getLocalPluginVersion();
    }elseif($this->type == 'theme'){
      $state->localVersion = $this->getLocalThemeVersion();
    }

    // Save the state before update just in case things go wrong.
    update_option($this->settingName, $state);

    $data = $this->getWUPData($state->localVersion);
    $state->wupVersion = $data->version;
    $state->detailsUrl = $data->detailsUrl;
    $state->downloadUrl = $data->downloadUrl;
    update_option($this->settingName, $state);
  }

  public function getLocalPluginVersion(){
    if(!function_exists('get_plugin_data')){
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
    }
    
    $filePath = WP_PLUGIN_DIR . '/' . $this->slug . '/' . $this->slug . '.php';

    $data = get_plugin_data($filePath);
    return $data['Version'];
  }

  public function getLocalThemeVersion(){
    $theme = wp_get_theme($this->theme);
	  return $theme->get('Version');
  }

  public function getWUPData($localVersion){
    global $wp_version;

    $args = array(
      'timeout'     => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
      'blocking'    => true,
      'headers'     => array(
        'WP_DOMAIN' => home_url(),
        'WP_VERSION' => $localVersion
      ),
      'cookies'     => array(),
      'body'        => null,
      'compress'    => false,
      'decompress'  => true,
      'sslverify'   => true,
      'stream'      => false,
      'filename'    => null
    );

    $response = wp_remote_get($this->url, $args);

    return json_decode($response['body']);
  }
}