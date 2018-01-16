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

define('EXCERPT_LENGTH', Configure::read('Forum.settings.excerptLength'));

/**
 * @property User $User
 * @property Forum $Forum
 * @property Poll $Poll
 * @property Post $FirstPost
 * @property Post $LastPost
 * @property User $LastUser
 */
class Topic extends ForumAppModel {
    public $useTable = 'forum_topics';
    /**
     * Type constants.
     */
    const NORMAL = 0;
    const STICKY = 1;
    const IMPORTANT = 2;
    const ANNOUNCEMENT = 3;

    /**
     * Behaviors
     *
     * @type array
     */
    public $actsAs = array(
        'Utility.Sluggable' => array(
            'length' => 100
        )
    );

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => USER_MODEL,
            'counterCache' => true
        ),
        'Forum' => array(
            'className' => 'Forum.Forum',
            'counterCache' => true,
            'foreignKey' => 'forum_id',
        ),
        'FirstPost' => array(
            'className' => 'Forum.Post',
            'foreignKey' => 'firstPost_id'
        ),
        'LastPost' => array(
            'className' => 'Forum.Post',
            'foreignKey' => 'lastPost_id'
        ),
        'LastUser' => array(
            'className' => USER_MODEL,
            'foreignKey' => 'lastUser_id'
        )
    );

    /**
     * Has one.
     *
     * @type array
     */
    public $hasOne = array(
        'Poll' => array(
            'className' => 'Forum.Poll',
            'foreignKey' => 'topic_id',
            'dependent' => true
        )
    );

    /**
     * Has many.
     *
     * @type array
     */
    public $hasMany = array(
        'Post' => array(
            'className' => 'Forum.Post',
            'limit' => 100,
            'order' => array('Post.created' => 'DESC'),
            'exclusive' => true,
            'dependent' => true,
            'foreignKey' => 'topic_id',
        ),
        'Subscription' => array(
            'className' => 'Forum.Subscription',
            'limit' => 100,
            'exclusive' => true,
            'dependent' => true,
            'foreignKey' => 'topic_id',
        )
    );

    /**
     * Validation.
     *
     * @type array
     */
    public $validations = array(
        'default' => array(
            'title' => array(
                'rule' => 'notBlank'
            ),
            'excerpt' => array(
                'notBlank' => array(
                    'rule' => 'notBlank'
                ),
                'maxLength' => array(
                    'rule' => array('maxLength', EXCERPT_LENGTH)
                )
            ),
            'forum_id' => array(
                'rule' => 'notBlank',
            ),
            'user_id' => array(
                'rule' => 'notBlank',
            ),
            'status' => array(
                'rule' => 'notBlank',
            ),
            'type' => array(
                'rule' => 'notBlank',
            ),
            'expires' => array(
                'rule' => 'numeric',
                'message' => 'Expiration must be a numerical value for days',
                'allowEmpty' => true
            ),
            'options' => array(
                'checkOptions' => array(
                    'rule' => array('checkOptions'),
                    'message' => 'You must supply a minimum of 2 options and a max of 10'
                ),
                'notBlank' => array(
                    'rule' => 'notBlank',
                    'message' => 'Please supply some answer options for your poll'
                )
            ),
            'content' => array(
                'rule' => 'notBlank'
            )
        )
    );

    /**
     * Enum.
     *
     * @type array
     */
    public $enum = array(
        'type' => array(
            self::NORMAL => 'NORMAL',
            self::STICKY => 'STICKY',
            self::IMPORTANT => 'IMPORTANT',
            self::ANNOUNCEMENT => 'ANNOUNCEMENT'
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-comment',
        'editorFields' => array('excerpt'),
        'paginate' => array(
            'order' => array('Topic.id' => 'DESC')
        )
    );

    public $reportActions = array(
        'delete' => 'delete',
        'close' => 'close',
    );

    public $reportViewURL = 'getViewURL';

    public function getViewURL($id) {
      $this->id = $id;
      $this->contain();
      $this->read(array('slug'));
      return array('admin' => false, 'plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $this->data[$this->alias]['slug']);
    }

    /**
     * Validate and add a topic.
     *
     * @param array $data
     * @return bool|int
     */
    public function addTopic($data) {
        $this->set($data);

        if ($this->validates()) {
            $isSuper = $this->Session->read('Acl.isSuper');
            $settings = Configure::read('Forum.settings');

            if (($secondsLeft = $this->checkFlooding($settings['topicFloodInterval'])) > 0 && !$isSuper) {
                return $this->invalid('title', 'You must wait %s more second(s) till you can post a topic', $secondsLeft);

            } else if ($this->checkHourly($settings['topicsPerHour']) && !$isSuper) {
                return $this->invalid('title', 'You are only allowed to post %s topic(s) per hour', $settings['topicsPerHour']);

            } else {
                $this->create();
                $this->save($data, false, array('forum_id', 'user_id', 'title', 'slug', 'excerpt', 'status', 'type'));

                $this->User->contain(array('ActiveMembership' => array('Membership' => array('points_enabled', 'points_per_topic'))));
                $user = $this->User->findById($data['user_id'], array('id'));
                if($user['ActiveMembership']['Membership']['points_enabled']) {
                  $this->User->pointsAdd($user['ActiveMembership']['Membership']['points_per_topic'], $user['User']['id']);
                }

                $data['topic_id'] = $this->id;
                $data['post_id'] = $this->Post->addFirstPost($data);

                $this->update($data['topic_id'], array(
                    'firstPost_id' => $data['post_id'],
                    'lastPost_id' => $data['post_id'],
                    'lastUser_id' => $data['user_id'],
                ));

                $this->Forum->chainUpdate($data['forum_id'], array(
                    'lastTopic_id' => $data['topic_id'],
                    'lastPost_id' => $data['post_id'],
                    'lastUser_id' => $data['user_id'],
                ));

                if (isset($data['options'])) {
                    $this->Poll->addPoll($data);
                }

                // Subscribe
                if ($settings['autoSubscribeSelf']) {
                    $this->Subscription->subscribeToTopic($data['user_id'], $data['topic_id']);
                }

                return $data['topic_id'];
            }
        }

        return false;
    }

    /**
     * Check the posting flood interval.
     *
     * @param int $interval
     * @return bool|int
     */
    public function checkFlooding($interval) {
        if ($topics = $this->Session->read('Forum.topics')) {
            $timeLeft = time() - array_pop($topics);

            if ($timeLeft <= $interval) {
                return $interval - $timeLeft;
            }
        }

        return false;
    }

    /**
     * Check the hourly posting.
     *
     * @param int $max
     * @return bool
     */
    public function checkHourly($max) {
        $pastHour = strtotime('-1 hour');

        if ($topics = $this->Session->read('Forum.topics')) {
            $count = 0;

            foreach ($topics as $time) {
                if ($time >= $pastHour) {
                    ++$count;
                }
            }

            if ($count >= $max) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check to make sure the poll is valid.
     *
     * @param array $data
     * @return bool
     */
    public function checkOptions($data) {
        $data = array_values($data);
        $options = explode("\n", $data[0]);
        $clean = array();

        if ($options) {
            foreach ($options as $option) {
                if ($option !== '') {
                    $clean[] = $option;
                }
            }
        }

        $total = count($clean);

        return ($total >= 2 && $total <= 10);
    }

    /**
     * Close a topic.
     *
     * @param int $id
     * @return bool
     */
    public function close($id) {
        $this->id = $id;

        return $this->saveField('status', self::CLOSED);
    }

    /**
     * Finds difference in days between dates.
     *
     * @param int $start
     * @param int $finish
     * @return int
     */
    public function daysBetween($start, $finish) {
        if (!is_int($start)) {
            $start = strtotime($start);
        }

        if (!is_int($finish)) {
            $finish = strtotime($finish);
        }

        $diff = $finish - $start;
        $days = $diff / 86400;

        return round($days);
    }

    /**
     * Robust method for saving all topic data.
     *
     * @param int $id
     * @param array $topic
     * @return bool
     */
    public function editTopic($id, $topic) {
        if ($topic) {
            foreach ($topic as $model => $data) {
                if ($model === 'Topic') {
                    $this->id = $id;
                    $this->save($data, false);

                } else if ($model === 'FirstPost') {
                    $this->Post->id = $data['id'];
                    $this->Post->save($data, false, array('content'));

                } else if ($model === 'Poll') {
                    $data['expires'] = !empty($data['expires']) ? date('Y-m-d H:i:s', strtotime('+' . $data['expires'] . ' days')) : null;

                    $this->Poll->id = $data['id'];
                    $this->Poll->save($data, false, array('expires'));

                    if (!empty($data['PollOption'])) {
                        foreach ($data['PollOption'] as $option) {
                            if ($option['delete']) {
                                $this->Poll->PollOption->delete($option['id'], true);

                            } else if ($option['option'] !== '') {
                                $this->Poll->PollOption->id = $option['id'];
                                $this->Poll->PollOption->save($option, false, array('option', 'poll_vote_count'));
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get all info for reading a topic.
     *
     * @param string $slug
     * @return array
     */
    public function getBySlug($slug) {
        $topic = $this->find('first', array(
            'conditions' => array('Topic.slug' => $slug),
            'contain' => array(
                'FirstPost',
                'Forum' => array('Parent'),
                'Poll' => array('PollOption')
            ),
            'cache' => array(__METHOD__, $slug)
        ));

        if (!empty($topic['Poll']['id'])) {
            $topic['Poll'] = $this->Poll->process($topic['Poll']);
        }

        return $topic;
    }

    /**
     * Get the latest topics.
     *
     * @param int $limit
     * @return array
     */
    public function getLatest($limit = 10) {
        return $this->find('all', array(
            'order' => array('Topic.created' => 'DESC'),
            'contain' => array('User', 'LastPost', 'FirstPost'),
            'limit' => $limit,
            'cache' => array(__METHOD__, $limit)
        ));
    }

    /**
     * Get the latest topics by a user.
     *
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function getLatestByUser($user_id, $limit = 10) {
        return $this->find('all', array(
            'conditions' => array('Topic.user_id' => $user_id),
            'order' => array('Topic.created' => 'DESC'),
            'contain' => array('LastPost', 'LastUser'),
            'limit' => $limit,
            'cache' => array(__METHOD__, $user_id, $limit)
        ));
    }

    /**
     * Get the latest topics in a forum.
     *
     * @param int $forum_id
     * @param int $limit
     * @return array
     */
    public function getLatestInForum($forum_id, $limit = 10) {
        return $this->find('all', array(
            'conditions' => array('Topic.forum_id' => $forum_id),
            'order' => array('Topic.created' => 'DESC'),
            'contain' => array('LastPost', 'LastUser'),
            'limit' => $limit,
            'cache' => array(__METHOD__, $forum_id, $limit)
        ));
    }

    /**
     * Return popular topics where the first post has a high rating score.
     *
     * @param string $timeframe
     * @param int $limit
     * @return array
     */
    public function getPopularTopics($timeframe = '-7 days', $limit = 10) {
        return $this->find('all', array(
            'conditions' => array(
                'Topic.created >=' => date('Y-m-d H:i:s', strtotime($timeframe)),
                'Topic.status' => self::OPEN
            ),
            'order' => array('FirstPost.score' => 'DESC'),
            'contain' => array('FirstPost', 'User'),
            'limit' => $limit,
            'cache' => array(__METHOD__, $timeframe, $limit)
        ));
    }

    /**
     * Get all high level topics within a forum.
     *
     * @param int $forum_id
     * @return array
     */
    public function getStickiesInForum($forum_id) {
        return $this->find('all', array(
            'order' => array('Topic.type' => 'DESC'),
            'conditions' => array(
                'OR' => array(
                    array('Topic.type' => self::ANNOUNCEMENT),
                    array(
                        'Topic.forum_id' => $forum_id,
                        'Topic.type' => array(self::STICKY, self::IMPORTANT)
                    )
                )
            ),
            'contain' => array('User', 'LastPost', 'LastUser', 'Poll'),
            'cache' => array(__METHOD__, $forum_id)
        ));
    }

    /**
     * Increase the view count.
     *
     * @param int $id
     * @return bool
     */
    public function increaseViews($id) {
        return $this->updateAll(
            array('Topic.view_count' => 'Topic.view_count + 1'),
            array('Topic.id' => $id)
        );
    }

    /**
     * Move all topics to a new forum.
     *
     * @param int $start_id
     * @param int $moved_id
     * @return bool
     */
    public function moveAll($start_id, $moved_id) {
        $this->Post->moveAll($start_id, $moved_id);

        return $this->updateAll(
            array('Topic.forum_id' => $moved_id),
            array('Topic.forum_id' => $start_id)
        );
    }

    /**
     * Open a topic.
     *
     * @param int $id
     * @return bool
     */
    public function open($id) {
        $this->id = $id;

        return $this->saveField('status', self::OPEN);
    }

    /**
     * Sticky a topic.
     *
     * @param int $id
     * @return bool
     */
    public function sticky($id) {
        $this->id = $id;

        return $this->saveField('type', self::STICKY);
    }

    /**
     * Unsticky a topic.
     *
     * @param int $id
     * @return bool
     */
    public function unsticky($id) {
        $this->id = $id;

        return $this->saveField('type', self::NORMAL);
    }

    /**
     * Parse the HTML version.
     *
     * @param array $options
     * @return bool
     */
    public function beforeSave($options = array()) {
        return $this->validateDecoda('Topic');
    }

    /**
     * After find.
     *
     * @param array $results
     * @param bool $primary
     * @return array
     */
    public function afterFind($results, $primary = false) {
        if ($results) {
            $settings = Configure::read('Forum.settings');
            $postsPerPage = $settings['postsPerPage'];
            $autoLock = $settings['topicDaysTillAutolock'];

            if (isset($results[0])) {
                foreach ($results as &$result) {
                    if (isset($result['Topic'])) {
                        $lock = isset($result['Forum']) ? $result['Forum']['autoLock'] : false;
                        $lastTime = null;

                        if (isset($result['LastPost'])) {
                            $lastTime = $result['LastPost']['created'];
                        } else if (isset($result['Topic']['modified'])) {
                            $lastTime = $result['Topic']['modified'];
                        }

                        if (isset($result['Topic']['post_count']) && $postsPerPage) {
                            $result['Topic']['page_count'] = ($result['Topic']['post_count'] > $postsPerPage) ? ceil($result['Topic']['post_count'] / $postsPerPage) : 1;
                        }

                        if ($lock && $lastTime && (strtotime($lastTime) < strtotime('-' . $autoLock . ' days'))) {
                            $result['Topic']['status'] = self::OPEN;
                        }
                    }
                }
            } else if (isset($results['post_count'])) {
                $results['page_count'] = ($results['post_count'] > $postsPerPage) ? ceil($results['post_count'] / $postsPerPage) : 1;
            }
        }

        return $results;
    }

    /**
     * Null associations.
     */
    public function afterDelete() {
        $this->Forum->updateAll(
            array('Forum.lastTopic_id' => null, 'Forum.lastPost_id' => null),
            array('Forum.lastTopic_id' => $this->id)
        );
    }

}
