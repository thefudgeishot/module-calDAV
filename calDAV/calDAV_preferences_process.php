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
$URL = $session->get('absoluteURL').'/index.php?q=/modules/calDAV/calDAV_preferences_process.php';

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
    $URL .= "&return=error1";
    header("Location: {$URL}");
} else {
    //Check strength of password
    $passwordMatch = doesPasswordMatchPolicy($connection2, $passwordNew);

    if ($passwordMatch == false) {
        $URL .= "&return=error6";
        header("Location: {$URL}");
    } else {
        //Check new passwords match
        if ($passwordNew != $passwordConfirm) {
            $URL .= "&return=error4";
            header("Location: {$URL}");
        } else {
            //Hash and write password
            $hash = md5($user['username'] . ':' . $realm . ':' . $passwordNew); // Hashes the salted password
            $user = $CalDAVUserGateway->selectBy([$gibbon->session->get('username') => $username])->fetch();
            $update = $CalDAVUserGateway->update($user, ['digesta1' => $hash]);
            //Success!
            $URL .= "&return=success0";
            header("Location: {$URL}");
        }
    }
}
