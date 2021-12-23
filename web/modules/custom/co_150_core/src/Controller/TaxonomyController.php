<?php

namespace Drupal\co_150_core\Controller;

use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;


class TaxonomyController
{
  private $vid; // machine_name

  /**
   * If the $vid not exist, then create
   * @param string $vid as machine_name
   * @param string $vocabulary as name
   * @param string $description as description
   */
  public function __construct($vid, $vocabulary, $description = '')
  {
    $this->vid = $vid;
    $exist = Vocabulary::load($vid);
    if (!$exist) { // If not exist, create it
      Vocabulary::create([
        'vid'         => $this->vid,
        'description' => $description,
        'name'        => $vocabulary,
      ])->save();
    }
  }
  /**
   * Agregar un termino al vocabulario
   * @param string $name
   */
  public function add_term($name)
  {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $name, 'vid' => $this->vid]);
    $term = reset($term);
    if (!$term) { // If the termn not exist, create
      Term::create([
        'name' => $name,
        'vid' => $this->vid,
      ])->save();
    }
  }
  /**
   * Agregar varios campos a la vez al vocabulario
   * @param array $names
   */
  public function add_terms($names)
  {
    foreach ($names as $name) {
      $this->add_term($name);
    }
  }

  public function delete_terms() {
    $cities_term = self::getTaxonomyTermsSortedByWeight('city');
    foreach ($cities_term as $key => $value) {
      $value->delete();
    }
  }

  public function getVid()
  {
    return $this->vid;
  }

  /**
   * Get taxonomy terms sorted by weight.
   *
   * @param int $vid
   *   The vocabulary id.
   *
   * @return array
   *   Returns an array of term id | name.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTaxonomyTermsSortedByWeight($vid)
  {

    // Initialize the items.
    $items = [];

    // Get the term storage.
    $entity_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

    // Query the terms sorted by weight.
    $query_result = $entity_storage->getQuery()
      ->condition('vid', $vid)
      ->sort('weight', 'ASC')
      ->execute();

    // Load the terms.
    $terms = $entity_storage->loadMultiple($query_result);

    foreach ($terms as $term) {
      $items[$term->id()] = $term;
    }
    // Return the items.
    return $items;
  }
}
