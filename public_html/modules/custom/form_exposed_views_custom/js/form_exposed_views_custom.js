(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.form_utechki_daydzhesty = {
        attach: function (context, settings) {
          $('input[name="publication"]').datepicker({
            dateFormat: 'dd.mm.yy',
            monthNames : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            dayNamesMin : ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
            firstDay: 1
          })

          $('#views-exposed-form-all-materials-block-1').once('all-materials-active').each(function (i) {
            $(this).find('[data-drupal-selector="edit-reset"]').click(function (e) {
              e.preventDefault()
              location.href = '/'
            })
          })

        }
    }
} (jQuery, Drupal, drupalSettings));
