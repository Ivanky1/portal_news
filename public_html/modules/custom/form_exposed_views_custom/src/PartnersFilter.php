<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\form_exposed_views_custom\BaseDataFilter;

class PartnersFilter {

    public static function getFilter(&$form) {
        $form['#action'] = '';
        $form['#prefix'] = '<div class="section"><div class="container"><div class="row"><div class="col-lg-8 mb-xs-5 mt-xs-10">
                                <h2>Найти партнера</h2></div><div class="col-lg-6 mb-xs-3">';
        $form['#suffix'] = '</div></div></div></div>';
        $form['country']['#options']['All'] = 'Страна';
        $form['country']['#attributes'] = [
            'class' => [
                'select2',
                'select2_filter',
                'js-select2',
                'js-choose-value',
            ],
        ];
        $form['title']['#placeholder'] = 'найти партнера';
        BaseDataFilter::addClassesDefault($form);
    }
}