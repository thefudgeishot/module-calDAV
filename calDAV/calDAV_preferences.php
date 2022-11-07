<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\Form;
use Gibbon\Module\calDAV\Domain\CalDAVUserGateway;
use Gibbon\Domain\User\UserGateway;

// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();

// Define User gateway
$userGateway = $container->get(UserGateway::class);
$criteria = $userGateway->newQueryCriteria()->fromPOST();
$users = $userGateway->selectBy(['status' => 'FULL']);

if (isActionAccessible($guid, $connection2, '/modules/calDAV/calDAV_preferences.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $user = $users->fetch();
    $calDAVUser = $CalDAVUserGateway->selectBy(['username' => $user['username']])->fetch();
    $settingGateway = $container->get(SettingGateway::class);
    $setting = $settingGateway->getSettingByScope('CalDAV', 'usersGenerateAccount', true);
    if ($calDAVUser == false && $setting['value'] == 'N') {
        // User does not have an account and can't make one
        $page->addError(__('You do not have a calDAV account, please notify your system admin if you believe this is an error.'));
    } elseif ($calDAVUser == false && $setting['value'] == 'Y') {
        // User does not have an account but can make one
        $form = Form::create('calDAVPreferences', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_preferences_generate_account_process.php');
        $row = $form->addRow()->addHeading('Generate CalDAV Account', __('Generate CalDAV Account'));
        // Mostlty stolen code from preferences.php in core
        $form->addHiddenValue('address', $session->get('address'));
        $policy = getPasswordPolicy($guid, $connection2);
        if ($policy != false) {
            $form->addRow()->addAlert($policy, 'warning');
        }
        $row = $form->addRow();
            $row->addLabel('username', __("Username"));
            $row->addTextField('username')->required()->readOnly()->setValue($user['username']);

        $row = $form->addRow();
            $row->addLabel('email', __("Email Address"));
            $row->addTextField('email')->required()->readOnly()->setValue($user['email']);

        $row = $form->addRow();
            $row->addLabel('passwordNew', __('New Password'));
            $row->addPassword('passwordNew')
                ->addPasswordPolicy($pdo)
                ->addGeneratePasswordButton($form)
                ->required()
                ->maxLength(30);
    
        $row = $form->addRow();
            $row->addLabel('passwordConfirm', __('Confirm New Password'));
            $row->addPassword('passwordConfirm')
                ->addConfirmation('passwordNew')
                ->required()
                ->maxLength(30);
    
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();
    
        echo $form->getOutput();
    } else {
        // User has an account and is trying to change password
        $page->breadcrumbs->add(__('CalDav Preferences'));

        $form = Form::create('calDAVPreferences', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_preferences_process.php');
        $row = $form->addRow()->addHeading('Change CalDAV Password', __('Change CalDAV Password'));
        // Mostlty stolen code from preferences.php in core
        $form->addHiddenValue('address', $session->get('address'));
        $policy = getPasswordPolicy($guid, $connection2);
        if ($policy != false) {
            $form->addRow()->addAlert($policy, 'warning');
        }

        $row = $form->addRow();
            $row->addLabel('passwordNew', __('New Password'));
            $row->addPassword('passwordNew')
                ->addPasswordPolicy($pdo)
                ->addGeneratePasswordButton($form)
                ->required()
                ->maxLength(30);

        $row = $form->addRow();
            $row->addLabel('passwordConfirm', __('Confirm New Password'));
            $row->addPassword('passwordConfirm')
                ->addConfirmation('passwordNew')
                ->required()
                ->maxLength(30);

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    
    }
}