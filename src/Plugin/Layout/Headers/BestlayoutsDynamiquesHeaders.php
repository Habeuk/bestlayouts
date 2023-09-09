<?php

namespace Drupal\bestlayouts\Plugin\Layout\Headers;

use Drupal\bootstrap_styles\StylesGroup\StylesGroupManager;
use Drupal\formatage_models\FormatageModelsThemes;
use Drupal\formatage_models\Plugin\Layout\FormatageModels;
use Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection;
use Drupal\Core\Form\FormStateInterface;

/**
 * Ce menu a pour objectif d'etre le plus dynamique possible.
 * il permet de gerer entirement l'entete d'un site.
 *
 * @Layout(
 *   id = "bestlayouts_dynamiques_headers",
 *   label = @Translation(" Bestlayous : header"),
 *   category = @Translation("bestlayouts"),
 *   path = "layouts/headers",
 *   template = "bestlayouts_dynamiques_headers",
 *   library = "bestlayouts/bestlayouts_dynamiques_headers",
 *   default_region = "header_top_left",
 *   regions = {
 *     "header_top_left" = {
 *       "label" = @Translation("header top left"),
 *     },
 *     "header_top_center" = {
 *       "label" = @Translation("header_top_center"),
 *     },
 *     "header_top_right" = {
 *       "label" = @Translation("header_top_right"),
 *     },
 *     "logo" = {
 *       "label" = @Translation("Logo"),
 *     },
 *     "menu" = {
 *       "label" = @Translation("menu"),
 *     },
 *     "right_menu" = {
 *       "label" = @Translation("right_menu"),
 *     },
 *     "search" = {
 *       "label" = @Translation("search"),
 *     },
 *   }
 * )
 */
class BestlayoutsDynamiquesHeaders extends FormatageModelsSection {

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\FormatageModels::__construct()
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StylesGroupManager $styles_group_manager) {
    // TODO Auto-generated method stub
    parent::__construct($configuration, $plugin_id, $plugin_definition, $styles_group_manager);
    $this->pluginDefinition->set('icon', $this->pathResolver->getPath('module', 'bestlayouts') . "/icones/headers/bestlayouts_dynamiques_headers.png");
  }

  function defaultConfiguration() {
    return [
      'containt_menu' => '',
      "derivate" => [
        'value' => 'style-merseille',
        'options' => [
          'style-merseille' => 'style-merseille'
        ]
      ]
    ] + parent::defaultConfiguration();
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection::buildConfigurationForm()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['containt_menu'] = [
      '#type' => 'textfield',
      '#title' => $this->t('containt_menu'),
      '#default_value' => $this->configuration['containt_menu']
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
    $this->configuration['containt_menu'] = $form_state->getValue('containt_menu');
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
    if ($build['search']) {
      $build['search'] = $this->FormatSearchForm($build['search']);
    }

    return $build;
  }

  public function FormatSearchForm(array $searchs) {
    $newSearchs = [];
    $attributes = $searchs['#attributes'];
    foreach ($searchs as $search) {
      if (!empty($search['#theme'])) {
        if ($attributes)
          $search['content']['#attributes'] = $attributes;
        if (!empty($search['content']['#form_id']) && $search['content']['#form_id'] == 'search_block_form') {
          $search['content']['#theme'][] = 'bestlayouts_form_search_style_merseille';
        }
        $newSearchs[] = $search['content'];
      }
    }
    // dump($newSearchs);
    return $newSearchs;
  }

  /**
   *
   * {@inheritdoc}
   */
  private function getMenus(array $menu_nav) {
    $attributes = $menu_nav['#attributes'];
    /**
     * on retourne directement le menu sans passer par les blocks.
     *
     * @var array $cleanContent
     */
    $cleanContent = [];
    foreach ($menu_nav as $k => $m) {
      if (isset($m['#base_plugin_id']) && $m['#base_plugin_id'] === 'system_menu_block' && !$m['#in_preview']) {
        // set new theme.
        $menu_nav[$k]['content']['#theme'] = 'layoutmenu_bestlayouts_dynamiques_headers';
        // add class
        $menu_nav[$k]['content']['#attributes'] = $attributes;
        // format-it if is not empty
        if (!empty($menu_nav[$k]['content']['#items'])) {
          $this->formatListMenus($menu_nav[$k]['content']['#items']);
          // dump($menu_nav[$k]['content']['#items']);
        }
        $cleanContent[] = $menu_nav[$k]['content'];
      }
    }
    return $cleanContent;
    // return $menu_nav;
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
      $menuRoute = $url->getRouteName();

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