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

require_once '../../gibbon.php';
$URL = $session->get('absoluteURL').'/index.php?q=/modules/calDAV/calDAV_generate_all.php';

// Define user gateway
$userGateway = $container->get(UserGateway::class);
$criteria = $userGateway->newQueryCriteria()->fromPOST();
// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();


// Variables
$user = $_POST["username"];
$realm = 'SabreDAV'; // Should be called from sabre/dav and not defined here | SUPER DANGEROUS
$password = $_POST['passwordConfirm'];

$hash = md5($user . ':' . $realm . ':' . $password); // Hashes the salted password
$gibbonUserTransfer = $CalDAVUserGateway->insert([
'username'              => $user,
'digesta1'              => $hash,
]);

$URL .= "&return=success0"; //TODO: IF THE ACCOUNT SYNC FAILS, WE MIGHT NOT WANT TO THROW A SUCCESS MESSAGE
header("Location: {$URL}");