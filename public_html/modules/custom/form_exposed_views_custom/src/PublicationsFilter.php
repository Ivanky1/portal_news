<?php

namespace Drupal\form_exposed_views_custom;


class PublicationsFilter {

    public static function getFilter(&$form, $block_num) {
        if ($block_num == 1) {
            $form['#prefix'] = '<div class="col-lg-3 d-xs-none d-lg-block">
                            <div class="news-search pb-lg-3 pb-md-0 pb-xs-3 mb-lg-2 mb-md-4 mb-xs-3">';
            $form['#suffix'] = '</div></div>';
        } else {
            $form['#prefix'] = '<div class="news-search d-lg-none pb-lg-3 pb-md-0 pb-xs-3 mb-lg-2 mb-md-4 mb-xs-3">';
            $form['#suffix'] = '</div>';
        }

        $form['#action'] = '';
        $form['title']['#attributes']['class'] = [
            'input-text', 'news-search__input-text'
        ];
        $form['title']['#placeholder'] = 'Введите запрос';
        $form['title']['#attributes']['autocomplete'] = 'off';

        $date_class = ['input-text, input-text__date-start_m, input-text_center, js-mask'];
        $date_form_data = $form['created_wrapper']['created'];
        $form['created_wrapper']['created']['min'] = array_merge(
            $date_form_data['min'],
            self::addDataInDate('min', $date_class)
        );
        $form['created_wrapper']['created']['max'] = array_merge(
            $date_form_data['max'],
            self::addDataInDate('max', $date_class)
        );

        $form['actions']['submit']['#attributes']['class'] = [
            'btn btn-primary-gradient mt-xs-1 mt-md-0 mt-lg-1'
        ];
        $form['actions']['reset']['#attributes']['class'] = [
            'btn btn-primary-gradient mt-xs-1 mt-md-0 mt-lg-1'
        ];

        unset($form['created']['min']['#title']);

    }

    /**
     * @param $type
     * @param $class
     * @return mixed
     */
    public static function addDataInDate($type, $class) {
        if ($type == 'min') {
            $data['#placeholder'] = '01.01.2016';
            $data['#attributes']['class'] = 'mr-xs-1';
        } else {
            $data['#placeholder'] = date('d.m.Y');
        }
        $data['#attributes']['autocomplete'] = 'off';
        $data['#attributes']['class'] = $class;
        return $data;
    }

}


