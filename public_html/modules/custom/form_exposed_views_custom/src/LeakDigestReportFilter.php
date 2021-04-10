<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\form_exposed_views_custom\BaseDataFilter;
use Drupal\Component\Utility\UrlHelper;

class LeakDigestReportFilter {

    public static function getFilter(&$form, $current_uri) {
        BaseDataFilter::setPropertyDefault($form);
        BaseDataFilter::clearLabel($form);
        BaseDataFilter::addClassesDefault($form);

        $form['title']['#attributes']['placeholder'] = 'Поиск по материалам';
        $form['type']['#options']['All'] = 'Все материалы';
        $form['links']['#markup'] = '';

        $params = \Drupal::request()->query->all();

        if (!isset($params['created']) || $params['created']['max'] == '') {
            $form['created']['max']['#value'] = date('d.m.Y');
        }

        $url_parser = UrlHelper::parse($current_uri);
        $url_parser_new = $url_parser;
        $url_query = $url_parser['query'];

        $basic_items = '';
        $arr_search = [
            "Утечки информации",
            "Аналитические отчеты",
        ];
        $arr_replace = ["Утечки", "Аналитика"];

        foreach ($form['type']['#options'] as $key => $value) {
            $class_active = '';

            if (isset($url_query['type']) && $url_query['type'] == $key) {
                $class_active = ' is-active';
            } elseif (!isset($url_query['type']) && $key == 'All') {
            	$class_active = ' is-active';
            }

            unset($url_parser_new['query']['page']);

            $url_parser_new['query']['type'] = $key;
            $value = str_replace($arr_search, $arr_replace, $value);
            $string = '<div class="page-menu__item' . $class_active . '">
                            <a class="page-menu__link mr-md-3" href=":options_page">' . $value . '</a>
                       </div>';
            $options = [
                ':options_page' => \Drupal\Core\Url::fromRoute('entity.node.canonical',
                    ['node' => 15163],
                    ['query' => $url_parser_new['query']])->toString(),
            ];
            $basic_items .= t($string, $options);
        }

        $content = [
            '#markup' => '<div class="page-menu__scroll">' . $basic_items . '</div>',
        ];

        $form['links']['#markup'] = render($content);
    }

}