<?php

namespace Drupal\form_exposed_views_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Quotes Speaker' Block.
 *
 * @Block(
 *   id = "material_create_block",
 *   admin_label = @Translation("Создание материалов блок"),
 *   category = @Translation("material create block"),
 * )
 */
class MaterialCreateBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $build = [];
        $build['#cache']['max-age'] = 0;
        $current_user = \Drupal::currentUser();

        if ($current_user->id() < 1) {
            return $build;
        }

        $build['create_material'] = [
            '#markup' => '<div class="material-create">
                            <div><strong>Создать материал</strong></div>
                            <ul class="list">
                                <li><a href="/node/add/news?type=1">Новость</a></li>
                                <li><a href="/node/add/news?type=2">История Успеха</a></li>
                                <li><a href="/node/add/material_social">Соц. сети</a></li>
                                <li><a href="/node/add/quote">Цитаты</a></li>
                            </ul>
                        </div>'
        ];

        return $build;
    }
}


