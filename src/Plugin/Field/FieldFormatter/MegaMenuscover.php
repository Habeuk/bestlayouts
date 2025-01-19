<?php

namespace Drupal\bestlayouts\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'entity reference label' formatter.
 * NB : on travaillera sur le rendu bien plus tard.
 *
 * @FieldFormatter(
 *   id = "bestlayouts_megamenu_cover",
 *   label = @Translation("MegaMenu display Cover"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MegaMenuscover extends MegaMenusSubMenu {
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [
      'theme_render' => [
        '#type' => 'hidden',
        '#default_value' => 'bestlayouts_megamenu'
      ],
      'layoutgenentitystyles_view' => [
        '#type' => 'hidden',
        '#default_value' => 'bestlayouts/bestlayouts_megamenu_cover'
      ]
    ] + parent::settingsForm($form, $form_state);
    return $elements;
  }
}