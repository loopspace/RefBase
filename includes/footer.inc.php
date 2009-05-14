<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./includes/footer.inc.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/includes/footer.inc.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    28-Jul-02, 11:30
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// This is the footer include file.
	// It contains functions that build the footer
	// which gets displayed on every page.

	// --------------------------------------------------------------------

	// Inserts the closing HTML </body> and </html> tags:
	function displayHTMLfoot()
	{
?>

</body>
</html>
<?php
	}

	// --------------------------------------------------------------------

	// Displays the visible footer:
	function showPageFooter($HeaderString)
	{
		global $officialDatabaseName; // usage example: <a href="index.php">[? echo encodeHTML($officialDatabaseName); ?]</a>
		global $hostInstitutionAbbrevName; // usage example: <a href="[? echo $hostInstitutionURL; ?]">[? echo encodeHTML($hostInstitutionAbbrevName); ?] Home</a>
		global $hostInstitutionName; // (note: in the examples above, square brackets must be replaced by their respective angle brackets)
		global $hostInstitutionURL;
		global $helpResourcesURL;
		global $librarySearchPattern;

		global $loginWelcomeMsg; // these variables are globally defined in function 'showLogin()' in 'include.inc.php'
		global $loginStatus;
		global $loginLinks;

		global $loc; // '$loc' is made globally available in 'core.php'
?>

<hr class="pagefooter" align="center" width="95%">
<table class="pagefooter" align="center" border="0" cellpadding="0" cellspacing="10" width="95%" summary="This table holds the footer">
<tr>
	<td class="small" width="105"><a href="index.php"<?php echo addAccessKey("attribute", "home"); ?> title="<?php echo $loc["LinkTitle_Home"] . addAccessKey("title", "home"); ?>"><?php echo $loc["Home"]; ?></a></td>
	<td class="small" align="center"><?php

		// -------------------------------------------------------
		if (isset($_SESSION['user_permissions']) AND ereg("allow_sql_search", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable contains 'allow_sql_search'...
		{
		// ... include a link to 'sql_search.php':
?>

		<a href="sql_search.php"<?php echo addAccessKey("attribute", "sql_search"); ?> title="<?php echo $loc["LinkTitle_SQLSearch"] . addAccessKey("title", "sql_search"); ?>"><?php echo $loc["SQLSearch"]; ?></a>
		&nbsp;|&nbsp;<?php
		}

		// -------------------------------------------------------
		if (!empty($librarySearchPattern))
		{
		// ... include a link to 'library_search.php':
?>

		<a href="library_search.php"<?php echo addAccessKey("attribute", "lib_search"); ?> title="<?php echo $loc["LinkTitle_LibrarySearch_Prefix"] . encodeHTML($hostInstitutionName) . $loc["LinkTitle_LibrarySearch_Suffix"] . addAccessKey("title", "lib_search"); ?>"><?php echo $loc["LibrarySearch"]; ?></a>
		&nbsp;|&nbsp;<?php
		}

		// -------------------------------------------------------
		if (isset($_SESSION['user_permissions']) AND ereg("allow_details_view", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable contains 'allow_details_view'...
		{
		// ... include a link to 'show.php':
?>

		<a href="show.php"<?php echo addAccessKey("attribute", "show_rec"); ?> title="<?php echo $loc["LinkTitle_ShowRecord"] . addAccessKey("title", "show_rec"); ?>"><?php echo $loc["ShowRecord"]; ?></a>
		&nbsp;|&nbsp;<?php
		}

		// -------------------------------------------------------
		if (isset($_SESSION['user_permissions']) AND ereg("allow_cite", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable contains 'allow_cite'...
		{
		// ... include a link to 'extract.php':
?>

		<a href="extract.php"<?php echo addAccessKey("attribute", "extract"); ?> title="<?php echo $loc["LinkTitle_ExtractCitations"] . addAccessKey("title", "extract"); ?>"><?php echo $loc["ExtractCitations"]; ?></a><?php
		}

		// -------------------------------------------------------
?>

	</td>
	<td class="small" align="right" width="105"><?php

		if (!empty($helpResourcesURL))
		{
?><a href="<?php echo $helpResourcesURL; ?>" title="<?php echo $loc["LinkTitle_Help"]; ?>"><?php echo $loc["Help"]; ?></a><?php
		}
?></td>
</tr>
</table><?php
	}
?>
