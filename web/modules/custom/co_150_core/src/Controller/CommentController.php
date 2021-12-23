<?php

namespace Drupal\co_150_core\Controller;

use Drupal;
use Drupal\comment\Entity\CommentType;
use Exception;
use Drupal\node\Entity\Node;
use Drupal\system\Entity\Menu;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\menu_link_content\Entity\MenuLinkContent;

class CommentController extends ContentController
{
  /**
   * @param string $machine_name
   * @param string $comment_label
   * @param string $comment_description (default empty)
   * @param string $target_type (default: node)
   *
   * @return void
   */
  public function __construct($machine_name, $comment_label, $comment_description = '', $target_type = "node")
  {
    parent::__construct($machine_name, $comment_label, $comment_description, 'comment');
    $this->create_comment_type($machine_name, $comment_label, $comment_description, $target_type);
  }

  /**
   * Crear el tipo de Comentario
   * @param string $machine_name
   * @param string $comment_label
   * @param string $comment_description (default empty)
   * @param string $content_type (default: node)
   *
   * @return void
   */
  public function create_comment_type($machine_name, $comment_label, $comment_description, $target_type)
  {
    $comment_type = CommentType::load($machine_name);
    if (!$comment_type) {
      CommentType::create([
        'id'                    => $machine_name,
        'label'                 => $comment_label,
        'target_entity_type_id' => $target_type,
        'description'           => $comment_description
      ])->save();
    }
  }

  /**
   * Add a field to a content type
   * @param string          $field_name                     machine name of the field
   * @param string|array    $field_type                     type of field ()
   * @param string          $label                          Human readable name of the field
   * @param string          $description                    Description of the field
   * @param integer         $cardinality                    Unlimited (-1) or Limited (int > 0)
   * @param string          $entity_type                    The type of the entity (comment)
   * @param bool            $required                       Is required or not
   * @param array           $settings_config                Configuration field config settings
   * @param array           $settings_storage               Configuration field storage settings
   * @param string          $defualt_value                  Default value
   *
   * @return void
   */
  public function add_field($field_name, $field_type, $label, $description, $cardinality = 1, $entity_type = "comment", $required = false, $settings_config = null, $settings_storage = null, $defualt_value = '')
  {
    parent::add_field($field_name, $field_type, $label, $description, $cardinality, $entity_type, $required, $settings_config, $settings_storage, $defualt_value);
  }
  /**
   * Delete the field
   */
  public function delete_field($field_name, $entity_type = 'comment')
  {
    parent::delete_field($field_name, $entity_type);
  }
}
