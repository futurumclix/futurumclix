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
 * @property ForumUser $ForumUser
 */
class ForumController extends ForumAppController {

    /**
     * Models.
     *
     * @type array
     */
    public $uses = array('Forum.Topic', 'Forum.ForumUser');

    /**
     * Components.
     *
     * @type array
     */
    public $components = array('RequestHandler');

    /**
     * Helpers.
     *
     * @type array
     */
    public $helpers = array('Rss', 'Currency');

    /**
     * Forum index.
     */
    public function index() {
        if ($this->RequestHandler->isRss()) {
            $this->set('items', $this->Topic->getLatest());
            return;
        }

        $this->set('menuTab', 'forums');
        $this->set('forums',         $this->Topic->Forum->getIndex());
        $this->set('totalPosts',     $this->Topic->Post->getTotal());
        $this->set('totalTopics',    $this->Topic->getTotal());
        $this->set('totalUsers',     $this->ForumUser->getTotal());
        $this->set('newestUser',     $this->ForumUser->getNewestUser());
        $this->set('whosOnline',     $this->ForumUser->whosOnline());
    }

    /**
     * Help.
     */
    public function help() {
        if(!Configure::read('Forum.helpActive')) {
            $this->redirect(array('action' => 'index'));
        }

        $this->Settings = ClassRegistry::init('Settings');

        $settings = $this->Settings->fetch('Forum.help');

        $this->set('help', $settings['Settings']['Forum.help']);
        $this->set('menuTab', 'help');
    }

    /**
     * Rules.
     */
    public function rules() {
        if(!Configure::read('Forum.ToSActive')) {
            $this->redirect(array('action' => 'index'));
        }

        $this->Settings = ClassRegistry::init('Settings');

        $settings = $this->Settings->fetch('Forum.ToS');

        $this->set('rules', $settings['Settings']['Forum.ToS']);
        $this->set('menuTab', 'rules');
    }

    /**
     * Jump to a specific topic and post.
     *
     * @param int $topic_id
     * @param int $post_id
     */
    public function jump($topic_id, $post_id = null) {
        $this->ForumToolbar->goToPage($topic_id, $post_id);
    }

    /**
     * Before filter.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        if(!$this->config['Forum']['onlyLogged']) {
            $this->Auth->allow();
        }
    }

}
