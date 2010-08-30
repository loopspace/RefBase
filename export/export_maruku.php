<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./export/export_xhtml.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/export/bibutils/export_xml2bib.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    28-Sep-04, 22:14
	// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
	//             $Author: msteffens $
	//             $Revision: 953 $

	// This is an export format file (which must reside within the 'export/' sub-directory of your refbase root directory). It contains a version of the
	// 'exportRecords()' function that outputs records in a maruku-style list (maruku is an extension of Markdown).

	// --------------------------------------------------------------------

	// --- BEGIN EXPORT FORMAT ---

	// Export found records in 'Maruku' format:

	function exportRecords($result, $rowOffset, $showRows, $exportStylesheet, $displayType)
	{
	  return exportMaruku($result);
	}

	// --- END EXPORT FORMAT ---

	// --------------------------------------------------------------------

function exportMaruku($result)
{
      $exportArray = array();
  while ($row = @ mysql_fetch_array($result))
    {

      $formVars = buildFormVarsArray($row); // function 'buildFormVarsArray()' is defined in 'include.inc.php'
      $citeKey = generateCiteKey($formVars); // function 'generateCiteKey()' is defined in 'include.inc.php'

      $MarukuFields = array(
			 "title",
			 "author",
			 "address",
			 "booktitle",
			 "coden",
			 "crossref",
			 "edition",
			 "editor",
			 "eprint",
			 "fjournal",
			 "howpublished",
			 "isbn",
			 "issn",
			 "journal",
			 "mrclass",
			 "mrnumber",
			 "mrreviewer",
			 "note",
			 "number",
			 "pages",
			 "publisher",
			 "series",
			 "url",
			 "arxiv",
			 "volume",
			 "year",
			 );

      $countMarukuFields = count($MarukuFields);

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
				    "arxiv" => "summary_language",
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

      $RefbaseTypesToMarukuTypesArray = array(
					      "Journal Article" => "article",
					      "Book Whole" => "book",
					      "Book Whole" => "booklet",
					      "Conference Article" => "conference",
					      "Book Chapter" => "inbook",
					      "Conference Article" => "incollection",
					      "Conference Article" => "inproceedings",
					      "Manual" => "manual",
					      "Thesis" => "mastersthesis",
					      "Miscellaneous" => "misc",
					      "Thesis" => "phdthesis",
					      "Conference Volume" => "proceedings",
					      "Report" => "techreport",
					      "unpublished" => "Miscellaneous"
					      );



      foreach ($row as $rowFieldName => $rowFieldValue)
	{
	  // function 'encodeHTMLspecialchars()' is defined in 'include.inc.php'
	  $row[$rowFieldName] = encodeHTMLspecialchars($row[$rowFieldName]);

	  // Convert field data to UTF-8:
	  // (if '$convertExportDataToUTF8' is set to "yes" in 'ini.inc.php' and character encoding is not UTF-8 already)
	  // (Note that charset conversion can only be done *after* the cite key has been generated, otherwise cite key
	  //  generation will produce garbled text!)
	  // function 'convertToCharacterEncoding()' is defined in 'include.inc.php'
	  if (($convertExportDataToUTF8 == "yes") AND ($contentTypeCharset != "UTF-8"))
	    $row[$rowFieldName] = convertToCharacterEncoding("UTF-8", "IGNORE", $row[$rowFieldName]);
	}

          // Defines field-specific search & replace 'actions' that will be applied to all those refbase fields that are listed in the corresponding 'fields' element:
    // (If you don't want to perform any search and replace actions, specify an empty array, like: '$fieldSpecificSearchReplaceActionsArray = array();'.
    //  Note that the search patterns MUST include the leading & trailing slashes -- which is done to allow for mode modifiers such as 'imsxU'.)
    //                                              "/Search Pattern/"  =>  "Replace Pattern"
      $fieldSpecificSearchReplaceActionsArray = array(
							array(
						    'fields' => array("url"),
						    'actions' => array("/^(.*)$/" => '<\1>')
							      ),
							array(
						    'fields' => array("expedition"),
						    'actions' => array("/(.*mr)(\d+)(.*)$/i" => '[\1\2\3](http://www.ams.org/mathscinet-getitem?mr=\2)')
							      ),
							array(
						    'fields' => array("summary_language"),
						    'actions' => array("/arxiv:(.*)/i" => '[\1](http://www.arxiv.org/abs/\1)')
							      ),
						      );

    if ($convertExportDataToUTF8 == "yes")
      $fieldSpecificSearchReplaceActionsArray[] = array(
                                                          'fields'  => array("title", "publication", "abbrev_journal", "address", "keywords", "abstract", "orig_title", "series_title", "abbrev_series_title", "notes"),
                                                          'actions' => $transtab_refbase_unicode
                                                      );

    // Apply field-specific search & replace 'actions' to all fields that are listed in the 'fields' element of the arrays contained in '$fieldSpecificSearchReplaceActionsArray':
    foreach ($fieldSpecificSearchReplaceActionsArray as $fieldActionsArray)
      foreach ($row as $rowFieldName => $rowFieldValue)
        if (in_array($rowFieldName, $fieldActionsArray['fields']))
          $row[$rowFieldName] = searchReplaceText($fieldActionsArray['actions'], $rowFieldValue, true); // function 'searchReplaceText()' is defined in 'include.inc.php'

    // Really ought to be a bit cleverer here ...

    $MarukuEntry = "[[!redirects "
      . $citeKey
      . "]]\n\n###Reference Detailes###\n\n+--\ncite key\n: "
      . $citeKey
      . "\n\n";


    for ($i = 0; $i< $countMarukuFields; $i++)
      {

	if (isset( $row[$tagsToRefbaseFieldsArray[$MarukuFields[$i]]]) and strlen($row[$tagsToRefbaseFieldsArray[$MarukuFields[$i]]]))
	  {
	    $MarukuEntry = $MarukuEntry
	      . $MarukuFields[$i]
	      . "\n: ";
	    $MarukuEntry .= $row[$tagsToRefbaseFieldsArray[$MarukuFields[$i]]];
	    $MarukuEntry .= "\n\n";
	  }
      }

    $MarukuEntry .= "refbase\n: ["
      . $row['serial']
      . "](http://www.math.ntnu.no/~stacey/RefBase/"
      . generateURL("show.php", "html", array('record' => $row['serial']))
      . ")\n\n=--\n{: .bibliography}\n\n---\n";

    array_push($exportArray, $MarukuEntry);

    }

  if (count($exportArray) > 1) {
    return implode("\n*\n", $exportArray);
  } else {
    return $exportArray[0];
  }
}

?>
