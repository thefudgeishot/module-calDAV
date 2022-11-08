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
        $form = Form::create('calDAVPreferences', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_generate_all_delete_account_process.php');
        $row = $form->addRow()->addHeading('Delete CalDAV Account', __('Delete CalDAV Account'));
        // Mostlty stolen code from preferences.php in core
        $form->addHiddenValue('address', $session->get('address'));

        $form->addRow()->addAlert('This process will likely results in the loss of data to the aflicted user. Please confirm with the user and ensure that any essential data is backed up.', 'warning');

        $row = $form->addRow();
            $row->addLabel('username', __("Username"));
            $row->addTextField('username')->required()->readOnly()->setValue($_GET['username']);

        $row = $form->addRow();
            $row->addLabel('confirmationVar', __("Type 'CONFIRM' to confirm your action."));
            $row->addTextField('confirmationVar')->required();

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
