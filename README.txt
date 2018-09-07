CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------
Imagefield Slideshow will provide a field formatter for image field,
so that the images uploaded for an image field would be rendered as a Slider.


REQUIREMENTS
------------
This module does not have any dependency.


INSTALLATION
------------
* Download the module and place in contrib module folder.
* Enable the Imagefield Slideshow module from the, modules page / drush / drupal console.
* You should now see a new field formatter for image fields,
  Ex: under Manage display section of each content types.


CONFIGURATION
-------------
* Visit any image fields display settings, you will be able to find
the Imagefield Slideshow formatter, select this one and one can also
select image styles.
Ex: admin/structure/types/manage/<content-type-machine-name>/display
* Have the image field settings "Allowed number of values"
to unlimited / limited(more than 1 image).
* Have a custom image style defined under
(http://d8.local/admin/config/media/image-styles)
* Add content & upload more than 1 images to the node and Save..
On node view, slideshow will appear for that image field.


MAINTAINERS
-----------

 * Karthik Kumar D K (heykarthikwithu)
     - https://www.drupal.org/u/heykarthikwithu
