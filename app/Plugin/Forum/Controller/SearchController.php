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
App::uses('Forum', 'Forum.Model');

/**
 * @property Topic $Topic
 */
class SearchController extends ForumAppController {

    /**
     * Models.
     *
     * @type array
     */
    public $uses = array('Forum.Topic');

    /**
     * Pagination.
     *
     * @type array
     */
    public $paginate = array(
        'Topic' => array(
            'order' => array('LastPost.created' => 'DESC'),
            'contain' => array('Forum', 'User', 'Poll', 'LastPost', 'LastUser'),
            'conditions' => array('Topic.forum_id' != null),
        )
    );

    /**
     * Search the topics.
     *
     * @param string $type
     */
    public function index($type = '') {
        $searching = false;
        $orderBy = array(
            'LastPost.created' => __d('forum', 'Last post time'),
            'Topic.created' => __d('forum', 'Topic created time'),
            'Topic.post_count' => __d('forum', 'Total posts'),
            'Topic.view_count' => __d('forum', 'Total views')
        );

        if ($this->request->params['named']) {
            foreach ($this->request->params['named'] as $field => $value) {
                $this->request->data['Topic'][$field] = urldecode($value);
            }
        }

        if ($type === 'new_posts') {
            $this->request->data['Topic']['orderBy'] = 'LastPost.created';
            $this->paginate['Topic']['conditions']['LastPost.created >='] = $this->Session->read('Forum.lastVisit');
        }

        $this->paginate['Topic']['conditions']['Topic.forum_id !='] = null;

        if ($this->request->data) {
            $searching = true;

            if (!empty($this->request->data['Topic']['keywords'])) {
                $this->paginate['Topic']['conditions']['Topic.title LIKE'] = '%' . Sanitize::clean($this->request->data['Topic']['keywords']) . '%';
            }

            if (!empty($this->request->data['Topic']['forum_id'])) {
                $this->paginate['Topic']['conditions']['Topic.forum_id'] = $this->request->data['Topic']['forum_id'];
            }

            if (!empty($this->request->data['Topic']['byUser'])) {
                $this->paginate['Topic']['conditions']['User.' . $this->config['User']['fieldMap']['username'] . ' LIKE'] = '%' . Sanitize::clean($this->request->data['Topic']['byUser']) . '%';
            }

            if (empty($this->request->data['Topic']['orderBy']) || !isset($orderBy[$this->request->data['Topic']['orderBy']])) {
                $this->request->data['Topic']['orderBy'] = 'LastPost.created';
            }

            $this->paginate['Topic']['order'] = array($this->request->data['Topic']['orderBy'] => 'DESC');
            $this->paginate['Topic']['limit'] = $this->settings['topicsPerPage'];

            $topics = $this->paginate('Topic');

            foreach($topics as $k => $topic) {
               try {
                  $this->ForumToolbar->verifyAccess(array(
                     'status' => $topic['Forum']['status'],
                     'access' => $topic['Forum']['accessRead']
                  ));
               } catch(Exception $e) {
                  unset($topics[$k]);
               }
            }
            $this->set(compact('topics'));
        }

        $this->set('menuTab', 'search');
        $this->set('searching', $searching);
        $this->set('orderBy', $orderBy);
        $this->set('forums', $this->Topic->Forum->getHierarchy());
    }

    /**
     * Proxy action to build named parameters.
     */
    public function proxy() {
        $named = array();

        if (isset($this->request->data['Search'])) {
            $this->request->data['Topic'] = $this->request->data['Search'];
        }

        foreach ($this->request->data['Topic'] as $field => $value) {
            if ($value !== '') {
                $named[$field] = urlencode($value);
            }
        }

        $this->redirect(array_merge(array('controller' => 'search', 'action' => 'index'), $named));
    }
}
