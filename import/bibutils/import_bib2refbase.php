<?php
// Project:    Web Reference Database (refbase) <http://www.refbase.net>
// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
//             original author(s).
//
//             This code is distributed in the hope that it will be useful,
//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
//             License for more details.
//
// File:       ./import/bibutils/import_bib2refbase.php
// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/import/bibutils/import_bib2refbase.php $
// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
//
// Created:    24-Feb-06, 02:07
// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
//             $Author: msteffens $
//             $Revision: 953 $

// This is an import format file (which must reside within the 'import/' sub-directory of your refbase root directory). It contains a version of the
// 'importRecords()' function that imports records from 'BibTeX'-formatted data, i.e. data that were formatted according to the export format used
// by the bibliographic companion to the LaTeX macro package (http://en.wikipedia.org/wiki/Bibtex).
	
// --------------------------------------------------------------------

// --- BEGIN IMPORT FORMAT ---

// Import records from Bibtex-formatted source data:

// Requires the following packages (available under the GPL):
//    - bibutils <http://www.scripps.edu/~cdputnam/software/bibutils/bibutils.html>

function importRecords($sourceText, $importRecordsRadio, $importRecordNumbersArray)
{
  // convert LaTeX/BibTeX markup into proper refbase markup:
  $sourceText = standardizeBibtexInput($sourceText); // function 'standardizeBibtexInput()' is defined in 'import.inc.php'

  // parse BibTeX format:
  return bibToRefbase($sourceText, $importRecordsRadio, $importRecordNumbersArray); // function 'risToRefbase()' is defined in 'import.inc.php'
}

// --- END IMPORT FORMAT ---

// BIB TO REFBASE
// This function converts records from BibTeX format into the standard "refbase"
// array format which can be then imported by the 'addRecords()' function in 'include.inc.php'.
function bibToRefbase($sourceText, $importRecordsRadio, $importRecordNumbersArray)
{
  global $contentTypeCharset; // defined in 'ini.inc.php'
  
  global $errors;
  global $showSource;

  require_once 'includes/classes/org/bibliophile/PARSEENTRIES.php';

  // uses the BibTeX class from bibcentral
  // minor bug in the class: the source from MathSciNet contains the line:
  // @import url (/mathscinet/css/msn.css);
  // this gets interpreted as a BibTeX entry and so throws up an error

  $parse = NEW PARSEENTRIES();
  $parse->expandMacro = TRUE;
  $parse->fieldExtract = TRUE;
  $parse->removeDelimit = TRUE; // maybe should be false?  Do we truse the source?

  $parse->loadBibtexString($sourceText);
  $parse->extractEntries();
  list($preamble, $strings, $entries, $undefinedStrings) = $parse->returnArrays();

  // Should check for success here.

  // $entries is now an array of entries (hopefully)
  
  // This array matches BibTeX tags with their corresponding refbase fields:
  // (fields that are unsupported in either RIS or refbase are commented out)
  // 								"RIS tag" => "refbase field" // RIS tag name (comment)
  $tagsToRefbaseFieldsArray = array(
				    "bibtexEntryType" => "type",
				    "bibtexCitation" => "cite_key",
				    "address" => "place",
				    "author" => "author",
				    "booktitle" => "original_title",
				    "coden" => "language",
				    "crossref" => "related",
				    "edition" => "edition",
				    "editor" => "editor",
				    "eprint" => "url",
				    "fjournal" => "publication",
				    "howpublished" => "medium",
				    "isbn" => "isbn",
				    "issn" => "issn",
				    "journal" => "abbrev_journal",
				    "key" => "notes",
				    "mrclass" => "area",
				    "mrnumber" => "expedition",
				    "mrreviewer" => "conference",
				    "note" => "notes",
				    "number" => "series volume",
				    "pages" => "pages",
				    "publisher" => "publisher",
				    "series" => "series_title",
				    "title" => "title",
				    "type" => "notes",
				    "url" => "url",
				    "volume" => "volume",
				    "year" => "year",
				    );
  
  // This array matches RIS reference types with their corresponding refbase types:
  // (RIS types that are currently not supported in refbase will be taken as is but will get
  //  prefixed with an "Unsupported: " label; '#fallback#' in comments indicates a type mapping that
  //  is not a perfect match but as close as currently possible)
  // 										"RIS type"  =>  "refbase type" // name of RIS reference type (comment)
  $referenceTypesToRefbaseTypesArray = array(
					     "article" => "Journal Article",
					     "book" => "Book Whole",
					     "booklet" => "Book Whole",
					     "conference" => "Conference Article",
					     "inbook" => "Book Chapter",
					     "incollection" => "Conference Article",
					     "inproceedings" => "Conference Article",
					     "manual" => "Manual",
					     "mastersthesis" => "Thesis",
					     "misc" => "Miscellaneous",
					     "phdthesis" => "Thesis",
					     "proceedings" => "Conference Volume",
					     "techreport" => "Report",
					     "unpublished" => "Miscellaneous"
					     );
  
  // -----------------------------------------

  // Don't currently use the validateRecords or parseRecords as we already have our data nicely split into arrays so just need to match 'em up.

  $recordsCount = count($entries); // number of records
  $importRecordNumbersRecognisedFormatArray = array(); // valid records (dummy for now)
  $importRecordNumbersNotRecognisedFormatArray = array(); // valid records (dummy for now)
  $errors = array();
  // LOOP OVER EACH RECORD:
  for ($i = 0; $i < $recordsCount; $i++) // for each record ...
    {
      $fieldParametersArray = array(); // to hold the extracted fields

      foreach ($entries[$i] as $fieldKey => $fieldData)
	{

	  // Start by a simple transliteration

	  $fieldParametersArray[$tagsToRefbaseFieldsArray[$fieldKey]] = $fieldData;
	}

      if (array_key_exists($fieldParametersArray["type"],$referenceTypesToRefbaseTypesArray))
	{
	  $fieldParametersArray["type"] = $referenceTypesToRefbaseTypesArray[$fieldParametersArray["type"]];
	} else {
	$fieldParametersArray["type"] = "Miscellaneous";
      }

      // Make sure that something goes in "publication" if there's a candidate

      if (!array_key_exists("publication",$fieldParametersArray) and array_key_exists("abbrev_journal",$fieldParametersArray))
	{
	  $fieldParametersArray["publication"] = $fieldParametersArray["abbrev_journal"];
	}

      $importRecordNumbersRecognisedFormatArray[] = $i+1;

      $parsedRecordsArray[] = $fieldParametersArray;
    }

  // Build refbase import array:
  $importDataArray = buildImportArray("refbase", // 'type' - the array format of the 'records' element
				      "1.0", // 'version' - the version of the given array structure
				      "http://refbase.net/import/bibtex/", // 'creator' - the name of the script/importer (preferably given as unique URI)
				      "Andrew Stacey", // 'author' - author/contact name of the person who's responsible for this script/importer
				      "stacey@math.ntnu.no", // 'contact' - author's email/contact address
				      array('prefix_call_number' => "true"), // 'options' - array with settings that control the behaviour of the 'addRecords()' function
				      $parsedRecordsArray); // 'records' - array of record(s) (with each record being a sub-array of fields)
  
  
  return array($importDataArray, $recordsCount, $importRecordNumbersRecognisedFormatArray, $importRecordNumbersNotRecognisedFormatArray, $errors);
}

// --------------------------------------------------------------------


// --------------------------------------------------------------------
?>
