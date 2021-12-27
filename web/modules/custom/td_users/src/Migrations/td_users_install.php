<?php

use \Drupal\Core\Entity;
use \Drupal\Core\Entity\Display\EntityFormDisplayInterface;

/**
 * Implement functions to install
 * 
 */
function td_users_install(){

  _addFieldsUser();

}

/**
 * Implement functions to add fields default
 * 
 */

function _addFieldsUser() {

  $fieldname = 'field_user_name';
  $fieldlabel = 'Nombre';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();

    // Create a form display for the default form mode.
    entity_get_form_display('user', 'user', 'default')
      ->setComponent($fieldname, array(
      'targetEntityType' => $entity_type,
      'bundle' => $bundle,
      'mode' => $form_mode,
      'weight' => 1,
      'status' => TRUE,
    ))->save();
  }

  $fieldname = 'field_user_lastname';
  $fieldlabel = 'Apellido';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_phone';
  $fieldlabel = 'Teléfono';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_dni';
  $fieldlabel = 'Documento Nal. de Identificación';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_birthdate';
  $fieldlabel = 'Fecha de nacimiento';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_gender';
  $fieldlabel = 'Genero';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_zipcode';
  $fieldlabel = 'Código postal';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'string',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
    ));
    $field->save();
  }

  $fieldname = 'field_user_terminos';
  $fieldlabel = 'Terminos';
  if(!\Drupal\field\Entity\FieldStorageConfig::loadByName('user',$fieldname)){

    $field_storage = \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'type' => 'boolean',
    ));
    $field_storage->save();

    $field = \Drupal\field\Entity\FieldConfig::create(array(
      'field_name' => $fieldname,
      'entity_type' => 'user',
      'bundle' => 'user',
      'label' => $fieldlabel,
      'settings' => array(
        'Si' => 'Si',
        'No' => 'No',
      )
    ));
    $field->save();

    // Create a form display for the default form mode.
    entity_get_form_display('user', 'user', 'default')
      ->setComponent($fieldname, array(
      'type' => 'boolean_checkbox',
      'targetEntityType' => $entity_type,
      'bundle' => $bundle,
      'mode' => $form_mode,
      'weight' => 1,
      'status' => TRUE,
    ))->save();
  }
}

/**
 * Implements hook_uninstall()
 */
function td_users_uninstall()
{

}
