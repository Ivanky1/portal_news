<?php
/**
 * @file
 * Contains \Drupal\form_modal_salesforce\Form\MiniModalForAllForm.
 */

namespace Drupal\approve_node_custom\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use \Drupal\node\Entity\Node;


/**
 * Class BookmarkForm
 *
 * @package Drupal\bookmark\Form
 */
class ApproveNodeForm extends FormBase {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'approve_node_form';
    }

    /**
     * @return string
     */
    public function getExplodePath() {
      $current_path = \Drupal::service('path.current')->getPath();
      $params = explode('/', $current_path);
      return isset($params[2]) && !isset($params[3]) ? $params[2] : '';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $nid = $this->getExplodePath();

        if (!is_numeric($nid)) {
            return '';
        }

        $node = Node::load($nid);
        $form['#data_custom'] = [
          'node_id' => $nid,
          'value' => $node->field_is_approve->value,
        ];

        if ($node->field_is_approve->value == 1) {
          $title = 'Согласовано';
          $class_name = ' yes';
        } else {
          $title = 'Проект';
          $class_name = ' no';
        }

        $form['approve_description'] = [
          '#markup' => '<div class="success-add'.$class_name.'">'.$title.'</div>'
        ];

        $form['submit'] = [
          '#id' => 'submit-bookmark',
          '#type' => 'submit',
          '#name' => 'submit',
          '#value' => 'Согласовать',
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
          '#prefix' => '<div id="approve-system-message">',
          '#suffix' => '</div>',
        ];

        return $form;
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
      $title = $form['#data_custom']['value'] ? 'Проект' : 'Согласовано';

      if ($form['#data_custom']['value'] == 1) {
        $form['#data_custom']['value'] = 0;
        $class_name = ' no';
      } else {
        $form['#data_custom']['value'] = 1;
        $class_name = ' yes';
      }

      $this->addValueApprove($form['#data_custom']);
      $message = '<div class="success-add'.$class_name.'">'.$title.'</div>';
      $response->addCommand(new ReplaceCommand('.success-add', $message));
      return $response;
    }

  /**
   * @param $data
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
    public function addValueApprove($data) {
        $node = Node::load($data['node_id']) ;
        $node->field_is_approve->value = $data['value'];
        $node->save();
    }

}
