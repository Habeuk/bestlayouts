<?php

namespace Drupal\bestlayouts\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for bestlayouts routes.
 */
class BestlayoutsController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
