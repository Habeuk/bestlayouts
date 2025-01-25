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
 *   id = "bestlayouts_headers",
 *   label = @Translation(" Bestlayous : header"),
 *   category = @Translation("bestlayouts"),
 *   path = "layouts/headers",
 *   template = "bestlayouts_headers",
 *   library = "bestlayouts/bestlayouts_headers",
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
class BestlayoutsHeaders extends FormatageModelsSection {
  
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
      'containt_menu' => ''
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
    FormatageModelsThemes::formatSettingValues($build);
    return $build;
  }
}