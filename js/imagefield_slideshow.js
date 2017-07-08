/**
 * @file
 * Defines Javascript behaviors for the imagefield_slideshow module.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  $(document).ready(function () {

    var slideshow = jQuery('.imagefield_slideshow').cycle({
      fx: drupalSettings.imagefield_slideshow.effect,
      pause: drupalSettings.imagefield_slideshow.pause,
      speed: drupalSettings.imagefield_slideshow.speed,
      timeout: drupalSettings.imagefield_slideshow.timeout
    });

    // If prev/next setting is enabled.
    if (drupalSettings.imagefield_slideshow.prev_next) {
      jQuery('.imagefield_slideshow-prev').click(function() {
        slideshow.cycle('prev');
      });
      jQuery('.imagefield_slideshow-next').click(function() {
        slideshow.cycle('next');
      });
    }

  });

})(jQuery, Drupal, drupalSettings);
