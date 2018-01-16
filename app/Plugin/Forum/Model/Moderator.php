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

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Forum $Forum
 * @property User $User
 */
class Moderator extends ForumAppModel {
    public $useTable = 'forum_moderators';
    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Forum' => array(
            'className' => 'Forum.Forum',
            'fields' => array('Forum.id', 'Forum.title', 'Forum.slug'),
            'foreignKey' => 'forum_id',
        ),
        'User' => array(
            'className' => USER_MODEL
        )
    );

    /**
     * Validation.
     *
     * @type array
     */
    public $validations = array(
        'default' => array(
            'user_id' => array(
                'notBlank' => array(
                    'rule' => 'notBlank'
                ),
                'checkUniqueMod' => array(
                    'rule' => 'checkUniqueMod',
                    'message' => 'This user is already moderating this forum'
                )
            ),
            'forum_id' => array(
                'rule' => 'notBlank'
            )
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-legal'
    );

    /**
     * Validate a user isn't already moderating a forum.
     *
     * @param array $check
     * @return bool
     */
    public function checkUniqueMod($check) {
        return !$this->isModerator($this->data[$this->alias]['user_id'], $this->data[$this->alias]['forum_id']);
    }

    /**
     * Get all forums you moderate.
     *
     * @param int $user_id
     * @return array
     */
    public function getModerations($user_id) {
        return $this->find('list', array(
            'conditions' => array('Moderator.user_id' => $user_id),
            'fields' => array('Moderator.forum_id')
        ));
    }

    /**
     * Check if the user is a moderator.
     *
     * @param int $user_id
     * @param int $forum_id
     * @return bool
     */
    public function isModerator($user_id, $forum_id) {
        return (bool) $this->find('count', array(
            'conditions' => array(
                'Moderator.user_id' => $user_id,
                'Moderator.forum_id' => $forum_id
            )
        ));
    }

}
