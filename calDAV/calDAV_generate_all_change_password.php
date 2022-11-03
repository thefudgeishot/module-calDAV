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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Forms\Form;
if (isActionAccessible($guid, $connection2, '/modules/calDAV/calDAV_generate_all.php') == false) {
        // Access denied
        $page->addError(__('You do not have access to this action.'));
    } else {
        //Proceed!
        $form = Form::create('calDAVPreferences', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_generate_all_change_password_process.php');
        $row = $form->addRow()->addHeading('Change CalDAV Password', __('Change CalDAV Password'));
        // Mostlty stolen code from preferences.php in core
        $form->addHiddenValue('address', $session->get('address'));
        $policy = getPasswordPolicy($guid, $connection2);
        if ($policy != false) {
            $form->addRow()->addAlert($policy, 'warning');
        }
        $row = $form->addRow();
            $row->addLabel('username', __("Username"));
            $row->addTextField('username')->required()->readOnly()->setValue($_GET['username']);

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
/*
    ?>
        <script>
        const button = document.querySelector('#timetableSubManage input[type=submit]');
        button.addEventListener('click', event => {
                setTimeout(()=>{
                  tb_remove();
                },1000);
          });
        </script>
*/