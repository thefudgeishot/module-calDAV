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

/*
if (isActionAccessible($guid, $connection2, '/modules/calDAV/calDAV_generate_all.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
*/
    //Proceed!
    $page->breadcrumbs->add(__('calDAV Generate All'));

    $form = Form::create('calDAVSettings', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_generate_all_process.php');

    $form->addHiddenValue('address', $session->get('address'));

    $row = $form->addRow()->addHeading('CalDAV Sync Users', __('CalDAV Sync Users'));
    $row = $form->addRow()->addSubHeading('This process will add/update all gibbon users into the calDAV database.', __('This process will add/update all gibbon users into the calDAV database.'));

    $row = $form->addRow();
    $row->addLabel('password', __('Default Password'))->description(__('This will be the default password for users created by this process, should be changed by the user later.'));
    $row->addTextField('password')->required();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
