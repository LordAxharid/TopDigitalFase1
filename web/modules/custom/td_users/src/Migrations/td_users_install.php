<?php

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\co_150_core\Controller\ContentController;

use Drupal\td_users\Service\Profile as ProfileService;

use Drupal\profile\Entity\ProfileType;

/**
 * 
 * 
 */
function td_users_install(){
  // $profile_type = ProfileType::load('user_register_form');
  // if (!$profile_type) {
      ProfileService::create();
  // }
  // return $profile_type;
}

/**
 * Implements hook_uninstall()
 */
function td_users_uninstall()
{

}




