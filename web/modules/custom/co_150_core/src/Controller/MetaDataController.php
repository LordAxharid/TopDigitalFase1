<?php

/**
 * @file
 * @author David Martinez
 * Contains \Drupal\vuedrupal\Controller\VueController.
 * Please place this file under your example(module_root_folder)/src/Controller/
 * Use https://www.drupal.org/docs/8/api/database-api/dynamic-queries/introduction-to-dynamic-queries  para consultes y actualización de data
 */

namespace Drupal\co_150_core\Controller;

/**
 * Provides route responses for the Example module.
 */
class MetaDataController
{

  /**
   * __construct
   *
   * @return void
   */
  function __construct()
  {
    $this->host = \Drupal::request()->getSchemeAndHttpHost();
  }
  /**
   * metadata, los datos se pasan por referencia a $variables['page']['#attached']['html_head']
   *
   * @param  mixed $variables
   * @return void
   */
  public function metadata($title, $description, $photo, &$variables)
  {
    $description = strip_tags($description);
    //data con la imagen, titulo y description
    $this->facebook($title, $description, $photo, $variables);
    $this->twitter($title, $description, $photo, $variables);
  }

  /**
   * facebook
   *
   * @param  mixed $data
   * @param  mixed $variables
   * @return void
   */
  function facebook($title, $description, $photo, &$variables, $width = 1200, $height = 628)
  {
    $metadatas = [
      'og:type'         => 'article',
      //'og:url'          => $this->host,
      'og:title'        => $title,
      'og:description'  => $description,
      'og:image'        => $photo,
      'og:image:width'  => $width,
      'og:image:height' => $height
    ];

    foreach ($metadatas as $key => $value) {
      $xuacompatible = [
        '#tag' => 'meta',
        '#attributes' => [
          'property' => $key,
          'content' => $value,
        ],
      ];
      $variables['page']['#attached']['html_head'][] = [$xuacompatible, 'meta_facebook_' . $key];
    }
  }

  /**
   * twitter
   *
   * @param  mixed $data
   * @param  mixed $variables
   * @return void
   */
  function twitter($title, $description, $photo, &$variables)
  {
    $metadatas = [
      'twitter:card'        => 'summary',
      //'twitter:url'         => $this->host,
      'twitter:title'       => $title,
      'twitter:description' => $description,
      'twitter:image'       => $photo
    ];

    foreach ($metadatas as $key => $value) {
      $xuacompatible = [
        '#tag' => 'meta',
        '#attributes' => [
          'name' => $key,
          'content' => $value,
        ],
      ];
      $variables['page']['#attached']['html_head'][] = [$xuacompatible, 'meta_twitter_' . $key];
    }
  }
}
