<?php
namespace Drupal\td_users\Service;
use Drupal\profile\Entity\ProfileType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\profile\Entity\Profile as EntityProfile;

/**
 * Provides route responses for the Example module.
 */
class Profile {

    /**
     * create
     *
     * @return void
     */
    public static function create($id = 'user_register_form'){
        $profile_type = ProfileType::load($id);
        if (!$profile_type) {
            $profile_type = ProfileType::create([
                'id'            => $id,
                'label'         => 'test',
                'display_label' => 'test',
                'registration'  => 'user_register_form',
                'roles'         => ['authenticated'],
            ]);
            $profile_type->save();

            self::create_fields($profile_type);
        }


        return $profile_type;
    }
    
    /**
     * create_fields
     *
     * @param  mixed $profile_type
     * @return void
     */
    public function create_fields($profile_type){

        $newFields = ['testone', 'testtwo'];
        $nameField = ['Tapit Nombres', 'Tapit Apellidos'];
        foreach ($newFields as $key => $newField) {
            $field_storage = FieldStorageConfig::create([
                'field_name' => $newField,
                'entity_type' => 'user',
                'type' => 'string',
              ]);
              $field_storage->save();
          
              $field = FieldConfig::create([
                'field_storage' => $field_storage,
                'bundle' => $profile_type->id(),
                'label' => $nameField[$key],
              ]);
              $field->save();
        }
        return $newFields;
    }
}