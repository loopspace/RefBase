<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./import/import_arxiv2refbase.php
	// Repository: $HeadURL$
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    09-Jun-08, 16:00
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author$
	//             $Revision: 1214 $

	// This is an import format file (which must reside within the 'import/' sub-directory of your refbase root directory). It contains a version of the
	// 'importRecords()' function that imports records from MathSciNet.

	// --------------------------------------------------------------------

	// --- BEGIN IMPORT FORMAT ---

	// Import records from MathSciNet data:

	function importRecords($sourceObject, $importRecordsRadio, $importRecordNumbersArray)
	{


		// parse arXiv Atom XML format:
		return mathscinetToRefbase($sourceObject, $importRecordsRadio, $importRecordNumbersArray); // function 'arxivToRefbase()' is defined in 'import.inc.php'
	}

	// --- END IMPORT FORMAT ---

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	// MathSciNet TO REFBASE
	// This function converts records from MathSciNet to RefBase
	// 

function mathscinetToRefbase($sourceText, $importRecordsRadio, $importRecordNumbersArray)
{

  include("import/bibutils/import_bib2refbase.php");
  return bibToRefbase($sourceText, $importRecordsRadio, $importRecordNumbersArray);
}

?>
