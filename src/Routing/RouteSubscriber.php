<?php

namespace Drupal\batch_add_terms\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\batch_add_terms\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Altering the access permissions as per vppr or taxonomy_access_fix.
    $moduleHandler = \Drupal::service('module_handler');
    // If vppr module enabled.
    if ($moduleHandler->moduleExists('vppr')){
      if ($route = $collection->get('batch_add_terms.mass_add')) {
        $route->setRequirements(array(
          '_custom_access' => '\vppr_route_access',
        ));
      }
      $route->setOption('op', '');
    }
    
    // If taxonomy_access_fix module enabled.
    if ($moduleHandler->moduleExists('taxonomy_access_fix')) {
      
      if ($route = $collection->get('batch_add_terms.mass_add')) {
        $route->setRequirements(array(
          '_custom_access' => '\taxonomy_access_fix_route_access',
        ));
        $route->setOption('op', 'add terms');
      }
    }
  }
}
