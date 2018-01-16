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

/**
 * @property ForumToolbarComponent $ForumToolbar
 * @property AdminToolbarComponent $AdminToolbar
 */
class ForumAppController extends AppController {

    /**
     * Remove parent models.
     *
     * @type array
     */
    public $uses = array();

    /**
     * Components.
     *
     * @type array
     */
    public $components = array(
        'Session', 'Security', 'Cookie', 'Acl',
        'Auth' => array(
            'authorize' => array('Controller')
        ),
        'Forum.ForumToolbar',
        'Forum.AdminToolbar',
    );

    /**
     * Helpers.
     *
     * @type array
     */
    public $helpers = array(
        'Html', 'Session', 'Time', 'Text',
        'Utility.Breadcrumb', 'Utility.OpenGraph',
        'Utility.Utility', 'Utility.Decoda',
        'Forum.Forum',
        'Form' => array(
            'className' => 'AppForm',
        ),
    );

    /**
     * Plugin configuration.
     *
     * @type array
     */
    public $config = array();

    /**
     * Database forum settings.
     *
     * @type array
     */
    public $settings = array();

    /**
     * Validate the user has the correct ACL permissions.
     *
     * @param array $user
     * @return bool
     * @throws UnauthorizedException
     */
    public function isAuthorized($user) {
        if ($this->Session->read('Acl.isSuper')) {
            return true;
        }

        $controller = strtolower($this->name);
        $action = $this->request->params['action'];
        $model = 'Forum.';

        // Change to polls when applicable
        if (isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] === 'poll') {
            $controller = 'polls';
        }

        // Allow for controllers that don't have ACL
        if (!in_array($controller, array('stations', 'topics', 'posts', 'polls'))) {
            return true;
        }

        switch ($controller) {
            case 'stations':    $model .= 'Forum'; break;
            case 'topics':        $model .= 'Topic'; break;
            case 'posts':        $model .= 'Post'; break;
            case 'polls':        $model .= 'Poll'; break;
        }

        // Validate based on action
        switch ($action) {

            // Allow if the user moderates
            case 'moderate':
                if ($this->Session->read('Forum.moderates')) {
                    return true;
                }
            break;

            // Check individual permissions
            case 'add':
            case 'view':
            case 'edit':
            case 'delete':
                $crud = array(
                    'add' => 'create',
                    'view' => 'read',
                    'edit' => 'update',
                    'delete' => 'delete'
                );

                $has = $this->AdminToolbar->hasAccess($model, $crud[$action], 'Forum.permissions', true);

                // If permission doesn't exist, they have it by default
                if ($has === null || $has) {
                    return true;
                }
            break;

            // All other actions should be available
            default:
                return true;
            break;
        }

        throw new UnauthorizedException();
    }

    /**
     * Before filter.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        if($this->request->prefix != 'admin' && !Configure::read('Forum.active')) {
           throw new NotFoundException(__d('forum', 'Forum is disabled'));
        }

        if(Configure::read('disableSSLForum')) {
           if($this->request->is('ssl')) {
               return $this->redirect('http://'.env('SERVER_NAME').$this->here);
           }
        } elseif(Configure::read('forceSSL')) {
           $this->Security->requireSecure();
        }

        $this->set('menuTab', '');

        if($this->Auth->loggedIn()) {
            $statusField = Configure::read('User.fieldMap.status');
            $actStatus = $this->Auth->user($statusField);
            $userModel = ClassRegistry::init(USER_MODEL);
            $userModel->contain();
            $user = $userModel->findById($this->Auth->user('id'), array(
                  $statusField,
            ));
            if(!empty($user) && $user['User'][$statusField] != $actStatus) {
                  $this->Session->write('Auth.User.'.$statusField, $user['User'][$statusField]);  
            }
            if($actStatus == Configure::read('User.statusMap.banned')) {
                  $this->Notice->info(__d('forum', 'Your account is banned.'));
            }
        }

        // Settings
        $this->config = Configure::read();
        $this->settings = Configure::read('Forum.settings');
        if(!isset($this->params['prefix']) || $this->params['prefix'] != 'admin') {
            $this->layout = $this->config['Forum']['viewLayout'];
        }
    }

    /**
     * Before render.
     */
    public function beforeRender() {
        parent::beforeRender();

        $this->set('user', $this->Auth->user());
        $this->set('userFields', $this->config['User']['fieldMap']);
        $this->set('userRoutes', $this->config['User']['routes']);
        $this->set('config', $this->config);
        $this->set('settings', $this->settings);
    }

}
