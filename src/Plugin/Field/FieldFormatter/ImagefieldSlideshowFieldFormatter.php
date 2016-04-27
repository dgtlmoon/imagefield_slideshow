<?php

namespace Drupal\imagefield_slideshow\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;

/**
 * Plugin implementation of the 'imagefield_slideshow_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "imagefield_slideshow_field_formatter",
 *   label = @Translation("Imagefield slideshow field formatter"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class ImagefieldSlideshowFieldFormatter extends ImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      // Implement default settings.
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(
      // Implement settings form.
    ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    $image_uri_values = [];
    foreach ($files as $delta => $file) {
      $uri = $file->getFileUri();
      $image_uri_value = ImageStyle::load('medium')->buildUrl($uri);

      $a = '';
      $image_uri_values[] = $image_uri_value;
    }
    $elements[] = array(
      '#theme' => 'imagefield_slideshow',
      '#url' => $image_uri_values,
    );

    /*foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }*/

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    // $item->value = 'kkkkkkkkk';
    return nl2br(Html::escape($item->value));
  }

}
