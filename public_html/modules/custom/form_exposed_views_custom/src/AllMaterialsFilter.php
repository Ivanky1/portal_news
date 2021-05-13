<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;

class AllMaterialsFilter {

    private static $type_all = [
        '/node/1' => 'news',
        '/social-network' => 'material_social',
        '/quotes' => 'quote',
    ];

    private static $types = [
        'Новости' => [
            'value' => '1',
            'href' => '/?types=1',
        ],
        'История успеха' => [
            'value' => '2',
            'href' => '/?types=2',
        ],
        'Соц. сети' => [
            'value' => '',
            'href' => '/social-network',
        ],
        'Цитаты' => [
            'value' => '',
            'href' => '/quotes',
        ],
    ];

    /**
     * @param $form
     * @param $current_uri
     */
    public static function getFilter(&$form, $current_uri) {
        $form['#action'] = '';
        $form['publication']['#attributes']['autocomplete'] = 'off';
        $url_parser = UrlHelper::parse($current_uri);
        $url_parser_new = $url_parser;
        $url_query = $url_parser['query'];

        $path = \Drupal::service('path.current')->getPath();
        $alias_url = \Drupal::service('path_alias.manager')->getAliasByPath($path);
        $url_query['node_type'] = self::$type_all[$alias_url];

        $speaker_items = self::getSpeakers($url_query);
        self::addFormListSpeaker($form, $speaker_items);

        $is_query_publication_exists = !isset($url_query['publication'])
        || $url_query['publication'] == '';

        if ($form['publication']['#default_value'] == 'today' && $is_query_publication_exists) {
            $form['publication']['#value'] = date('d.m.Y');
        }

        $themes_day = self::getLinksNews($url_query);
        self::addFormListThemesDay($form, $themes_day);

        $tags_all = self::getTagsAll($url_query);
        $form['tags'] = [];
        $form['tags'] = [
            '#type' => 'hidden',
        ];

        $form['types'] = [
            '#type' => 'hidden',
        ];

        $basic_items = '';


        foreach ($tags_all as $tag) {
            $class_active = '';

            if (isset($url_query['tags']) && $url_query['tags'] == $tag) {
                $class_active = ' is-active';
                $form['tags']['#value'] = $tag;
            }

            $url_parser_new['query']['tags'] = str_replace(' ', '+', $tag);
            $string = '<a class="'. $class_active . '" href=":options_page">' .
            $tag . '</a>';
            $options = [
              ':options_page' => Url::fromRoute('entity.node.canonical',
                ['node' => 1],
                ['query' => $url_parser_new['query']])->toString(),
            ];

            $basic_items .= t($string, $options);
        }

        $types_wrapper = '<div class="page-menu__scroll">';

        foreach (self::$types as $name => $param) {
            $class_active = '';

            if (isset($url_query['types']) && $param['value'] != '' && $url_query['types'] == $param['value']) {
                $class_active .= ' is-active';
            } elseif ($alias_url == $param['href']) {
                $class_active .= ' is-active';
            }

            $types_wrapper .= '<div class="page-menu__item'.$class_active.'">
                <a class="page-menu__link mr-2" href="'.$param['href'].'">'.$name.'</a>
            </div>';
        }

        $types_wrapper .= '</div>';
        $form['link_type']['#markup'] = $types_wrapper;


        $content = [
            '#markup' => '<div class="tags mt-4"><div class="tags-cloud">' .
                $basic_items . '</div></div>',
        ];

        $links_content = str_replace('/node/1', '',
            urldecode(
                render($content)
            )
        );
        $form['tags_links']['#markup'] = $links_content;
        $form['#cache']['max-age'] = 0;
    }

  /**
   * @param $form
   * @param $items
   */
    public static function addFormListSpeaker(&$form, $items) {
      $form['speaker'] = [];
      $options = ['' => '--Все--'];

      foreach ($items as $item) {
          $options[$item->field_speaker_value] = $item->field_speaker_value;
      }

      $form['speaker'] = [
        '#title' => 'Спикеры',
        '#type' => 'select2',
        '#default_value' => '',
        '#options' => $options
      ];
    }

  /**
   * @param $form
   * @param $items
   */
    public static function addFormListThemesDay(&$form, $items) {
      $form['links'] = [];
      $options = [''=>'--Все--'];

      foreach ($items as $item) {
        $options[$item->link_to_news] = $item->title;
      }

      $form['links'] = [
        '#title' => 'Тема дня',
        '#type' => 'select2',
        '#default_value' => '',
        '#options' => $options
      ];
    }

  /**
   * @return array
   */
    public static function getTagsAll($url_query) {
        $url_query['exclude'] = [];
        $url_query['exclude'][] = 'tags';
        $q = \Drupal::database()->select('node_field_data', 'node');
        $q->join('node__field_tags', 'tags', 'tags.entity_id = node.nid');
        $q->condition('node.type', $url_query['node_type']);
        self::addConditional($q, $url_query);
        $q->fields('tags', ['field_tags_value']);
        $q->fields('node', ['created']);
        $q->orderBy('node.created', 'DESC');
        $obj_tags = $q->distinct()->execute()->fetchAll();

        if (count($obj_tags) < 1) {
            return  [];
        }

        $tags_value = [];

        foreach ($obj_tags as $value) {
            $tags_value[] = $value->field_tags_value;
        }

        $tags = explode(',', implode(',', $tags_value));
        $tags_all = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if ($tag != '' && !isset($tags_all[$tag])) {
                $tags_all[$tag] = 0;
            }

            $tags_all[$tag] = $tags_all[$tag] + 1;
        }

        arsort($tags_all);

        return array_slice(array_keys($tags_all), 0, 15);
    }

    /**
    * @return mixed
    */
    public static function getSpeakers($url_query) {
        $url_query['exclude'] = [];
        $url_query['exclude'][] = 'speaker';
        $q = \Drupal::database()->select('node_field_data', 'node');
        $q->join('node__field_speaker', 'speaker', 'speaker.entity_id = node.nid');
        self::addConditional($q, $url_query);
        $q->condition('node.type', $url_query['node_type']);
        $q->fields('speaker', ['field_speaker_value']);
        return $q->distinct()->execute()->fetchAll();
    }

    /**
     * @param $q
     * @param $url_query
     */
    public static function addConditional(&$q, $url_query) {
        if (self::existsQueryParam($url_query, 'publication')) {
            $q->join('node__field_date_publication', 'date', 'date.entity_id = node.nid');
            $date_modify = substr($url_query['publication'], 6).
                '-'.substr($url_query['publication'], 3, 2).
                '-'.substr($url_query['publication'], 0, 2);
            $q->condition('date.field_date_publication_value', $date_modify);
        }

        if (self::existsQueryParam($url_query, 'speaker')) {
            $q->join('node__field_speaker', 'speaker', 'speaker.entity_id = node.nid');
            $q->condition('speaker.field_speaker_value', $url_query['speaker']);
        }

        if (self::existsQueryParam($url_query, 'links')) {
            $q->join('node__field_link_to_news', 'links', 'links.entity_id = node.nid');
            $q->condition('links.field_link_to_news_target_id', $url_query['links']);
        }

        if (self::existsQueryParam($url_query, 'types') && $url_query['types'] != 'All') {
            $q->join('node__field_type_material', 'type', 'type.entity_id = node.nid');
            $q->condition('type.field_type_material_target_id', $url_query['types']);
        }

        if (self::existsQueryParam($url_query, 'tags')) {
            $q->join('node__field_tags', 'tags', 'tags.entity_id = node.nid');
            $q->condition('tags.field_tags_value', '%'.$url_query['tags'].'%', 'like');
        }

    }

    public static function existsQueryParam($url_query, $key) {
      return isset($url_query[$key]) && $url_query[$key] != '' && !in_array($key, $url_query['exclude']);
    }

    /**
     * @param $url_query
     * @return mixed
     */
    public static function getLinksNews($url_query) {
        $url_query['exclude'] = [];
        $url_query['exclude'][] = 'links';
        $q = \Drupal::database()->select('node_field_data', 'node');
        $q->join('node__field_link_to_news', 'links', 'links.field_link_to_news_target_id = node.nid');
        self::addConditional($q, $url_query);
        $q->condition('links.bundle', $url_query['node_type']);
        $q->addField('links', 'field_link_to_news_target_id', 'link_to_news');
        $q->fields('node', ['title']);
        return $q->distinct()->execute()->fetchAll();
    }

}


