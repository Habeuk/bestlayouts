<?php

namespace Drupal\bestlayouts\Plugin\Field\FieldFormatter;

use Drupal\more_fields\Plugin\Field\FieldFormatter\MoreFieldsMenuFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'entity reference label' formatter.
 * NB : on travaillera sur le rendu bien plus tard.
 *
 * @FieldFormatter(
 *   id = "bestlayouts_megamenu_cover",
 *   label = @Translation("MegaMenu display"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MegaMenuscover extends MoreFieldsMenuFormatter {
  
  public static function defaultSettings() {
    return [
      'theme_render' => 'layoutmenu_bestlayouts_dynamiques_headers',
      'text_display' => true
    ] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      'theme_render' => [
        '#type' => 'select',
        '#title' => $this->t('Select render model'),
        '#default_value' => $this->getSetting('theme_render'),
        '#required' => true,
        '#options' => [
          'layoutmenu_bestlayouts_dynamiques_headers' => 'Rendu avec les sous menu',
          'bestlayouts_megamenu' => 'Rendu MegaMenu'
        ]
      ]
    ] + parent::settingsForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $theme_render = $this->getSetting('theme_render');
    foreach ($elements as &$element) {
      $element["#theme"] = $theme_render;
      $this->formatListMenus($element['#items']);
    }
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  private function formatListMenus(array &$items, $firstLevel = true) {
    $routeName = \Drupal::routeMatch()->getRouteName();
    foreach ($items as $k => $item) {
      /**
       *
       * @var \Drupal\Core\Url $url
       */
      $url = $item['url'];
      /**
       * On met dans un cache, car dans certaines conditions( quand on vient de
       * supprimer un lien) Cela declenche : UnexpectedValueException
       */
      try {
        $menuRoute = $url->getRouteName();
      }
      catch (\Exception $e) {
        $menuRoute = null;
      }
      
      if (!empty($item['attributes'])) {
        /**
         *
         * @var \Drupal\Core\Template\Attribute $attribute
         */
        $attribute = $item['attributes'];
        $attribute->addClass('item');
        // add sub menu
        if ($item['is_expanded']) {
          $attribute->addClass('sub-alt');
        }
        // menu actif
        if ($item['in_active_trail']) {
          $attribute->addClass('is-active');
        }
        /**
         * On essaie d'identifier la home page, car in_active_trail ne
         * fonctionne pas dessus.
         */
        if ($routeName == 'view.frontpage.page_1' && ($menuRoute == '<front>' || $menuRoute == '/')) {
          $attribute->addClass('is-active');
        }
        $items[$k]['attributes'] = $attribute;
        //
        if (!empty($item['below'])) {
          $this->formatListMenus($item['below'], false);
          $items[$k]['below'] = $item['below'];
        }
      }
    }
  }
}