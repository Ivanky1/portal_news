<?php

namespace Drupal\bookmark\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\config_family\IWConfigController;

/**
 * Provides a 'bookmark custom' Block.
 *
 * @Block(
 *   id = "bookmark_custom_block",
 *   admin_label = @Translation("Блок закладки"),
 *   category = @Translation("bookmark custom block"),
 * )
 */
class BookmarkBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $build['#cache']['max-age'] = 1;

        $current_path = \Drupal::service('path.current')->getPath();
        $args = explode('/', $current_path);
        $uid = \Drupal::currentUser()->id();

        if (!is_numeric($args[2]) || isset($args[3])) {
            return [];
        }

        if ($args[1] == 'node') {
            $build[] = \Drupal::formBuilder()->getForm('Drupal\bookmark\Form\BookmarkForm');
            return $build;

        } elseif ($args[1] == 'user' && $uid == $args[2]) {
            $uid = \Drupal::currentUser()->id();
            $data = [
                'url' => 'bookmark.render_list',
                'params' => ['user' => $uid],
                'text_link' => 'Посмотреть мои закладки',
            ];
            $link = IWConfigController::getUrlTransform($data);
            $html = '<div class="view-bookmark">'.$link.'</div>';
            $build['#markup'] = $html;
            return $build;
        }

        return $build;
    }
}


