<?php
/**
 * @file
 * Contains \Drupal\form_modal_salesforce\Form\MiniModalForAllForm.
 */

namespace Drupal\bookmark\Form;

use Drupal\config_family\IWConfigController;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use \Drupal\node\Entity\Node;


/**
 * Class BookmarkForm
 *
 * @package Drupal\bookmark\Form
 */
class BookmarkForm extends FormBase {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'bookmark_form';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $nid = IWConfigController::getExplodePath();

        if (!is_numeric($nid)) {
            return '';
        }

        $uid = \Drupal::currentUser()->id();
        $node = $this->getNidByNode($nid, $uid);

        if (is_null($node['entity_id'])) {
            $form['#data_custom'] = [
                'node_id' => $nid,
                'user_id' => $uid,
            ];

            $form['submit'] = [
                '#id' => 'submit-bookmark',
                '#type' => 'submit',
                '#name' => 'submit',
                '#value' => 'Добавить в закладки',
                '#ajax' => [
                    'callback' => '::ajaxSubmitCallback',
                    'event' => 'click',
                    'progress' => [
                        'type' => 'throbber',
                    ],
                ],
                '#attributes' => [
                    'class' => [
                        'btn-primary-gradient'
                    ],
                ],
                '#prefix' => '<div id="bookmark-system-message">',
                '#suffix' => '</div>',
            ];
        } else {
            $form['bookmark_exists'] = [
                '#markup' => '<div class="success-add">Новость добавлена в закладки</div>'
            ];
        }

        return $form;
    }

    /**
     * @param $table
     * @param $nid
     * @param string $uid
     *
     * @return int
     */
    protected function getCountLikesAndViewer($table, $nid, $uid = '') {
        $id = abs($nid);
        $query = \Drupal::database()->select($table, 'block');
        $query->condition('block.node_nid', $id);

        if ($uid != '') {
            $query->condition('block.user_id', $uid);
        }

        $query->fields('block');
        $result = $query->execute()->fetchAll();
        return count($result);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {}

    /**
     * AJAX callback handler that displays any errors or a success message.
     */
    public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
        $response = new AjaxResponse();

        if ($form['#data_custom'] != '') {
            $this->saveBookmark($form['#data_custom']);
        }

        $title = '<div class="success-add">Новость добавлена в закладки</div>';
        $response->addCommand(new HtmlCommand('#bookmark-system-message', $title));
        return $response;
    }

    /**
     * @param $data
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
    public function saveBookmark($data) {
        $node = Node::create([
            'type' => 'bookmark',
            'title' => 'Закладка - '.$data['node_id'].' Пользователь - '.$data['user_id'],
            'field_link_to_node_bookmark' => [
                'target_id' => $data['node_id']
            ],
            'field_link_to_user_bookmark' => [
                'target_id' => $data['user_id']
            ],
        ]);
        $node->save();
    }

    /**
     * @param $nid
     * @param $uid
     *
     * @return mixed
     */
    public function getNidByNode($nid, $uid) {
        $q = \Drupal::database()->select('node__field_link_to_node_bookmark', 'link_node');
        $q->join('node__field_link_to_user_bookmark', 'link_user', 'link_user.entity_id = link_node.entity_id');
        $q->condition('link_node.field_link_to_node_bookmark_target_id', $nid);
        $q->condition('link_user.field_link_to_user_bookmark_target_id', $uid);
        $q->fields('link_node', ['field_link_to_node_bookmark_target_id']);
        $q->fields('link_user', ['entity_id']);
        return $q->execute()->fetchAssoc();
    }
}