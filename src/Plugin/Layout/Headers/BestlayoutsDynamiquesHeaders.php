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
 *   label = @Translation(" Bestlayous : dynamique header"),
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
    /**
     * Configuration du menu.
     */
    $form['menu_config'] = [
      '#type' => 'details',
      '#title' => 'Configuration du menu',
      '#open' => false
    ];
    $form['menu_config']['menu_static'] = [
      '#type' => 'select',
      // '#type' => 'checkbox',
      '#title' => $this->t(" Mode d'affichage "),
      '#default_value' => isset($this->configuration['menu_config']['menu_static']) ? $this->configuration['menu_config']['menu_static'] : '',
      // '#return_value' => 'menu-static'
      '#options' => [
        '' => 'None',
        'menu-static' => 'Menu static',
        'menu-static tablette' => 'Menu static on tablette (992)'
      ]
    ];
    $form['menu_config']['not_cover_section'] = [
      '#type' => 'checkbox',
      '#title' => $this->t(" Not cover section "),
      '#default_value' => isset($this->configuration['menu_config']['not_cover_section']) ? $this->configuration['menu_config']['not_cover_section'] : '',
      '#return_value' => 'not-cover-section',
      '#description' => "Permet de pousser le site en dessous du menu"
    ];
    $form['menu_config']['bg-color'] = [
      '#type' => 'select',
      '#title' => $this->t('Selectionner une couleur'),
      '#options' => [
        '' => 'Aucun',
        'menu-bg-background' => 'Menu bg background',
        'menu-bg-light' => 'Menu light'
      ],
      '#default_value' => isset($this->configuration['menu_config']['bg-color']) ? $this->configuration['menu_config']['bg-color'] : ''
    ];
    $form['menu_config']['items-position'] = [
      '#type' => 'select',
      '#title' => $this->t('Position des items de menu'),
      '#options' => [
        '' => 'Equi-distant',
        'menu-to-left' => 'Aligner à gauche',
        'menu-to-right' => 'Aligner à droite',
        'menu-to-center' => 'Aligner au centre'
      ],
      '#default_value' => isset($this->configuration['menu_config']['items-position']) ? $this->configuration['menu_config']['items-position'] : ''
    ];
    $form['menu_config']['menu_multiligne'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Menu multi-ligne'),
      '#default_value' => isset($this->configuration['menu_config']['menu_multiligne']) ? $this->configuration['menu_config']['menu_multiligne'] : '',
      '#return_value' => 'menu-multiligne'
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
    $this->configuration['menu_config'] = $form_state->getValue('menu_config');
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\FormatageModels::build()
   */
  public function build(array $regions) {
    // TODO Auto-generated method stub
    $build = parent::build($regions);
    /**
     *
     * @var \Drupal\bestlayouts\Plugin\Layout\Headers\BestlayoutsDynamiquesHeaders $layout
     */
    $layout = $build['#layout'];
    
    FormatageModelsThemes::formatSettingValues($build);
    
    if (is_array($build['menu'])) {
      $build['menu'] = $this->getMenus($build['menu']);
    }
    if ($build['search']) {
      $build['search'] = $this->FormatSearchForm($build['search']);
    }
    
    if (!empty($this->configuration['menu_config'])) {
      foreach ($this->configuration['menu_config'] as $value) {
        if ($value)
          $build['#attributes']['class'][] = $value;
      }
    }
    return $build;
  }
  
  /**
   *
   * @param array $searchs
   * @return string[]
   */
  public function FormatSearchForm(array $searchs) {
    $newSearchs = [];
    // $attributes = $searchs['#attributes'];
    foreach ($searchs as $key => $search) {
      if (!empty($search['#theme'])) {
        // if ($attributes)
        // $search['content']['#attributes'] = $attributes;
        if (!empty($search['content']['#form_id']) && $search['content']['#form_id'] == 'search_block_form') {
          $search['content']['#theme'][] = 'bestlayouts_form_search_style_merseille';
          $search['content']['#attributes']['class'][] = 'd-flex';
          // dd($search['content']);
        }
      }
      $newSearchs[$key] = $search;
    }
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
      // stop formatter if in preview, i.e si l'utilisateur est entrain de
      // configurer le bloc.
      if (!empty($m['#in_preview'])) {
        return $menu_nav;
      }
      
      if (isset($m['#base_plugin_id']))
        // cas ou on a directement injecte le menu.
        if ($m['#base_plugin_id'] === 'system_menu_block') {
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
        // si on passe par un champs.
        elseif ($m['#base_plugin_id'] === 'field_block') {
          $menu_nav[$k]['content']['#theme'] = 'layoutmenu_bestlayouts_dynamiques_headers';
          // add class
          $menu_nav[$k]['content']['#attributes'] = $attributes;
          // format-it if is not empty
          if (!empty($menu_nav[$k]['content']['#items'])) {
            // il faudra faire ceci, autrement afin de supprimer [0].
            $this->formatListMenus($menu_nav[$k]['content'][0]["#items"]);
            // On ramene les elments à la racine.
            $menu_nav[$k]['content']["#items"] = $menu_nav[$k]['content'][0]["#items"];
          }
          $cleanContent[] = $menu_nav[$k]['content'];
          // return $menu_nav;
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