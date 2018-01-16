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
 * @property Poll $Poll
 * @property PollVote $PollVote
 */
class PollOption extends ForumAppModel {
    public $useTable = 'forum_poll_options';
    /**
     * Display field.
     *
     * @type string
     */
    public $displayField = 'option';

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Poll' => array(
            'className' => 'Forum.Poll',
            'foreignKey' => 'poll_id',
        )
    );

    /**
     * Has many.
     *
     * @type array
     */
    public $hasMany = array(
        'PollVote' => array(
            'className' => 'Forum.PollVote',
            'limit' => 100,
            'foreignKey' => 'poll_option_id',
        )
    );

    /**
     * Behaviors.
     *
     * @type array
     */
    public $actsAs = array(
        'Utility.Filterable' => array(
            'option' => array(
                'html' => true,
                'strip' => true
            )
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
            'option' => array(
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
        'iconClass' => 'icon-list',
        'paginate' => array(
            'order' => array('PollOption.topic_id' => 'DESC', 'PollOption.id' => 'ASC')
        )
    );

}
