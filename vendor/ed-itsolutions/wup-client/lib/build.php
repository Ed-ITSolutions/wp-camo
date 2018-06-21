<?php
function build_and_release($slug, $rootPath, $deployKey, $url){
  $zip = new ZipArchive();
  $filename = $rootPath . '/' . $slug . '.zip';

  unlink($filename);

  if($zip->open($filename, ZipArchive::CREATE) !== true){
    exit("Could not create {$filename}");
  }

  $files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
  );

  foreach ($files as $name => $file){
    // Skip directories (they would be added automatically)
    if(!$file->isDir()){
      // Get real and relative path for current file
      $filePath = $file->getRealPath();
      $relativePath = substr($filePath, strlen($rootPath) + 1);

      $dirs = explode(DIRECTORY_SEPARATOR, $relativePath);

      if(
        $dirs[0] !== '.git'
        &&
        $dirs[0] !== '.circleci'
      ){
        $zip->addFile($filePath, $slug . '/' . $relativePath);
      }    
    }
  }

  $zip->close();

  $postOpts = array(
    'action' => 'wup_release',
    'deployKey' => $deployKey,
    'release' => new CURLFile($filename)
  );


  $request = curl_init($url);
  curl_setopt($request, CURLOPT_POST, true);
  curl_setopt(
    $request,
    CURLOPT_POSTFIELDS,
    $postOpts
  );
  curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($request);
  $details = json_decode($result);

  curl_close($request);

  if(isset($details->error)){
    echo($details->error);
  }else{
    echo($details->success);
  }
}