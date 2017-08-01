<?php

namespace Drupal\batch_add_terms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Displays Batch Add Terms Form.
 */
class BatchAddTermForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'batch_add_term_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['terms'] = array(
      '#type' => 'textarea',
      '#title' => t('Terms'),
      '#description' => t('Enter one term name per line.'),
      '#required' => TRUE,
      '#rows' => 15,
    );

    $form['check_duplicates'] = array(
      '#type' => 'checkbox',
      '#title' => t('Check for duplicates'),
      '#description' => t('Enable this option if you do not want to add existing terms.'),
    );

    $form['actions'] = array(
      '#type' => '#actions',
    );

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $route_match = \Drupal::service('current_route_match');
    $vocabulary_id = $route_match->getParameter('taxonomy_vocabulary');
    
    $terms_names = trim($form_state->getValue('terms'));
    $check_duplicates = trim($form_state->getValue('check_duplicates'));

    $terms_names_array = explode("\n", $terms_names);
    $parent_terms = array();
    $weight = 0;
    foreach ($terms_names_array as $key => $term_name) {
      $term_depth = 0;
      if (preg_match('#^(-+)(.+)#', $term_name, $matches)) {
        $term_depth = strlen($matches[1]);
        $term_name = $matches[2];
      }
      $term_name = trim($term_name);
      if ($check_duplicates && taxonomy_term_load_multiple_by_name($term_name, $vocabulary_id)) {
        continue;
      }
      $term = \Drupal\taxonomy\Entity\Term::create(array(
            'parent' => $term_depth ? array($parent_terms[$term_depth - 1]) : array(),
            'name' => $term_name,
            'vid' => $vocabulary_id,
            'weight' => $weight++,
          ));
      $term->save();
      $parent_terms[$term_depth] = $term->id();
    }
    drupal_set_message(t('Added @count new terms', array('@count' => count($terms_names_array))));
    // Redirect to the term list page.
    $form_state->setRedirect('entity.taxonomy_vocabulary.overview_form', ['taxonomy_vocabulary' => $vocabulary_id]);
    return;
  }
}

