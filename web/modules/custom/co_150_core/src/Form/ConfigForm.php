<?php

namespace Drupal\co_150_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class ConfigForm extends ConfigFormBase
{
  /** @var string Config settings */
  const SETTINGS = 'adminconfig.adminsettings';


  public function __construct(){
    $this->s3 = ['KeyId', 'SecretKey', 'Region', 'Bucket', 'Dir'];
  }
  /**
   * {@inheritdoc}
   */

  public function getFormId()
  {
    return 'paradise_settings';
  }

  /**
   * {@inheritdoc}
   */

  protected function getEditableConfigNames()
  {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config(static::SETTINGS);

    /**
     * CONFIG S3
     */
    foreach ($this->s3 as $key => $value) {
      $form['s3_'.$value] = [
        '#type' => 'textfield',
        '#title' => $value,
        '#required' => FALSE,
        '#default_value' => $config->get('s3_'.$value),
      ];
    }
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    foreach ($this->s3 as $key => $value) {
      $this->configFactory->getEditable(static::SETTINGS)->set('s3_'.$value, $form_state->getValue('s3_'.$value))->save();
    }
    parent::submitForm($form, $form_state);
  }
}
