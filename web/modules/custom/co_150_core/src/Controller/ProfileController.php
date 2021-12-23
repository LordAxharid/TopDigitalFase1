<?php

namespace Drupal\co_150_core\Controller;

use Drupal;
use Drupal\co_150_core\Controller\ContentController;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\profile\Entity\ProfileType;
use Exception;

class ProfileController extends ContentController
{

  private $bundle;

  public function __construct(string $id, string $label, bool $registration = false, array $roles = [])
  {
    $this->bundle = $id;
    $this->label = $label;
    $profile_type = ProfileType::load($id);
    if (!$profile_type) {
      // Profile type not exist, so create
      $this->createProfileType($id, $label, $registration, $roles);
    }
  }

  public function createProfileType(string $id, string $label, bool $registration = false, array $roles = [])
  {
    $type = ProfileType::create([
      'id'            => $id,
      'label'         => $label,
      'display_label' => $label,
      'registration'  => $registration,
      'roles'         => $roles,
    ]);
    $type->save();

    return $type;
  }

  public function profile_field(string $field_name, string $label, string $description, string $field_type, bool $required = true, int $cardinality = 1, array $settings_storage = null, array $settings_config = null, $defualt_value = '', string $entity_type = 'profile')
  {
    if (is_array($field_type) && count($field_type) == 2) {
      $type_storage       = $field_type[0];
      $type_form_display  = $field_type[1];
    } else {
      switch ($field_type) {
        case 'textarea':
          $type_storage       = "string_long";
          $type_form_display  = "string_textarea";
          break;
        case 'integer':
          $type_storage       = "integer";
          $type_form_display  = "number";
          break;
        case 'string':
          $type_storage       = "string";
          $type_form_display  = "string_textfield";
          break;
        case 'html':
          $type_storage       = "text_long";
          $type_form_display  = "text_textarea";
          break;
        case 'paragraph':
          $type_storage       = "entity_reference_revisions";
          $type_form_display  = "entity_reference_paragraphs";
          break;
        case 'taxonomy':
          $type_storage       = "entity_reference";
          $type_form_display  = "options_buttons";
          break;
        case 'image':
          $type_storage       = "image";
          $type_form_display  = "image_image";
          break;
        case 'checkbox':
          $type_storage       = "boolean";
          $type_form_display  = "boolean_checkbox";
          break;
        case 'str_list':
          $type_storage       = "list_string";
          $type_form_display  = "options_select";
          break;
        case 'link':
          $type_storage       = "link";
          $type_form_display  = "link_default";
          break;
        case 'file':
          $type_storage       = "file";
          $type_form_display  = "file_generic";
          break;
        case 'comment':
          $type_storage       = "comment";
          $type_form_display  = "comment_default";
          if ($defualt_value == '') {
            // Si es comentario, el $default_value si no se pasa, es necesario que sea de tipo array vacio
            $defualt_value = [];
          }
          break;
        case 'time_range':
          Drupal::service('module_installer')->install(['time_range']); // Neccesarry for this
          $type_storage       = "daterange";
          $type_form_display  = "time_range";
          break;
        default:
          throw new Exception("No se encuentra el tipo de campo '$field_type'. Puedes seleccionar una combinación custom haciendo uso de un arreglo", 1);
          break;
      }
    }
    $bundle = $this->bundle;
    $start = "field_";
    // Check if the field_name start or no with the field word
    if (stripos($field_name, $start) === 0) {
      // Start with the field, do nothing
    }
    else {
      // Not start with field, concat at beginning
      $field_name = $start . $field_name;
    }
    $storage = $this->createProfileStorageConfig($field_name, $type_storage, $cardinality, $settings_storage, $entity_type);
    $this->createFieldConfig($field_name, $label, $description, $storage, $required, $defualt_value, $settings_config, $entity_type);
    $this->create_field_form_display($entity_type, $bundle, $field_name, $type_form_display);
  }

  public function createProfileStorageConfig(string $field_name, string $type_storage, int $cardinality = 1, $settings_storage = null, string $entity_type = 'profile')
  {
    return  $this->create_field_storage($entity_type, $field_name, $type_storage, $cardinality, $settings_storage);
  }

  public function createFieldConfig(string $field_name, string $label, string $description, FieldStorageConfig $field_storage, bool $required = true, $defualt_value = [], array $settings_config = null, string $entity_type = 'profile')
  {
    $bundle = $this->bundle;
    $FieldConfig = $this->create_field_config($entity_type, $bundle, $field_name, $label, $description, $required, $defualt_value, $settings_config);
    $FieldConfig->set('field_storage', $field_storage);
    $FieldConfig->save();
  }
}
