<?php

namespace Drupal\co_150_core\Controller;

use Drupal;
use Exception;
use Drupal\node\Entity\Node;
use Drupal\system\Entity\Menu;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\webform\Entity\Webform;
use Drupal\Core\Serialization\Yaml;

class ContentController
{


  private $machine_name;

  private $content_title;

  private $content_description;

  /**
   * @param string $machine_name
   * @param string $content_title
   * @param string $content_description
   * @param string $content_type (default: node)
   */
  public function __construct($machine_name, $content_title, $content_description, $content_type = "node")
  {
    $this->machine_name         = $machine_name;
    $this->content_title        = $content_title;
    $this->content_description  = $content_description;
    $this->create_content($content_type);
  }

  public function getMachineName()
  {
    return $this->machine_name;
  }

  /**
   * Crear el contenido
   * @param string $content_type (default: node)
   */
  public function create_content($content_type = "node")
  {
    switch ($content_type) {
      case 'node':
        $this->create_node_content_type($this->machine_name, $this->content_title, $this->content_description);
        break;
      case 'paragraph':
        $this->create_paragraph_type($this->machine_name, $this->content_title, $this->content_description);
        break;
      case 'block_content':
          $this->create_block_content_type($this->machine_name, $this->content_title, $this->content_description);
          break;
      case 'webform':
        $this->create_webform_type($this->machine_name, $this->content_title, $this->content_description);
        break;
      default:
        //throw new Exception("Se necesita el tipo de contenido como parametro", 1);
        break;
    }
  }
  /**
   * Create new content type (Node Type)
   * @param string $machine_name
   * @param string $content_name
   * @param string $description
   *
   * @return void
   */
  public function create_node_content_type($machine_name, $content_name, $description)
  {
    $NodeType = NodeType::load($machine_name);
    if (!$NodeType) {
      $NodeType = NodeType::create(['type' => $machine_name, 'name' => $content_name, 'description' => $description])->save();
    }
  }

  /**
   * Create new Block type (Node Type)
   * @param string $machine_name
   * @param string $content_name
   * @param string $description
   *
   * @return void
   */
  public function create_block_content_type($machine_name, $content_name, $description)
  {
    $BlockType = BlockContentType::load($machine_name);
    if (!$BlockType) {
      $BlockType = BlockContentType::create(['id' => $machine_name, 'label' => $content_name, 'description' => $description])->save();
    }
  }
  /**
   *
   * Delete content type (Node Type)
   * @param string $machine_name
   *
   * @return void
   */
  public static function delete_node_content_type($machine_name)
  {
    $NodeType = NodeType::load($machine_name);
    if ($NodeType) {
      $NodeType->delete();
    }
  }

  /**
   * Create new content type (Paragraph Type)
   * @param string $machine_name
   * @param string $content_name
   * @param string $description
   *
   * @return void
   */

  public function create_paragraph_type($machine_name, $content_name, $description)
  {
    // This need the module entity_reference_revisions and paragraphs enabled:
    Drupal::service('module_installer')->install(['entity_reference_revisions']);
    Drupal::service('module_installer')->install(['paragraphs']);
    $type = ParagraphsType::load($machine_name);
    if (!$type) {
      ParagraphsType::create(['id' => $machine_name, 'label' => $content_name, 'description' => $description])->save();
    }
  }

  /**
   * Create new webform type (Webform Type)
   * @param string $machine_name
   * @param string $content_name
   * @param string $description
   *
   * @return void
   */

  public function create_webform_type($machine_name, $content_name, $description, $settings = [])
  {
    // This need the module entity_reference_revisions and webform enabled:
    $moduleHandler = \Drupal::service('module_handler');
    if (!($moduleHandler->moduleExists('webform'))) {
      Drupal::service('module_installer')->install(['webform']);
    }

    if (!($moduleHandler->moduleExists('webform_ui'))) {
      Drupal::service('module_installer')->install(['webform_ui']);
    }

    $settings += Webform::getDefaultSettings();


    $webformExist = \Drupal::entityTypeManager()->getStorage('webform')->load($machine_name);

    if(!$webformExist){
      // Create a webform.
      $webform = Webform::create([
        'id' => $machine_name,
        'title' => $content_name,
        'description' => $description,
        'elements' => Yaml::encode([]),
        'settings' => [],
      ]);
      $webform->save();
    }
  }


  /**
   * Add a field to a content type
   * @param string          $field_name                     machine name of the field
   * @param string|array    $field_type                     type of field ()
   * @param string          $label                          Human readable name of the field
   * @param string          $description                    Description of the field
   * @param integer         $cardinality                    Unlimited (-1) or Limited (int > 0)
   * @param string          $entity_type                    The type of the entity (paragraph, node, block_content, etc..)
   * @param bool            $required                       Is required or not
   * @param array           $settings_config                Configuration field config settings
   * @param array           $settings_storage               Configuration field storage settings
   * @param string          $defualt_value                  Default value
   *
   * @return void
   */
  public function add_field($field_name, $field_type, $label, $description = '', $cardinality = 1, $entity_type = "node", $required = false, $settings_config = null, $settings_storage = null, $defualt_value = '')
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
        case 'entity':
          $type_storage       = "entity_reference";
          $type_form_display  = "options_select";
          break;
        case 'media':
          // Enable necessary modules
          Drupal::service('module_installer')->install(['media']);
          Drupal::service('module_installer')->install(['media_library']);
          $type_storage       = "entity_reference";
          $type_form_display  = "media_library_widget";
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
    $bundle = $this->machine_name;
    $start = "field_";
    // Check if the field_name start or no with the field word
    if (stripos($field_name, $start) === 0) {
      // Start with the field, do nothing
    } else {
      // Not start with field, concat at beginning
      $field_name = $start . $field_name;
    }

    $this->create_field_storage($entity_type, $field_name, $type_storage, $cardinality, $settings_storage);
    $this->create_field_config($entity_type, $bundle, $field_name, $label, $description, $required, $defualt_value, $settings_config);
    $this->create_field_form_display($entity_type, $bundle, $field_name, $type_form_display);
  }

  /**
   * Get the array to save a file as node nada
   * @param string $file_path
   * @param string $alt_text
   * @param string|null $title_text
   *
   * @return array data
   */
  public static function get_file_image_array($file_path, $ext, $alt_text, $title_text = null)
  {
    if ($title_text == null) {
      $title_text = $alt_text;
    }
    $original_file = file_get_contents($file_path);
    $new_file = file_save_data($original_file, "public://$alt_text.$ext");
    return [
      'target_id' => $new_file->id(),
      'alt'       => $alt_text,
      'title'     => $title_text,
    ];
  }

  public static function save_media_image(string $file_path, string $ext, string $alt_text, string $filename = null)
  {
    if(is_null($filename)) {
      $filename = str_replace(' ', '_', $alt_text . time());
    }
    $media = \Drupal::entityTypeManager()->getStorage('media')->loadByProperties(['name' => $filename]);
    if (!$media) {
      $file_data = file_get_contents($file_path);
      $file = file_save_data($file_data, "public://$filename.$ext");
      $media = Media::create([
        'bundle'            => 'image',
        'uid'               => 0, // Anonymous user
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt'       => $alt_text,
          'title'     => $alt_text,
        ],
      ]);
      $media->setName($filename)->setPublished(TRUE)->save();
    }
    else {
      $media = reset($media);
    }
    return $media->id();
  }

  /**
   * Add Media Type field image related
   * @param string $field_machine_name
   * @param string|array $target_paragraph_machine_name
   * @param string $field_label
   * @param string $field_desc
   * @param integer $cardinality
   * @param boolean $required
   *
   * @return void
   */
  public function add_media_image($field_machine_name, $field_label, $field_desc = '', $cardinality = -1, $required = false, $entity_type = 'node')
  {
    $this->add_field(
      $field_machine_name,
      'media',
      $field_label,
      $field_desc,
      $cardinality,
      $entity_type,
      $required,
      [
        'handler_settings' => [
          'target_bundles' => [
            'image' => 'image'
          ],
        ]
      ],
      ['target_type' => 'media']
    );
  }

  /**
   * Add Paragraph field related
   * @param string $field_machine_name
   * @param string|array $target_paragraph_machine_name
   * @param string $field_label
   * @param string $field_desc
   * @param integer $cardinality
   * @param boolean $required
   *
   * @return void
   */
  public function add_paragraph($field_machine_name, $target_paragraph_machine_name, $field_label, $field_desc = '', $cardinality = -1, $required = false, $entity_type = 'node')
  {

    $target_bundles = [];
    $target_bundles_drag_drop = [];
    if (is_array($target_paragraph_machine_name)) {
      foreach ($target_paragraph_machine_name as $target) {
        $target_bundles[$target] = $target;
        $target_bundles_drag_drop[$target] = ['enabled' => 1];
      }
    } else {
      $target_bundles[$target_paragraph_machine_name] = $target_paragraph_machine_name;
      $target_bundles_drag_drop[$target_paragraph_machine_name] = ['enabled' => 1];
    }
    $this->add_field(
      $field_machine_name,
      'paragraph',
      $field_label,
      $field_desc,
      $cardinality,
      $entity_type,
      $required,
      [
        'handler_settings' => [
          'target_bundles' => $target_bundles,
          'target_bundles_drag_drop' => $target_bundles_drag_drop,
        ]
      ],
      ['target_type' => 'paragraph']
    );
  }

  /**
   * Delete the field
   */
  public function delete_field($field_name, $entity_type = 'node')
  {
    $bundle = $this->machine_name;
    $this->delete_storage_config($entity_type, $field_name);
    $this->delete_field_config($entity_type, $bundle, $field_name);
  }
  /**
   * Delete the Field Storage Config
   * @param string $entity_type
   * @param string $field_name
   */
  public function delete_storage_config($entity_type, $field_name)
  {
    $storage = FieldStorageConfig::loadByName($entity_type, $field_name);
    if ($storage) { // Si existe
      $storage->delete();
    }
  }
  /**
   * Dele the Field Config
   * @param string $entity_type
   * @param string $bundle
   * @param string $field_name
   */
  public function delete_field_config($entity_type, $bundle, $fieldname)
  {
    $field = FieldConfig::loadByName($entity_type, $bundle, $fieldname);
    if ($field) {
      $field->delete();
    }
  }

  public function create_field_form_display($entity_type, $bundle, $field_name, $type_form_display)
  {
    // Manage form display
    $form_display = EntityFormDisplay::load("$entity_type.$bundle.default");
    if (!$form_display) {
      $form_display = EntityFormDisplay::create(['targetEntityType' => $entity_type, 'bundle' => $bundle, 'mode' => 'default', 'status' => TRUE]);
    }
    $form_display->setComponent($field_name, ['type' => $type_form_display]);
    $form_display->save();
  }

  public function create_field_config($entity_type, $bundle, $field_name, $label, $description, $required = false, $defualt_value = '', $settings_config = null)
  {
    $FieldConfig = FieldConfig::load("$entity_type.$bundle.$field_name");
    if (!$FieldConfig) {
      $array_config = [
        'field_name'        => $field_name,
        'entity_type'       => $entity_type,
        'bundle'            => $bundle, // content type
        'label'             => $label,
        'description'       => $description,
        'required'          => $required,
        'default_value'     => $defualt_value,
      ];
      if ($settings_config != null) {
        $array_config['settings'] = $settings_config;
      }
      $FieldConfig = FieldConfig::create($array_config);
      $FieldConfig->save();
    }
    return $FieldConfig;
  }

  public function create_field_storage($entity_type, $field_name, $type_storage, $cardinality = 1, $settings_storage = null)
  {
    $FieldStorageConfig = FieldStorageConfig::load("$entity_type.$field_name");
    if (!$FieldStorageConfig) {
      $arr_storage = array(
        'field_name'    => $field_name,
        'entity_type'   => $entity_type,
        'type'          => $type_storage,
        'cardinality'   => $cardinality,
      );
      if ($settings_storage != null) {
        $arr_storage['settings'] = $settings_storage;
      }
      $FieldStorageConfig = FieldStorageConfig::create($arr_storage);
      $FieldStorageConfig->save();
    }
    return $FieldStorageConfig;
  }

  public static function update_node_data_by_alias($alias, $data)
  {
    $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
    $node = null;
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = \Drupal\node\Entity\Node::load($matches[1]);
    }
    if ($node) {
      foreach ($data as $field_name => $value) {
        if (isset($value['is_node'])) {
          $node->set($field_name, $value['data']);
          continue;
        }/* else if (isset($value['is_paragraph'])) {
          $data_paragraphs = $value['data'];
          $content_paragraph = [];
          foreach ($data_paragraphs as $data) {
            $content_paragraph[] = Paragraph::create($data);
          }
          $array_content[$field_name] = $content_paragraph;
        } else if (isset($value['is_entity'])) {
          $data_ids = $value['data'];
          $content_paragraph = [];
          foreach ($data_ids as $nid) {
            $array_content[$field_name][] = ['target_id' => $nid];
          }
        }*/ else {
          continue;
        }
      }
      $node->save();
      return $node->id();
    }
    return null;
  }

  public static function create_node_data($title, $data, $alias, $target_content_type)
  {
    $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
    $node = null;
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = \Drupal\node\Entity\Node::load($matches[1]);
    }
    if (!$node) {
      $array_content = [
        'type'  => $target_content_type,
        'title' => $title,
        'path' => [
          'alias' => $alias,
        ]
      ];
      foreach ($data as $field_name => $value) {
        if (isset($value['is_node'])) {
          $array_content[$field_name] = $value['data'];
          continue;
        } else if (isset($value['is_paragraph'])) {
          $data_paragraphs = $value['data'];
          $content_paragraph = [];
          foreach ($data_paragraphs as $data_par) {
            if (isset($data_par['is_paragraph'])) { // Tiene otro paragraph dentro (Ejemplo en install de co_club_colombia_gran_colombia)
              $second_field_name = $data_par['is_paragraph'];
              $second_field_data = $data_par['field_data'];
              unset($data_par['is_paragraph']);
              unset($data_par['field_data']);
              foreach ($second_field_data as $second_par_data) {
                $data_par[$second_field_name][] = Paragraph::create($second_par_data);
              }
            }
            $content_paragraph[] = Paragraph::create($data_par);
          }
          $array_content[$field_name] = $content_paragraph;
        } else if (isset($value['is_entity'])) { // (Ejemplo en install de co_club_colombia_gran_colombia)
          $data_ids = $value['data'];
          $content_paragraph = [];
          foreach ($data_ids as $nid) {
            $array_content[$field_name][] = ['target_id' => $nid];
          }
        } else {
          continue;
        }
      }

      $node = Node::create($array_content);
      $node->save();
    }
    return $node->id();
  }

  /**
   * Delete Node Content
   */
  public static function delete_node_data($title, $alias = null)
  {
    //Search by title
    $node = null;
    if ($title) {
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['title' => $title]);
      foreach ($nodes as $nd) {
        $node = $nd;
        break;
      }
    } else if ($alias) {
      $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
    }
    if ($node) {
      $node->delete();
    }
  }


  /**
   * Create menu if not exist
   */
  public static function create_menu($id, $label, $description)
  {
    if (!Menu::load($id)) {
      return Menu::create([ // create the menu
        'id' => $id,
        'label' => $label,
        'description' => $description,
      ])->save();
    }
    return false;
  }


  /**
   * Create menu item if not exist, update or delete.
   * @param array $items
   *  [
   *    '<TITULO>' => [
   *      'title' => '<TITULO>',
   *      'link' => ['uri' => '<URL>'],
   *      'menu_name' => <MACHINE_NAME_MENU_PARENT>,
   *      'expanded' => FALSE,
   *      'weight' => <WEIGHT>,
   *    ],
   *  ]
   *
   */
  public static function create_menu_item($items)
  {
    foreach ($items as $old_title => $item) {
      $delete = isset($item['delete']);
      $menu_link_content = \Drupal::entityTypeManager()->getStorage('menu_link_content');
      $link = $menu_link_content->loadByProperties(['title' => $old_title]);
      if ((!$link || empty($link)) && isset($item['enabled'])) { // If enter here, then is to disabled
        $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
        $link = $menu_link_manager->getDefinition($old_title);
        $link['enabled'] = $item['enabled'];
        $menu_link_manager->updateDefinition($old_title, $link);
        continue;
      }
      if ($link) {
        $menu_item = reset($link);
        if ($delete) {
          $menu_item->delete();
        } else {
          foreach ($item as $attr => $value) {
            $menu_item->{$attr} = $value;
          }
          $menu_item->save();
        }
      } else { //The item not exist by title
        if (!$delete) {
          // si no esta marcado apra eliminar, se crea
          MenuLinkContent::create($item)->save();
        }
      }
    }
  }
  /**
   * Load the tree of specific menu and convert to sort array by weight (Load the sub items aka childrens too)
   */
  public static function get_items_menu($name)
  {
    $tree = \Drupal::menuTree()->load($name, new \Drupal\Core\Menu\MenuTreeParameters());
    $tree_array = self::loadMenu($tree);
    //Sort the array by key. Remember the index is "weight|ID" and only wee need to sort by weight
    uksort($tree_array, function ($a, $b) {
      $a = explode("|", $a)[0];
      $b = explode("|", $b)[0];
      return $a <=> $b;
    });
    return $tree_array;
  }

  /**
   * Load the structure of the tree in array.
   * If have children, call the same function by recursion
   */
  public static function loadMenu($tree)
  {

    $menu = [];
    foreach ($tree as $key => $items) {
      if ($items->link->isEnabled()) {

        $menu[$items->link->getWeight() . "|$key"] = [
          'title' => $items->link->getTitle(),
          'url' => $items->link->getUrlObject()->toString(),
          'description' => $items->link->getDescription(),
          'has_children' => $items->hasChildren,
          'children' => self::loadMenu($items->subtree),
          'target' => ($items->link->getUrlObject()->isExternal() ? '_blank' : '_self'),
        ];
      }
    }
    return $menu;
  }

  /**
   * Convert a string from ['UTF-8', 'ISO-8859-1', 'ISO-8859-2'] to 'UTF-8'
   */
  public static function convertToUTF8($string)
  {
    $encode  = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'ISO-8859-2'], true);
    $new_str = mb_convert_encoding($string, 'UTF-8', $encode);
    return $new_str;
  }
}
