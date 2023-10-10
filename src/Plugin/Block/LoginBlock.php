<?php

namespace Drupal\bestlayouts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxy;

/**
 * Provides an exemple block.
 * Ce bloc affiche une icone de connexion ou un texte de connextion.
 *
 * @Block(
 *   id = "bestlayouts_login_block",
 *   admin_label = @Translation("Login block"),
 *   category = @Translation("bestlayouts")
 * )
 */
class LoginBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  /**
   *
   * @var AccountProxy
   */
  protected $current_user;
  
  /**
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param string $plugin_definition
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccountProxy $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->current_user = $current_user;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'), $container->get('current_user'));
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Component\Plugin\ConfigurableInterface::defaultConfiguration()
   */
  public function defaultConfiguration() {
    return [
      'model_display_enter' => 'icon',
      'model_display_enter_options' => [
        'icon_dropdown' => 'Icon with dropdown',
        'text' => 'Texte'
      ],
      'use_entity__text_before_login' => true,
      'use_entity__text_after_login' => true,
      'text_before_login' => '',
      'text_after_login' => '',
      'entity_before_login' => '',
      'entity_after_login' => '',
      'model_display_enter_icon_before_login' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z"/></svg>',
      'model_display_enter_icon_after_login' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"/></svg>'
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Block\BlockPluginInterface::blockForm()
   */
  public function blockForm($form, $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['model_display_enter'] = [
      '#type' => 'select',
      '#title' => 'model_display_enter',
      '#options' => $this->configuration['model_display_enter_options'],
      '#default_value' => $this->configuration['model_display_enter']
    ];
    
    //
    $blocks = $this->entityTypeManager->getStorage('block')->loadMultiple();
    $options = [];
    foreach ($blocks as $block) {
      $options[$block->id()] = $block->label();
    }
    $form['entity_before_login'] = [
      '#type' => 'select',
      '#title' => 'entity_before_login',
      '#options' => $options,
      '#default_value' => $this->configuration['entity_before_login']
    ];
    /**
     *
     * @var \Drupal\Core\Entity\EntityViewBuilder $userView
     */
    $query = $this->entityTypeManager->getStorage('entity_view_mode')->getQuery();
    $query->condition('targetEntityType', 'user');
    $query->accessCheck(TRUE);
    $ids = $query->execute();
    if ($ids) {
      $modes = [];
      $entities = $this->entityTypeManager->getStorage('entity_view_mode')->loadMultiple($ids);
      foreach ($entities as $entity) {
        $mode = explode(".", $entity->id());
        /**
         *
         * @var \Drupal\Core\Entity\Entity\EntityViewMode $entity
         */
        if (!empty($mode[1]))
          $modes[$mode[1]] = $entity->label();
      }
      $form['entity_after_login'] = [
        '#type' => 'select',
        '#title' => 'entity_after_login',
        '#options' => $modes,
        '#default_value' => $this->configuration['entity_after_login']
      ];
    }
    $form['model_display_enter_icon_before_login'] = [
      '#type' => 'textarea',
      '#title' => 'model_display_enter_icon_before_login',
      '#default_value' => $this->configuration['model_display_enter_icon_before_login']
    ];
    $form['model_display_enter_icon_after_login'] = [
      '#type' => 'textarea',
      '#title' => 'model_display_enter_icon_before_login',
      '#default_value' => $this->configuration['model_display_enter_icon_after_login']
    ];
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [];
    if ($this->current_user->isAnonymous()) {
      if ($this->configuration['use_entity__text_before_login']) {
        $blockBeforeview = [];
        if (!empty($this->configuration['entity_before_login'])) {
          $block = $this->entityTypeManager->getStorage('block')->load($this->configuration['entity_before_login']);
          if ($block) {
            $blockBeforeview = $this->entityTypeManager->getViewBuilder('block')->view($block);
          }
        }
        
        if ($this->configuration['model_display_enter'] === 'text') {
          $build['content']['entity_before_login'] = $blockBeforeview;
        }
        elseif ($this->configuration['model_display_enter'] === 'icon_dropdown') {
          $build['content']['entity_before_login'] = [
            '#theme' => 'bestlayouts_login_block_icon_dropdown',
            '#svg_icon' => $this->configuration['model_display_enter_icon_before_login'],
            '#entity_render' => $blockBeforeview
          ];
        }
      }
    }
    // lofin user
    else {
      $blockAfterview = [];
      if (!empty($this->configuration['entity_after_login'])) {
        $user = \Drupal\user\Entity\User::load($this->current_user->id());
        $blockAfterview = $this->entityTypeManager->getViewBuilder('user')->view($user, $this->configuration['entity_after_login']);
      }
      $build['content']['entity_after_login'] = [
        '#theme' => 'bestlayouts_login_block_icon_dropdown',
        '#svg_icon' => $this->configuration['model_display_enter_icon_after_login'],
        '#entity_render' => $blockAfterview
      ];
    }
    
    return $build;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Block\BlockPluginInterface::blockSubmit()
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['entity_before_login'] = $form_state->getValue('entity_before_login');
    $this->configuration['entity_after_login'] = $form_state->getValue('entity_after_login');
    $this->configuration['model_display_enter'] = $form_state->getValue('model_display_enter');
    $this->configuration['model_display_enter_icon_before_login'] = $form_state->getValue('model_display_enter_icon_before_login');
    $this->configuration['model_display_enter_icon_after_login'] = $form_state->getValue('model_display_enter_icon_after_login');
  }
  
}
