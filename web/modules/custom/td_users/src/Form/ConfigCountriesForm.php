<?php

namespace Drupal\td_users\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\co_150_core\Controller\DecodeController;


/**
 * Configure example settings for this site.
 */
class ConfigCountriesForm extends ConfigFormBase
{
  /** @var string Config settings */
  const SETTINGS = 'td_users_countries.config';

  /**
   * {@inheritdoc}
   */

  public function getFormId()
  {
    return 'td_users_countries';
  }

  /**
   * {@inheritdoc}
   */

  public function getEditableConfigNames()
  {
    return [
      'adminconfig.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('adminconfig.adminsettings');

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Nombre',
      '#required' => TRUE,
      '#default_value' => $config->get('name'),
    ];

    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => 'Prefix',
      '#required' => TRUE,
      '#default_value' => $config->get('prefix'),
    ];
	
	  $form['iso'] = [
		  '#type' => 'textfield',
		  '#title' => t('ISO'),
		  '#required' => TRUE,
		  '#default_value' => $config->get('iso'),
	  ];
	  
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    parent::submitForm($form, $form_state);

      $name  = $form_state->getValue('name');
      $prefix    = $form_state->getValue('prefix');
      $iso = $form_state->getValue('iso');

    $this->config('adminconfig.adminsettings')
      ->set('name', $name)
      ->set('prefix', $prefix)
	    ->set('iso', $iso)
      ->save();

    parent::submitForm($form, $form_state);

    // save new country
    $requestSaveData = $this->save_country($name, $prefix, $iso);
  }

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(){
    $this->table = 'td_users_countries';
    $this->db = \Drupal::service('database');
    $this->user = \Drupal::currentUser();
  }

  /**
   * save_country
   *
   * @return void
   */
  public function save_country ($name, $iso ,$prefix){
    
        $id = \Drupal::database()->select($this->table, 'c')
        ->fields('c', ['id'])
        ->condition('c.name', $name)
        ->condition('c.iso', $iso)
        ->condition('c.prefix', $prefix)
        ->execute()
        ->fetchField();

        if (!$id) {

          $query = $this->db->insert($this->table)
          ->fields([
          'name' => $name, 
          'iso' => $iso,
          'prefix' => $prefix
          ])
          ->execute();

        }else {
          
          

        }
    
  }

}
