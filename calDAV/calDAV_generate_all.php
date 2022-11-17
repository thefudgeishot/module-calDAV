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


use Gibbon\Services\Format;
use Gibbon\Data\Validator;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\Form;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Tables\DataTable;
use Gibbon\Domain\Students\StudentGateway;
use Gibbon\Domain\Staff\StaffGateway;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Module\calDAV\Domain\CalDAVUserGateway;
use Gibbon\Module\calDAV\Domain\CalDAVPrincipalsGateway;

// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();
// Define CalDAVPrincipals gateway
$CalDAVPrincipalsGateway = $container->get(CalDAVPrincipalsGateway::class);
$criteria = $CalDAVPrincipalsGateway->newQueryCriteria()->fromPOST();

$CalDavPrincipal = $CalDAVPrincipalsGateway->selectBy(['username'])->fetch();

if (isActionAccessible($guid, $connection2, '/modules/calDAV/calDAV_generate_all.php') == false) {
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
    // calDAV sync form
    $page->breadcrumbs->add(__('calDAV Generate All'));

    $form = Form::create('calDAVSettings', $session->get('absoluteURL').'/modules/'.$session->get('module').'/calDAV_generate_all_process.php');

    $form->addHiddenValue('address', $session->get('address'));

    $row = $form->addRow()->addHeading('CalDAV Sync Users', __('CalDAV Sync Users'));
    $row = $form->addRow()->addSubHeading('This process will add/update all gibbon users into the calDAV database. It is only recommended to do this on a fresh database.', __('This process will add/update all gibbon users into the calDAV database. It is only recommended to do this on a fresh database.'));

    $row = $form->addRow();
    $row->addLabel('password', __('Default Password'))->description(__('This will be the default password for users created by this process, should be changed by the user later.'));
    $row->addTextField('password')->required();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
    // User List
    //Get action with highest precendence
    $highestAction = getHighestGroupedAction($guid, $_GET['q'], $connection2);
    if ($highestAction == false) {
        $page->addError(__('The highest grouped action cannot be determined.'));
        return;
    }
    //Proceed!

    $search = $_GET['search'] ?? '';

    // CRITERIA
    $userGateway = $container->get(UserGateway::class);
    $criteria = $userGateway->newQueryCriteria(true)
    ->searchBy($userGateway->getSearchableColumns(), $search)
    ->sortBy(['surname', 'preferredName'])
    ->fromPOST();

    $form = Form::create('filter', $gibbon->session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__('Search'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$gibbon->session->get('module').'/calDAV_generate_all.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search For'))->description(__('Preferred, surname, username, role, student ID, email, phone number'));
        $row->addTextField('search')->setValue($criteria->getSearchText());

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();


    // QUERY
    $dataSet = $userGateway->queryAllUsers($criteria);

    // Join a set of family data per user
    $people = $dataSet->getColumn('gibbonPersonID');
    $familyData = $userGateway->selectFamilyDetailsByPersonID($people)->fetchGrouped();
    $dataSet->joinColumn('gibbonPersonID', 'families', $familyData);

    // DATA TABLE
    $table = DataTable::createPaginated('userManage', $criteria);
    $table->setTitle(__('View'));

    $table->addMetaData('filterOptions', [
        'status:full'     => __('Status').': '.__('Full'),
        'status:left'     => __('Status').': '.__('Left'),
    ]);


    $table->addColumn('fullName', __('Name'))
        ->context('primary')
        ->width('33%')
        ->sortable(['surname', 'preferredName'])
        ->format(Format::using('name', ['title', 'preferredName', 'surname', 'Student', true]));

    $table->addColumn('status', __('Status'))
        ->width('33%')
        ->translatable();

    $table->addColumn('username', __('Username'))->context('primary');


    // ACTIONS

    $table->addActionColumn()
    ->format(function ($person, $actions) use ($gibbon, $highestAction, $CalDAVUserGateway, $userGateway) {
        $calDAVUser = $CalDAVUserGateway->selectBy(['username' => $person['username']])->fetch();
        $user = $userGateway->selectBy(['username' => $person['username']])->fetch();
        // Conditional formatting depending on if a password can be changed or if an account needs to be generated
        if ($calDAVUser == true) {
            $actions->addAction('password', __('Change Password'))
            ->modalWindow()
            ->setURL('/modules/calDAV/calDAV_generate_all_change_password.php')
            ->addParam('username', $person['username'])
            ->setIcon('key');

            $actions->addAction('removeAccount', __('Remove account'))
            ->modalWindow()
            ->setURL('/modules/calDAV/calDAV_generate_all_delete_account.php') 
            ->addParam('username', $person['username'])
            ->setIcon('garbage');
        }
        if ($calDAVUser == false) {
            $actions->addAction('genAccount', __('Generate Account'))
                ->modalWindow()
                ->setURL('/modules/calDAV/calDAV_generate_all_generate_account.php')
                ->addParam('username', $person['username'])
                ->addParam('email', $user['email'])
                ->setIcon('refresh');
        }
});

    echo $table->render($dataSet);
}
}