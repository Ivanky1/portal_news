<?php

namespace Drupal\form_exposed_views_custom;

class HelperMaterialsBase {

    /**
     * @param $type
     * @param string $filter_date
     * @param string $limit
     * @return mixed
     */
    public static function getNewsWithData($type, $filter_data = [], $limit = '') {
        $q = \Drupal::database()->select('node_field_data', 'n');
        $q->join('node__field_link_to_news', 'link', 'link.entity_id = n.nid');
        $q->leftJoin('node__field_date_publication', 'date', 'date.entity_id = n.nid');
        $q->leftJoin('node__body', 'body', 'body.entity_id = n.nid');
        $q->leftJoin('node__field_tags', 'tags', 'tags.entity_id = n.nid');
        $q->leftJoin('node__field_speaker', 'speaker', 'speaker.entity_id = n.nid');
        $q->leftJoin('node__field_is_approve', 'approve', 'approve.entity_id = n.nid');
        $q->condition('n.type', $type);

        if ($type == 'material_social') {
            $q->leftJoin('node__field_img_news', 'img', 'img.entity_id = n.nid');
            $q->leftJoin('file_managed', 'file', 'file.fid = img.field_img_news_target_id');
            $q->join('node__field_social_network', 'social', 'social.entity_id = n.nid');
            $q->fields('file', ['uri']);
            $q->fields('social', ['field_social_network_value']);
        }

        if (count($filter_data) > 0) {
            if (isset($filter_data['date_first']) != '') {
                $q->condition('date.field_date_publication_value', $filter_data['date_first'], '<');
            }

            if (isset($filter_data['speakers']) != '') {
                $q->condition('speaker.field_speaker_value', $filter_data['speakers'], 'in');
            }
        }

        $q->fields('n', ['nid', 'title']);
        $q->fields('link', ['field_link_to_news_target_id']);
        $q->fields('date', ['field_date_publication_value']);
        $q->fields('body', ['body_value']);
        $q->fields('tags', ['field_tags_value']);
        $q->fields('speaker', ['field_speaker_value']);
        $q->fields('approve', ['field_is_approve_value']);
        $q->orderBy('date.field_date_publication_value', 'DESC');

        if ($limit != '') {
            $q->range(0, $limit);
        }

        return $q->execute()->fetchAll();
    }


    public static function getSpeakersNews($filter_date = '') {
        $q = \Drupal::database()->select('node_field_data', 'n');
        $q->join('node__field_date_publication', 'date', 'date.entity_id = n.nid');
        $q->join('node__field_speaker', 'speaker', 'speaker.entity_id = n.nid');

        if ($filter_date != '') {
            $q->condition('date.field_date_publication', $filter_date);
        }

        $q->condition('n.type', 'news');
        $q->fields('speaker', ['field_speaker_value']);
        $res = $q->execute()->fetchAll();

        $speakers_news = [];

        foreach ($res as $r) {
            $speakers_news[] = $r->field_speaker_value;
        }

        return $speakers_news;
    }

    /**
     * @param $result
     * @param $type
     * @param string $relation
     * @return array
     */
    public static function getDataOfType($result, $type, $relation = 'theme_id') {
        $news_with_data = [];

        foreach ($result as $r) {
            $url_alias = \Drupal::service('path_alias.manager')
                ->getAliasByPath('/node/'. $r->nid, 'ru');

            $data = [
                'nid' => $r->nid,
                'date_raw' => $r->field_date_publication_value,
                'date' => substr($r->field_date_publication_value, 8).
                    '.'.substr($r->field_date_publication_value, 5,2).
                    '.'.substr($r->field_date_publication_value, 0,4),
                'body' => $r->body_value,
                'tags' => $r->field_tags_value,
                'speaker' => $r->field_speaker_value,
                'title' => '<a href="'.$url_alias.'">'.$r->title.'</a>',
                'theme_id' => $r->field_link_to_news_target_id,
                'is_approve' => $r->field_is_approve_value,
            ];

            if ($type == 'material_social') {
                $img_data = [
                    '#theme' => 'image_style',
                    '#style_name' => 'medium',
                    '#uri' => $r->uri,
                ];

                $data['img'] = render($img_data);
                $data['network'] = $r->field_social_network_value;
            }

            if ($relation == 'theme_id') {
                $news_with_data[$r->field_link_to_news_target_id][] = $data;
            } elseif ($r->field_speaker_value != '') {
                $news_with_data[$r->field_speaker_value][] = $data;
            }

        }

        return $news_with_data;
    }

}