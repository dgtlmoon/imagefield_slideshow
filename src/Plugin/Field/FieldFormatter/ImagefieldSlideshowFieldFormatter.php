<?php

namespace Drupal\imagefield_slideshow\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'imagefield_slideshow_field_formatter' formatter.
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
   * @param \Drupal\Core\Entity\EntityStorageInterface $image_style_storage
   *   The image style.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, EntityStorageInterface $image_style_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->imageStyleStorage = $image_style_storage;
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
      $container->get('entity.manager')->getStorage('image_style')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      // Implement default settings.
      'imagefield_slideshow_style' => 'large',
      'imagefield_slideshow_style_effects' => 'fade',
      'imagefield_slideshow_style_pause' => 'false',
      'imagefield_slideshow_prev_next' => FALSE,
      'imagefield_slideshow_transition_speed' => FALSE,
      'imagefield_slideshow_timeout' => FALSE,
      'imagefield_slideshow_pager' => FALSE,
    ) + parent::defaultSettings();
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
        '#access' => $this->currentUser->hasPermission('administer image styles')
      ],
    ];
    $effects = [
      'none' => 'none',
//      'blindX' => 'blindX',
//      'blindY' => 'blindY',
//      'blindZ' => 'blindZ',
//      'cover' => 'cover',
//      'curtainX' => 'curtainX',
//      'curtainY' => 'curtainY',
      'fade' => 'fade',
      'fadeout' => 'fadeout',
//      'fadeZoom' => 'fadeZoom',
//      'growX' => 'growX',
//      'growY' => 'growY',
//      'scrollUp' => 'scrollUp',
//      'scrollDown' => 'scrollDown',
//      'scrollLeft' => 'scrollLeft',
//      'scrollRight' => 'scrollRight',
      'scrollHorz' => 'scrollHorz',
//      'scrollVert' => 'scrollVert',
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
      'false' => "No"
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
    $timeout = $transition_speed;
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
      $summary[] = t("Image style: @style", array('@style' => $image_styles[$image_style_setting]));
    }
    else {
      $summary[] = t("Original image");
    }

    $image_style_effect = $this->getSetting('imagefield_slideshow_style_effects');
    if (isset($image_style_effect)) {
      $summary[] .= t("Effect :" . $image_style_effect);
    }

    $image_style_pause = $this->getSetting('imagefield_slideshow_style_pause');
    if (!empty($image_style_pause)) {
      $summary[] .= t("Pause :" . $image_style_pause);
    }

    $image_prev_next = $this->getSetting('imagefield_slideshow_prev_next');
    if ($image_prev_next) {
      $summary[] .= t("Prev & Next :" . $image_prev_next);
    }

    $image_transition_speed = $this->getSetting('imagefield_slideshow_transition_speed');
    if ($image_transition_speed) {
      $summary[] .= t("Transition Speed :" . $image_transition_speed . ' fx');
    }

    $image_slideshow_timeout = $this->getSetting('imagefield_slideshow_timeout');
    if ($image_slideshow_timeout) {
      $summary[] .= t("Timeout :" . $image_slideshow_timeout);
    }

    $image_pager = $this->getSetting('imagefield_slideshow_pager');
    if ($image_pager) {
      $summary[] .= t("Pager :" . $image_pager);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    $image_style_setting = $this->getSetting('imagefield_slideshow_style');
    $image_style = NULL;
    if (!empty($image_style_setting)) {
      $image_style = \Drupal::entityTypeManager()->getStorage('image_style')->load($image_style_setting);
    }

    $image_uri_values = [];
    foreach ($items as $item) {
      if ($item->entity) {
        $image_uri = $item->entity->getFileUri();
        // Get image style URL
        if ($image_style) {
          $image_uri = ImageStyle::load($image_style->getName())->buildUrl($image_uri);
        } else {
          // Get absolute path for original image
          $image_uri = $item->entity->url();
        }
        $image_uri_values[] = $image_uri;
      }
    }

    // Enable prev next if only more than one image.
    $prev_next = $this->getSetting('imagefield_slideshow_prev_next');
    if (count($image_uri_values) <= 1) {
      $prev_next = FALSE;
    }

    $elements[] = array(
      '#theme' => 'imagefield_slideshow',
      '#url' => $image_uri_values,
      '#prev_next' => $prev_next,
      '#effect' => $this->getSetting('imagefield_slideshow_style_effects'),
      '#pause' => $this->getSetting('imagefield_slideshow_style_pause'),
      '#speed' => $this->getSetting('imagefield_slideshow_transition_speed'),
      '#timeout' => $this->getSetting('imagefield_slideshow_timeout'),
      '#pager' => $this->getSetting('imagefield_slideshow_pager'),
    );

    // Attach the image field slide show library.
    $elements['#attached']['library'][] = 'imagefield_slideshow/imagefield_slideshow';

    // Not to cache this field formatter.
    $elements['#cache']['max-age'] = 0;

    return $elements;
  }

}
