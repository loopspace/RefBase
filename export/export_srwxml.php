<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./export/export_srwxml.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/export/export_srwxml.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    17-May-05, 16:31
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// This exports SRW XML. This file must reside in the 'export' directory of the refbase root directory.
	// It uses functions from include files 'srwxml.inc.php', 'modsxml.inc.php' and 'oaidcxml.inc.php' that require
	// the ActiveLink PHP XML Package, which is available under the GPL from: <http://www.active-link.com/software/>
	
	// --------------------------------------------------------------------

	// --- BEGIN EXPORT FORMAT ---

	// Export found records as SRW XML:
	function exportRecords($result, $rowOffset, $showRows, $exportStylesheet, $displayType)
	{
		global $rowsFound;

		if ($rowsFound > 0 && ($rowOffset + 1) > $rowsFound) // Invalid offset for current MySQL result set, error with an appropriate diagnostics response:
		{
			if ($rowsFound == 1)
				$recordString = "record";
			else
				$recordString = "records";

			$recordCollection = srwDiagnostics(61, "Record offset " . ($rowOffset + 1) . " is invalid for current result set (" . $rowsFound . " " . $recordString . " found)", $exportStylesheet); // function 'srwDiagnostics()' is defined in 'srwxml.inc.php'
		}
		else // Generate and serve a SRW XML file of ALL records:
		{
			$recordCollection = srwCollection($result, $rowOffset, $showRows, $exportStylesheet, $displayType); // function 'srwCollection()' is defined in 'srwxml.inc.php'
		}
	
		return $recordCollection;
	}

	// --- END EXPORT FORMAT ---

	// --------------------------------------------------------------------
?>
