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

App::import('Lib', 'FAdmin');

/**
 * @property Controller $Controller
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class AdminToolbarComponent extends Component {

    /**
     * Components.
     *
     * @type array
     */
    public $components = array('Auth', 'Session');

    /**
     * Store the controller.
     *
     * @param Controller $controller
     * @throws ForbiddenException
     */
    public function startup(Controller $controller) {
        $this->Controller = $controller;

        // Set ACL session
        $user_id = $this->Auth->user('id');

        if (!$user_id || $this->Auth->user(Configure::read('User.fieldMap.status')) == Configure::read('User.statusMap.banned')) {
            return;
        }

        if (!$this->Session->check('Acl')) {
            $roles = ClassRegistry::init('RequestObject')->getRoles($user_id);
            $isAdmin = false;
            $isSuper = false;
            $roleMap = array();

            foreach ($roles as $role) {
                if (!$isSuper && $role['RequestObject']['alias'] == Configure::read('Admin.aliases.superModerator')) {
                    $isSuper = true;
                }

                $roleMap[$role['RequestObject']['id']] = $role['RequestObject']['alias'];
            }

            $this->Session->write('Acl.isSuper', $isSuper);
            $this->Session->write('Acl.roles', $roleMap);
        }

        // Set reported count
        if (Configure::read('Admin.menu.reports')) {
            Configure::write('Admin.menu.reports.count', FAdmin::introspectModel('Admin.ItemReport')->getCountByStatus());
        }
    }

/**
     * Get a list of valid containable model relations.
     * Should also get belongsTo data for hasOne and hasMany.
     *
     * @param Model $model
     * @param bool $extended
     * @return array
     */
    public function getDeepRelations(Model $model, $extended = true) {
        $contain = array_keys($model->belongsTo);
        $contain = array_merge($contain, array_keys($model->hasAndBelongsToMany));

        if ($extended) {
            foreach (array($model->hasOne, $model->hasMany) as $assocs) {
                foreach ($assocs as $alias => $assoc) {
                    $contain[$alias] = array_keys($model->{$alias}->belongsTo);
                }
            }
        }

        return $contain;
    }

    /**
     * Return a record based on ID.
     *
     * @param Model $model
     * @param int $id
     * @param bool $deepRelation
     * @return array
     */
    public function getRecordById(Model $model, $id, $deepRelation = true) {
        $model->id = $id;

        @$data = $model->find('first', array(
            'conditions' => array($model->alias . '.' . $model->primaryKey => $id),
            'contain' => $this->getDeepRelations($model, $deepRelation)
        ));

        if ($data) {
            $model->set($data);
        }

        return $data;
    }

    /**
     * Check to see if a user has specific CRUD access for a model.
     *
     * @param string $model
     * @param string $action
     * @param string $session
     * @param bool $exit - Exit early if the CRUD key doesn't exist in the session
     * @return bool
     */
    public function hasAccess($model, $action, $session = 'Admin.crud', $exit = false) {
        return FAdmin::hasAccess($model, $action, $session, $exit);
    }

    /**
     * Redirect after a create or update.
     *
     * @param Model $model
     * @param string $action
     */
    public function redirectAfter(Model $model, $action = null) {
        if (!$action) {
            $action = $this->Controller->request->data[$model->alias]['redirect_to'];
        }

        if ($action === 'parent') {
            $parentName = $this->Controller->request->data[$model->alias]['redirect_to_model'];
            $className = $model->belongsTo[$parentName]['className'];
            $foreignKey = $model->belongsTo[$parentName]['foreignKey'];
            $id = !empty($this->Controller->request->data[$model->alias][$foreignKey]) ? $this->Controller->request->data[$model->alias][$foreignKey] : null;
            $foreignModel = FAdmin::introspectModel($className);
            $url = array('plugin' => 'admin', 'controller' => 'crud', 'action' => $id ? 'read' : 'index', $id, 'model' => $foreignModel->urlSlug);

        } else {
            $url = array('plugin' => 'admin', 'controller' => 'crud', 'action' => $action, 'model' => $model->urlSlug);

            switch ($action) {
                case 'read':
                case 'update':
                case 'delete':
                    $url[] = $model->id;
                break;
            }

        }

        $this->Controller->redirect($url);
    }

    /**
     * Convenience method to set a flash message.
     *
     * @param string $message
     * @param string $type
     */
    public function setFlashMessage($message, $type = 'is-success') {
        $this->Session->setFlash($message, 'flash', array('class' => $type));
    }

}