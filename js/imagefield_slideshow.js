/**
 * @file
 * Defines Javascript behaviors for the imagefield_slideshow module.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  $(document).ready(function () {

    jQuery('.imagefield_slideshow').cycle({
      fx: drupalSettings.imagefield_slideshow.effect,
      pause: drupalSettings.imagefield_slideshow.pause,
      prev: '#prev',
      next: '#next'
    });

    console.log(drupalSettings.imagefield_slideshow);

  });

})(jQuery, Drupal, drupalSettings);