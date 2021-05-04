(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.form_utechki_daydzhesty = {
        attach: function (context, settings) {
            $('input[name="publication"]').datepicker({
                dateFormat: 'dd.mm.yy',
                monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                firstDay: 1
            })

            $('#views-exposed-form-all-materials-block-1').once('all-materials-active').each(function (i) {
                $(this).find('[data-drupal-selector="edit-reset"]').click(function (e) {
                    e.preventDefault()
                    location.href = '/'
                })
            })

            $('.success-add').once('success-add-active').each(function (i) {
                if ($('.approve-node-form .success-add.yes').length > 0) {
                    $('nav.tabs').hide()
                } else {
                    $('nav.tabs').show()
                }
            })

            $('.approve-hidden').once('approve-hidden-active').each(function (i) {
                if ($('.approve-hidden').length > 0 && $('.approve-hidden').val() == 0) {
                    $('nav.tabs').show()
                }
            })

            async function f() {
                try {
                    var speakers = [];
                    var date_first = [];

                    $('.news-speaker').each(function () {
                        var item = $(this).text().trim()

                        if (!speakers.includes(item)) {
                            speakers.push(item)
                        }
                    })

                    $('.date-news').each(function () {
                        var item = $(this).text().trim()
                        date_first = item
                    })

                    if (speakers.length < 1) {
                        throw new Error('data_speakers is empty');
                    }

                    date_first = date_first.substr(6)
                        +'-'+ date_first.substr(3, 2)
                        +'-'+ date_first.substr(0, 2)


                    const data_all = {speakers, date_first}
                    return await Promise.resolve(data_all);

                } catch (e) {

                }

            }

            $('.block-views-blockall-materials-block-1').once('blockall-materials-active').each(function (i) {
                f().then(response => {
                    $.ajax({
                        type: 'POST',
                        url: '/path/quotes/speakers',
                        data: 'data_all='+JSON.stringify(response),
                        success: function (data) {
                            $('.quotes-speaker').html(data);
                        }
                    });
                });
            })

        }
    }
}(jQuery, Drupal, drupalSettings));
