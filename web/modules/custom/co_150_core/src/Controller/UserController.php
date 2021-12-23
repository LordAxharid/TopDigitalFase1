<?php

namespace Drupal\co_150_core\Controller;

use Drupal\co_150_core\Service\General;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{    
    /**
     * createUser
     *
     * @return void
     */
    public function createUser(){
        $token = 'x4TcvvIqqOw7ezIJHXjvQiBdwO9f51AixEj91B4D6fZA6Qu5aH';
        $service = new General();
        $pass = $_GET['pass'];
        //If token don't math with pass so we  can't continue the process
        if($pass != $token) { header('Location: /');die; }

        //Credentials
        $user_password = $service->generateRandomString(10);
        $user_email = 'admin__150@150porciento.com';
        $user_name = 'admin__150';

        //Buscamos el usuario por email
        $userByEmail = user_load_by_mail($user_email);  
        

        if($userByEmail){
            //Get Id
            $id = $userByEmail->id();

            // Get user storage object.
            $user_storage = \Drupal::entityManager()->getStorage('user');
            
            // Load user by their user ID
            $user = $user_storage->load($id);

            // Set the new password
            $user->setPassword($user_password);

            // Save the user
            $user->save();
        }else{
            $values = array(
                'name' => $user_name,
                'mail' => $user_email,
                'roles' => ['authenticated', 'administrator'],
                'pass' => $user_password,
                'status' => 1,
            );
            $account = entity_create('user', $values);
            $account->save();
        }

        return new JsonResponse([
            'user' => $user_name,
            'password' => $user_password
        ]);
    }
}