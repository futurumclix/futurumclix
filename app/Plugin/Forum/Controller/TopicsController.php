<?php
/**
 * Copyright (c) 2018 FuturumClix
 *
 * This program is free software: you can redistribute it and/or  modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Please notice this program incorporates variety of libraries or other
 * programs that may or may not have their own licenses, also they may or
 * may not be modified by FuturumClix. All modifications made by
 * FuturumClix are available under the terms of GNU Affero General Public
 * License, version 3, if original license allows that.
 *
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('ForumAppController', 'Forum.Controller');

/**
 * @property Topic $Topic
 * @property PostRating $PostRating
 * @property Subscription $Subscription
 * @property AjaxHandlerComponent $AjaxHandler
 */
class TopicsController extends ForumAppController {

    /**
     * Models.
     *
     * @type array
     */
    public $uses = array('Forum.Topic', 'Forum.Subscription');

    /**
     * Components.
     *
     * @type array
     */
    public $components = array('Utility.AjaxHandler', 'RequestHandler', 'Report');

    /**
     * Pagination.
     *
     * @type array
     */
    public $paginate = array(
        'Post' => array(
            'order' => array('Post.created' => 'ASC'),
            'contain' => array('User')
        ),
    );

    /**
     * Helpers.
     *
     * @type array
     */
    public $helpers = array('Rss');

    /**
     * Redirect.
     */
    public function index() {
        $this->ForumToolbar->goToPage();
    }

    /**
     * Post a new topic or poll.
     *
     * @param string $slug
     * @param string $type
     * @throws NotFoundException
     */
    public function add($slug, $type = '') {
        $forum = $this->Topic->Forum->getBySlug($slug);
        $user_id = $this->Auth->user('id');

        if ($type === 'poll') {
            $pageTitle = __d('forum', 'Create Poll');
            $access = 'accessPoll';
        } else {
            $pageTitle = __d('forum', 'Create Topic');
            $access = 'accessPost';
        }

        if (!$forum) {
            throw new NotFoundException();
        }

        $this->ForumToolbar->verifyAccess(array(
            'status' => $forum['Forum']['status'],
            'access' => $forum['Forum'][$access]
        ));

        if ($this->request->data) {
            $this->request->data['Topic']['status'] = Topic::OPEN;
            $this->request->data['Topic']['user_id'] = $user_id;
            $this->request->data['Topic']['userIP'] = $this->request->clientIp();

            if ($topic_id = $this->Topic->addTopic($this->request->data['Topic'])) {
                $this->ForumToolbar->updateTopics($topic_id);
                $this->ForumToolbar->goToPage($topic_id);
            }
        } else {
            $this->request->data['Topic']['forum_id'] = $forum['Forum']['id'];
        }

        $this->set('pageTitle', $pageTitle);
        $this->set('type', $type);
        $this->set('forum', $forum);
        $this->set('forums', $this->Topic->Forum->getHierarchy());
    }

    /**
     * Edit a topic.
     *
     * @param string $slug
     * @param string $type
     * @throws NotFoundException
     */
    public function edit($slug, $type = '') {
        $topic = $this->Topic->getBySlug($slug);

        if (!$topic) {
            throw new NotFoundException();
        }

        $this->ForumToolbar->verifyAccess(array(
            'moderate' => $topic['Topic']['forum_id'],
            'ownership' => $topic['Topic']['user_id']
        ));

        if ($this->request->data) {
            if ($this->Topic->saveAll($this->request->data, array('validate' => 'only'))) {
                if ($this->Topic->editTopic($topic['Topic']['id'], $this->request->data)) {
                    $this->ForumToolbar->goToPage($topic['Topic']['id']);
                }
            }
        } else {
            if ($topic['Poll']['expires']) {
                $topic['Poll']['expires'] = $this->Topic->daysBetween($topic['Poll']['created'], $topic['Poll']['expires']);
            }

            $this->request->data = $topic;
        }

        $this->set('topic', $topic);
        $this->set('forums', $this->Topic->Forum->getHierarchy());
    }

    /**
     * Delete a topic.
     *
     * @param string $slug
     * @throws NotFoundException
     */
    public function delete($slug) {
        $topic = $this->Topic->getBySlug($slug);

        if (!$topic) {
            throw new NotFoundException();
        }

        $this->ForumToolbar->verifyAccess(array(
            'moderate' => $topic['Topic']['forum_id']
        ));

        $this->Topic->delete($topic['Topic']['id'], true);

        $this->redirect(array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
    }

    /**
     * Report a topic.
     *
     * @param string $slug
     * @throws NotFoundException
     */
    public function report($slug) {
        $topic = $this->Topic->getBySlug($slug);
        $user_id = $this->Auth->user('id');

        if (!$topic) {
            throw new NotFoundException();
        }

        if ($this->request->is('post')) {
            $data = $this->request->data['Report'];

            if ($this->Report->reportItem($data['type'], $this->Topic, $topic['Topic']['id'], $data['comment'], $user_id)) {
                $this->Session->setFlash(__d('forum', 'You have successfully reported this topic! A moderator will review this topic and take the necessary action.'), 'flash');
                unset($this->request->data['Report']);
            }
        }

        $this->set('topic', $topic);
    }

    /**
     * Read a topic.
     *
     * @param string $slug
     * @throws NotFoundException
     */
    public function view($slug) {
        $topic = $this->Topic->getBySlug($slug);
        $user_id = $this->Auth->user('id');

        if (!$topic) {
            throw new NotFoundException();
        }

        $this->ForumToolbar->verifyAccess(array(
            'status' => $topic['Forum']['status'],
            'access' => $topic['Forum']['accessRead']
        ));

        $this->paginate['Post']['limit'] = $this->settings['postsPerPage'];
        $this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

        if ($this->RequestHandler->isRss()) {
            $this->set('posts', $this->paginate('Post'));
            $this->set('topic', $topic);

            return;
        }

        $this->loadModel('Forum.PostRating');

        if (!empty($this->request->data['Poll']['option'])) {
            $this->Topic->Poll->vote($topic['Poll']['id'], $this->request->data['Poll']['option'], $user_id);
            $this->Topic->deleteCache(array('Topic::getBySlug', $slug));

            $this->redirect(array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug));
        }

        $this->ForumToolbar->markAsRead($topic['Topic']['id']);
        $this->Topic->increaseViews($topic['Topic']['id']);

        $this->paginate['Post']['contain'] = array(
            'User' => array(
               'username',
               'role',
               'email',
               'forum_statistics',
               'topic_count',
               'post_count',
               'refs_count',
               'rented_refs_count',
               'signature',
               'avatar',
               'location',
               Configure::read('User.fieldMap.status'),
               'UserStatistic' => array(
                  'total_clicks_earned',
                  'total_drefs_clicks_earned',
                  'total_rrefs_clicks_earned',
                  'total_cashouts',
                  'purchase_balance_cashouts',
                  // TODO: add autopay and autorenew
               ),
               'ActiveMembership' => array(
                  'id',
                  'Membership' => array('name'),
               ),
               'ForumModerator',
            ),
        );
        $posts = $this->paginate('Post');

        foreach($posts as &$post) {
            if($post['User']['forum_statistics']) {
               $post['User']['UserStatistic']['total_earned'] = $this->Topic->User->UserStatistic->getUserEarnings($post['User'], $post['User']['id']);
            }
        }

        $this->set(compact('topic', 'posts'));
        $this->set('subscription', $this->Subscription->isSubscribedToTopic($user_id, $topic['Topic']['id']));
        $this->set('ratings', $this->PostRating->getRatingsInTopic($user_id, $topic['Topic']['id']));
        $this->set('rss', $slug);
    }

    /**
     * Subscribe to a topic.
     *
     * @param int $id
     */
    public function subscribe($id) {
        $success = false;
        $data = __d('forum', 'Failed To Subscribe');

        if ($this->settings['enableTopicSubscriptions'] && $this->Subscription->subscribeToTopic($this->Auth->user('id'), $id)) {
            $success = true;
            $data = __d('forum', 'Subscribed');
        }

        $this->AjaxHandler->respond('json', array(
            'success' => $success,
            'data' => $data
        ));
    }

    /**
     * Unsubscribe from a topic.
     *
     * @param int $id
     */
    public function unsubscribe($id) {
        $success = false;
        $data = __d('forum', 'Failed To Unsubscribe');

        if ($this->settings['enableTopicSubscriptions'] && $this->Subscription->unsubscribe($id)) {
            $success = true;
            $data = __d('forum', 'Unsubscribed');
        }

        $this->AjaxHandler->respond('json', array(
            'success' => $success,
            'data' => $data
        ));
    }

    /**
     * Moderate a topic.
     *
     * @param string $slug
     * @throws NotFoundException
     */
    public function moderate($slug) {
        $topic = $this->Topic->getBySlug($slug);

        if (!$topic) {
            throw new NotFoundException();
        }

        $this->ForumToolbar->verifyAccess(array(
            'moderate' => $topic['Topic']['forum_id']
        ));

        if (!empty($this->request->data['Post']['items'])) {
            $items = $this->request->data['Post']['items'];
            $action = $this->request->data['Post']['action'];
            $message = null;

            foreach ($items as $post_id) {
                if (is_numeric($post_id)) {
                    if ($action === 'delete') {
                        $this->Topic->Post->delete($post_id, true);
                        $message = __d('forum', 'A total of %d post(s) have been permanently deleted');
                    }
                }
            }

            $this->Session->setFlash(sprintf($message, count($items)), 'flash');
        }

        $this->paginate['Post']['limit'] = $this->settings['postsPerPage'];
        $this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

        $this->set('topic', $topic);
        $this->set('posts', $this->paginate('Post'));
    }

    /**
     * Before filter.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        if(!$this->config['Forum']['onlyLogged']) {
           $this->Auth->allow('index', 'view', 'feed');
        }
        $this->AjaxHandler->handle('subscribe', 'unsubscribe');
        $this->Security->unlockedFields = array('option', 'items');

        $this->set('menuTab', 'forums');
    }

}
