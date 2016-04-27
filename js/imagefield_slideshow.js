/**
 * @file
 * Defines Javascript behaviors for the imagefield_slideshow module.
 */

(function ($) {
  Drupal.behaviors.imagefieldSlideshow = {
    attach: function () {

      jQuery('.slideshow').cycle({
        fx: 'fade',
        pause: 1,
        prev: '#prev',
        next: '#next'
      });

    }
  };
})(jQuery);