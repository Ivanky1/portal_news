<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\Component\Utility\UrlHelper;
use Drupal\form_exposed_views_custom\BaseDataFilter;

class AllMaterialsFilter {

    public static function getFilter(&$form, $current_uri) {
      $form['#action'] = '';
      $form['publication']['#attributes']['autocomplete'] = 'off';
      $url_parser = UrlHelper::parse($current_uri);
      $url_parser_new = $url_parser;
      $url_query = $url_parser['query'];

      $basic_items = '';
      self::addFormListSpeaker($form);

      $is_query_publication_exists = !isset($url_query['publication'])
        || $url_query['publication'] == '';

      if ($form['publication']['#default_value'] == 'today' && $is_query_publication_exists) {
        $form['publication']['#value'] = date('d.m.Y');
      }

    /*  $date_for_link = isset($form['publication']['#value'])
        ? $form['publication']['#value'] = date('d.m.Y')
        : $url_query['publication'];

      $date_for_link = substr($date_for_link, 6).
            '-'.substr($date_for_link, 3, 2).
            '-'.substr($date_for_link, 0, 2);*/

      $date_for_link = '';

      $themes_day = self::getLinksNews($date_for_link);

      self::addFormListThemesDay($form, $themes_day);
      $tags_all = self::getTagsAll();

      foreach ($tags_all as $tag) {
        $class_active = '';

        if (isset($url_query['tags']) && $url_query['tags'] == $tag) {
          $class_active = ' is-active';
        }

        $url_parser_new['query']['tags'] = str_replace(' ', '+', $tag);
        $string = '<a class="page-menu__link mr-md-3'. $class_active . '" href=":options_page">' .
        $tag . '</a>';
        $options = [
          ':options_page' => \Drupal\Core\Url::fromRoute('entity.node.canonical',
            ['node' => 1],
            ['query' => $url_parser_new['query']])->toString(),
        ];

        $basic_items .= t($string, $options);
      }

      $content = [
        '#markup' => '<div class="tags"><h2>Тэги:</h2>' . $basic_items . '</div>',
      ];

      $links_content = str_replace('/node/1', '',
        urldecode(
          render($content)
        )
      );
      $form['tags_links']['#markup'] = $links_content;
      $form['#cache']['max-age'] = 0;
    }

    public static function addFormListSpeaker(&$form) {
      $items = self::getSpeakers();
      $form['speaker'] = [];
      $options = [''=>'--Все--'];

      foreach ($items as $item) {
          $options[$item->field_speaker_value] = $item->field_speaker_value;
      }

      $form['speaker'] = [
        '#title' => 'Спикеры:',
        '#type' => 'select',
        '#default_value' => '',
        '#options' => $options
      ];
    }

    public static function addFormListThemesDay(&$form, $items) {
      $form['links'] = [];
      $options = [''=>'--Все--'];

      foreach ($items as $item) {
        $options[$item->link_to_news] = $item->title;
      }

      $form['links'] = [
        '#title' => 'Тема дня:',
        '#type' => 'select',
        '#default_value' => '',
        '#options' => $options
      ];
    }

    public static function getTagsAll() {
      $q = \Drupal::database()->select('node__field_tags', 'tags');
      $q->fields('tags', ['field_tags_value']);
      $obj_tags = $q->distinct()->execute()->fetchAll();
      $tags_value = [];

      foreach ($obj_tags as $value) {
        $tags_value[] = $value->field_tags_value;
      }

      $tags = explode(',', implode(',', $tags_value));
      $tags_all = [];

      foreach ($tags as $tag) {
        $tag = trim($tag);

        if (!in_array($tag, $tags_all)) {
          $tags_all[] = $tag;
        }

      }

      return $tags_all;
    }

    public static function getSpeakers() {
      $q = \Drupal::database()->select('node__field_speaker', 'speaker');
      $q->fields('speaker', ['field_speaker_value']);
      return $q->distinct()->execute()->fetchAll();
    }

    public static function getLinksNews($date = '') {
      $q = \Drupal::database()->select('node__field_link_to_news', 'link');
      $q->join('node_field_data', 'node', 'node.nid = link.field_link_to_news_target_id');
      $q->join('node__field_date_publication', 'date', 'date.entity_id = link.entity_id');

      if ($date != '') {
        $q->condition('date.field_date_publication_value', $date);
      }

      $q->addField('link', 'field_link_to_news_target_id', 'link_to_news');
      $q->fields('node', ['title']);
      return $q->distinct()->execute()->fetchAll();
    }

}


