<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./queries.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/queries.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    16-May-04, 22:03
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// This script takes a user query name (which was passed to the script by use of the 'Recall My Query' form on the main page 'index.php')
	// and extracts all saved settings for this particular query from the 'queries' MySQL table. It will then build an appropriate query URL
	// and pass that to 'search.php' which will finally display all matching records in list view.
	// TODO: I18n


	// Incorporate some include files:
	include 'initialize/db.inc.php'; // 'db.inc.php' is included to hide username and password
	include 'includes/include.inc.php'; // include common functions
	include 'initialize/ini.inc.php'; // include common variables

	// --------------------------------------------------------------------

	// START A SESSION:
	// call the 'start_session()' function (from 'include.inc.php') which will also read out available session variables:
	start_session(true);

	// --------------------------------------------------------------------

	// Initialize preferred display language:
	// (note that 'locales.inc.php' has to be included *after* the call to the 'start_session()' function)
	include 'includes/locales.inc.php'; // include the locales

	// --------------------------------------------------------------------

	// Extract any parameters passed to the script:
	if (isset($_REQUEST['querySearchSelector']))
		$querySearchSelector = $_REQUEST['querySearchSelector']; // get the name of the saved query that was chosen by the user
	else
		$querySearchSelector = "";

	// Determine the button that was hit by the user (in English localization, either 'Go' or 'Edit'):
	$submitAction = $_REQUEST['submit'];


	// Check the correct parameters have been passed:
	if (empty($querySearchSelector)) // if 'queries.php' was called without any valid parameters:
	{
		// return an appropriate error message:
		$HeaderString = returnMsg($loc["Warning_IncorrectOrMissingParams"] . " '" . scriptURL() . "'!", "warning", "strong", "HeaderString"); // functions 'returnMsg()' and 'scriptURL()' are defined in 'include.inc.php'

		// Redirect the browser back to the calling page:
		header("Location: " . $referer); // variable '$referer' is globally defined in function 'start_session()' in 'include.inc.php'
		exit; // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> !EXIT! <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	}


	else // the script was called with required parameters
	{
		connectToMySQLDatabase(); // function 'connectToMySQLDatabase()' is defined in 'include.inc.php'

		// CONSTRUCT SQL QUERY:
		// Fetch all saved settings for the user's query from the 'queries' table:
		$query = "SELECT query_id, display_type, view_type, query, show_query, show_links, show_rows, cite_style_selector, cite_order FROM $tableQueries WHERE user_id = " . quote_smart($loginUserID) . " AND query_name = " . quote_smart($querySearchSelector); // the global variable '$loginUserID' gets set in function 'start_session()' within 'include.inc.php'

		$result = queryMySQLDatabase($query); // RUN the query on the database through the connection (function 'queryMySQLDatabase()' is defined in 'include.inc.php')

		$rowsFound = @ mysql_num_rows($result);
		if ($rowsFound == 1) // if there was exactly one row found (normally, this should be the case) ...
		{
			$row = mysql_fetch_array($result);

			// redirect the browser to 'query_manager.php':
			if (encodeHTML($submitAction) == $loc["ButtonTitle_Edit"]) // note that we need to HTML encode '$submitAction' for comparison with the HTML encoded locales (function 'encodeHTML()' is defined in 'include.inc.php')
			{
				header("Location: query_manager.php?queryAction=edit&queryID=" . $row['query_id']);
				exit; // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> !EXIT! <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			}
		}
		else // if ($rowsFound != 1) // if there was NOT exactly one row found (i.e., something went wrong) ...
		{
			if ($rowsFound > 1) // if there were more than one row found ...
				$HeaderString = "<b><span class=\"warning\">There's more than one saved query matching your query title!</span></b>";
			else // if ($rowsFound == 0) // nothing found
				$HeaderString = "<b><span class=\"warning\">Your saved query couldn't be found!</span></b>";

			// update the 'userQueries' session variable:
			getUserQueries($loginUserID); // function 'getUserQueries()' is defined in 'include.inc.php'

			// Write back session variable:
			saveSessionVariable("HeaderString", $HeaderString); // function 'saveSessionVariable()' is defined in 'include.inc.php'
	
			// Redirect the browser back to the calling page:
			header("Location: " . $referer);
			exit; // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> !EXIT! <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		}

		// We also update the time stamp for that query in the 'queries' table:
		$updateQuery = "UPDATE $tableQueries SET "
					. "last_execution = NOW() " // set 'last_execution' field to the current date & time in 'DATETIME' format (which is 'YYYY-MM-DD HH:MM:SS', e.g.: '2003-12-31 23:45:59')
					. "WHERE user_id = " . quote_smart($loginUserID) . " AND query_id = " . quote_smart($row['query_id']);

		$updateResult = queryMySQLDatabase($updateQuery); // RUN the query on the database through the connection (function 'queryMySQLDatabase()' is defined in 'include.inc.php')

		// update the 'userQueries' session variable:
		getUserQueries($loginUserID); // function 'getUserQueries()' is defined in 'include.inc.php'

		disconnectFromMySQLDatabase(); // function 'disconnectFromMySQLDatabase()' is defined in 'include.inc.php'


		// Build the correct query URL:
		// TODO: use function 'generateURL()'
		$queryURL = "sqlQuery=" . rawurlencode($row['query']) . "&formType=sqlSearch&submit=" . $row['display_type'] . "&viewType=" . $row['view_type'] . "&showQuery=" . $row['show_query'] . "&showLinks=" . $row['show_links'] . "&showRows=" . $row['show_rows'] . "&citeOrder=" . $row['cite_order'] . "&citeStyle=" . $row['cite_style_selector'];

	
		// call 'search.php' with the correct query URL in order to display all records matching the user's query:
		header("Location: search.php?$queryURL");
	}
?>
