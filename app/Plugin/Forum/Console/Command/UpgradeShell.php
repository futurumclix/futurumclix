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

App::uses('BaseUpgradeShell', 'Utility.Console/Command');
App::uses('Admin', 'Admin.Lib');

class UpgradeShell extends BaseUpgradeShell {

    /**
     * Trigger upgrade.
     */
    public function main() {
        if (!CakePlugin::loaded('Admin')) {
            $this->err('<error>Admin plugin is not installed, aborting!</error>');
            return;
        }

        $this->setSteps(array(
            'Check Database Configuration' => 'checkDbConfig',
            'Set Table Prefix' => 'checkTablePrefix',
            'Set Users Table' => 'checkUsersTable',
            'Upgrade Version' => 'versions'
        ))
        ->setVersions(array(
            '4.0.0' => 'Admin + Utility Plugin Migration',
            '4.1.0' => 'Post Rating + Forum ACL Changes'
        ))
        ->setDbConfig(FORUM_DATABASE)
        ->setTablePrefix(FORUM_PREFIX);

        $this->out('Plugin: Forum v' . Configure::read('Forum.version'));
        $this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
        $this->out('Help: http://milesj.me/code/cakephp/forum');

        parent::main();
    }

    /**
     * Upgrade to 4.0.0.
     */
    public function to_400() {
        $this->out('<warning>This upgrade will delete the following tables after migration: settings, access, access_levels, profiles, reported.</warning>');
        $answer = strtoupper($this->in('All data will be migrated to the new admin system, are you sure you want to continue?', array('Y', 'N')));

        if ($answer === 'N') {
            exit();
        }

        // Migrate old reports to the new admin system
        $this->out('<success>Migrating reports...</success>');

        $ItemReport = ClassRegistry::init('Admin.ItemReport');

        $Reported = new AppModel(null, 'reported', $this->dbConfig);
        $Reported->alias = 'Reported';
        $Reported->tablePrefix = $this->tablePrefix;

        foreach ($Reported->find('all') as $report) {
            switch ($report['Reported']['itemType']) {
                case 1: $model = 'Forum.Topic'; break;
                case 2: $model = 'Forum.Post'; break;
                case 3: $model = $this->usersModel; break;
            }

            $ItemReport->reportItem(array(
                'reporter_id' => $report['Reported']['user_id'],
                'model' => $model,
                'foreign_key' => $report['Reported']['item_id'],
                'item' => $report['Reported']['item_id'],
                'reason' => $report['Reported']['comment'],
                'created' => $report['Reported']['created']
            ));
        }

        // Migrate profile data to users table
        $this->out('<success>Migrating user profiles...</success>');

        $User = ClassRegistry::init($this->usersModel);
        $fieldMap = Configure::read('User.fieldMap');

        $Profile = new AppModel(null, 'profiles', $this->dbConfig);
        $Profile->alias = 'Profile';
        $Profile->tablePrefix = $this->tablePrefix;

        foreach ($Profile->find('all') as $prof) {
            $query = array();

            foreach (array('signature', 'locale', 'timezone', 'totalPosts', 'totalTopics', 'lastLogin') as $field) {
                if ($key = $fieldMap[$field]) {
                    $query[$key] = $prof['Profile'][$field];
                }
            }

            if (!$query) {
                continue;
            }

            $User->id = $prof['Profile']['user_id'];
            $User->save(array_filter($query), false);
        }

        // Delete tables handled by parent shell
        $this->out('<success>Deleting old tables...</success>');

        return true;
    }

}