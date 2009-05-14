<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./cite/styles/cite_AnnGlaciol_JGlaciol.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/cite/styles/cite_AnnGlaciol_JGlaciol.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    07-Sep-05, 14:53
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// This is a citation style file (which must reside within the 'cite/styles/' sub-directory of your refbase root directory). It contains a
	// version of the 'citeRecord()' function that outputs a reference list from selected records according to the citation style used by
	// the journals "Annals of Glaciology" and "Journal of Glaciology" (International Glaciological Society, www.igsoc.org).

	// --------------------------------------------------------------------

	// --- BEGIN CITATION STYLE ---

	function citeRecord($row, $citeStyle, $citeType, $markupPatternsArray, $encodeHTML)
	{
		$record = ""; // make sure that our buffer variable is empty

		// --- BEGIN TYPE = JOURNAL ARTICLE / MAGAZINE ARTICLE / NEWSPAPER ARTICLE --------------------------------------------------------------

		if (ereg("^(Journal Article|Magazine Article|Newspaper Article)$", $row['type']))
			{
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
						$author = reArrangeAuthorContents($row['author'], // 1.
						                                  true, // 2.
						                                  " *; *", // 3.
						                                  ", ", // 4.
						                                  " and ", // 5.
						                                  " *, *", // 6.
						                                  ", ", // 7.
						                                  " ", // 8.
						                                  ".", // 9.
						                                  false, // 10.
						                                  true, // 11.
						                                  true, // 12.
						                                  "6", // 13.
						                                  "1", // 14.
						                                  " " . $markupPatternsArray["italic-prefix"] . "and __NUMBER_OF_AUTHORS__ others" . $markupPatternsArray["italic-suffix"], // 15.
						                                  $encodeHTML); // 16.

						if (!ereg("\. *$", $author))
							$record .= $author . ".";
						else
							$record .= $author;
					}

				if (!empty($row['year']))      // year
					{
						if (!empty($row['author']))
							$record .= " ";

						$record .= $row['year'] . ".";
					}

				if (!empty($row['title']))      // title
					{
						if (!empty($row['author']) || !empty($row['year']))
							$record .= " ";

						$record .= $row['title'];
						if (!ereg("[?!.]$", $row['title']))
							$record .= ".";
					}

				// From here on we'll assume that at least one of the fields 'author', 'year' or 'title' did contain some contents
				// if this is not the case, the output string will begin with a space. However, any preceding/trailing whitespace will be removed at the cleanup stage (see below)

				if (!empty($row['abbrev_journal']))      // abbreviated journal name
					$record .= " " . $markupPatternsArray["italic-prefix"] . $row['abbrev_journal'] . $markupPatternsArray["italic-suffix"];

				// if there's no abbreviated journal name, we'll use the full journal name
				elseif (!empty($row['publication']))      // publication (= journal) name
					$record .= " " . $markupPatternsArray["italic-prefix"] . $row['publication'] . $markupPatternsArray["italic-suffix"];

				if (!empty($row['volume']))      // volume
					{
						if (!empty($row['abbrev_journal']) || !empty($row['publication']))
							$record .= ",";

						$record .= " " . $markupPatternsArray["bold-prefix"] . $row['volume'] . $markupPatternsArray["bold-suffix"];
					}

				if (!empty($row['issue']))      // issue
					$record .= "(" . $row['issue'] . ")";

				if ($row['online_publication'] == "yes") // this record refers to an online article
				{
					// instead of any pages info (which normally doesn't exist for online publications) we append
					// an optional string (given in 'online_citation') plus the DOI:

					if (!empty($row['online_citation']))      // online_citation
					{
						if (!empty($row['volume']) || !empty($row['issue']) || !empty($row['abbrev_journal']) || !empty($row['publication'])) // only add "," if either volume, issue, abbrev_journal or publication isn't empty
							$record .= ",";

						$record .= " " . $row['online_citation'];
					}

					if (!empty($row['doi']))      // doi
					{
						if (!empty($row['online_citation']) OR (empty($row['online_citation']) AND (!empty($row['volume']) || !empty($row['issue']) || !empty($row['abbrev_journal']) || !empty($row['publication'])))) // only add "," if online_citation isn't empty, or else if either volume, issue, abbrev_journal or publication isn't empty
							$record .= ".";

						$record .= " (" . $row['doi'] . ".)";
					}
				}
				else // $row['online_publication'] == "no" -> this record refers to a printed article, so we append any pages info instead:
				{
					if (!empty($row['pages']))      // pages
					{
						if (!empty($row['volume']) || !empty($row['issue']) || !empty($row['abbrev_journal']) || !empty($row['publication'])) // only add "," if either volume, issue, abbrev_journal or publication isn't empty
							$record .= ", ";

						$record .= formatPageInfo($row['pages'], $markupPatternsArray["endash"]); // function 'formatPageInfo()' is defined in 'cite.inc.php'
					}
				}

				if (!ereg("\.\)? *$", $record))
					$record .= ".";
			}

		// --- BEGIN TYPE = ABSTRACT / BOOK CHAPTER / CONFERENCE ARTICLE ------------------------------------------------------------------------

		elseif (ereg("^(Abstract|Book Chapter|Conference Article)$", $row['type']))
			{
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
						$author = reArrangeAuthorContents($row['author'], // 1.
						                                  true, // 2.
						                                  " *; *", // 3.
						                                  ", ", // 4.
						                                  " and ", // 5.
						                                  " *, *", // 6.
						                                  ", ", // 7.
						                                  " ", // 8.
						                                  ".", // 9.
						                                  false, // 10.
						                                  true, // 11.
						                                  true, // 12.
						                                  "6", // 13.
						                                  "1", // 14.
						                                  " " . $markupPatternsArray["italic-prefix"] . "and __NUMBER_OF_AUTHORS__ others" . $markupPatternsArray["italic-suffix"], // 15.
						                                  $encodeHTML); // 16.

						if (!ereg("\. *$", $author))
							$record .= $author . ".";
						else
							$record .= $author;
					}

				if (!empty($row['year']))      // year
					{
						if (!empty($row['author']))
							$record .= " ";

						$record .= $row['year'] . ".";
					}

				if (!empty($row['title']))      // title
					{
						if (!empty($row['author']) || !empty($row['year']))
							$record .= " ";

						$record .= $row['title'];
						if (!ereg("[?!.]$", $row['title']))
							$record .= ".";
					}

				// From here on we'll assume that at least one of the fields 'author', 'year' or 'title' did contain some contents
				// if this is not the case, the output string will begin with a space. However, any preceding/trailing whitespace will be removed at the cleanup stage (see below)

				if (!empty($row['editor']))      // editor
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
						$editor = reArrangeAuthorContents($row['editor'], // 1.
						                                  true, // 2.
						                                  " *; *", // 3.
						                                  ", ", // 4.
						                                  " and ", // 5.
						                                  " *, *", // 6.
						                                  ", ", // 7.
						                                  " ", // 8.
						                                  ".", // 9.
						                                  false, // 10.
						                                  true, // 11.
						                                  true, // 12.
						                                  "6", // 13.
						                                  "1", // 14.
						                                  " " . $markupPatternsArray["italic-prefix"] . "and __NUMBER_OF_AUTHORS__ others" . $markupPatternsArray["italic-suffix"], // 15.
						                                  $encodeHTML); // 16.

						$record .= " " . $markupPatternsArray["italic-prefix"] . "In" . $markupPatternsArray["italic-suffix"] . " " . $editor;
						if (ereg("^[^;\r\n]+(;[^;\r\n]+)+$", $row['editor'])) // there are at least two editors (separated by ';')
							$record .= ", " . $markupPatternsArray["italic-prefix"] . "eds" . $markupPatternsArray["italic-suffix"] . ".";
						else // there's only one editor (or the editor field is malformed with multiple editors but missing ';' separator[s])
							$record .= ", " . $markupPatternsArray["italic-prefix"] . "ed" . $markupPatternsArray["italic-suffix"] . ".";
					}

				$publication = ereg_replace("[ \r\n]*\(Eds?:[^\)\r\n]*\)", "", $row['publication']);
				if (!empty($publication))      // publication
					$record .= " " . $markupPatternsArray["italic-prefix"] . $publication . $markupPatternsArray["italic-suffix"] . ".";

				if (!empty($row['place']))      // place
					$record .= " " . $row['place'];

				if (!empty($row['publisher']))      // publisher
					{
						if (!empty($row['place']))
							$record .= ",";

						$record .= " " . $row['publisher'];
					}

				if (!empty($row['pages']))      // pages
				{
					if (!empty($row['place']) || !empty($row['publisher']))
						$record .= ", ";

					$record .= formatPageInfo($row['pages'], $markupPatternsArray["endash"]); // function 'formatPageInfo()' is defined in 'cite.inc.php'
				}

				if (!ereg("\. *$", $record))
					$record .= ".";

				if (!empty($row['abbrev_series_title']) OR !empty($row['series_title'])) // if there's either a full or an abbreviated series title
					{
						$record .= " (";

						if (!empty($row['abbrev_series_title']))
							$record .= $row['abbrev_series_title'];      // abbreviated series title

						// if there's no abbreviated series title, we'll use the full series title instead:
						elseif (!empty($row['series_title']))
							$record .= $row['series_title'];      // full series title

						if (!empty($row['series_volume'])||!empty($row['series_issue']))
							$record .= " ";

						if (!empty($row['series_volume']))      // series volume
							$record .= $row['series_volume'];

						if (!empty($row['series_issue']))      // series issue (I'm not really sure if -- for this cite style -- the series issue should be rather omitted here)
							$record .= "(" . $row['series_issue'] . ")";

						$record .= ".)";
					}
			}

		// --- BEGIN TYPE = BOOK WHOLE / CONFERENCE VOLUME / JOURNAL / MANUAL / MANUSCRIPT / MAP / MISCELLANEOUS / PATENT / REPORT / SOFTWARE ---

		else // if (ereg("Book Whole|Conference Volume|Journal|Manual|Manuscript|Map|Miscellaneous|Patent|Report|Software", $row['type']))
			// note that this also serves as a fallback: unrecognized resource types will be formatted similar to whole books
			{
				if (!empty($row['author']))      // author
					{
						$author = ereg_replace("[ \r\n]*\(eds?\)", "", $row['author']);

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
						$author = reArrangeAuthorContents($author, // 1.
						                                  true, // 2.
						                                  " *; *", // 3.
						                                  ", ", // 4.
						                                  " and ", // 5.
						                                  " *, *", // 6.
						                                  ", ", // 7.
						                                  " ", // 8.
						                                  ".", // 9.
						                                  false, // 10.
						                                  true, // 11.
						                                  true, // 12.
						                                  "6", // 13.
						                                  "1", // 14.
						                                  " " . $markupPatternsArray["italic-prefix"] . "and __NUMBER_OF_AUTHORS__ others" . $markupPatternsArray["italic-suffix"], // 15.
						                                  $encodeHTML); // 16.

						// if the author is actually the editor of the resource we'll append ', ed' (or ', eds') to the author string:
						// [to distinguish editors from authors in the 'author' field, the 'modify.php' script does append ' (ed)' or ' (eds)' if appropriate,
						//  so we're just checking for these identifier strings here. Alternatively, we could check whether the editor field matches the author field]
						if (ereg("[ \r\n]*\(ed\)", $row['author'])) // single editor
							$author = $author . ", " . $markupPatternsArray["italic-prefix"] . "ed" . $markupPatternsArray["italic-suffix"];
						elseif (ereg("[ \r\n]*\(eds\)", $row['author'])) // multiple editors
							$author = $author . ", " . $markupPatternsArray["italic-prefix"] . "eds" . $markupPatternsArray["italic-suffix"];

						if (!ereg("\. *$", $author))
							$record .= $author . ".";
						else
							$record .= $author;
					}

				if (!empty($row['year']))      // year
					{
						if (!empty($row['author']))
							$record .= " ";

						$record .= $row['year'] . ".";
					}

				if (!empty($row['title']))      // title
					{
						if (!empty($row['author']) || !empty($row['year']))
							$record .= " ";

						$record .= $markupPatternsArray["italic-prefix"] . $row['title'] . $markupPatternsArray["italic-suffix"];
						if (!ereg("[?!.]$", $row['title']))
							$record .= ".";
					}

				if (!empty($row['thesis']))      // thesis
					{
						$record .= " (" . $row['thesis'];
						$record .= ", " . $row['publisher'] . ".)";
					}
				else  // not a thesis
					{
						if (!empty($row['place']))      // place
							$record .= " " . $row['place'];

						if (!empty($row['publisher']))      // publisher
							{
								if (!empty($row['place']))
									$record .= ",";

								$record .= " " . $row['publisher'];
							}

//						if (!empty($row['pages']))      // pages
//						{
//							if (!empty($row['place']) || !empty($row['publisher']))
//								$record .= ", ";
//
//							$record .= formatPageInfo($row['pages'], $markupPatternsArray["endash"]); // function 'formatPageInfo()' is defined in 'cite.inc.php'
//						}

						if (!ereg("\. *$", $record))
							$record .= ".";
					}

				if (!empty($row['abbrev_series_title']) OR !empty($row['series_title'])) // if there's either a full or an abbreviated series title
					{
						$record .= " (";

						if (!empty($row['abbrev_series_title']))
							$record .= $row['abbrev_series_title'];      // abbreviated series title

						// if there's no abbreviated series title, we'll use the full series title instead:
						elseif (!empty($row['series_title']))
							$record .= $row['series_title'];      // full series title

						if (!empty($row['series_volume'])||!empty($row['series_issue']))
							$record .= " ";

						if (!empty($row['series_volume']))      // series volume
							$record .= $row['series_volume'];

						if (!empty($row['series_issue']))      // series issue (I'm not really sure if -- for this cite style -- the series issue should be rather omitted here)
							$record .= "(" . $row['series_issue'] . ")";

						$record .= ".)";
					}
			}

		// --- BEGIN POST-PROCESSING -----------------------------------------------------------------------------------------------------------

		// do some further cleanup:
		$record = trim($record); // remove any preceding or trailing whitespace


		return $record;
	}

	// --- END CITATION STYLE ---
?>
