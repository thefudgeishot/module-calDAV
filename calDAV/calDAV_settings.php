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
use Gibbon\Module\calDAV\Domain\CalDAVUserGateway;
use Gibbon\Forms\Form;

// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();

if (isActionAccessible($guid, $connection2, '/modules/calDAV/calDAV_settings.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $calDAVUser = $CalDAVUserGateway->selectBy(['username' => 'admin'])->fetch();
    if ($calDAVUser == false) {
        // Generate admin credentials
        $form = Form::create('calDAVPreferences', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_generate_all_generate_admin_process.php');
        $row = $form->addRow()->addHeading('Generate CalDAV Admin Credentials', __('Generate CalDAV Admin Credentials'));
        // Mostlty stolen code from preferences.php in core
        $form->addHiddenValue('address', $session->get('address'));
        $policy = getPasswordPolicy($guid, $connection2);
        if ($policy != false) {
            $form->addRow()->addAlert($policy, 'warning');
        }
        $row = $form->addRow();
            $row->addLabel('username', __("Username"));
            $row->addTextField('username')->required()->readOnly()->setValue('admin');

        $row = $form->addRow();
            $row->addLabel('email', __("Email Address"));
            $row->addTextField('email')->required();

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
        //Proceed!
        $page->breadcrumbs->add(__('CalDav Settings'));

        $form = Form::create('calDAVSettings', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_settings_process.php' );

        $form->addHiddenValue('address', $session->get('address'));

        $row = $form->addRow()->addHeading('Settings', __('Settings'));

        $settingGateway = $container->get(SettingGateway::class);
        $setting = $settingGateway->getSettingByScope('CalDAV', 'usersGenerateAccount', true);
        $row = $form->addRow();
            $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
            $row->addYesNo($setting['name'])->selected($setting['value'])->required();

        $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

        echo $form->getOutput();
    }
}