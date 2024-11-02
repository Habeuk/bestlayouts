<?php
declare(strict_types = 1);

namespace Drupal\bestlayouts\Entity;

use Drupal\bestlayouts\MegaMenusInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the megamenus entity class.
 *
 * @ContentEntityType(
 *   id = "btly_megamenus",
 *   label = @Translation("MegaMenus"),
 *   label_collection = @Translation("MegaMeni"),
 *   label_singular = @Translation("megamenus"),
 *   label_plural = @Translation("megameni"),
 *   label_count = @PluralTranslation(
 *     singular = "@count megameni",
 *     plural = "@count megameni",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bestlayouts\MegaMenusListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\bestlayouts\Form\MegaMenusForm",
 *       "edit" = "Drupal\bestlayouts\Form\MegaMenusForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *   },
 *   base_table = "btly_megamenus",
 *   data_table = "btly_megamenus_field_data",
 *   revision_table = "btly_megamenus_revision",
 *   revision_data_table = "btly_megamenus_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer btly_megamenus",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/btly-megamenus",
 *     "add-form" = "/btly-megamenus/add",
 *     "canonical" = "/btly-megamenus/{btly_megamenus}",
 *     "edit-form" = "/btly-megamenus/{btly_megamenus}/edit",
 *     "delete-form" = "/btly-megamenus/{btly_megamenus}/delete",
 *     "delete-multiple-form" = "/admin/content/btly-megamenus/delete-multiple",
 *     "revision" = "/btly-megamenus/{btly_megamenus}/revision/{btly_megamenus_revision}/view",
 *     "revision-delete-form" = "/btly-megamenus/{btly_megamenus}/revision/{btly_megamenus_revision}/delete",
 *     "revision-revert-form" = "/btly-megamenus/{btly_megamenus}/revision/{btly_megamenus_revision}/revert",
 *     "version-history" = "/btly-megamenus/{btly_megamenus}/revisions",
 *   },
 *   field_ui_base_route = "entity.btly_megamenus.settings",
 * )
 */
final class MegaMenus extends RevisionableContentEntityBase implements MegaMenusInterface {
  
  use EntityChangedTrait;
  use EntityOwnerTrait;
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    $fields['label'] = BaseFieldDefinition::create('string')->setRevisionable(TRUE)->setTranslatable(TRUE)->setLabel(t('Label'))->setRequired(TRUE)->setSetting('max_length', 255)->setDisplayOptions(
      'form', [
        'type' => 'string_textfield',
        'weight' => -5
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'string',
      'weight' => -5
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['status'] = BaseFieldDefinition::create('boolean')->setRevisionable(TRUE)->setLabel(t('Status'))->setDefaultValue(TRUE)->setSetting('on_label', 'Enabled')->setDisplayOptions('form',
      [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE
        ],
        'weight' => 0
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'type' => 'boolean',
      'label' => 'above',
      'weight' => 0,
      'settings' => [
        'format' => 'enabled-disabled'
      ]
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')->setRevisionable(TRUE)->setTranslatable(TRUE)->setLabel(t('Author'))->setSetting('target_type', 'user')->setDefaultValueCallback(
      self::class . '::getDefaultEntityOwner')->setDisplayOptions('form',
      [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => ''
        ],
        'weight' => 15
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'author',
      'weight' => 15
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Authored on'))->setTranslatable(TRUE)->setDescription(t('The time that the megamenus was created.'))->setDisplayOptions(
      'view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('form', [
      'type' => 'datetime_timestamp',
      'weight' => 20
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setTranslatable(TRUE)->setDescription(t('The time that the megamenus was last edited.'));
    
    return $fields;
  }
}
