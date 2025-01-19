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
 *   id = "bestlayouts_megamenu_sub_menu",
 *   label = @Translation("MegaMenu display with sub menus"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MegaMenusSubMenu extends MoreFieldsMenuFormatter {
  
  public static function defaultSettings() {
    return [
      'theme_render' => 'layoutmenu_bestlayouts_dynamiques_headers',
      'text_display' => true,
      'layoutgenentitystyles_view' => 'bestlayouts/bestlayouts_megamenu_submenu'
    ] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [
      'theme_render' => [
        '#type' => 'hidden',
        '#default_value' => 'layoutmenu_bestlayouts_dynamiques_headers'
      ],
      'layoutgenentitystyles_view' => [
        '#type' => 'hidden',
        '#default_value' => 'bestlayouts/bestlayouts_megamenu_submenu'
      ]
    ] + parent::settingsForm($form, $form_state);
    return $elements;
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
      $this->formatListMenus($element['#items'], $langcode);
    }
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  private function formatListMenus(array &$items, $langcode, $firstLevel = true) {
    $routeName = \Drupal::routeMatch()->getRouteName();
    foreach ($items as $k => $item) {
      // On ajoute les rendu du layout s'il existe.
      if ($this->getSetting('theme_render') == 'bestlayouts_megamenu') {
        /**
         *
         * @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $original_link
         */
        $original_link = $item['original_link'];
        if ($original_link->getEntity()->hasField('btly_megamenus_layout')) {
          $ids = [];
          foreach ($original_link->getEntity()->get('btly_megamenus_layout')->getValue() as $value) {
            $ids[] = $value['target_id'];
          }
          if ($ids) {
            $items[$k]['btly_megamenus_layout'] = $this->loadBtlyMegamenus($ids, $langcode);
            $items[$k]['attributes']->addClass('layout');
          }
        }
      }
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
          $this->formatListMenus($item['below'], $langcode, false);
          $items[$k]['below'] = $item['below'];
        }
      }
    }
  }
  
  private function loadBtlyMegamenus(array $ids, $langcode) {
    $renders = [];
    foreach (\Drupal\bestlayouts\Entity\MegaMenus::loadMultiple($ids) as $entity) {
      $renders[] = \Drupal::entityTypeManager()->getViewBuilder('btly_megamenus')->view($entity, 'full', $langcode);
    }
    return $renders;
  }
}