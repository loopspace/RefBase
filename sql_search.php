<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./sql_search.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/sql_search.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    29-Jul-02, 16:39
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// Search form that offers to specify a custom sql query.
	// It offers some output options (like how many records to display per page)
	// and provides some examples and links for further information on sql queries.


	// Incorporate some include files:
	include 'initialize/db.inc.php'; // 'db.inc.php' is included to hide username and password
	include 'includes/header.inc.php'; // include header
	include 'includes/footer.inc.php'; // include footer
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

	// If there's no stored message available:
	if (!isset($_SESSION['HeaderString']))
		$HeaderString = $loc["SearchSQL"].":"; // Provide the default message
	else
	{
		$HeaderString = $_SESSION['HeaderString']; // extract 'HeaderString' session variable (only necessary if register globals is OFF!)

		// Note: though we clear the session variable, the current message is still available to this script via '$HeaderString':
		deleteSessionVariable("HeaderString"); // function 'deleteSessionVariable()' is defined in 'include.inc.php'
	}

	// Extract the view type requested by the user (either 'Mobile', 'Print', 'Web' or ''):
	// ('' will produce the default 'Web' output style)
	if (isset($_REQUEST['viewType']))
		$viewType = $_REQUEST['viewType'];
	else
		$viewType = "";

	// Check if the script was called with parameters (like: 'sql_search.php?customQuery=1&sqlQuery=...&showQuery=...&showLinks=...')
	// If so, the parameter 'customQuery=1' will be set:
	if (isset($_REQUEST['customQuery']))
		$customQuery = $_REQUEST['customQuery']; // accept any previous SQL queries
	else
		$customQuery = "0";

	if ($customQuery == "1") // the script was called with parameters
	{
		$sqlQuery = $_REQUEST['sqlQuery']; // accept any previous SQL queries
		$sqlQuery = stripSlashesIfMagicQuotes($sqlQuery); // function 'stripSlashesIfMagicQuotes()' is defined in 'include.inc.php'

		$showQuery = $_REQUEST['showQuery']; // extract the $showQuery parameter
		if ("$showQuery" == "1")
			$checkQuery = " checked";
		else
			$checkQuery = "";

		$showLinks = $_REQUEST['showLinks']; // extract the $showLinks parameter
		if ("$showLinks" == "1")
			$checkLinks = " checked";
		else
			$checkLinks = "";

		$showRows = $_REQUEST['showRows']; // extract the $showRows parameter

		$displayType = $_REQUEST['submit']; // extract the type of display requested by the user (either 'Display', 'Cite', 'List' or '')
		$citeStyle = $_REQUEST['citeStyle']; // get the cite style chosen by the user (only occurs in 'extract.php' form and in query result lists)
		$citeOrder = $_REQUEST['citeOrder']; // get the citation sort order chosen by the user (only occurs in 'extract.php' form and in query result lists)
	}
	else // if there was no previous SQL query provide the default one:
	{
		// default SQL query:
		// TODO: build the complete SQL query using functions 'buildFROMclause()' and 'buildORDERclause()'
		$sqlQuery = buildSELECTclause("", "", "", false, false); // function 'buildSELECTclause()' is defined in 'include.inc.php'

		if (isset($_SESSION['loginEmail']))
			$sqlQuery .= " FROM $tableRefs WHERE location RLIKE \"" . $loginEmail . "\" ORDER BY year DESC, author"; // '$loginEmail' is defined in function 'start_session()' (in 'include.inc.php')
		else
			$sqlQuery .= " FROM $tableRefs WHERE year &gt; 2001 ORDER BY year DESC, author";

		$checkQuery = "";
		$checkLinks = " checked";

		// Get the default number of records per page preferred by the current user:
		$showRows = $_SESSION['userRecordsPerPage'];

		$displayType = ""; // ('' will produce the default view)
		$citeStyle = "";
		$citeOrder = "";
	}

	// Show the login status:
	showLogin(); // (function 'showLogin()' is defined in 'include.inc.php')

	// (2a) Display header:
	// call the 'displayHTMLhead()' and 'showPageHeader()' functions (which are defined in 'header.inc.php'):
	displayHTMLhead(encodeHTML($officialDatabaseName) . " -- " . $loc["SQLSearch"], "index,follow", "Search the " . encodeHTML($officialDatabaseName), "", false, "", $viewType, array());
	showPageHeader($HeaderString);

	// (2b) Start <form> and <table> holding the form elements:
?>

<form action="search.php" method="GET">
<input type="hidden" name="formType" value="sqlSearch">
<input type="hidden" name="submit" value="<?php echo $displayType; ?>">
<input type="hidden" name="citeStyle" value="<?php echo rawurlencode($citeStyle); ?>">
<input type="hidden" name="citeOrder" value="<?php echo $citeOrder; ?>">
<table align="center" border="0" cellpadding="0" cellspacing="10" width="95%" summary="This table holds the search form">
<tr>
	<td width="58" valign="top"><b><?php echo $loc["SQLQuery"]; ?>:</b></td>
	<td width="10">&nbsp;</td>
	<td colspan="2">
		<textarea name="sqlQuery" rows="6" cols="60"><?php echo $sqlQuery; ?></textarea>
	</td>
</tr>
<tr>
	<td valign="top"><b><?php echo $loc["DisplayOptions"]; ?>:</b></td>
	<td>&nbsp;</td>
	<td width="205" valign="top">
		<input type="checkbox" name="showLinks" value="1"<?php echo $checkLinks; ?>>&nbsp;&nbsp;&nbsp;<?php echo $loc["ShowLinks"]; ?>

	</td>
	<td valign="top">
		<?php echo $loc["ShowRecordsPerPage_Prefix"]; ?>&nbsp;&nbsp;&nbsp;<input type="text" name="showRows" value="<?php echo $showRows; ?>" size="4" title="<?php echo $loc["DescriptionShowRecordsPerPage"]; ?>">&nbsp;&nbsp;&nbsp;<?php echo $loc["ShowRecordsPerPage_Suffix"]; ?>

	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td valign="top">
		<input type="checkbox" name="showQuery" value="1"<?php echo $checkQuery; ?>>&nbsp;&nbsp;&nbsp;<?php echo $loc["DisplaySQLquery"]; ?>

	</td>
	<td valign="top">
		<?php echo $loc["ViewType"]; ?>:&nbsp;&nbsp;
		<select name="viewType">
			<option value="Web"><?php echo $loc["web"]; ?></option>
			<option value="Print"><?php echo $loc["print"]; ?></option>
			<option value="Mobile"><?php echo $loc["mobile"]; ?></option>
		</select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td><?php

	if (isset($_SESSION['user_permissions']) AND ereg("allow_sql_search", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable contains 'allow_sql_search'...
	// adjust the title string for the search button
	{
		$sqlSearchButtonLock = "";
		$sqlSearchTitle = $loc["SearchVerbatim"];
	}
	else // Note, that disabling the submit button is just a cosmetic thing -- the user can still submit the form by pressing enter or by building the correct URL from scratch!
	{
		$sqlSearchButtonLock = " disabled";
		$sqlSearchTitle = $loc["NoPermission"] . $loc["NoPermission_ForSQL"];
	}
?>

	<td colspan="2">
		<br>
		<input type="submit" value="<?php echo $loc["Search"]; ?>" title="<?php echo $sqlSearchTitle; ?>"<?php echo $sqlSearchButtonLock; ?>>
	</td>
</tr>
<tr>
	<td align="center" colspan="4">&nbsp;</td>
</tr>
<tr>
	<td valign="top"><b><?php echo $loc["Examples"]; ?>:</b></td>
	<td>&nbsp;</td>
	<td colspan="2">
		<code>SELECT author, title, year, publication FROM <?php echo $tableRefs; ?> WHERE publication = "Polar Biology" AND author RLIKE "Legendre|Ambrose" ORDER BY year DESC, author</code>
	</td>
</tr>
<tr>
	<td valign="top">&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="2">
		<code>SELECT serial, author, title, year, publication, volume FROM <?php echo $tableRefs; ?> ORDER BY serial DESC LIMIT 10</code>
	</td>
</tr>
<tr>
	<td valign="top"><b><?php echo $loc["Help"]; ?>:</b></td>
	<td>&nbsp;</td>
	<td colspan="2">
		<?php echo $loc["MySQL-Info"]; ?>

	</td>
</tr>
</table>
</form><?php

	// --------------------------------------------------------------------

	// DISPLAY THE HTML FOOTER:
	// call the 'showPageFooter()' and 'displayHTMLfoot()' functions (which are defined in 'footer.inc.php')
	showPageFooter($HeaderString);

	displayHTMLfoot();

	// --------------------------------------------------------------------
?>
