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
 * @property Post $Post
 * @property Topic $Topic
 */
class PostRating extends ForumAppModel {
    public $useTable = 'forum_post_ratings';
    const UP = 1;
    const DOWN = 0;

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => USER_MODEL
        ),
        'Post' => array(
            'className' => 'Forum.Post',
            'foreignKey' => 'post_id',
        ),
        'Topic' => array(
            'className' => 'Forum.Topic',
            'foreignKey' => 'topic_id',
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-star-half-empty'
    );

    /**
     * Enum.
     *
     * @type array
     */
    public $enum = array(
        'type' => array(
            self::UP => 'UP',
            self::DOWN => 'DOWN'
        )
    );

    /**
     * Get all the rated posts within a topic.
     *
     * @param int $user_id
     * @param int $topic_id
     * @return array
     */
    public function getRatingsInTopic($user_id, $topic_id) {
        return $this->find('list', array(
            'conditions' => array(
                'PostRating.user_id' => $user_id,
                'PostRating.topic_id' => $topic_id
            ),
            'fields' => array('PostRating.id', 'PostRating.post_id')
        ));
    }

    /**
     * Check if the user has rated a post.
     *
     * @param int $user_id
     * @param int $post_id
     * @return bool
     */
    public function hasRated($user_id, $post_id) {
        return (bool) $this->find('count', array(
            'conditions' => array(
                'PostRating.user_id' => $user_id,
                'PostRating.post_id' => $post_id
            )
        ));
    }

    /**
     * Rate a post.
     *
     * @param int $user_id
     * @param int $post_id
     * @param int $topic_id
     * @param int $type
     * @return bool
     */
    public function ratePost($user_id, $post_id, $topic_id, $type) {
        $this->create();

        if ($this->save(array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'topic_id' => $topic_id,
            'type' => $type
        ), false)) {
            if ($type == self::UP) {
                $this->Post->rateUp($post_id);
            } else {
                $this->Post->rateDown($post_id);
            }

            return true;
        }

        return false;
    }

}