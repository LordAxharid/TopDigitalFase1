<?php

namespace Drupal\co_150_core\Service;
use Drupal\file\Entity\File;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

use Drupal;
use Exception;

class S3 {
  protected $s3Obj;

  public function __construct() {
    $config = \Drupal::service('config.factory')->getEditable('adminconfig.adminsettings');

    $keyID = $config->get('s3_KeyId') ?? 'AKIAZSJKB6ZN4CYRTWH7';
    $secretKey = $config->get('s3_SecretKey') ?? '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';
    $region = $config->get('s3_Region') ?? 'us-east-2';
    $this->bucket =  $config->get('s3_Bucket') ?? 'dopresidente';
    $this->dir = $config->get('s3_Dir') ?? 'vaccine-hotel';


    $s3client = new S3Client([
        'version'     => 'latest',
        'region'      => $region,
        'credentials' => [
          'key'    => $keyID,
          'secret' => $secretKey,
        ],
      ]);

    $this->s3Obj = $s3client ;
  }

  public function sendFile($file, $native = false) {
    if(!$file){ return 'File Not Found'; }
    if($native){
        $file_tmp = $file['tmp_name'];
        $link_to_file = file_create_url($file['name']);
        $content_type = $file['type'];
    }else{
        $file_tmp = File::load( $file );
        $file_uri = $file_tmp->getFileUri();
        $link_to_file = file_create_url($file_uri);
        $content_type = $file_tmp->getMimeType();
        $file_tmp = $file_uri;
    }
    $ext = pathinfo($link_to_file, PATHINFO_EXTENSION);
    $name = pathinfo($link_to_file, PATHINFO_FILENAME);

    $key = $this->dir.'/file_'.$name.time().'.'.$ext;
    try{
      $result = $this->s3Obj->putObject([
        'Bucket' => $this->bucket,
        'Key' => $key,
        'ContentType' => $content_type,
        'SourceFile' => $file_tmp
      ]);
      $link = $result['ObjectURL'];
      return $link;
    } catch (S3Exception $e) {
      \Drupal::logger('S3File:Error')->notice('S3 --->'.$e->getMessage());
      return FALSE;
    }
  }

}

