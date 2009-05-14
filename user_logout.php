<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./user_logout.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/user_logout.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    16-Apr-02, 10:54
	// Modified:   $Date: 2008-11-07 08:38:02 -0800 (Fri, 07 Nov 2008) $
	//             $Author: msteffens $
	//             $Revision: 1300 $

	// This script logs a user out and redirects 
	// to the calling page. If the script is called
	// unexpectedly, an error message is generated.


	// Incorporate some include files:
	include 'includes/include.inc.php'; // include common functions

	// --------------------------------------------------------------------

	// START A SESSION:
	// call the 'start_session()' function (from 'include.inc.php') which will also read out available session variables:
	start_session(true);

	// --------------------------------------------------------------------

	// Is the user logged in?
	if (isset($_SESSION['loginEmail']))
	{
		// Delete the 'loginEmail' session variable & other session variables we've registered on login:
		// (function 'deleteSessionVariable()' is defined in 'include.inc.php')
		deleteSessionVariable("loginEmail"); // remove the user's email address (as a result the user will be logged out)
		deleteSessionVariable("loginUserID"); // clear the user's user ID
		deleteSessionVariable("loginFirstName"); // clear the user's first name
		deleteSessionVariable("loginLastName"); // clear the user's last name
		deleteSessionVariable("abbrevInstitution"); // clear the user's abbreviated institution name
		deleteSessionVariable("userLanguage"); // clear the user's preferred language
		deleteSessionVariable("userDefaultView"); // clear the user's default view setting
		deleteSessionVariable("userRecordsPerPage"); // clear the user's preferred number of records per page
		deleteSessionVariable("userAutoCompletions"); // clear the user's preference for displaying auto-completions
		deleteSessionVariable("userMainFields"); // clear the user's preferred list of "main fields"
		deleteSessionVariable("lastLogin"); // clear the user's last login date & time

		if (isset($_SESSION['userGroups']))
			deleteSessionVariable("userGroups"); // clear the user's user groups (if any)

		if (isset($_SESSION['adminUserGroups']))
			deleteSessionVariable("adminUserGroups"); // clear the admin's user groups (if any)

		if (isset($_SESSION['userQueries']))
			deleteSessionVariable("userQueries"); // clear the user's saved queries (if any)

		if (isset($_SESSION['user_export_formats']))
			deleteSessionVariable("user_export_formats"); // clear the user's export formats (if any)

		if (isset($_SESSION['user_cite_formats']))
			deleteSessionVariable("user_cite_formats"); // clear the user's cite formats (if any)

		if (isset($_SESSION['user_styles']))
			deleteSessionVariable("user_styles"); // clear the user's styles (if any)

		if (isset($_SESSION['user_types']))
			deleteSessionVariable("user_types"); // clear the user's types (if any)

		if (isset($_SESSION['user_permissions']))
			deleteSessionVariable("user_permissions"); // clear any user-specific permissions

		if (isset($_SESSION['HeaderString']))
			deleteSessionVariable("HeaderString"); // clear any previous messages

		if (isset($_SESSION['cqlQuery']))
			deleteSessionVariable("cqlQuery"); // clear any stored OpenSearch/CQL query

		if (isset($_SESSION['oldQuery']))
			deleteSessionVariable("oldQuery"); // clear any query URL pointing to the formerly displayed results page

		if (isset($_SESSION['oldMultiRecordQuery']))
			deleteSessionVariable("oldMultiRecordQuery"); // clear any query URL pointing to the last multi-record query

		if (isset($_SESSION['lastListViewQuery']))
			deleteSessionVariable("lastListViewQuery"); // clear any SQL query generated for the last List view

		if (isset($_SESSION['lastDetailsViewQuery']))
			deleteSessionVariable("lastDetailsViewQuery"); // clear any SQL query generated for the last Details view

//		if (isset($_SESSION['lastCitationViewQuery']))
//			deleteSessionVariable("lastCitationViewQuery"); // clear any SQL query generated for the last Citation view

		if (isset($_SESSION['queryHistory']))
			deleteSessionVariable("queryHistory"); // clear any links to previous search results
	}
	else
	{
		// save an error message:
		$HeaderString = "<b><span class=\"warning\">You cannot logout since you are not logged in anymore!</span></b>";

		// Write back session variables:
		saveSessionVariable("HeaderString", $HeaderString); // function 'saveSessionVariable()' is defined in 'include.inc.php'
	}

	if (!preg_match("/.*user(_details|_options|_receipt|s)\.php.*|.*(error|install|query_manager|query_history)\.php.*/", $referer)) // variable '$referer' is globally defined in function 'start_session()' in 'include.inc.php'
		header("Location: " . $referer); // redirect the user to the calling page
	else
		header("Location: index.php"); // back to main page
?>
