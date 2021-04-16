<?php

namespace Drupal\approve_node_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'approve node custom' Block.
 *
 * @Block(
 *   id = "approve_node_custom_block",
 *   admin_label = @Translation("Согласовать блок в нодах"),
 *   category = @Translation("approve node custom block"),
 * )
 */
class ApproveNodeBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $build = [];
        $build['#cache']['max-age'] = 0;
        $current_path = \Drupal::service('path.current')->getPath();
        $args = explode('/', $current_path);

        if (!is_numeric($args[2]) || isset($args[3])) {
          return $build;
        }

        $node = Node::load($args[2]);

        if (!isset($node->field_is_approve)) {
          return $build;
        }

        if ($args[1] == 'node') {
            $build[] = \Drupal::formBuilder()->getForm(
              'Drupal\approve_node_custom\Form\ApproveNodeForm'
            );
            return $build;

        }

        return $build;
    }
}


