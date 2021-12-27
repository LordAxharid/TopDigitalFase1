<?php

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\co_150_core\Controller\ContentController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;


use Drupal\td_users\Service\Profile as ProfileService;

use Drupal\profile\Entity\ProfileType;

/**
 * 
 * 
 */
function td_users_install(){

  td_users_entity_base_field_info();

}

function td_users_entity_base_field_info(EntityTypeInterface $entity_type) {
  if ($entity_type->id() === 'user') {
    $fields['field1'] = BaseFieldDefinition::create('text')
      ->setLabel(t('New field 1'))
      ->setTranslatable(TRUE);
    return $fields;
  }
}

/**
 * Implements hook_uninstall()
 */
function td_users_uninstall()
{

}




