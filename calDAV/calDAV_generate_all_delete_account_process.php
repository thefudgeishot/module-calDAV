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
use Gibbon\Data\Validator;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Module\calDAV\Domain\CalDAVUserGateway;
use Gibbon\Module\calDAV\Domain\CalDAVPrincipalsGateway;

require_once '../../gibbon.php';
$URL = $session->get('absoluteURL').'/index.php?q=/modules/calDAV/calDAV_generate_all.php';

// Define user gateway
$userGateway = $container->get(UserGateway::class);
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$CalDAVPrincipalsGateway = $container->get(CalDAVPrincipalsGateway::class);

// Variables
$user = $_POST["username"];
$confirmationVar = $_POST['confirmationVar'];
$userAcc = $CalDAVUserGateway->selectBy(['username' => $user])->fetch();

if ($confirmationVar == 'CONFIRM') {

    $deleteUser = $CalDAVUserGateway->deleteWhere(['username' => $userAcc['username']]);

    $deletePrincipal = $CalDAVPrincipalsGateway->deleteWhere(['uri' => 'principals/' . $user . '']);

    $URL .= "&return=success0"; //TODO: IF THE ACCOUNT SYNC FAILS, WE MIGHT NOT WANT TO THROW A SUCCESS MESSAGE
    header("Location: {$URL}");
}

