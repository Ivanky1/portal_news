<?php

namespace Drupal\form_exposed_views_custom;

use Drupal\form_exposed_views_custom\BaseDataFilter;

class EventsPlanningFilter {

    /**
     * @param $form
     */
    public static function getFilter(&$form) {
        if (
            (!\Drupal::request()->query->has('created')
                || \Drupal::request()->query->get('created')['max'] == ''
            )
            &&  $max_date_event = self::getLastDateEvent()) {
            $form['created']['max']['#value'] = date('d.m.Y', strtotime($max_date_event. '+1 days'));
        }

        BaseDataFilter::setPropertyDefault($form);
        BaseDataFilter::addClassesDefault($form);
    }

    /**
     * @return mixed
     */
    public static function getLastDateEvent() {
        $q = \Drupal::database()->select('node_field_data', 'node');
        $q->join('node__field_date_webinar', 'date', 'date.entity_id = node.nid');
        $q->condition('node.status', 1);
        $q->fields('date', ['field_date_webinar_value']);
        $q->orderBy('date.field_date_webinar_value', 'DESC');
        $q->range(0, 1);
        return $q->execute()->fetchField();
    }
}


