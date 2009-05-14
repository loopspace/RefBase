<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./simple_search.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/simple_search.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    29-Jul-02, 16:39
	// Modified:   $Date: 2008-11-18 13:13:00 -0800 (Tue, 18 Nov 2008) $
	//             $Author: msteffens $
	//             $Revision: 1321 $

	// Search form providing access to the main fields of the database.
	// It offers some output options (like how many records to display per page)
	// and let's you specify the output sort order (up to three levels deep).


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

	// (1) Open the database connection and use the literature database:
	connectToMySQLDatabase(); // function 'connectToMySQLDatabase()' is defined in 'include.inc.php'

	// If there's no stored message available:
	if (!isset($_SESSION['HeaderString']))
		$HeaderString = $loc["SearchMain"].":"; // Provide the default message
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

	// Get the default number of records per page preferred by the current user:
	$showRows = $_SESSION['userRecordsPerPage'];

	// Get the user's preference for displaying auto-completions:
	$showAutoCompletions = $_SESSION['userAutoCompletions'];

	// Show the login status:
	showLogin(); // (function 'showLogin()' is defined in 'include.inc.php')

	// (2a) Display header:
	// call the 'displayHTMLhead()' and 'showPageHeader()' functions (which are defined in 'header.inc.php'):
	displayHTMLhead(encodeHTML($officialDatabaseName) . " -- " . $loc["SimpleSearch"], "index,follow", "Search the " . encodeHTML($officialDatabaseName), "", false, "", $viewType, array());
	showPageHeader($HeaderString);

	// Define variables holding common drop-down elements, i.e. build properly formatted <option> tag elements:
	$dropDownConditionals1Array = array("contains"         => $loc["contains"],
	                                    "does not contain" => $loc["contains not"],
	                                    "is equal to"      => $loc["equal to"],
	                                    "is not equal to"  => $loc["equal to not"],
	                                    "starts with"      => $loc["starts with"],
	                                    "ends with"        => $loc["ends with"]);

	$dropDownItems1 = buildSelectMenuOptions($dropDownConditionals1Array, "", "\t\t\t", true); // function 'buildSelectMenuOptions()' is defined in 'include.inc.php'


	$dropDownConditionals2Array = array("is greater than" => $loc["is greater than"],
	                                    "is less than"    => $loc["is less than"],
	                                    "is within range" => $loc["is within range"],
	                                    "is within list"  => $loc["is within list"]);

	$dropDownItems2 = buildSelectMenuOptions($dropDownConditionals2Array, "", "\t\t\t", true); // function 'buildSelectMenuOptions()' is defined in 'include.inc.php'


	// TODO: if possible, we should use function 'mapFieldNames()' here
	$dropDownFieldNameArray = array("author"         => $loc["DropDownFieldName_Author"],
	                                "title"          => $loc["DropDownFieldName_Title"],
	                                "year"           => $loc["DropDownFieldName_Year"],
	                                "publication"    => $loc["DropDownFieldName_Publication"],
	                                "volume_numeric" => $loc["DropDownFieldName_Volume"], // 'volume_numeric' is used instead of 'volume' in the sort dropdown menus
	                                "pages"          => $loc["DropDownFieldName_Pages"]);

	$dropDownItems3 = buildSelectMenuOptions($dropDownFieldNameArray, "", "\t\t\t", true); // function 'buildSelectMenuOptions()' is defined in 'include.inc.php'

	// Build HTML elements that allow for search suggestions for text entered by the user:
	if (isset($_SESSION['userAutoCompletions']) AND ($_SESSION['userAutoCompletions'] == "yes"))
	{
		$authorSuggestElements = buildSuggestElements("authorName", "authorSuggestions", "authorSuggestProgress", "col-author-"); // function 'buildSuggestElements()' is defined in 'include.inc.php'
		$titleSuggestElements = buildSuggestElements("titleName", "titleSuggestions", "titleSuggestProgress", "col-title-");
		$yearSuggestElements = buildSuggestElements("yearNo", "yearSuggestions", "yearSuggestProgress", "col-year-");
		$publicationSuggestElements = buildSuggestElements("publicationName2", "publicationSuggestions", "publicationSuggestProgress", "col-publication-");
		$volumeSuggestElements = buildSuggestElements("volumeNo", "volumeSuggestions", "volumeSuggestProgress", "col-volume-");
		$pagesSuggestElements = buildSuggestElements("pagesNo", "pagesSuggestions", "pagesSuggestProgress", "col-pages-");
	}
	else
	{
		$authorSuggestElements = "";
		$titleSuggestElements = "";
		$yearSuggestElements = "";
		$publicationSuggestElements = "";
		$volumeSuggestElements = "";
		$pagesSuggestElements = "";
	}

	// (2b) Start <form> and <table> holding the form elements:
?>

<form action="search.php" method="GET" name="simpleSearch">
<input type="hidden" name="formType" value="simpleSearch">
<input type="hidden" name="showQuery" value="0">
<table align="center" border="0" cellpadding="0" cellspacing="10" width="95%" summary="This table holds the search form">
<tr>
	<th align="left"><?php echo $loc["Show"]; ?></th>
	<th align="left"><?php echo $loc["Field"]; ?></th>
	<th align="left"><?php echo $loc["That..."]; ?></th>
	<th align="left"><?php echo $loc["Searchstring"]; ?></th>
</tr>
<tr>
	<td width="20" valign="middle"><input type="checkbox" id="showAuthor" name="showAuthor" value="1" checked></td>
	<td width="40"><b><?php echo $loc["Author"]; ?>:</b></td>
	<td width="125">
		<select id="authorSelector" name="authorSelector"><?php echo $dropDownItems1; ?>

		</select>
	</td>
	<td>
		<input type="text" id="authorName" name="authorName" size="42"><?php echo $authorSuggestElements; ?>

	</td>
</tr>
<tr>
	<td valign="middle"><input type="checkbox" id="showTitle" name="showTitle" value="1" checked></td>
	<td><b><?php echo $loc["Title"]; ?>:</b></td>
	<td>
		<select id="titleSelector" name="titleSelector"><?php echo $dropDownItems1; ?>

		</select>
	</td>
	<td>
		<input type="text" id="titleName" name="titleName" size="42"><?php echo $titleSuggestElements; ?>

	</td>
</tr>
<tr>
	<td valign="middle"><input type="checkbox" id="showYear" name="showYear" value="1" checked></td>
	<td><b><?php echo $loc["Year"]; ?>:</b></td>
	<td>
		<select id="yearSelector" name="yearSelector"><?php echo $dropDownItems1 . $dropDownItems2; ?>

		</select>
	</td>
	<td>
		<input type="text" id="yearNo" name="yearNo" size="42"><?php echo $yearSuggestElements; ?>

	</td>
</tr>
<tr>
	<td valign="middle"><input type="checkbox" id="showPublication" name="showPublication" value="1" checked></td>
	<td><b><?php echo $loc["Publication"]; ?>:</b></td>
	<td>
		<input type="hidden" id="publicationRadioB" name="publicationRadio" value="0">
		<select id="publicationSelector2" name="publicationSelector2"><?php echo $dropDownItems1; ?>

		</select>
	</td>
	<td>
		<input type="text" id="publicationName2" name="publicationName2" size="42"><?php echo $publicationSuggestElements; ?>

	</td>
</tr>
<tr>
	<td valign="middle"><input type="checkbox" id="showVolume" name="showVolume" value="1" checked></td>
	<td><b><?php echo $loc["Volume"]; ?>:</b></td>
	<td>
		<select id="volumeSelector" name="volumeSelector"><?php echo $dropDownItems1 . $dropDownItems2; ?>

		</select>
	</td>
	<td>
		<input type="text" id="volumeNo" name="volumeNo" size="42"><?php echo $volumeSuggestElements; ?>

	</td>
</tr>
<tr>
	<td valign="middle"><input type="checkbox" id="showPages" name="showPages" value="1" checked></td>
	<td><b><?php echo $loc["Pages"]; ?>:</b></td>
	<td>
		<select id="pagesSelector" name="pagesSelector"><?php echo $dropDownItems1; ?>

		</select>
	</td>
	<td>
		<input type="text" id="pagesNo" name="pagesNo" size="42"><?php echo $pagesSuggestElements; ?>

	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td valign="top"><b><?php echo $loc["DisplayOptions"]; ?>:</b></td>
	<td valign="middle"><input type="checkbox" id="showLinks" name="showLinks" value="1" checked>&nbsp;&nbsp;&nbsp;<?php echo $loc["ShowLinks"]; ?></td>
	<td valign="middle"><?php echo $loc["ShowRecordsPerPage_Prefix"]; ?>&nbsp;&nbsp;&nbsp;<input type="text" id="showRows" name="showRows" value="<?php echo $showRows; ?>" size="4" title="<?php echo $loc["DescriptionShowRecordsPerPage"]; ?>">&nbsp;&nbsp;&nbsp;<?php echo $loc["ShowRecordsPerPage_Suffix"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php echo $loc["ButtonTitle_Search"]; ?>"></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>1.&nbsp;<?php echo $loc["sort by"]; ?>:</td>
	<td>
		<select id="sortSelector1" name="sortSelector1"><?php

$sortSelector1DropDownItems = ereg_replace("<option([^>]*)>" . $loc["DropDownFieldName_Author"], "<option\\1 selected>" . $loc["DropDownFieldName_Author"], $dropDownItems3); // select the 'author' menu entry ...
echo $sortSelector1DropDownItems;
?>

		</select>
	</td>
	<td>
		<input type="radio" id="sortRadio1A" name="sortRadio1" value="0" checked>&nbsp;&nbsp;&nbsp;<?php echo $loc["ascending"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" id="sortRadio1B" name="sortRadio1" value="1">&nbsp;&nbsp;&nbsp;<?php echo $loc["descending"]; ?>

	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>2.&nbsp;<?php echo $loc["sort by"]; ?>:</td>
	<td>
		<select id="sortSelector2" name="sortSelector2"><?php

$sortSelector2DropDownItems = ereg_replace("<option([^>]*)>" . $loc["DropDownFieldName_Year"], "<option\\1 selected>" . $loc["DropDownFieldName_Year"], $dropDownItems3); // select the 'year' menu entry ...
echo $sortSelector2DropDownItems;
?>

		</select>
	</td>
	<td>
		<input type="radio" id="sortRadio2A" name="sortRadio2" value="0">&nbsp;&nbsp;&nbsp;<?php echo $loc["ascending"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" id="sortRadio2B" name="sortRadio2" value="1" checked>&nbsp;&nbsp;&nbsp;<?php echo $loc["descending"]; ?>

	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>3.&nbsp;<?php echo $loc["sort by"]; ?>:</td>
	<td>
		<select id="sortSelector3" name="sortSelector3"><?php

$sortSelector3DropDownItems = ereg_replace("<option([^>]*)>" . $loc["DropDownFieldName_Publication"], "<option\\1 selected>" . $loc["DropDownFieldName_Publication"], $dropDownItems3); // select the 'publication' menu entry ...
echo $sortSelector3DropDownItems;
?>

		</select>
	</td>
	<td>
		<input type="radio" id="sortRadio3A" name="sortRadio3" value="0" checked>&nbsp;&nbsp;&nbsp;<?php echo $loc["ascending"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" id="sortRadio3B" name="sortRadio3" value="1">&nbsp;&nbsp;&nbsp;<?php echo $loc["descending"]; ?>

	</td>
</tr>
</table>
</form><?php

	// (5) Close the database connection:
	disconnectFromMySQLDatabase(); // function 'disconnectFromMySQLDatabase()' is defined in 'include.inc.php'

	// --------------------------------------------------------------------

	// DISPLAY THE HTML FOOTER:
	// call the 'showPageFooter()' and 'displayHTMLfoot()' functions (which are defined in 'footer.inc.php')
	showPageFooter($HeaderString);

	displayHTMLfoot();

	// --------------------------------------------------------------------
?>
