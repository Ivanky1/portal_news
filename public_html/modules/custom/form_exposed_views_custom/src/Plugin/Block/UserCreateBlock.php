<?php

namespace Drupal\form_exposed_views_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a 'User Create' Block.
 *
 * @Block(
 *   id = "user_create_block",
 *   admin_label = @Translation("Создание пользователей блок"),
 *   category = @Translation("user create block"),
 * )
 */
class UserCreateBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $build = [];
        $build['#cache']['max-age'] = 0;
        $current_user = \Drupal::currentUser();
        $user = User::load($current_user->id());

        if (!$user->hasRole('editor') && !$user->hasRole('administrator')) {
            return $build;
        }

        $build['user_material'] = [
            '#markup' => '<div class="user-create">
                            <ul class="list">
                                <li><a href="/admin/people/create">Создать пользователя</a></li>
                            </ul>
                        </div>'
        ];

        return $build;
    }
}


