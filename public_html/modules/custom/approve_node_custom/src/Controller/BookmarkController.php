<?php

namespace Drupal\bookmark\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;
use Drupal\config_family\IWConfigController;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Url;


/**
 * Class BookmarkController
 *
 * @package Drupal\bookmark\Controller
 */
class BookmarkController extends ControllerBase {

    /**
     * @param \Drupal\Core\Session\AccountInterface $user
     * @param \Drupal\Core\Session\AccountInterface $account
     *
     * @return mixed
     */
    public function accessBookmark(AccountInterface $user, AccountInterface $account) {
        $is_access = $account->id() == $user->id();
        return AccessResult::allowedIf($is_access);
    }

    /**
     * @param \Drupal\Core\Session\AccountInterface $user
     *
     * @return array
     */
    public function runRender(AccountInterface $user) {
        $build = [];
        $nodes = $this->getAllBookmarkForCurrentUser($user->id());


        if ($nodes == '') {
            $build = [
                '#markup' => 'У вас пока нет закладок'
            ];
            return $build;
        }

        $list_items = [];
        $list_items_delete = [];

        foreach ($nodes['nodes'] as $node) {
            $data = [
                'url' => 'entity.node.canonical',
                'params' => ['node' => $node->nid],
                'text_link' => $node->title,
            ];
            $data2 = [
                'url' => 'bookmark.delete_node',
                'params' => ['user' => $user->id(), 'node' => $nodes['bookmark_nids'][$node->nid]],
                'text_link' => 'Удалить',
            ];

            $list_items[] = IWConfigController::getUrlTransform($data);
            $list_items_delete[] = IWConfigController::getUrlTransform($data2);
        }

        $build[] = [
            '#prefix' => '<ul><li>',
            '#markup' => implode('</li><li>', $list_items),
            '#suffix' => '</li></ul>',
        ];
        $build[] = [
            '#prefix' => '<ul><li>',
            '#markup' => implode('</li><li>', $list_items_delete),
            '#suffix' => '</li></ul>',
        ];

        return $build;
    }

    /**
     * @param AccountInterface $user
     * @param NodeInterface $node
     * @return RedirectResponse
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
    public function runDelete(AccountInterface $user, NodeInterface $node) {
        if ($node->getType() != 'bookmark') {
            throw new NotFoundHttpException();
        }

        $node->delete();
        $url = Url::fromRoute('bookmark.render_list', ['user' => $user->id()])->toString();
        return new RedirectResponse($url);
    }

    /**
     * @param $uid
     *
     * @return string
     */
    public function getAllBookmarkForCurrentUser($uid) {
        $q = \Drupal::database()->select('node__field_link_to_node_bookmark', 'node_link');
        $q->innerJoin('node__field_link_to_user_bookmark', 'link_user', 'node_link.entity_id = link_user.entity_id');
        $q->condition('link_user.field_link_to_user_bookmark_target_id', $uid);
        $q->fields('node_link', ['entity_id', 'field_link_to_node_bookmark_target_id']);
        $res = $q->execute()->fetchAll();

        if (count($res) < 1) {
            return '';
        }

        $bookmark_nids = [];
        $links_to_node = [];

        foreach ($res as $r) {
            $bookmark_nids[$r->field_link_to_node_bookmark_target_id] = $r->entity_id;
            $links_to_node[] = $r->field_link_to_node_bookmark_target_id;
        }

        $q = \Drupal::database()->select('node_field_data', 'node');
        $q->condition('node.nid', $links_to_node, 'in');
        $q->fields('node', ['nid', 'title']);
        $nodes = $q->execute()->fetchAll();

        return [
            'nodes' => $nodes,
            'bookmark_nids' => $bookmark_nids
        ];
    }
}


