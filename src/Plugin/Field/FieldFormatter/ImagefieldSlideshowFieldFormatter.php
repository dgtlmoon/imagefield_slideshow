<?php

namespace Drupal\imagefield_slideshow\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of 'imagefield_slideshow_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "imagefield_slideshow_field_formatter",
 *   label = @Translation("Imagefield Slideshow"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class ImagefieldSlideshowFieldFormatter extends ImageFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The image style entity storage.
   *
   * @var \Drupal\image\ImageStyleStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * Constructs an ImageFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->imageStyleStorage = $this->entityTypeManager->getStorage('image_style');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'imagefield_slideshow_style' => 'large',
      'imagefield_slideshow_style_effects' => 'fade',
      'imagefield_slideshow_style_pause' => 'false',
      'imagefield_slideshow_prev_next' => 'true',
      'imagefield_slideshow_transition_speed' => 100,
      'imagefield_slideshow_timeout' => 100,
      'imagefield_slideshow_pager' => TRUE,
      'imagefield_slideshow_link_image_to' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $image_styles = image_style_options(FALSE);
    $description_link = Link::fromTextAndUrl(
      $this->t("Configure Image Styles"),
      Url::fromRoute('entity.image_style.collection')
    );
    $element['imagefield_slideshow_style'] = [
      '#title' => t("Image style"),
      '#type' => 'select',
      '#default_value' => $this->getSetting('imagefield_slideshow_style'),
      '#empty_option' => t("None (original image)"),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
        '#access' => $this->currentUser->hasPermission('administer image styles'),
      ],
    ];
    $effects = [
      'none' => 'none',
    // 'blindX' => 'blindX',
    //      'blindY' => 'blindY',
    //      'blindZ' => 'blindZ',
    //      'cover' => 'cover',
    //      'curtainX' => 'curtainX',
    //      'curtainY' => 'curtainY',
      'fade' => 'fade',
      'fadeout' => 'fadeout',
    // 'fadeZoom' => 'fadeZoom',
    //      'growX' => 'growX',
    //      'growY' => 'growY',
    //      'scrollUp' => 'scrollUp',
    //      'scrollDown' => 'scrollDown',
    //      'scrollLeft' => 'scrollLeft',
    //      'scrollRight' => 'scrollRight',
      'scrollHorz' => 'scrollHorz',
    // 'scrollVert' => 'scrollVert',
    //      'shuffle' => 'shuffle',
    //      'slideX' => 'slideX',
    //      'slideY' => 'slideY',
    //      'toss' => 'toss',
    //      'turnUp' => 'turnUp',
    //      'turnDown' => 'turnDown',
    //      'turnLeft' => 'turnLeft',
    //      'turnRight' => 'turnRight',
    //      'uncover' => 'uncover',
    //      'wipe' => 'wipe',
    //      'zoom' => 'zoom',
      'flipHorz' => 'flipHorz',
      'flipVert' => 'flipVert',
      'shuffle' => 'shuffle',
    ];
    $element['imagefield_slideshow_style_effects'] = [
      '#type' => 'select',
      '#title' => t("Effect"),
      '#options' => $effects,
      '#default_value' => $this->getSetting('imagefield_slideshow_style_effects'),
      '#description' => t("The transition effect that will be used to change between images. Not all options below may be relevant depending on the effect. <a href='http://jquery.malsup.com/cycle/browser.html' target='_black'>Follow this link to see examples of each effect.</a>"),
    ];
    $image_pause = [
      'true' => "Yes",
      'false' => "No",
    ];
    $element['imagefield_slideshow_style_pause'] = [
      '#title' => t("Image pause"),
      '#type' => 'select',
      '#default_value' => $this->getSetting('imagefield_slideshow_style_pause'),
      '#options' => $image_pause,
      '#description' => t("Should image be paused on hover."),
    ];
    $element['imagefield_slideshow_prev_next'] = [
      '#title' => $this->t("Enable Prev & Next button"),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('imagefield_slideshow_prev_next'),
      '#description' => $this->t('This will show the Prev and Next button for slideshow.'),
    ];
    $range0 = array_combine(range(100, 1000, 100), range(100, 1000, 100));
    $range1 = array_combine(range(2000, 10000, 1000), range(2000, 10000, 1000));
    $transition_speed = array_replace($range0, $range1);
    $element['imagefield_slideshow_transition_speed'] = [
      '#type' => 'select',
      '#title' => t("Transition Speed"),
      '#options' => $transition_speed,
      '#default_value' => $this->getSetting('imagefield_slideshow_transition_speed'),
      '#description' => t("The transition speed between images."),
    ];
    $range3 = array_combine(range(0, 1000, 100), range(0, 1000, 100));
    $timeout = array_replace($range3, $range1);
    $element['imagefield_slideshow_timeout'] = [
      '#type' => 'select',
      '#title' => t("Timeout"),
      '#options' => $timeout,
      '#default_value' => $this->getSetting('imagefield_slideshow_timeout'),
      '#description' => t("The timeout for slides."),
    ];
    $element['imagefield_slideshow_pager'] = [
      '#title' => $this->t("Enable Pager ?"),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('imagefield_slideshow_pager'),
      '#description' => $this->t('This will show the Pager on slideshow.'),
    ];
    $link_image_to = ['' => 'Nothing', 'content' => 'Content', 'file' => 'File'];
    $element['imagefield_slideshow_link_image_to'] = [
      '#type' => 'select',
      '#title' => t("Link image to"),
      '#options' => $link_image_to,
      '#default_value' => $this->getSetting('imagefield_slideshow_link_image_to'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('imagefield_slideshow_style');
    if (isset($image_styles[$image_style_setting])) {
      $summary[] = t("Image style: @style", ['@style' => $image_styles[$image_style_setting]]);
    }
    else {
      $summary[] = $this->t("Original image");
    }

    $image_style_effect = $this->getSetting('imagefield_slideshow_style_effects');
    if (isset($image_style_effect)) {
      $summary[] = $this->t("Effect: @image_style_effect", [
        "@image_style_effect" => $image_style_effect,
      ]);
    }

    $image_style_pause = $this->getSetting('imagefield_slideshow_style_pause');
    if (!empty($image_style_pause)) {
      $summary[] = $this->t("Pause: @image_style_pause", [
        "@image_style_pause" => $image_style_pause,
      ]);
    }

    $image_prev_next = $this->getSetting('imagefield_slideshow_prev_next');
    if ($image_prev_next) {
      $summary[] = $this->t("Prev & Next: @image_prev_next", [
        "@image_prev_next" => $image_prev_next,
      ]);
    }

    $image_transition_speed = $this->getSetting('imagefield_slideshow_transition_speed');
    if ($image_transition_speed) {
      $summary[] = $this->t("Transition Speed: @image_transition_speed fx", [
        "@image_transition_speed" => $image_transition_speed,
      ]);
    }

    $image_slideshow_timeout = $this->getSetting('imagefield_slideshow_timeout');
    if ($image_slideshow_timeout) {
      $summary[] = $this->t("Timeout: @image_slideshow_timeout", [
        "@image_slideshow_timeout" => $image_slideshow_timeout,
      ]);
    }

    $image_pager = $this->getSetting('imagefield_slideshow_pager');
    if ($image_pager) {
      $summary[] = $this->t("Pager: @image_pager", [
        "@image_pager" => $image_pager,
      ]);
    }

    $link_image_to = $this->getSetting('imagefield_slideshow_link_image_to');
    if ($link_image_to) {
      $summary[] = $this->t("Link Image to: @link_image_to", [
        "@link_image_to" => $link_image_to,
      ]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // Get the Entity value as array.
    $file = $items->getEntity()->toArray();

    // Early opt-out if the field is empty.
    $images = $this->getEntitiesToView($items, $langcode);
    if (empty($images)) {
      return $elements;
    }

    $image_style_setting = $this->getSetting('imagefield_slideshow_style');
    $image_style = NULL;
    if (!empty($image_style_setting)) {
      $image_style = \Drupal::entityTypeManager()->getStorage('image_style')->load($image_style_setting);
    }

    $image_uri_values = [];
    /** @var \Drupal\file\FileInterface $image */
    foreach ($images as $image) {
      $image_uri = $image->getFileUri();
      // Get image style URL.
      if ($image_style) {
        $image_uri = ImageStyle::load($image_style->getName())->buildUrl($image_uri);
      }
      else {
        // Get absolute path for original image.
        $image_uri = $image->createFileUrl(FALSE);
      }
      // Populate image uri's with fid.
      $fid = $image->toArray()['fid'][0]['value'];
      $image_uri_values[$fid] = ['uri' => $image_uri];
    }

    // Populate the title and alt of images based on fid.
    foreach (['title', 'alt'] as $element_name) {
      $field_name = $this->fieldDefinition->getName();
      if (array_key_exists($field_name, $file)) {
        foreach ($file[$field_name] as $key => $value) {
          $image_uri_values[$value['target_id']]['alt'] = $value['alt'];
          $image_uri_values[$value['target_id']]['title'] = $value['title'];
        }
      }
    }

    // Enable prev next if only more than one image.
    $prev_next = $this->getSetting('imagefield_slideshow_prev_next');
    if (count($image_uri_values) <= 1) {
      $prev_next = FALSE;
    }

    $imagefield_slideshow_link_image_to = $this->getSetting('imagefield_slideshow_link_image_to');
    $link_image_to = [];
    $link_image_to['type'] = $imagefield_slideshow_link_image_to;
    if ($imagefield_slideshow_link_image_to == 'content') {
      $link_image_to['type'] = 'content';
      $content_url = '/node/' . $file['nid'][0]['value'];
      $link_image_to['path'] = \Drupal::service('path_alias.manager')->getAliasByPath($content_url);
    }
    elseif ($imagefield_slideshow_link_image_to == '') {
      $link_image_to = FALSE;
    }

    $elements[] = [
      '#theme' => 'imagefield_slideshow',
      '#url' => $image_uri_values,
      '#prev_next' => $prev_next,
      '#effect' => $this->getSetting('imagefield_slideshow_style_effects'),
      '#pause' => $this->getSetting('imagefield_slideshow_style_pause'),
      '#speed' => $this->getSetting('imagefield_slideshow_transition_speed'),
      '#timeout' => $this->getSetting('imagefield_slideshow_timeout'),
      '#pager' => $this->getSetting('imagefield_slideshow_pager'),
      '#link_image_to' => $link_image_to,
    ];

    // Attach the image field slide show library.
    $elements['#attached']['library'][] = 'imagefield_slideshow/imagefield_slideshow';

    // Not to cache this field formatter.
    $elements['#cache']['max-age'] = 0;

    return $elements;
  }

}
