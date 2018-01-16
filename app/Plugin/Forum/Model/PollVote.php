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
 * @property User $User
 * @property Poll $Poll
 * @property PollOption $PollOption
 */
class PollVote extends ForumAppModel {
    public $useTable = 'forum_poll_votes';
    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Poll' => array(
            'className' => 'Forum.Poll',
            'foreignKey' => 'poll_id',
        ),
        'PollOption' => array(
            'className' => 'Forum.PollOption',
            'counterCache' => true,
            'foreignKey' => 'poll_option_id',
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
            'poll_id' => array(
                'rule' => 'notBlank'
            ),
            'poll_option_id' => array(
                'rule' => 'notBlank'
            ),
            'user_id' => array(
                'notBlank' => array(
                    'rule' => 'notBlank'
                ),
                'checkHasVoted' => array(
                    'rule' => 'checkHasVoted',
                    'message' => 'This user has already voted'
                )
            )
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-list-ol'
    );

    /**
     * Add a voter for a poll.
     *
     * @param int $poll_id
     * @param int $option_id
     * @param int $user_id
     * @return bool
     */
    public function addVoter($poll_id, $option_id, $user_id) {
        if ($this->hasVoted($user_id, $poll_id)) {
            return true;
        }

        $data = array(
            'poll_id' => $poll_id,
            'poll_option_id' => $option_id,
            'user_id' => $user_id
        );

        $this->create();

        return $this->save($data, false, array_keys($data));
    }

    /**
     * Validate a user hasn't voted.
     *
     * @return bool
     */
    public function checkHasVoted() {
        return !$this->hasVoted($this->data[$this->alias]['user_id'], $this->data[$this->alias]['poll_id']);
    }

    /**
     * Check to see if a person voted.
     *
     * @param int $user_id
     * @param int $poll_id
     * @return mixed
     */
    public function hasVoted($user_id, $poll_id) {
        $vote = $this->find('first', array(
            'conditions' => array(
                'PollVote.poll_id' => $poll_id,
                'PollVote.user_id' => $user_id
            ),
            'contain' => false
        ));

        if ($vote) {
            return $vote['PollVote']['poll_option_id'];
        }

        return false;
    }

}
