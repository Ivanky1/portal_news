<?php

namespace Drupal\form_exposed_views_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\form_exposed_views_custom\HelperMaterialsBase;

class QuotesSpeakerController extends ControllerBase {

    public function postAction() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo 'Error post data';
            exit();
        }

        if (!\Drupal::request()->request->has('data_all')) {
            echo 'Error post data data_speakers';
            exit();
        }

        $data_raw = \Drupal::request()->request->get('data_all');
        $data = (array) json_decode($data_raw);
        $results = HelperMaterialsBase::getNewsWithData('quote', $data, 10);
        $rows = HelperMaterialsBase::getDataOfType($results, '', 'speaker');
        echo $this->getContentHtml($rows);
        exit();
    }

    private function getContentHtml($rows) {
        $html = '<strong>Ранее сказано</strong>';

        foreach ($rows as $row) {
            foreach ($row as $r) {
                if ($r['is_approve'] == 1) {
                    $html .= ' <div class="yes t-a-r">Согласовано</div>';
                } else {
                    $html .= '<div class="no t-a-r">Проект</div>';
                }

                $html .= '<p class="date">'.$r['date'].'</p>
                      <h2>'.$r['title'].'</h2>
                      '.$r['body'];

                $html .= '<p>
                          <i>Тэги: '.$r['tags'].' </i><br/>
                          <i><strong>Спикер: '.$r['speaker'].'</strong></i>
                      </p>';
                $html .= '<p>Автор: '.$r['author'].'</p>';
            }
        }

        return $html;
    }


}
