<?php

namespace Drupal\form_exposed_views_custom;


class BaseDataFilter {

    static $classes_btn = [
        'btn', 'btn-primary-gradient'
    ];

    public static function setPropertyDefault(&$form, $date_key = 'created') {
        $wrapper_key = $date_key.'_wrapper';
        $date_wrapper = $form[$wrapper_key][$date_key];
        $date_wrapper['min']['#attributes']['autocomplete'] = 'off';
        $date_wrapper['max']['#attributes']['autocomplete'] = 'off';
        $date_wrapper['min']['#placeholder'] = '01.01.2016';
        $date_wrapper['max']['#placeholder'] = date('d.m.Y');
        $form[$wrapper_key][$date_key] = $date_wrapper;
        $form['title']['#attributes']['autocomplete'] = 'off';
    }

    public static function addClassInput(&$form, $classes) {
        foreach ($classes as $key => $values) {
            $form[$key]['#attributes']['class'] = $values;
        }
    }

    public static function addClassesDefault(&$form) {
        $form['title']['#attributes']['class'] = ['search-webinar__input-text'];
        $form['actions']['submit']['#attributes']['class'] = self::$classes_btn;
        $form['actions']['reset']['#attributes']['class'] = self::$classes_btn;
    }

    public static function clearLabel(&$form) {
        $form['created']['min']['#title'] = '';
    }

}