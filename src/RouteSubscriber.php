<?php

namespace Drupal\saml_idp;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Alters routes for the IdP single logout feature.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The route collection for adding routes.
   */
  protected function alterRoutes(RouteCollection $collection) {
    // @todo - Make this configurable.
    if ($route = $collection->get('user.logout')) {
      $route->setPath('/saml_idp/logout');
    }
  }

}
