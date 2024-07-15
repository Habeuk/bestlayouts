<?php

namespace Drupal\bestlayouts\Plugin\Layout\Sections;

use Drupal\bootstrap_styles\StylesGroup\StylesGroupManager;
use Drupal\formatage_models\FormatageModelsThemes;
use Drupal\formatage_models\Plugin\Layout\FormatageModels;
use Drupal\formatage_models\Plugin\Layout\Sections\FormatageModelsSection;

/**
 * A contact section layout from bestlayouts
 *
 * @Layout(
 *   id = "bestlayouts_facmaster_button_right",
 *   label = @Translation(" Bestlayous : Facmaster button right"),
 *   category = @Translation("bestlayouts"),
 *   path = "layouts/sections",
 *   template = "bestlayouts_facmaster_button_right",
 *   library = "bestlayouts/bestlayouts_facmaster_button_right",
 *   default_region = "subtitle",
 *   regions = {
 *     "subtitle" = {
 *       "label" = @Translation("subtitle"),
 *     },
 *     "title" = {
 *       "label" = @Translation("title"),
 *     },
 *     "description" = {
 *       "label" = @Translation("description"),
 *     },
 *     "button" = {
 *       "label" = @Translation("button"),
 *     },
 *   }
 * )
 */
class BestlayoutsFacmasterButtonRight extends FormatageModelsSection {

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\formatage_models\Plugin\Layout\FormatageModels::__construct()
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StylesGroupManager $styles_group_manager) {
    // TODO Auto-generated method stub
    parent::__construct($configuration, $plugin_id, $plugin_definition, $styles_group_manager);
    $this->pluginDefinition->set('icon', $this->pathResolver->getPath('module', 'bestlayouts') . "/icones/sections/facmaster-button-right.png");
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

  /**
   *
   * {@inheritdoc}
   *
   */
  function defaultConfiguration() {
    return [
      'load_libray' => true,
      'region_tag_subtitle' => 'h4',
      'region_tag_title' => 'h2',
      'infos' => [
        'builder-form' => true,
        'info' => [
          'title' => 'Content',
          'loader' => 'static'
        ],
        'fields' => [
          "subtitle" => [
            'text_html' => [
              'label' => "Subtitle",
              'value' => 'highly specialized engineers'
            ]
          ],
          "title" => [
            'text_html' => [
              'label' => "Title",
              'value' => 'the head of the factory'
            ]
          ],
          "text" => [
            'text_html' => [
              'label' => "Text",
              'value' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.
                            Ipsum maxime optio consequatur nihil pari.'
            ]
          ],
          "button" => [
            'url' => [
              'label' => 'Link',
              'value' => [
                'text' => 'view all team',
                'class' => 'facmaster-button-right__right_button',
                'link' => '#'
              ]
            ]
          ]
        ]
      ]
    ] + parent::defaultConfiguration();
  }

}