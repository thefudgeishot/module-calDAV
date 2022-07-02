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

use Gibbon\Http\Url;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Module\calDAV\Domain\CalDAVUserGateway;

include './gibbon.php';

// Most things stolen from preferencesProcess.php in core

// Define CalDAVUsers gateway
$CalDAVUserGateway = $container->get(CalDAVUserGateway::class);
$criteria = $CalDAVUserGateway->newQueryCriteria()->fromPOST();

//Check password address is not blank
$passwordNew = $_POST['passwordNew'] ?? '';
$passwordConfirm = $_POST['passwordConfirm'] ?? '';

//Variables
$realm = 'SabreDAV'; // Should be called from sabre/dav and not defined here | SUPER DANGEROUS

//Check passwords are not blank
if ($passwordNew == '' or $passwordConfirm == '') {
    header("Location: {$URL->withReturn('error1')}");
} else {
    //Check strength of password
    $passwordMatch = doesPasswordMatchPolicy($connection2, $passwordNew);

    if ($passwordMatch == false) {
        header("Location: {$URL->withReturn('error6')}");
    } else {
        //Check new passwords match
        if ($passwordNew != $passwordConfirm) {
            header("Location: {$URL->withReturn('error4')}");
        } else {
            //Hash and write password
            $hash = md5($user['username'] . ':' . $realm . ':' . $password); // Hashes the salted password
            $user = $CalDAVUserGateway->selectBy([$gibbon->session->get('username') => $username])->fetch();
            $update = $CalDAVUserGateway->update($user, ['digesta1' => $hash]);
            //Success!
            header("Location: {$URL->withReturn('success0')}");
        }
    }
}
