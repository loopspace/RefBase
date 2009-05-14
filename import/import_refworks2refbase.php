<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./import/import_refworks2refbase.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/import/import_refworks2refbase.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    25-Aug-06, 13:10
	// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
	//             $Author: msteffens $
	//             $Revision: 953 $

	// This is an import format file (which must reside within the 'import/' sub-directory of your refbase root directory). It contains a version of the
	// 'importRecords()' function that imports records from 'RefWorks'-formatted data, i.e. data formatted according to the tagged text format used
	// by the commercial bibliographic web application 'RefWorks' (http://www.refworks.com).

	// --------------------------------------------------------------------

	// --- BEGIN IMPORT FORMAT ---

	// Import records from RefWorks-formatted source data:

	function importRecords($sourceText, $importRecordsRadio, $importRecordNumbersArray)
	{
		// parse RefWorks format:
		return refworksToRefbase($sourceText, $importRecordsRadio, $importRecordNumbersArray); // function 'refworksToRefbase()' is defined in 'import.inc.php'
	}

	// --- END IMPORT FORMAT ---

	// --------------------------------------------------------------------
?>
