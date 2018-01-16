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

class ForumUser extends ForumAppModel {

    /**
     * Force the model to act like the User model.
     *
     * @type string
     */
    public $name = USER_MODEL;
    public $alias = USER_MODEL;

    /**
     * Disable admin.
     *
     * @type bool
     */
    public $admin = false;

    /**
     * Get the newest signup.
     *
     * @return array
     */
    public function getNewestUser() {
        return $this->find('first', array(
            'order' => array('User.created' => 'DESC'),
            'limit' => 1,
            'cache' => __METHOD__
        ));
    }

    /**
     * Increase the post count.
     *
     * @param int $user_id
     * @return bool
     */
    public function increasePosts($user_id) {
        $field = Configure::read('User.fieldMap.totalPosts');

        if (!$this->hasField($field)) {
            return false;
        }

        return $this->updateAll(
            array('User.' . $field => 'User.' . $field . ' + 1'),
            array('User.id' => $user_id)
        );
    }

    /**
     * Increase the topic count.
     *
     * @param int $user_id
     * @return bool
     */
    public function increaseTopics($user_id) {
        $field = Configure::read('User.fieldMap.totalTopics');

        if (!$this->hasField($field)) {
            return false;
        }

        return $this->updateAll(
            array('User.' . $field => 'User.' . $field . ' + 1'),
            array('User.id' => $user_id)
        );
    }

    /**
     * Get who's online within the past x minutes.
     *
     * @return array
     */
    public function whosOnline() {
        $minutes = Configure::read('Forum.settings.whosOnlineInterval');
        $currentLogin = Configure::read('User.fieldMap.currentLogin');

        if (!$currentLogin) {
            return null;
        }

        return $this->find('all', array(
            'conditions' => array('User.' . $currentLogin . ' >' => date('Y-m-d H:i:s', strtotime($minutes))),
            'cache' => array(__METHOD__, $minutes),
            'cacheExpires' => '+15 minutes'
        ));
    }

}