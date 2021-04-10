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
      $tags = self::getTags();
      $basic_items = '';
      self::addFormListSpeaker($form);

      $is_query_publication_exists = !isset($url_query['publication']) || $url_query['publication'] == '';

      if ($form['publication']['#default_value'] == 'today' && $is_query_publication_exists) {
        $form['publication']['#value'] = date('d.m.Y');
      }

      foreach ($tags as $tag) {
        $class_active = '';

        if (isset($url_query['tags']) && $url_query['tags'] == $tag->field_tags_value) {
          $class_active = ' is-active';
        }

        $url_parser_new['query']['tags'] = str_replace(' ', '+', $tag->field_tags_value);
        $string = '<a class="page-menu__link mr-md-3'. $class_active . '" href=":options_page">' .
        $tag->field_tags_value . '</a>';
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

    public static function getTags() {
      $q = \Drupal::database()->select('node__field_tags', 'tags');
      $q->fields('tags', ['field_tags_value']);
      return $q->distinct()->execute()->fetchAll();
    }

    public static function getSpeakers() {
      $q = \Drupal::database()->select('node__field_speaker', 'speaker');
      $q->fields('speaker', ['field_speaker_value']);
      return $q->distinct()->execute()->fetchAll();
    }

}


