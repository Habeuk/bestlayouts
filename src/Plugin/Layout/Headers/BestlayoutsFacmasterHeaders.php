<?php

namespace Drupal\bestlayouts\Plugin\Layout\Headers;

use Drupal\bootstrap_styles\StylesGroup\StylesGroupManager;
use Drupal\formatage_models\FormatageModelsThemes;
use Drupal\formatage_models\Plugin\Layout\FormatageModels;
use Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection;
use Drupal\Core\Form\FormStateInterface;

/**
 * A contact section layout from bestlayouts
 *
 * @Layout(
 *   id = "bestlayouts_facmaster_headers",
 *   label = @Translation(" Bestlayous : header"),
 *   category = @Translation("bestlayouts"),
 *   path = "layouts/headers",
 *   template = "bestlayouts_facmaster_headers",
 *   library = "bestlayouts/bestlayouts_facmaster_headers",
 *   default_region = "header_top_left",
 *   regions = {
 *     "header_top_left" = {
 *       "label" = @Translation("header_top_left"),
 *     },
 *     "header_langdown" = {
 *       "label" = @Translation("header_langdown"),
 *     },
 *     "description" = {
 *       "label" = @Translation("description"),
 *     },
 *     "buttons_icone" = {
 *       "label" = @Translation("buttons_icone"),
 *     },
 *     "buttons_icone" = {
 *       "label" = @Translation("buttons_icone"),
 *     },
 *     "site_branding" = {
 *       "label" = @Translation("site_branding"),
 *     },
 *     "fields_info" = {
 *       "label" = @Translation("fields_info"),
 *     },
 *     "menu" = {
 *       "label" = @Translation("menu"),
 *     },
 *     "call_to_action" = {
 *       "label" = @Translation("call_to_action"),
 *     },
 *     "search" = {
 *       "label" = @Translation("search"),
 *     },
 *     "cart" = {
 *       "label" = @Translation("cart"),
 *     },
 *   }
 * )
 */
class BestlayoutsFacmasterHeaders extends FormatageModelsSection {
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\FormatageModels::__construct()
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StylesGroupManager $styles_group_manager) {
    // TODO Auto-generated method stub
    parent::__construct($configuration, $plugin_id, $plugin_definition, $styles_group_manager);
    $this->pluginDefinition->set('icon', drupal_get_path('module', 'bestlayouts') . "/icones/headers/bestlayouts_facmaster_headers.png");
  }
  
  function defaultConfiguration() {
    return [
      'load_libray' => true,
      'class_header_top_left' => 'col-md-5 d-none d-lg-block'
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection::buildConfigurationForm()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['class_header_top_left'] = [
      '#type' => 'textfield',
      '#title' => $this->t('class_header_top_left'),
      '#default_value' => $this->configuration['class_header_top_left']
    ];
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection::submitConfigurationForm()
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['class_header_top_left'] = $form_state->getValue('class_header_top_left');
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\FormatageModels::build()
   */
  public function build(array $regions) {
    // TODO Auto-generated method stub
    $build = parent::build($regions);
    FormatageModelsThemes::formatSettingValues($build);
    if (is_array($build['menu'])) {
      $build['menu'] = $this->getMenus($build['menu']);
    }
    
    return $build;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  private function getMenus(array $menu_nav) {
    foreach ($menu_nav as $k => $m) {
      if (isset($m['#base_plugin_id']) && $m['#base_plugin_id'] === 'system_menu_block' && !$m['#in_preview']) {
        // set new theme.
        $menu_nav[$k]['content']['#theme'] = 'layoutmenu_bestlayouts_first_menu';
        // add class
        $menu_nav[$k]['content']['#attributes'] = [
          'class' => [
            'nav-list'
          ]
        ];
        // format-it if is not empty
        if (!empty($menu_nav[$k]['content']['#items'])) {
          $this->formatListMenus($menu_nav[$k]['content']['#items']);
          // dump($menu_nav[$k]['content']['#items']);
        }
      }
    }
    return $menu_nav;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  private function formatListMenus(array &$items) {
    foreach ($items as $k => $item) {
      if (!empty($item['attributes'])) {
        /**
         *
         * @var \Drupal\Core\Template\Attribute $attribute
         */
        $attribute = $item['attributes'];
        $attribute->addClass('nav-item');
        // add sub menu
        if ($item['is_expanded']) {
          $attribute->addClass('sub-alt');
        }
        // menu actif
        if ($item['in_active_trail']) {
          $attribute->addClass('nav-item--active');
        }
        $items[$k]['attributes'] = $attribute;
        //
        if (!empty($item['below'])) {
          $this->formatListMenus($item['below']);
          $items[$k]['below'] = $item['below'];
        }
      }
    }
  }
  
}