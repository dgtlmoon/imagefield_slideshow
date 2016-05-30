/**
 * @file
 * Defines Javascript behaviors for the imagefield_slideshow module.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  $(document).ready(function () {

    jQuery('.imagefield_slideshow').cycle({
      fx: 'fade',
      pause: 1,
      prev: '#prev',
      next: '#next'
    });

    // alert(drupalSettings.fluffiness.cuddlySlider.foo);

  });

})(jQuery, Drupal, drupalSettings);