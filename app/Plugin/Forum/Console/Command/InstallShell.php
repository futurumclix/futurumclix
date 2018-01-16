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

App::uses('BaseInstallShell', 'Utility.Console/Command');

class InstallShell extends BaseInstallShell {

    /**
     * Trigger install.
     */
    public function main() {
        $this->setSteps(array(
            'Check Database Configuration' => 'checkDbConfig',
            'Set Table Prefix' => 'checkTablePrefix',
            'Set Users Table' => 'checkUsersTable',
            'Check Table Status' => 'checkRequiredTables',
            'Create Database Tables' => 'createTables',
            'Finish Installation' => 'finish'
        ))
        ->setDbConfig(FORUM_DATABASE)
        ->setTablePrefix(FORUM_PREFIX);
        ->setRequiredTables(array('aros', 'acos', 'aros_acos'));

        $this->out('Plugin: Forum v' . Configure::read('Forum.version'));
        $this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
        $this->out('Help: http://milesj.me/code/cakephp/forum');

        parent::main();
    }

    /**
     * Finalize the installation.
     *
     * @return bool
     */
    public function finish() {
        $this->hr(1);
        $this->out('Forum installation complete!');
        $this->out('Please read the documentation for further instructions:');
        $this->out('http://milesj.me/code/cakephp/forum');
        $this->hr(1);

        return true;
    }

}