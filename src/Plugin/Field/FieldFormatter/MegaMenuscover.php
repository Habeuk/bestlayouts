<?php

namespace Drupal\bestlayouts\Plugin\Field\FieldFormatter;

use Drupal\more_fields\Plugin\Field\FieldFormatter\MoreFieldsMenuFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'entity reference label' formatter.
 * NB : on travaillera sur le rendu bien plus tard.
 *
 * @FieldFormatter(
 *   id = "bestlayouts_megamenu_cover",
 *   label = @Translation("MegaMenu display cover"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MegaMenuscover extends MoreFieldsMenuFormatter {
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as &$element) {
      $element["#theme"] = "layoutmenu_bestlayouts_dynamiques_headers";
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