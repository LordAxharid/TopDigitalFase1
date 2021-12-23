<?php

namespace Drupal\co_150_core\Controller;

class BlockContentController extends ContentController
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
  public function __construct($machine_name, $content_title, $content_description)
  {
    parent::__construct($machine_name, $content_title, $content_description, 'block_content');
  }

  /**
   * Add a field to a paragraph content
   * @param string          $field_name                     machine name of the field
   * @param string|array    $field_type                     type of field ()
   * @param string          $label                          Human readable name of the field
   * @param string          $description                    Description of the field
   * @param integer         $cardinality                    Unlimited (-1) or Limited (int > 0)
   * @param bool            $required                       Is required or not
   * @param array           $settings_config                Configuration field config settings
   * @param array           $settings_storage               Configuration field storage settings
   * @param string          $defualt_value                  Default value
   *
   * @return void
   */
  public function add_b_field($field_name, $field_type, $label, $description = '', $cardinality = 1, $required = false, $settings_config = null, $settings_storage = null, $defualt_value = '')
  {
    parent::add_field($field_name, $field_type, $label, $description, $cardinality, 'block_content', $required, $settings_config, $settings_storage, $defualt_value);
  }
}
