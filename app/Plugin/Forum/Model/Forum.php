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
define('ICONS_DIRECTORY', APP.'webroot'.DS.'img'.DS.'forum-icons'.DS);

/**
 * @property Forum $Parent
 * @property Forum $Children
 * @property Topic $Topic
 * @property Topic $LastTopic
 * @property Post $LastPost
 * @property User $LastUser
 * @property Moderator $Moderator
 * @property Subscription $Subscription
 */
class Forum extends ForumAppModel {
    public $useTable = 'forum_forums';

    /**
     * Behaviors.
     *
     * @type array
     */
    public $actsAs = array(
        'Tree',
        'Utility.Sluggable' => array(
            'length' => 100
        ),
    );

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Parent' => array(
            'className' => 'Forum.Forum',
            'foreignKey' => 'parent_id',
            'fields' => array('Parent.id', 'Parent.title', 'Parent.slug', 'Parent.parent_id')
        ),
        'LastTopic' => array(
            'className' => 'Forum.Topic',
            'foreignKey' => 'lastTopic_id'
        ),
        'LastPost' => array(
            'className' => 'Forum.Post',
            'foreignKey' => 'lastPost_id'
        ),
        'LastUser' => array(
            'className' => USER_MODEL,
            'foreignKey' => 'lastUser_id'
        ),
        'AccessRead' => array(
            'className' => 'RequestObject',
            'foreignKey' => 'accessRead',
        ),
        'AccessPost' => array(
            'className' => 'RequestObject',
            'foreignKey' => 'accessPost',
        ),
        'AccessPoll' => array(
            'className' => 'RequestObject',
            'foreignKey' => 'accessPoll',
        ),
        'AccessReply' => array(
            'className' => 'RequestObject',
            'foreignKey' => 'accessReply',
        ),
    );

    /**
     * Has many.
     *
     * @type array
     */
    public $hasMany = array(
        'Topic' => array(
            'className' => 'Forum.Topic',
            'limit' => 25,
            'order' => array('Topic.created' => 'DESC'),
            'dependent' => false,
            'foreignKey' => 'forum_id',
        ),
        'Children' => array(
            'className' => 'Forum.Forum',
            'foreignKey' => 'parent_id',
            'order' => array('Children.orderNo' => 'ASC'),
            'dependent' => false,
        ),
        'Moderator' => array(
            'className' => 'Forum.Moderator',
            'dependent' => true,
            'exclusive' => true,
            'foreignKey' => 'forum_id',
        ),
        'Subscription' => array(
            'className' => 'Forum.Subscription',
            'exclusive' => true,
            'dependent' => true,
            'foreignKey' => 'forum_id',
        ),
    );

    /**
     * Validate.
     *
     * @type array
     */
    public $validations = array(
        'default' => array(
            'title' => array(
                'rule' => 'notBlank'
            ),
            'status' =>  array(
                'rule' => 'notBlank'
            ),
            'orderNo' => array(
                'numeric' => array(
                    'rule' => 'numeric'
                ),
                'notBlank' => array(
                    'rule' => 'notBlank'
                )
            ),
            'file' => array(
                'extension' => array(
                    'rule' => array('extension', array('jpg', 'jpeg', 'png', 'gif')),
                    'message' => 'Please supply a valid image',
                    'allowEmpty' => true,
                ),
                'mimeType' => array(
                     'rule' => array('mimeType', array('image/gif', 'image/png','image/jpg','image/jpeg')),
                     'message' => 'Invalid file, only images allowed (gif, png, jpg)',
                ),
                'fileSize' => array(
                     'rule' => array('fileSize', '<=', '2MB'),
                     'message' => 'Image must be less than 2MB',
                ),
                'error' => array(
                     'rule' => 'uploadError',
                     'message' => 'Something went wrong with upload. Please try again.',
                ),
                'exists' => array(
                     'rule' => 'fileExists',
                     'message' => 'File with this name already exists',
                ),
                'finishUpload' => array(
                     'rule' => 'finishUpload',
                     'message' => 'Something went wrong with upload. Please try again.',
                     'last' => true,
                ),
            ),
         ),
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-list-alt',
        'paginate' => array(
            'order' => array('Forum.lft' => 'ASC')
        )
    );


    public function afterDelete() {
        $file = new File(ICONS_DIRECTORY.$this->data[$this->alias]['icon']);
        $file->delete();
        $file->close();
    }

    public function fileExists($check = array()) {
        return !file_exists(ICONS_DIRECTORY.$check['file']['name']);
    }

    public function finishUpload($check = array()) {
        if(!empty($check['file']['tmp_name'])) {
            if(is_uploaded_file($check['file']['tmp_name'])) {

               $this->data[$this->alias]['icon'] = $check['file']['name'];

               if(move_uploaded_file($check['file']['tmp_name'], ICONS_DIRECTORY.$this->data[$this->alias]['icon'])) {
                  return true;
               }
            }
        }
        return false;
    }

    /**
     * Update all forums by going up the parent chain.
     *
     * @param int $id
     * @param array $data
     * @return void
     */
    public function chainUpdate($id, array $data) {
        $this->id = $id;
        $this->save($data, false, array_keys($data));

        $forum = $this->getById($id);

        if ($forum['Forum']['parent_id'] != null) {
            $this->chainUpdate($forum['Forum']['parent_id'], $data);
        }
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
     * Get a forum.
     *
     * @param string $slug
     * @return array
     */
    public function getBySlug($slug) {
        $forum = $this->find('first', array(
            'conditions' => array(
                'Forum.slug' => $slug
            ),
            'contain' => array(
                'Parent',
                'Children' => array(
                    'conditions' => array(
                        'Children.status' => self::OPEN
                    ),
                    'LastTopic', 'LastPost', 'LastUser'
                ),
                'Moderator' => array('User')
            ),
            'cache' => array(__METHOD__, $slug)
        ));

        return $this->filterByRole($forum);
    }

    /**
     * Get the tree and reorganize into a hierarchy.
     * Code borrowed from TreeBehavior::generateTreeList().
     *
     * @param bool $group
     * @return array
     */
    public function getHierarchy($group = true, $includeParents = false) {
        return $this->cache(array(__METHOD__, $this->Session->read('Acl.roles'), $group), function($self) use ($group, $includeParents) {
            /** @type Forum $self */

            $keyPath = '{n}.Forum.id';
            $valuePath = array('%s%s', '{n}.tree_prefix', '{n}.Forum.title');
            $results = $self->filterByRole($self->find('all', array(
                'conditions' => array(
                    'Forum.status' => Forum::OPEN,
                    'OR' => array(
                        'Forum.parent_id' => null,
                        'Parent.status' => Forum::OPEN
                    )
                ),
                'contain' => array('Parent'),
                'order' => array('Forum.lft' => 'ASC')
            )));

            // Reorganize tree
            $stack = array();

            foreach ($results as $i => $result) {
                $count = count($stack);

                while ($stack && ($stack[$count - 1] < $result['Forum']['rght'])) {
                    array_pop($stack);
                    $count--;
                }

                $results[$i]['tree_prefix'] = str_repeat(' -- ', $count);
                $stack[] = $result['Forum']['rght'];
            }

            if (!$results) {
                return array();
            }

            $tree = Hash::combine($results, $keyPath, $valuePath);

            if (!$group) {
                return $tree;
            }

            // Reorganize the tree so top level forums are an optgroup
            $hierarchy = array();
            $parent = null;

            foreach ($tree as $key => $value) {
                // Child
                if (strpos($value, ' -- ') === 0) {
                    $hierarchy[$parent][$key] = substr($value, 4);

                // Parent
                } else {
                    $hierarchy[$value] = array();
                    $parent = $value;
                    if($includeParents) {
                       $hierarchy[$key] = $value;
                    }
                }
            }

            return $hierarchy;
        });
    }

    /**
     * Get the list of forums for the board index.
     *
     * @return array
     */
    public function getIndex() {
        $forums = $this->find('all', array(
            'order' => array('Forum.orderNo' => 'ASC'),
            'conditions' => array(
                'Forum.parent_id' => null,
                'Forum.status' => self::OPEN
            ),
            'contain' => array(
                'Children' => array(
                    'conditions' => array(
                        'Children.status' => self::OPEN
                    ),
                    'Children' => array(
                        'fields' => array('Children.id', 'Children.accessRead', 'Children.title', 'Children.slug'),
                        'conditions' => array(
                            'Children.status' => self::OPEN
                        )
                    ),
                    'LastTopic', 'LastPost', 'LastUser'
                )
            ),
            'cache' => array(__METHOD__, $this->Session->read('Acl.roles'))
        ));

        return $this->filterByRole($forums);
    }

    /**
     * Filter down the forums if the user doesn't have the specific ARO (role) access.
     *
     * @param array $forums
     * @return array
     */
    public function filterByRole($forums) {
        $roles = (array) $this->Session->read('Acl.roles');
        $isSuper = $this->Session->read('Acl.isSuper');
        $isMulti = true;

        if (!isset($forums[0])) {
            $forums = array($forums);
            $isMulti = false;
        }

        foreach ($forums as $i => $forum) {
            $aro_id = null;

            if (isset($forum['Forum']['accessRead'])) {
                $aro_id = $forum['Forum']['accessRead'];
            } else if (isset($forum['accessRead'])) {
                $aro_id = $forum['accessRead'];
            }

            // Filter down children
            if (!empty($forum['Children'])) {
                $forums[$i]['Children'] = $this->filterByRole($forum['Children']);
            }

            // Admins and super mods get full access
            if ($isSuper) {
                continue;
            }

            // Remove the forum if not enough role access
            if ($aro_id && !in_array($aro_id, array_keys($roles))) {
                unset($forums[$i]);
            }
        }

        if (!$isMulti) {
            if(empty($forums)) {
               return array();
            } else {
               return $forums[0];
            }
        }

        return array_values($forums);
    }

    /**
     * Move all categories to a new forum.
     *
     * @param int $start_id
     * @param int $moved_id
     * @return bool
     */
    public function moveAll($start_id, $moved_id) {
        return $this->updateAll(
            array('Forum.parent_id' => $moved_id),
            array('Forum.parent_id' => $start_id)
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

    public function deleteIcon($id = null) {
        if($id !== null) {
            $this->id = $id;
        }

        $this->read(array('icon'));

        $file = new File(ICONS_DIRECTORY.$this->data[$this->alias]['icon']);
        $file->delete();
        $file->close();

        return $this->saveField('icon', '');
    }

/**
 * beforeValidate callback
 *
 * If file is not submitted unset file validation rules.
 *
 * @return boolean
 */
    public function beforeValidate($options = array()) {
        if(empty($this->data['Forum']['file']['tmp_name'])) {
            unset($this->validate['file']);
        }

        return true;
    }
}
