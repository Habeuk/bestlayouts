<?php

namespace Drupal\bestlayouts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Form\SubformState;

/**
 * Provides an exemple block.
 * Ce bloc permet d'afficher directement le formulaire de connexion, d'afficher
 * un modele de rendu de l'utilisateur et de charger un block_content.
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
   * The menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTree
   */
  protected $menuTree;
  
  /**
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param string $plugin_definition
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccountProxy $current_user, MenuLinkTree $MenuLinkTree) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->current_user = $current_user;
    $this->menuTree = $MenuLinkTree;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'), $container->get('current_user'), $container->get('menu.link_tree'));
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Component\Plugin\ConfigurableInterface::defaultConfiguration()
   */
  public function defaultConfiguration() {
    return [
      'model_display_enter' => 'icon_dropdown',
      'proccess_options' => [
        'block_content_render' => 'Block content render',
        'user_render' => "Modele d'affichage de l'utilisateur",
        'menu_render' => "Rendu via le menu",
        'text_render' => "Texte",
        'login_form' => "Formulaire de connexion"
      ],
      'proccess_before' => [
        'login' => 'login_form',
        'data' => null
      ],
      'proccess_after' => [
        'login' => 'user_render',
        'data' => null
      ],
      'model_display_enter_icon_before_login' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z"/></svg>',
      'model_display_enter_icon_after_login' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"/></svg>'
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Block\BlockPluginInterface::blockSubmit()
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['model_display_enter'] = $form_state->getValue('model_display_enter');
    $this->configuration['proccess_before'] = $form_state->getValue('proccess_before');
    $this->configuration['proccess_after'] = $form_state->getValue('proccess_after');
    $this->configuration['model_display_enter_icon_before_login'] = $form_state->getValue('model_display_enter_icon_before_login');
    $this->configuration['model_display_enter_icon_after_login'] = $form_state->getValue('model_display_enter_icon_after_login');
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Block\BlockPluginInterface::blockForm()
   */
  public function blockForm($form, $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['header_message'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => ' This block allows to directly display the login form or display a user rendering model or load a block_content '
    ];
    $form['proccess_before'] = [
      '#type' => 'details',
      '#title' => 'Proccess before login',
      '#tree' => true,
      '#open' => false
    ];
    $form['proccess_before']['login'] = [
      '#type' => 'select',
      '#title' => t('Connection display'),
      '#options' => $this->configuration['proccess_options'],
      '#default_value' => $this->configuration['proccess_before']['login'],
      '#required' => true
    ];
    /**
     * On a une erreur avec $form_state->getValue('settings').
     *
     * @see https://www.drupal.org/project/drupal/issues/2798261#comment-12735075
     */
    if ($form_state instanceof SubformState) {
      $settings = $form_state->getCompleteFormState()->getValue('settings');
    }
    $typeLogin = !empty($settings['proccess_before']['login']) ? $settings['proccess_before']['login'] : $this->configuration['proccess_before']['login'];
    if ($typeLogin) {
      $this->buildSubForm($typeLogin, $form, 'proccess_before');
    }
    $form['proccess_after'] = [
      '#type' => 'details',
      '#title' => 'Proccess after login',
      '#tree' => true,
      '#open' => false
    ];
    
    $form['proccess_after']['login'] = [
      '#type' => 'select',
      '#title' => t('Connection display after'),
      '#options' => $this->configuration['proccess_options'],
      '#default_value' => $this->configuration['proccess_after']['login'],
      '#required' => true
    ];
    $typeAfterLogin = !empty($settings['proccess_after']['login']) ? $settings['proccess_after']['login'] : $this->configuration['proccess_after']['login'];
    if (empty($typeAfterLogin)) {
      $typeAfterLogin = $this->configuration['proccess_after']['login'];
    }
    if ($typeAfterLogin) {
      $this->buildSubForm($typeAfterLogin, $form, 'proccess_after');
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
    $loginType = NULL;
    if ($this->current_user->isAnonymous()) {
      $loginType = $this->configuration['proccess_before']['login'];
      $data = $this->configuration['proccess_before']['data'];
    }
    else {
      $loginType = $this->configuration['proccess_after']['login'];
      $data = $this->configuration['proccess_after']['data'];
    }
    if ($loginType)
      switch ($loginType) {
        case 'login_form':
          $build['content']['data'] = [
            '#theme' => 'bestlayouts_login_block_icon_dropdown',
            '#svg_icon' => $this->configuration['model_display_enter_icon_before_login'],
            '#entity_render' => \Drupal::formBuilder()->getForm('\Drupal\user\Form\UserLoginForm')
          ];
          break;
        case 'text_render':
          $build['content']['data'] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#value' => !empty($data['value']) ? $data['value'] : ''
          ];
          break;
        case 'menu_render':
          /**
           *
           * @var \Drupal\Core\Menu\MenuTreeParameters $parameters
           */
          $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($data);
          
          $tree = $this->menuTree->load($data, $parameters);
          if (!empty($tree)) {
            $manipulators = [
              [
                'callable' => 'menu.default_tree_manipulators:checkAccess'
              ],
              [
                'callable' => 'menu.default_tree_manipulators:generateIndexAndSort'
              ]
            ];
            $tree = $this->menuTree->transform($tree, $manipulators);
            $build['content']['data'] = $this->menuTree->build($tree);
          }
          break;
        case 'user_render':
          $user = \Drupal\user\Entity\User::load($this->current_user->id());
          $build['content']['entity_after_login'] = [
            '#theme' => 'bestlayouts_login_block_icon_dropdown',
            '#svg_icon' => $this->configuration['model_display_enter_icon_after_login'],
            '#entity_render' => $this->entityTypeManager->getViewBuilder('user')->view($user, $data)
          ];
          break;
        case 'block_content_render':
          $block_content = $this->entityTypeManager->getStorage('block_content')->load($data);
          if ($block_content)
            $build['content']['entity_after_login'] = [
              '#theme' => 'bestlayouts_login_block_icon_dropdown',
              '#svg_icon' => $this->configuration['model_display_enter_icon_after_login'],
              '#entity_render' => $this->entityTypeManager->getViewBuilder('block_content')->view($block_content)
            ];
          break;
      }
    return $build;
  }
  
  protected function buildSubForm($typeLogin, &$form, $key = 'proccess_before') {
    switch ($typeLogin) {
      case 'text_render':
        $form[$key]['data'] = [
          '#type' => 'text_format',
          '#title' => "Texte à afficher",
          '#required' => true,
          '#default_value' => $this->configuration[$key]['data']
        ];
        break;
      case 'menu_render':
        $menus = $this->entityTypeManager->getStorage('menu')->loadMultiple();
        $menuOptions = [];
        foreach ($menus as $menu) {
          $menuOptions[$menu->id()] = $menu->label();
        }
        $form[$key]['data'] = [
          '#type' => 'select',
          '#title' => "Selectionne le menu",
          '#required' => true,
          '#options' => $menuOptions,
          '#default_value' => $this->configuration[$key]['data']
        ];
        break;
      case 'block_content':
        $block_contents = $this->entityTypeManager->getStorage('block_content')->loadMultiple();
        $block_contentOptions = [];
        foreach ($block_contents as $block_content) {
          $block_contentOptions[$block_content->id()] = $block_content->label();
        }
        $form[$key]['data'] = [
          '#type' => 'select',
          '#title' => "Selectionne le block content",
          '#required' => true,
          '#options' => $block_contentOptions,
          '#default_value' => $this->configuration[$key]['data']
        ];
        break;
      case 'user_render':
        $query = $this->entityTypeManager->getStorage('entity_view_mode')->getQuery();
        $query->condition('targetEntityType', 'user');
        $query->accessCheck(TRUE);
        $ids = $query->execute();
        $modes = [];
        if ($ids) {
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
          //
          $form[$key]['data'] = [
            '#type' => 'select',
            '#title' => "Selectionne le rendu utilisateur",
            '#required' => true,
            '#options' => $modes,
            '#default_value' => $this->configuration[$key]['data']
          ];
        }
        break;
      default:
        ;
        break;
    }
  }
}
