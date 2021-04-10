(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.PartnerFilter = {
        attach: function (context, settings) {

                $('#views-exposed-form-partners-block-2').once('form_partners_block_2_active').each(function (i) {
                     $(".select2").on("change", function () {                    
                        $("#edit-submit-partners").click();
                    });
                     $(".lupa").on("click", function () {                    
                        $("#edit-submit-partners").click();
                    });
                })


        }
    }
}(jQuery, Drupal, drupalSettings));