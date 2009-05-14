<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./initialize/db.inc.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/initialize/db.inc.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    15-Oct-02, 19:11
	// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
	//             $Author: msteffens $
	//             $Revision: 953 $

	// This file holds crucial
	// database access information.
	// Please read the notes below!

	
	// NOTE: Edit the variables '$databaseName', '$username' and '$password' to suit your setup!
	//       (Although you'll be able to use the refbase package without modifying these variables,
	//        we highly recommend not to use the default values!)
	// CAUTION: To avoid security risks you must not permit any remote user to view this file!
	//          E.g., this can be done by adjusting the config file of your server ("httpd.conf"
	//          in case of the Apache web server) to disallow viewing of "*\.inc.php" files
	//          ("Deny from all"). Please see your web server documentation for further information.

	// --------------------------------------------------------------------

	// The host name of your MySQL installation:
	$hostName = "localhost"; // e.g.: "localhost"

	// The name of the MySQL database that you're planning to use with the
	// refbase package:
	// Note: - if there's no existing database with the specified name,
	//         the 'install.php' script will create it for you
	$databaseName = "literature"; // e.g.: "literature"

	// The name of the MySQL user that's going to be used with your MySQL
	// literature database:
	// Note: - this user should be a separate MySQL user (different from the
	//         user that has full administrative privileges like the root user!)
	//       - if there's no existing MySQL user with the specified name,
	//         the 'install.php' script will create this user for you
	$username = "litwww"; // e.g.: "litwww"

	// The password by which the above MySQL user will be granted access to
	// your MySQL literature database:
	$password = "%l1t3ratur3?"; // e.g.: "%l1t3ratur3?"

	// --------------------------------------------------------------------

	// NOTE: You shouldn't change anything below this line unless you know what you're doing!

	// If you need to put the refbase tables into an existing MySQL database, you may want to
	// rename the refbase tables to avoid any name conflicts. If you do so, you need to adopt
	// the table prefix (and/or table names) accordingly. Note that you must then also change
	// the table names in files 'install.sql' and 'update.sql'.

	// The table name prefix used for refbase MySQL tables:
	$tablePrefix = ""; // e.g.: "rb_"

	// The names of the MySQL database tables used by refbase:
	//                                                             -> in code comments referred to as table:
	$tableAuth =             $tablePrefix . "auth";             // -> 'auth'
	$tableDeleted =          $tablePrefix . "deleted";          // -> 'deleted'
	$tableDepends =          $tablePrefix . "depends";          // -> 'depends'
	$tableFormats =          $tablePrefix . "formats";          // -> 'formats'
	$tableLanguages =        $tablePrefix . "languages";        // -> 'languages'
	$tableQueries =          $tablePrefix . "queries";          // -> 'queries'
	$tableRefs =             $tablePrefix . "refs";             // -> 'refs'
	$tableStyles =           $tablePrefix . "styles";           // -> 'styles'
	$tableTypes =            $tablePrefix . "types";            // -> 'types'
	$tableUserData =         $tablePrefix . "user_data";        // -> 'user_data'
	$tableUserFormats =      $tablePrefix . "user_formats";     // -> 'user_formats'
	$tableUserOptions =      $tablePrefix . "user_options";     // -> 'user_options'
	$tableUserPermissions =  $tablePrefix . "user_permissions"; // -> 'user_permissions'
	$tableUserStyles =       $tablePrefix . "user_styles";      // -> 'user_styles'
	$tableUserTypes =        $tablePrefix . "user_types";       // -> 'user_types'
	$tableUsers =            $tablePrefix . "users";            // -> 'users'
?>
