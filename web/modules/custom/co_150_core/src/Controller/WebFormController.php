<?php

namespace Drupal\co_150_core\Controller;
use Drupal\webform\Entity\Webform;
use Drupal\Core\Serialization\Yaml;


class WebFormController extends ContentController
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
    $this->machine_name = $machine_name;
    parent::__construct($machine_name, $content_title, $content_description, 'webform');
  }

  /**
   * Add a field to a WebForm content
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
  public function add_w_field($elements)
  {
    $webform = Webform::load($this->machine_name);
    // $elementsWebForm = Yaml::decode($webform->get('elements'));
    // $elements = array_merge($elementsWebForm, $elements);
    // print(json_encode($elements));die;

    $webform->set('elements', Yaml::encode($elements));
    $webform->save();
  }
}
