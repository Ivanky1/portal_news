<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\form_exposed_views_custom\BaseDataFilter;

class WebinarsFilter {

    public static function getFilter(&$form) {
        $form['#action'] = '';
        $form['#prefix'] = '
            <div class="section mt-md-6 mt-xs-4 mb-md-6 mb-xs-4">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-xs-12 mb-lg-4 mb-md-3 mb-xs-2">
                            <div class="section__title-line d-md-none mb-xs-2">&nbsp;</div>
                            <h2 class="mb-xs-0">Записи прошедших вебинаров</h2>
                        </div>
                    </div>
        
                    <div class="row webinar-search">
                        <div class="col-lg-12 col-xs-12"><div class="webinar-search__form">';
        $form['#suffix'] = '</div></div></div></div></div>';


        $form['title']['#placeholder'] = 'Введите запрос';

        BaseDataFilter::setPropertyDefault($form, 'date_filter');
        BaseDataFilter::addClassesDefault($form);

        unset($form['date_filter']['min']['#title']);
    }

}


