<?php

declare(strict_types=1);

namespace Drupal\bestlayouts;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a megamenus entity type.
 */
interface MegaMenusInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
