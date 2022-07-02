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
$criteria = $userGateway->newQueryCriteria()->fromPOST();
// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();
// Define CalDAVPrincipals gateway
$CalDAVPrincipalsGateway = $container->get(CalDAVPrincipalsGateway::class);
$criteria = $CalDAVPrincipalsGateway->newQueryCriteria()->fromPOST();

// Variables
$users = $userGateway->selectBy(['status' => 'FULL']);
$realm = 'SabreDAV'; // Should be called from sabre/dav and not defined here | SUPER DANGEROUS
$password = $_POST['password'];

while ($user = $users->fetch()) {
  $hash = md5($user['username'] . ':' . $realm . ':' . $password); // Hashes the salted password
  $email = $user['email'] ; // Gets the email from the user table
  $gibbonUserTransfer = $CalDAVUserGateway->insert([
    'username'              => $user['username'],
    'digesta1'              => $hash,
  ]);
  $gibbonUserTransfer = $CalDAVPrincipalsGateway->insert([
    'uri'                   => 'principals/' . $user['username'] . '',
    'email'                 => $email,
    'displayname'           => $user['username'],
  ]);
  $gibbonUserTransfer = $CalDAVPrincipalsGateway->insert([
    'uri'                   => 'principals/' . $user['username'] . '/calendar-proxy-read',
  ]);
  // $SQL1 = 'INSERT INTO users (username, digitsta1) VALUES (' . $user['username']. ', '. $hash . ')'; // Add gibbon user to the users table with default password
  // $SQL2 = 'INSERT INTO principals (uri, email, displayname) VALUES (principals/' . $user['username'] . ', ' . $user['email'] . ', ' . $user['username'] . ')'; // Add user to the principals table
  // $SQL3 = 'INSERT INTO principals (uri) VALUES (principals/' . $user['username'] . '/calendar-proxy-read)'; // Add permissions for user to the principals table
}

$URL .= "&return=success0"; //TODO: IF THE ACCOUNT SYNC FAILS, WE MIGHT NOT WANT TO THROW A SUCCESS MESSAGE
header("Location: {$URL}");