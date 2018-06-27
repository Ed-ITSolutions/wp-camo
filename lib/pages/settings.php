<?php

if(isset($_POST['camo_domains'])){
  $rawDomains = explode("\n", $_POST['camo_domains']);

  $domains = array();
  
  foreach($rawDomains as $domain){
    $domains[] = str_replace(array("\r", "\n"), "", $domain);
  }

  update_option('wp_camo_domains', $domains);

  if($_POST['http_camo'] == "1"){
    update_option('wp_camo_http', true);
  }else{
    update_option('wp_camo_http', false);
  }
}

$httpCamo = get_option('wp_camo_http', 1);
$httpCamoValue = "";

if($httpCamo == 1){
  $httpCamoValue = "checked";
}

$domains = get_option('wp_camo_domains', array());

?>
<div class="wrap">
  <h1><?php _e('WP-Camo', 'wp-camo'); ?></h1>
  <form action="" method="POST">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><?php _e('Prevent mixed content errors', 'wp-camo'); ?></th>
          <td>
            <fieldset>
              <label for="http_camo">
                <input type="checkbox" id="http_camo" name="http_camo" <?php echo($httpCamoValue); ?> value="1" />
                Filter page content for http images.
              </label>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row"><?php _e('Apply WP-Camo to these domains (One Per Line)', 'wp-camo'); ?></th>
          <td>
            <fieldset>
              <textarea name="camo_domains" id="camo_domains" class="large-text"><?php echo(join("\n", $domains)); ?></textarea>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
    <input type="submit" value="Save Settings" class="button button-primary" />
  </form>
</div>