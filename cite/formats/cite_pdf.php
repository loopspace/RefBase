<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./cite/formats/cite_pdf.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/cite/formats/cite_pdf.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    10-Jun-06, 02:04
	// Modified:   $Date: 2008-11-04 00:44:34 -0800 (Tue, 04 Nov 2008) $
	//             $Author: msteffens $
	//             $Revision: 1296 $

	// This is a citation format file (which must reside within the 'cite/formats/' sub-directory of your refbase root directory). It contains a
	// version of the 'citeRecords()' function that outputs a reference list from selected records in PDF format.
	// PDF format specification is available at <http://partners.adobe.com/public/developer/pdf/index_reference.html>, more info at <http://en.wikipedia.org/wiki/PDF>

	// LIMITATIONS: - export of cited references to PDF does currently only work with a latin1 database but not with UTF-8 (since I don't know how to write Unicode characters to PDF)
	//              - there's currently no conversion of en-/emdashes or markup for greek letters and super-/subscript (reasons are that I don't know how to print chars by code number)

	// --------------------------------------------------------------------


	// Include the pdf-php package
	require_once 'includes/classes/org/pdf-php/class.ezpdf.php';


	// --- BEGIN CITATION FORMAT ---

	// Requires the "PHP Pdf creation (pdf-php)" Package (by Wayne Munro, R&OS Ltd), which is available
	// under a public domain licence: <http://www.ros.co.nz/pdf>

	function citeRecords($result, $rowsFound, $query, $queryURL, $showQuery, $showLinks, $rowOffset, $showRows, $previousOffset, $nextOffset, $wrapResults, $citeStyle, $citeOrder, $citeType, $orderBy, $headerMsg, $userID, $viewType)
	{
		global $officialDatabaseName; // these variables are defined in 'ini.inc.php'
		global $databaseBaseURL;
		global $contentTypeCharset;
		global $pdfPageSize;

		global $client;

		// The array '$transtab_refbase_pdf' contains search & replace patterns for conversion from refbase markup to PDf markup & entities
		global $transtab_refbase_pdf; // defined in 'transtab_refbase_pdf.inc.php'

		// Initialize array variables:
		$yearsArray = array();
		$typeTitlesArray = array();

		// Define inline text markup to be used by the 'citeRecord()' function:
		$markupPatternsArray = array("bold-prefix"        => "<b>", // html-style fontshape markup is recognized and converted by the pdf-php package
		                             "bold-suffix"        => "</b>",
		                             "italic-prefix"      => "<i>",
		                             "italic-suffix"      => "</i>",
		                             "underline-prefix"   => "<u>",
		                             "underline-suffix"   => "</u>",
		                             "endash"             => "�", // see notes for "*-quote-*" below; we could also use "�" here
		                             "emdash"             => "�", // an emdash might also be faked with two endashes ("��")
		                             "ampersand"          => "&",
		                             "double-quote"       => '"',
		                             "double-quote-left"  => "�", // AFAIK, the ISO-8859-1 (latin1) character set does not have any curly quotes,
		                             "double-quote-right" => "�", // see e.g. <http://www.ramsch.org/martin/uni/fmi-hp/iso8859-1.html>; but ...
		                             "single-quote"       => "'",
		                             "single-quote-left"  => "�", // ... since the pdf-php package let's you replace an (unused) character for any other PostScript char (see below), we use
		                             "single-quote-right" => "�", // the '$diff' array below to replace e.g. "�" with a single left curly quote and "�" with a single right curly quote, etc
		                             "less-than"          => "<",
		                             "greater-than"       => ">",
		                             "newline"            => "\n"
		                            );

		// Defines search & replace 'actions' that will be applied upon PDF output to all those refbase fields that are listed
		// in the corresponding 'fields' element:
		$pdfSearchReplaceActionsArray = array(
		                                      array('fields'  => array("title", "publication", "abbrev_journal", "address", "keywords", "abstract", "orig_title", "series_title", "abbrev_series_title", "notes"),
		                                            'actions' => $transtab_refbase_pdf
		                                           )
		                                     );

		// For CLI queries, we'll allow paging thru the result set, i.e. we honour the values of the CLI options '-S|--start' ('$rowOffset')
		// and '-R|--rows' ('$showRows') ('$rowOffset' and '$showRows' are re-assigned in function 'seekInMySQLResultsToOffset()' in 'include.inc.php')
		if (eregi("^cli", $client)) // if the query originated from a command line client such as the "refbase" CLI client ("cli-refbase-1.0")
			$showMaxRows = $showRows; // show only rows up to the value given in '$showRows'
		else
			$showMaxRows = $rowsFound; // otherwise show all rows


		// Setup the basic PDF document structure (PDF functions defined in 'class.ezpdf.php'):
		$pdf = new Cezpdf($pdfPageSize, 'portrait'); // initialize PDF object

		if (!empty($headerMsg)) // adjust upper page margin if a custom header message was given
			$pageMarginTop = "70";
		else
			$pageMarginTop = "50";			

		$pdf -> ezSetMargins($pageMarginTop, 70, 50, 50); // set document margins (top, bottom, left, right)

		// Set fonts:
		$headingFont = 'includes/classes/org/pdf-php/fonts/Helvetica.afm';
		$textBodyFont = 'includes/classes/org/pdf-php/fonts/Times-Roman.afm';

		// Re-map character numbers from the 0->255 range to a named character, i.e. replace an (unused) character for any other PostScript char;
		// see the PDF reference for a list of supported PostScript/PDF character names: <http://www.adobe.com/devnet/pdf/pdf_reference.html>;
		// for the decimal code numbers of the ISO-8859-1 character set, see e.g.: <http://www.ramsch.org/martin/uni/fmi-hp/iso8859-1.html>
		$diff = array(
		               166 => 'endash', // "�"
		               169 => 'emdash', // "�"
		               170 => 'quotedblleft', // "�"
		               172 => 'quotedblright', // "�"
		               174 => 'quoteleft', // "�"
		               182 => 'quoteright' // "�"
		             );

		// Select a font:
		$pdf->selectFont($textBodyFont, array('encoding' => 'WinAnsiEncoding', 'differences' => $diff));

		$pdf->openHere('Fit');

		// Put a footer (and optionally a header) on all the pages:
		$all = $pdf->openObject(); // start an independent object; all further writes to a page will actually go into this object, until a 'closeObject()' call is made
		$pdf->saveState();

		$pdf->setStrokeColor(0, 0, 0, 1); // set line color
		$pdf->setLineStyle(0.5); // set line width

		// - print header line and header message at the specified x/y position:
		if (!empty($headerMsg)) // if a custom header message was given
		{
			// Remove any colon (":") from end of header message:
			$headerMsg = trimTextPattern($headerMsg, ":", false, true); // function 'trimTextPattern()' is defined in 'include.inc.php'

			// Decode any HTML entities:
			// (these may occur in the header message e.g. if the user's preferred display language is not English but German or French, etc)
			$headerMsg = decodeHTML($contentTypeCharset, $headerMsg); // function 'decodeHTML()' is defined in 'include.inc.php', and '$contentTypeCharset' is defined in 'ini.inc.php'

			// Convert refbase markup in the header message into appropriate PDF markup & entities:
			$headerMsg = searchReplaceText($transtab_refbase_pdf, $headerMsg, true); // function 'searchReplaceText()' is defined in 'include.inc.php'

			if ($pdfPageSize == 'a4') // page dimensions 'a4': 595.28 x 841.89 pt.
			{
				$pdf->line(20, 800, 575, 800);
				$pdf->addText(50, 805, 10, $headerMsg);
			}
			elseif ($pdfPageSize == 'letter') // page dimensions 'letter': 612 x 792 pt.
			{
				$pdf->line(20, 750, 592, 750);
				$pdf->addText(50, 755, 10, $headerMsg);
			}
		}

		// - print footer line and footer text at the specified x/y position:
		if ($pdfPageSize == 'a4')
		{
			$pdf->line(20, 40, 575, 40);
			$pdf->addText(50, 28, 10, $officialDatabaseName . ' � ' . $databaseBaseURL); // w.r.t. the endash, see notes at '$markupPatternsArray' and '$diff' above
		}
		elseif ($pdfPageSize == 'letter')
		{
			$pdf->line(20, 40, 592, 40);
			$pdf->addText(50, 28, 10, $officialDatabaseName . ' � ' . $databaseBaseURL);
		}

		$pdf->restoreState();
		$pdf->closeObject(); // close the currently open object; further writes will now go to the current page
		$pdf->addObject($all, 'all'); // note that object can be told to appear on just odd or even pages by changing 'all' to 'odd' or 'even'

		// Start printing page numbers:
		if ($pdfPageSize == 'a4')
		{
			$pdf->ezStartPageNumbers(550, 28, 10, '', '', 1);
		}
		elseif ($pdfPageSize == 'letter')
		{
			$pdf->ezStartPageNumbers(567, 28, 10, '', '', 1);
		}


		// LOOP OVER EACH RECORD:
		// Fetch one page of results (or less if on the last page)
		// (i.e., upto the limit specified in $showMaxRows) fetch a row into the $row array and ...
		for ($rowCounter=0; (($rowCounter < $showMaxRows) && ($row = @ mysql_fetch_array($result))); $rowCounter++)
		{
			foreach ($row as $rowFieldName => $rowFieldValue)
				// Apply search & replace 'actions' to all fields that are listed in the 'fields' element of the arrays contained in '$pdfSearchReplaceActionsArray':
				foreach ($pdfSearchReplaceActionsArray as $fieldActionsArray)
					if (in_array($rowFieldName, $fieldActionsArray['fields']))
						$row[$rowFieldName] = searchReplaceText($fieldActionsArray['actions'], $row[$rowFieldName], true); // function 'searchReplaceText()' is defined in 'include.inc.php'


			// Order attributes according to the chosen output style & record type:
			$record = citeRecord($row, $citeStyle, $citeType, $markupPatternsArray, false); // function 'citeRecord()' is defined in the citation style file given in '$citeStyleFile' (which, in turn, must reside in the 'cite' directory of the refbase root directory), see function 'generateCitations()'


			// Print out the current record:
			if (!empty($record)) // unless the record buffer is empty...
			{
				// Print any section heading(s):
				if (eregi("year|type", $citeOrder))
				{
					$headingPrefix = "";
					$headingSuffix = "";
					$sectionMarkupPrefix = "<b>";
					$sectionMarkupSuffix = "</b>\n";
					$subSectionMarkupPrefix = "";
					$subSectionMarkupSuffix = "\n";

					if ($citeOrder == "type-year")
						$sectionMarkupSuffix .= "\n";

					list($yearsArray, $typeTitlesArray, $sectionHeading) = generateSectionHeading($yearsArray, $typeTitlesArray, $row, $citeOrder, $headingPrefix, $headingSuffix, $sectionMarkupPrefix, $sectionMarkupSuffix, $subSectionMarkupPrefix, $subSectionMarkupSuffix); // function 'generateSectionHeading()' is defined in 'cite.inc.php'

					if (!empty($sectionHeading))
					{
						$pdf->selectFont($headingFont, array('encoding' => 'WinAnsiEncoding', 'differences' => $diff)); // use Helvetica
						$pdf->ezText($sectionHeading, '14', array('justification' => 'left')); // create heading using a font size of 14pt
					}
				}

				// If character encoding is not UTF-8 already, convert record text to UTF-8:
//				if ($contentTypeCharset != "UTF-8")
//					$record = convertToCharacterEncoding("UTF-8", "IGNORE", $record); // function 'convertToCharacterEncoding()' is defined in 'include.inc.php'

				// NOTE: Export of cited references to PDF does currently only work with a latin1 database but not with UTF-8 (since I don't know how to write Unicode characters to PDF).
				//       As a workaround, we could convert UTF-8 characters to latin1 if possible (and omit any other higher ASCII chars)
				// TODO: While this workaround indeed fixes display issues with higher ASCII chars that have equivalents in the latin1 charset, this will currently swallow higher ASCII
				//       hyphens/dashes such as endashes (which display correctly without this workaround).
//				if ($contentTypeCharset == "UTF-8")
//					$record = convertToCharacterEncoding("ISO-8859-1", "TRANSLIT", $record, "UTF-8"); // function 'convertToCharacterEncoding()' is defined in 'include.inc.php'

				// Set paragraph text options:
				$textOptions = array(
				                     'justification' => 'full'
				//                   'aleft'         => '50',
				//                   'aright'        => '545'
				                    );
				// possible array options:
				// 'left'=> number, gap to leave from the left margin
				// 'right'=> number, gap to leave from the right margin
				// 'aleft'=> number, absolute left position (overrides 'left')
				// 'aright'=> number, absolute right position (overrides 'right')
				// 'justification' => 'left','right','center','centre','full'
				// 
				// only set one of the next two items (leading overrides spacing)
				// 'leading' => number, defines the total height taken by the line, independent of the font height.
				// 'spacing' => a real number, though usually set to one of 1, 1.5, 2 (line spacing as used in word processing)

				// Write PDF paragraph:
				$pdf->selectFont($textBodyFont, array('encoding' => 'WinAnsiEncoding')); // use Times-Roman
				$pdf->ezText($record . "\n", '12', $textOptions); // create text block with record text using "Times Roman" and a font size of 12pt
			}
		}

		return $pdf->ezStream();
	}

	// --- END CITATION FORMAT ---
?>
