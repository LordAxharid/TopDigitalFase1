<?php

namespace Drupal\td_users\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class RegisterController extends ControllerBase{  

 public function registerForm(){

        $entity = \Drupal::entityTypeManager()->getStorage('user')->create(array());
        $formObject = \Drupal::entityTypeManager()
        ->getFormObject('user', 'register')
        ->setEntity($entity);
        $form = \Drupal::formBuilder()->getForm($formObject);
        return $form;
          
    }
}
