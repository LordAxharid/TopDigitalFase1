<?php

namespace Drupal\td_users\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\co_150_core\Controller\DecodeController;


/**
 * Configure example settings for this site.
 */
class ConfigCitiesForm extends ConfigFormBase
{
  /** @var string Config settings */
  const SETTINGS = 'td_users_cities.config';

  /**
   * {@inheritdoc}
   */

  public function getFormId()
  {
    return 'td_users_cities';
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

    $form['cid'] = [
      '#type' => 'textfield',
      '#title' => 'CID',
      '#required' => TRUE,
      '#default_value' => $config->get('cid'),
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Nombre',
      '#required' => TRUE,
      '#default_value' => $config->get('name'),
    ];
	  
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    parent::submitForm($form, $form_state);

      $cid  = $form_state->getValue('cid');
      $name    = $form_state->getValue('name');

    $this->config('adminconfig.adminsettings')
      ->set('cid', $cid)
      ->set('name', $name)
      ->save();

    parent::submitForm($form, $form_state);

    // save new country
    $requestSaveData = $this->save_country($cid, $name);
  }

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(){
    $this->table = 'td_users_cities';
    $this->db = \Drupal::service('database');
    $this->user = \Drupal::currentUser();
  }

  /**
   * save_country
   *
   * @return void
   */
  public function save_country ($cid, $name){
    
    $this->db->insert($this->table)
        ->fields(array('cid' => $cid, 'name' => $name ))
        ->execute();
    
  }

} 
