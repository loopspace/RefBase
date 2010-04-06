<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./cite/styles/cite_APA.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/cite/styles/cite_APA.php $
	// Author(s):  Richard Karnesky <mailto:karnesky@gmail.com> and
	//             Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    06-Nov-06, 13:00
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// This is a citation style file (which must reside within the 'cite/styles/' sub-directory of your refbase root directory). It contains a
	// version of the 'citeRecord()' function that outputs a reference list from selected records according to the citation style used by
	// the APA

	// based on 'cite_AnnGlaciol_JGlaciol.php'

	// TODO: - magazine articles, conference proceedings, patents & reports?

	// --------------------------------------------------------------------

	// --- BEGIN CITATION STYLE ---

function citeRecord($row, $citeStyle, $citeType, $markupPatternsArray, $encodeHTML)
{
  $record = ""; // make sure that our buffer variable is empty

		// Very basic formatting for now

  // <nowiki>[nlab citation]</nowiki> A. Author, Title, Journal, (arXiv)

  print '<pre>';
  print_r($row);
  print '</pre>';


  if (!empty($row['author']))      // author
    {
      // Call the 'reArrangeAuthorContents()' function (defined in 'include.inc.php') in order to re-order contents of the author field. Required Parameters:
      //   1. input:  contents of the author field
      //   2. input:  boolean value that specifies whether the author's family name comes first (within one author) in the source string
      //              ('true' means that the family name is followed by the given name (or initials), 'false' if it's the other way around)
      //
      //   3. input:  pattern describing old delimiter that separates different authors
      //   4. output: for all authors except the last author: new delimiter that separates different authors
      //   5. output: for the last author: new delimiter that separates the last author from all other authors
      //
      //   6. input:  pattern describing old delimiter that separates author name & initials (within one author)
      //   7. output: for the first author: new delimiter that separates author name & initials (within one author)
      //   8. output: for all authors except the first author: new delimiter that separates author name & initials (within one author)
      //   9. output: new delimiter that separates multiple initials (within one author)
      //  10. output: for the first author: boolean value that specifies if initials go *before* the author's name ['true'], or *after* the author's name ['false'] (which is the default in the db)
      //  11. output: for all authors except the first author: boolean value that specifies if initials go *before* the author's name ['true'], or *after* the author's name ['false'] (which is the default in the db)
      //  12. output: boolean value that specifies whether an author's full given name(s) shall be shortened to initial(s)
      //
      //  13. output: if the total number of authors is greater than the given number (integer >= 1), only the number of authors given in (14) will be included in the citation along with the string given in (15); keep empty if all authors shall be returned
      //  14. output: number of authors (integer >= 1) that is included in the citation if the total number of authors is greater than the number given in (13); keep empty if not applicable
      //  15. output: string that's appended to the number of authors given in (14) if the total number of authors is greater than the number given in (13); the actual number of authors can be printed by including '__NUMBER_OF_AUTHORS__' (without quotes) within the string
      //
      //  16. output: boolean value that specifies whether the re-ordered string shall be returned with higher ASCII chars HTML encoded
      $author = reArrangeAuthorContents(strip_tags($row['author']), // 1.  strip_tags added to avoid hyperlinks causing problems
					true, // 2.
					" *; *", // 3.
					", ", // 4.
					", " . $markupPatternsArray["ampersand"] . " ", // 5.
					" *, *", // 6.
					", ", // 7.
					", ", // 8.
					". ", // 9.
					false, // 10.
					false, // 11.
					true, // 12.
					"6", // 13.
					"6", // 14.
					", et al.", // 15.
					$encodeHTML); // 16.

      if (!ereg(", *$", $author))
	$record .= $author . ",";
      else
	$record .= $author;
    }

  if (!empty($row['title']))      // title
    {
      if (!empty($row['author']))
	$record .= " ";

      $record .= $markupPatternsArray["italic-prefix"] . $row['title'] . $markupPatternsArray["italic-suffix"];
      if (!ereg("[?!.,]$", $row['title']))
	$record .= ",";
    }

  if (!empty($row['abbrev_journal']))      // abbreviated journal name
    $record .= " " . $row['abbrev_journal'];
  
  // if there's no abbreviated journal name, we'll use the full journal name
  elseif (!empty($row['publication']))      // publication (= journal) name
    $record .= " " . $row['publication'];

  if (!empty($row['volume']))      // volume (=month)
    $record .= " " . $markupPatternsArray["bold-prefix"] . $row['volume'] . $markupPatternsArray["bold-suffix"];


  if (!empty($row['year']))
    $record .= "(" . $row['year'] . ")";

  if (!empty($row['pages']))
    $record .= ", " . formatPageInfo($row['pages'], $markupPatternsArray["endash"]);

  if (!empty($row['summary_language']))
    {
      $id = preg_replace('/arxiv:/i','',$row['summary_language']);
      $record .= " ([arXiv](http://arxiv.org/" . $id . "))";
    }


  // --- BEGIN POST-PROCESSING -----------------------------------------------------------------------------------------------------------

  // do some further cleanup:
  $record = trim($record); // remove any preceding or trailing whitespace


  return $record;
}

// --- END CITATION STYLE ---
?>
