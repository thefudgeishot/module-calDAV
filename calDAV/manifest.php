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
along with this program.  If not, see <http:// www.gnu.org/licenses/>.
*/

// This file describes the module, including database tables

// Basic variables
$name        = 'calDAV';            // The name of the module as it appears to users. Needs to be unique to installation. Also the name of the folder that holds the unit.
$description = 'A module that adds a calDAV backend to the gibbon platform.';            // Short text description
$entryURL    = "calDAV_preferences.php";   // The landing page for the unit, used in the main menu
$type        = "Additional";  // Do not change.
$category    = 'Other';            // The main menu area to place the module in
$version     = '0.0.95';            // Version number
$author      = 'Vaughan Lowe, Ashton Power';            // Your name
$url         = 'http://localhost/';            // Your URL

// Module tables & gibbonSettings entries
$moduleTables[] = "CREATE TABLE addressbooks (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    principaluri VARBINARY(255),
    displayname VARCHAR(255),
    uri VARBINARY(200),
    description TEXT,
    synctoken INT(11) UNSIGNED NOT NULL DEFAULT '1',
    UNIQUE(principaluri(100), uri(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"; // One array entry for every database table you need to create. Might be nice to preface the table name with the module name, to keep the db neat. 
$moduleTables[] = "CREATE TABLE cards (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    addressbookid INT(11) UNSIGNED NOT NULL,
    carddata MEDIUMBLOB,
    uri VARBINARY(200),
    lastmodified INT(11) UNSIGNED,
    etag VARBINARY(32),
    size INT(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"; // Also can be used to put data into gibbonSettings. Other sql can be run, but resulting data will not be cleaned up on uninstall.
$moduleTables[] = "CREATE TABLE addressbookchanges (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    addressbookid INT(11) UNSIGNED NOT NULL,
    uri VARBINARY(200),
    synctoken INT(11) UNSIGNED NOT NULL,
    operation VARCHAR(10),
    time INT(11) UNSIGNED NOT NULL,
    INDEX(addressbookid),
    INDEX(synctoken)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$moduleTables[] = "CREATE TABLE calendarobjects (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    calendardata MEDIUMBLOB,
    uri VARBINARY(200),
    calendarid INTEGER UNSIGNED NOT NULL,
    lastmodified INT(11) UNSIGNED,
    etag VARBINARY(32),
    size INT(11) UNSIGNED NOT NULL,
    componenttype VARBINARY(8),
    firstoccurence INT(11) UNSIGNED,
    lastoccurence INT(11) UNSIGNED,
    uid VARBINARY(200),
    UNIQUE(calendarid, uri),
    INDEX calendarid_time (calendarid, firstoccurence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$moduleTables[] = "CREATE TABLE calendars (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    synctoken INTEGER UNSIGNED NOT NULL DEFAULT '1',
    components VARBINARY(21)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE calendarinstances (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    calendarid INTEGER UNSIGNED NOT NULL,
    principaluri VARBINARY(100),
    access TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 = owner, 2 = read, 3 = readwrite',
    displayname VARCHAR(100),
    uri VARBINARY(200),
    description TEXT,
    calendarorder INT(11) UNSIGNED NOT NULL DEFAULT '0',
    calendarcolor VARBINARY(10),
    timezone TEXT,
    transparent TINYINT(1) NOT NULL DEFAULT '0',
    share_href VARBINARY(100),
    share_displayname VARCHAR(100),
    share_invitestatus TINYINT(1) NOT NULL DEFAULT '2' COMMENT '1 = noresponse, 2 = accepted, 3 = declined, 4 = invalid',
    UNIQUE(principaluri, uri),
    UNIQUE(calendarid, principaluri),
    UNIQUE(calendarid, share_href)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE calendarchanges (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uri VARBINARY(200) NOT NULL,
    synctoken INT(11) UNSIGNED NOT NULL,
    calendarid INT(11) UNSIGNED NOT NULL,
    operation TINYINT(1) NOT NULL,
    INDEX calendarid_synctoken (calendarid, synctoken)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE calendarsubscriptions (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uri VARBINARY(200) NOT NULL,
    principaluri VARBINARY(100) NOT NULL,
    source TEXT,
    displayname VARCHAR(100),
    refreshrate VARCHAR(10),
    calendarorder INT(11) UNSIGNED NOT NULL DEFAULT '0',
    calendarcolor VARBINARY(10),
    striptodos TINYINT(1) NULL,
    stripalarms TINYINT(1) NULL,
    stripattachments TINYINT(1) NULL,
    lastmodified INT(11) UNSIGNED,
    UNIQUE(principaluri, uri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE schedulingobjects (
    id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    principaluri VARBINARY(255),
    calendardata MEDIUMBLOB,
    uri VARBINARY(200),
    lastmodified INT(11) UNSIGNED,
    etag VARBINARY(32),
    size INT(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE locks (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    owner VARCHAR(100),
    timeout INTEGER UNSIGNED,
    created INTEGER,
    token VARBINARY(100),
    scope TINYINT,
    depth TINYINT,
    uri VARBINARY(1000),
    INDEX(token),
    INDEX(uri(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE principals (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uri VARBINARY(200) NOT NULL,
    email VARBINARY(80),
    displayname VARCHAR(80),
    UNIQUE(uri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE groupmembers (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    principal_id INTEGER UNSIGNED NOT NULL,
    member_id INTEGER UNSIGNED NOT NULL,
    UNIQUE(principal_id, member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE TABLE propertystorage (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    path VARBINARY(1024) NOT NULL,
    name VARBINARY(100) NOT NULL,
    valuetype INT UNSIGNED,
    value MEDIUMBLOB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "CREATE UNIQUE INDEX path_property ON propertystorage (path(600), name(100));";

$moduleTables[] = "CREATE TABLE users (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARBINARY(50),
    digesta1 VARBINARY(32),
    UNIQUE(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$moduleTables[] = "INSERT INTO `gibbonSetting` (`gibbonSettingID`, `scope`, `name`, `nameDisplay`, `description`, `value`)
    VALUES
    (NULL, 'CalDAV', 'usersGenerateAccount', 'User Generated Account', 'Whether or not a user can generate their own account', FALSE)";

// Add gibbonSettings entries
//$gibbonSetting[] = ""; TODO: add settings to enable/disable calendar lol

// Action rows
// One array per action
$actionRows[] = [
    'name'                      => 'User Settings', // The name of the action (appears to user in the right hand side module menu)
    'precedence'                => '0',// If it is a grouped action, the precedence controls which is highest action in group
    'category'                  => '', // Optional: subgroups for the right hand side module menu
    'description'               => '', // Text description
    'URLList'                   => 'calDAV_preferences.php', // List of pages included in this action
    'entryURL'                  => 'calDAV_preferences.php', // The landing action for the page.
    'entrySidebar'              => 'Y', // Whether or not there's a sidebar on entry to the action
    'menuShow'                  => 'Y', // Whether or not this action shows up in menus or if it's hidden
    'defaultPermissionAdmin'    => 'Y', // Default permission for built in role Admin
    'defaultPermissionTeacher'  => 'Y', // Default permission for built in role Teacher
    'defaultPermissionStudent'  => 'Y', // Default permission for built in role Student
    'defaultPermissionParent'   => 'Y', // Default permission for built in role Parent
    'defaultPermissionSupport'  => 'Y', // Default permission for built in role Support
    'categoryPermissionStaff'   => 'Y', // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => 'Y', // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => 'Y', // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => 'Y', // Should this action be available to user roles in the Other category?
];

$actionRows[] = [
    'name'                      => 'Admin settings', // The name of the action (appears to user in the right hand side module menu)
    'precedence'                => '0',// If it is a grouped action, the precedence controls which is highest action in group
    'category'                  => '', // Optional: subgroups for the right hand side module menu
    'description'               => 'test', // Text description
    'URLList'                   => 'calDAV_settings.php', // List of pages included in this action
    'entryURL'                  => 'calDAV_settings.php', // The landing action for the page.
    'entrySidebar'              => 'Y', // Whether or not there's a sidebar on entry to the action
    'menuShow'                  => 'Y', // Whether or not this action shows up in menus or if it's hidden
    'defaultPermissionAdmin'    => 'Y', // Default permission for built in role Admin
    'defaultPermissionTeacher'  => 'N', // Default permission for built in role Teacher
    'defaultPermissionStudent'  => 'N', // Default permission for built in role Student
    'defaultPermissionParent'   => 'N', // Default permission for built in role Parent
    'defaultPermissionSupport'  => 'N', // Default permission for built in role Support
    'categoryPermissionStaff'   => 'N', // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => 'N', // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => 'N', // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => 'N', // Should this action be available to user roles in the Other category?
];
$actionRows[] = [
    'name'                      => 'CalDAV Configuration', // The name of the action (appears to user in the right hand side module menu)
    'precedence'                => '0',// If it is a grouped action, the precedence controls which is highest action in group
    'category'                  => '', // Optional: subgroups for the right hand side module menu
    'description'               => '', // Text description
    'URLList'                   => 'calDAV_generate_all.php', // List of pages included in this action
    'entryURL'                  => 'calDAV_generate_all.php', // The landing action for the page.
    'entrySidebar'              => 'Y', // Whether or not there's a sidebar on entry to the action
    'menuShow'                  => 'Y', // Whether or not this action shows up in menus or if it's hidden
    'defaultPermissionAdmin'    => 'Y', // Default permission for built in role Admin
    'defaultPermissionTeacher'  => 'N', // Default permission for built in role Teacher
    'defaultPermissionStudent'  => 'N', // Default permission for built in role Student
    'defaultPermissionParent'   => 'N', // Default permission for built in role Parent
    'defaultPermissionSupport'  => 'N', // Default permission for built in role Support
    'categoryPermissionStaff'   => 'N', // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => 'N', // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => 'N', // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => 'N', // Should this action be available to user roles in the Other category?
];

// Hooks
// $hooks[] = ''; // Serialised array to create hook and set options. See Hooks documentation online.
