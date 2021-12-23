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
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\co_150_core\Controller\WebFormController;

class TestController
{
    public function webformExample(){
        $webform = new WebFormController('testregister', 'testregister', 'Basic register test', );
        // $webform->add_field('title', 'string', 'Titulo', 'Titulo de la pregunta', 1, 'paragraph');

        // add a field
        $form['first_name'] = [
            '#minlength' => 2,
            '#type' => 'textfield',
            '#placeholder' => 'Nombre',
            '#attributes' => array(
              'placeholder' => 'Nombre',
              'onkeypress' => "return /[a-zA-ZñÑ]/i.test(event.key)",//"return /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]*$/.test(event.key)",
            ),
            '#size' => 60,
            '#maxlength' => 20,
            '#required' => TRUE,
          ];
      
          $form['last_name'] = [
            '#minlength' => 2,
            '#type' => 'textfield',
            '#placeholder' => 'Apellido',
            '#attributes' => [
              'placeholder' => 'Apellido',
              'onkeypress' => "return /[a-zA-ZñÑ]/i.test(event.key)",
            ],
            '#size' => 60,
            '#maxlength' => 20,
            '#required' => TRUE,
          ];
      
          $form['email'] = [
            '#type' => 'email',
            '#placeholder' => 'Ingresa tu correo electrónico',
            '#required' => TRUE,
          ];
      
          $form['phone'] = [
            '#type' => 'textfield',
            '#title' => 'Telefono',
            '#placeholder' => 'Ingresa tu número de celular',
            '#attributes' => array(
              'onkeypress' => "return /[0-9]/i.test(event.key)",
          ),
            '#required' => TRUE,
            '#maxlength' => 10,
            '#minlength' => 7,
          ];

        $webform->add_w_field($form);

        print(json_encode('I did it'));die;
        // var_dump($webform);die;
    }
}