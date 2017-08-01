<?php

namespace Drupal\batch_add_terms\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class BatchTermController.
 *
 * @package Drupal\batch_add_terms\Controller
 */
class BatchTermController extends ControllerBase {
  public function batchAddTerms($taxonomy_vocabulary) {
    return \Drupal::formBuilder()->getForm('Drupal\batch_add_terms\Form\BatchAddTermForm');
  }
}
